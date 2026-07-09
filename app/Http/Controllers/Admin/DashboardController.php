<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarView;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Inquiry;
use App\Models\PageView;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Dozwolone zakresy w dniach — cokolwiek innego z ?range spada do 30. */
    private const RANGES = [7, 30, 90];

    public function index(Request $request)
    {
        $days = (int) $request->query('range', 30);
        if (!in_array($days, self::RANGES, true)) $days = 30;

        // Okno bieżące i poprzednie tej samej długości — stąd biorą się
        // wszystkie "+12% względem poprzednich 30 dni" na kafelkach.
        $to        = now();
        $from      = now()->subDays($days);
        $prevFrom  = now()->subDays($days * 2);
        $prevTo    = $from;

        $traffic     = $this->traffic($from, $to);
        $prevTraffic = $this->traffic($prevFrom, $prevTo);

        return view('admin.dashboard', [
            'days'        => $days,
            'ranges'      => self::RANGES,
            'traffic'     => $traffic,
            'deltas'      => $this->deltas($traffic, $prevTraffic),
            'chart'       => $this->chart($days),
            'channels'    => $this->channels($from, $to),
            'events'      => $this->events($from, $to),
            'devices'     => $this->devices($from, $to),
            'topCars'     => $this->topCars($from, $to),
            'topPages'    => $this->topPages($from, $to),
            'landings'    => $this->landingPages($from, $to),
            'stats'       => $this->inventory(),
            'recentCars'  => Car::with(['brand', 'images'])->latest()->take(6)->get(),
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
        ]);
    }

    /**
     * Metryki ruchu w oknie czasu.
     *
     * Uwaga na "pageviews": middleware deduplikuje odsłonę tej samej ścieżki
     * w tej samej sesji przez 30 minut, więc to liczba UNIKALNYCH odsłon
     * stron, nie surowych trafień. Bounce i czas sesji liczą się z tego
     * samego źródła, więc są spójne — ale nie porównuj ich 1:1 z GA.
     */
    private function traffic(Carbon $from, Carbon $to): array
    {
        $base = fn() => PageView::whereBetween('created_at', [$from, $to]);

        $pageviews = (clone $base())->count();
        $visitors  = (clone $base())->distinct()->count('visitor_id');
        $sessions  = (clone $base())->distinct()->count('session_id');

        // Sesje jednoodsłonowe = odbicia.
        $singlePageSessions = DB::table('page_views')
            ->select('session_id')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->havingRaw('COUNT(*) = 1')
            ->get()
            ->count();

        // Czas sesji = ostatnia minus pierwsza odsłona. Sesje jednoodsłonowe
        // mają z definicji 0s (nie wiemy, kiedy użytkownik wyszedł) i celowo
        // NIE wchodzą do średniej — inaczej zaniżałyby ją do zera.
        $durations = DB::table('page_views')
            ->selectRaw('session_id, MIN(created_at) as first_seen, MAX(created_at) as last_seen, COUNT(*) as hits')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $avgSeconds = $durations->count()
            ? (int) round($durations->avg(fn($d) => Carbon::parse($d->last_seen)->diffInSeconds(Carbon::parse($d->first_seen))))
            : 0;

        // Nowi = odwiedzający, których PIERWSZA odsłona w historii wpada w okno.
        $newVisitors = DB::table('page_views')
            ->select('visitor_id')
            ->whereNotNull('visitor_id')
            ->groupBy('visitor_id')
            ->havingRaw('MIN(created_at) >= ?', [$from])
            ->get()
            ->count();

        $contacts = Event::whereBetween('created_at', [$from, $to])
            ->whereIn('name', Analytics::CONTACT_EVENTS)
            ->count();

        $leads = Inquiry::whereBetween('created_at', [$from, $to])->count()
            + ContactMessage::whereBetween('created_at', [$from, $to])->count();

        return [
            'pageviews'    => $pageviews,
            'visitors'     => $visitors,
            'sessions'     => $sessions,
            'new_visitors' => $newVisitors,
            'car_views'    => CarView::whereBetween('created_at', [$from, $to])->count(),
            'contacts'     => $contacts,
            'leads'        => $leads,
            'bounce_rate'  => $sessions > 0 ? round($singlePageSessions / $sessions * 100, 1) : 0.0,
            'avg_seconds'  => $avgSeconds,
            'conversion'   => $sessions > 0 ? round($leads / $sessions * 100, 2) : 0.0,
            'pages_per_session' => $sessions > 0 ? round($pageviews / $sessions, 2) : 0.0,
        ];
    }

    /** Zmiana procentowa bieżącego okna względem poprzedniego. */
    private function deltas(array $now, array $prev): array
    {
        $out = [];

        foreach ($now as $key => $value) {
            $before = $prev[$key] ?? 0;

            if ($before == 0) {
                // Z zera nie da się policzyć procentu. null = dashboard pokaże "—".
                $out[$key] = $value > 0 ? null : 0.0;
                continue;
            }

            $out[$key] = round(($value - $before) / $before * 100, 1);
        }

        return $out;
    }

    /** Szereg dzienny — jedno zapytanie na serię zamiast N zapytań w pętli. */
    private function chart(int $days): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $daily = fn(string $table) => DB::table($table)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', $from)
            ->groupBy('d')
            ->pluck('c', 'd');

        $views  = $daily('page_views');
        $cars   = $daily('car_views');
        $events = DB::table('events')
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', $from)
            ->whereIn('name', Analytics::CONTACT_EVENTS)
            ->groupBy('d')
            ->pluck('c', 'd');

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d   = now()->subDays($i);
            $key = $d->toDateString();

            $out[] = [
                'label'    => $d->format('d.m'),
                'views'    => (int) ($views[$key] ?? 0),
                'cars'     => (int) ($cars[$key] ?? 0),
                'contacts' => (int) ($events[$key] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * Kanały pozyskania. Liczone z PIERWSZEJ odsłony każdej sesji — inaczej
     * własna domena zdominowałaby raport jako "odesłania", bo referer jest
     * zapisywany przy każdym kliknięciu w nawigacji.
     */
    private function channels(Carbon $from, Carbon $to): array
    {
        $entryIds = DB::table('page_views')
            ->selectRaw('MIN(id) as id')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->pluck('id');

        if ($entryIds->isEmpty()) return [];

        $entries = PageView::whereIn('id', $entryIds)
            ->get(['referer', 'utm_source', 'utm_medium']);

        $grouped = $entries
            ->groupBy(fn($e) => Analytics::channel($e->referer, $e->utm_source, $e->utm_medium))
            ->map->count()
            ->sortDesc();

        $total = max(1, $grouped->sum());

        return $grouped->map(fn($count, $name) => [
            'name'    => $name,
            'count'   => $count,
            'percent' => round($count / $total * 100, 1),
        ])->values()->all();
    }

    /** Wejściowe strony (landing pages) — z tych samych pierwszych odsłon. */
    private function landingPages(Carbon $from, Carbon $to)
    {
        $entryIds = DB::table('page_views')
            ->selectRaw('MIN(id) as id')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->pluck('id');

        if ($entryIds->isEmpty()) return collect();

        return PageView::whereIn('id', $entryIds)
            ->selectRaw('path, COUNT(*) as count')
            ->groupBy('path')
            ->orderByDesc('count')
            ->take(6)
            ->get();
    }

    /** Zdarzenia z etykietami PL, posortowane malejąco. */
    private function events(Carbon $from, Carbon $to): array
    {
        $rows = Event::whereBetween('created_at', [$from, $to])
            ->selectRaw('name, COUNT(*) as count, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('name')
            ->orderByDesc('count')
            ->get();

        return $rows->map(fn($r) => [
            'name'     => $r->name,
            'label'    => Analytics::eventLabel($r->name),
            'count'    => (int) $r->count,
            'visitors' => (int) $r->visitors,
            'contact'  => in_array($r->name, Analytics::CONTACT_EVENTS, true),
        ])->all();
    }

    private function devices(Carbon $from, Carbon $to): array
    {
        $rows = PageView::whereBetween('created_at', [$from, $to])
            ->whereNotNull('device')
            ->selectRaw('device, COUNT(DISTINCT session_id) as count')
            ->groupBy('device')
            ->pluck('count', 'device');

        $total = max(1, $rows->sum());

        $labels = ['mobile' => 'Telefon', 'tablet' => 'Tablet', 'desktop' => 'Komputer'];

        return collect($labels)
            ->map(fn($label, $key) => [
                'label'   => $label,
                'count'   => (int) ($rows[$key] ?? 0),
                'percent' => round(($rows[$key] ?? 0) / $total * 100, 1),
            ])
            ->sortByDesc('count')
            ->values()
            ->all();
    }

    /**
     * Top auta w oknie: odsłony, unikalni oglądający i zapytania. Kolumna
     * "zapytania / odsłony" mówi, czy oferta faktycznie konwertuje, czy
     * tylko zbiera kliknięcia.
     */
    private function topCars(Carbon $from, Carbon $to)
    {
        $viewsPerCar = CarView::whereBetween('created_at', [$from, $to])
            ->selectRaw('car_id, COUNT(*) as views, COUNT(DISTINCT visitor_id) as uniques')
            ->groupBy('car_id')
            ->orderByDesc('views')
            ->take(6)
            ->get()
            ->keyBy('car_id');

        if ($viewsPerCar->isEmpty()) return collect();

        $inquiriesPerCar = Inquiry::whereBetween('created_at', [$from, $to])
            ->whereIn('car_id', $viewsPerCar->keys())
            ->selectRaw('car_id, COUNT(*) as c')
            ->groupBy('car_id')
            ->pluck('c', 'car_id');

        return Car::with(['brand', 'images'])
            ->whereIn('id', $viewsPerCar->keys())
            ->get()
            ->map(function ($car) use ($viewsPerCar, $inquiriesPerCar) {
                $row = $viewsPerCar[$car->id];
                $car->range_views     = (int) $row->views;
                $car->range_uniques   = (int) $row->uniques;
                $car->range_inquiries = (int) ($inquiriesPerCar[$car->id] ?? 0);
                $car->range_cvr       = $car->range_views > 0
                    ? round($car->range_inquiries / $car->range_views * 100, 1)
                    : 0.0;
                return $car;
            })
            ->sortByDesc('range_views')
            ->values();
    }

    private function topPages(Carbon $from, Carbon $to)
    {
        return PageView::selectRaw('path, COUNT(*) as count, COUNT(DISTINCT visitor_id) as visitors')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('path')
            ->orderByDesc('count')
            ->take(6)
            ->get();
    }

    private function inventory(): array
    {
        $activeCars = Car::where('status', 'active')->where('is_sold', false);

        return [
            'total_cars'    => Car::count(),
            'active_cars'   => (clone $activeCars)->count(),
            'sold_cars'     => Car::where('is_sold', true)->count(),
            'draft_cars'    => Car::where('status', 'draft')->count(),
            'featured_cars' => Car::where('is_featured', true)->count(),
            'brands'        => Brand::count(),
            'stock_value'   => (clone $activeCars)->sum('price'),
            'avg_price'     => (int) (clone $activeCars)->avg('price'),
            'new_last_30'   => Car::where('created_at', '>=', now()->subDays(30))->count(),
            'unread_msgs'   => ContactMessage::whereNull('read_at')->count(),
            'total_msgs'    => ContactMessage::count(),
            'unread_inquiries' => Inquiry::whereNull('read_at')->count(),
        ];
    }
}
