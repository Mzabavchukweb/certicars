<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarView;
use App\Models\ContactMessage;
use App\Models\PageView;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeCars = Car::where('status', 'active')->where('is_sold', false);

        $stats = [
            'total_cars'     => Car::count(),
            'active_cars'    => (clone $activeCars)->count(),
            'sold_cars'      => Car::where('is_sold', true)->count(),
            'draft_cars'     => Car::where('status', 'draft')->count(),
            'featured_cars'  => Car::where('is_featured', true)->count(),
            'brands'         => Brand::count(),
            'stock_value'    => (clone $activeCars)->sum('price'),
            'avg_price'      => (int) (clone $activeCars)->avg('price'),
            'new_last_30'    => Car::where('created_at', '>=', now()->subDays(30))->count(),
            'unread_msgs'    => ContactMessage::whereNull('read_at')->count(),
            'total_msgs'     => ContactMessage::count(),

            'views_today'    => PageView::whereDate('created_at', today())->count(),
            'views_7d'       => PageView::where('created_at', '>=', now()->subDays(7))->count(),
            'views_30d'      => PageView::where('created_at', '>=', now()->subDays(30))->count(),
            'views_total'    => PageView::count(),
            'car_views_total'=> CarView::count(),
            'car_views_7d'   => CarView::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentCars     = Car::with(['brand', 'images'])->latest()->take(6)->get();
        $recentMessages = ContactMessage::latest()->take(5)->get();

        $chart = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $chart[] = [
                'label' => $d->format('d.m'),
                'views' => PageView::whereDate('created_at', $d)->count(),
                'cars'  => CarView::whereDate('created_at', $d)->count(),
                'msgs'  => ContactMessage::whereDate('created_at', $d)->count(),
            ];
        }

        $topCars = Car::select('cars.*')
            ->selectSub(CarView::selectRaw('COUNT(*)')->whereColumn('car_id', 'cars.id'), 'views_count')
            ->with(['brand', 'images'])
            ->orderByDesc('views_count')
            ->take(5)
            ->get()
            ->filter(fn($c) => $c->views_count > 0)
            ->values();

        $topReferers = PageView::select(DB::raw("
            CASE
                WHEN referer IS NULL OR referer = '' THEN 'Bezpośrednio'
                ELSE referer
            END as source
        "), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('source')
            ->orderByDesc('count')
            ->take(6)
            ->get()
            ->map(function ($r) {
                $r->display = $r->source === 'Bezpośrednio'
                    ? 'Bezpośrednio'
                    : (parse_url($r->source, PHP_URL_HOST) ?: $r->source);
                return $r;
            });

        $topPages = PageView::select('path', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('path')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentCars', 'recentMessages', 'chart', 'topCars', 'topReferers', 'topPages'
        ));
    }
}
