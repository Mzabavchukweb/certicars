@extends('admin.layouts.app')
@section('title','Dashboard')
@section('actions')
<a href="{{ route('admin.cars.create') }}" class="btn btn-blue"><i data-lucide="plus"></i> Dodaj samochód</a>
@endsection
@section('content')

@php
    // Delta: null = nie da się policzyć procentu (poprzedni okres miał zero).
    // Dla bounce_rate i avg_seconds spadek jest DOBRY — stąd $invert.
    $delta = function ($key, $invert = false) use ($deltas) {
        $d = $deltas[$key] ?? 0;
        if ($d === null) return ['text' => 'nowe', 'color' => '#047857', 'icon' => 'trending-up'];
        if (abs($d) < 0.05) return ['text' => 'bez zmian', 'color' => 'var(--text-3)', 'icon' => 'minus'];
        $good = $invert ? $d < 0 : $d > 0;
        return [
            'text'  => ($d > 0 ? '+' : '') . number_format($d, 1, ',', ' ') . '%',
            'color' => $good ? '#047857' : '#b91c1c',
            'icon'  => $d > 0 ? 'trending-up' : 'trending-down',
        ];
    };
    $dur = function ($s) {
        if ($s <= 0) return '—';
        return $s >= 60 ? intdiv($s, 60) . 'm ' . ($s % 60) . 's' : $s . 's';
    };
    $num = fn($n) => number_format($n, 0, ',', ' ');
@endphp

{{-- ============ ZAKRES DAT ============ --}}
<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <h2 style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);font-weight:700;margin:0">
        Ruch na stronie — ostatnie {{ $days }} dni
    </h2>
    <div style="display:flex;gap:4px;background:var(--bg);padding:3px;border-radius:9px">
        @foreach($ranges as $r)
            <a href="{{ route('admin.dashboard', ['range' => $r]) }}"
               style="padding:6px 14px;border-radius:7px;font-size:12.5px;font-weight:700;text-decoration:none;
                      {{ $r === $days ? 'background:#fff;color:var(--blue);box-shadow:0 1px 3px rgba(0,0,0,.08)' : 'color:var(--text-3)' }}">
                {{ $r }} dni
            </a>
        @endforeach
    </div>
</div>
<p style="font-size:12px;color:var(--text-3);margin:-8px 0 14px">
    Zmiany procentowe porównują ten okres z poprzednimi {{ $days }} dniami.
</p>

{{-- ============ KAFELKI RUCHU ============ --}}
@php
    $tiles = [
        ['Unikalni odwiedzający', $num($traffic['visitors']), $traffic['new_visitors'] . ' nowych', 'users', 'var(--blue-bg)', 'var(--blue)', $delta('visitors')],
        ['Sesje', $num($traffic['sessions']), $traffic['pages_per_session'] . ' stron na sesję', 'activity', '#ecfdf5', '#047857', $delta('sessions')],
        ['Odsłony stron', $num($traffic['pageviews']), $num($traffic['car_views']) . ' odsłon ofert', 'eye', '#eff6ff', '#1e40af', $delta('pageviews')],
        ['Kontakty', $num($traffic['contacts']), $traffic['leads'] . ' leadów w bazie', 'phone-call', '#fffbeb', '#b45309', $delta('contacts')],
        ['Współczynnik odrzuceń', number_format($traffic['bounce_rate'], 1, ',', ' ') . '%', 'sesje z jedną odsłoną', 'log-out', '#fef2f2', '#b91c1c', $delta('bounce_rate', true)],
        ['Średni czas sesji', $dur($traffic['avg_seconds']), 'bez sesji jednostronowych', 'clock', '#f5f3ff', '#6d28d9', $delta('avg_seconds')],
        ['Konwersja', number_format($traffic['conversion'], 2, ',', ' ') . '%', 'leadów na sesję', 'target', '#ecfeff', '#0e7490', $delta('conversion')],
    ];
@endphp
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px">
    @foreach($tiles as [$label, $value, $sub, $icon, $bg, $fg, $d])
    {{-- .stat-ico jest pozycjonowana absolutnie w prawym górnym rogu karty,
         dlatego etykieta dostaje padding-right — inaczej wchodzi pod ikonę. --}}
    <div class="stat">
        <div class="stat-ico" style="background:{{ $bg }};color:{{ $fg }}"><i data-lucide="{{ $icon }}"></i></div>
        <div class="stat-label" style="padding-right:48px">{{ $label }}</div>
        <div style="display:flex;align-items:baseline;gap:8px;flex-wrap:wrap">
            <div class="stat-value">{{ $value }}</div>
            <span style="display:inline-flex;align-items:center;gap:3px;font-size:11.5px;font-weight:700;color:{{ $d['color'] }};white-space:nowrap">
                <i data-lucide="{{ $d['icon'] }}" style="width:12px;height:12px"></i>{{ $d['text'] }}
            </span>
        </div>
        <div class="stat-sub">{{ $sub }}</div>
    </div>
    @endforeach
</div>

{{-- ============ WYKRES ============ --}}
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0">Aktywność ({{ $days }} dni)</h2>
        <div style="display:flex;gap:14px;font-size:12px;flex-wrap:wrap">
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:var(--blue);border-radius:2px"></span> Odsłony stron</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:#f59e0b;border-radius:2px"></span> Odsłony ofert</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:#10b981;border-radius:2px"></span> Kontakty</span>
        </div>
    </div>
    <canvas id="activityChart" height="70"></canvas>
</div>

{{-- ============ ZDARZENIA + KANAŁY + URZĄDZENIA ============ --}}
<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <h2 style="margin:0">Zdarzenia ({{ $days }} dni)</h2>
        </div>
        <p style="font-size:12px;color:var(--text-3);margin:0 0 12px">Pomarańczowa kropka oznacza zdarzenie kontaktowe — wchodzi do metryki „Kontakty".</p>
        @if(count($events))
        <table class="data-table">
            <thead><tr><th>Zdarzenie</th><th style="text-align:right">Wystąpień</th><th style="text-align:right">Unikalnych osób</th></tr></thead>
            <tbody>
            @foreach($events as $e)
            <tr>
                <td>
                    @if($e['contact'])<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#f59e0b;margin-right:8px;vertical-align:middle"></span>@endif
                    <strong style="font-weight:600">{{ $e['label'] }}</strong>
                </td>
                <td style="text-align:right"><strong>{{ $num($e['count']) }}</strong></td>
                <td style="text-align:right;color:var(--text-3)">{{ $num($e['visitors']) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="padding:30px">
            <div class="ic"><i data-lucide="mouse-pointer-click"></i></div>
            <p style="font-size:13px">Brak zdarzeń w tym okresie. Pojawią się, gdy ktoś kliknie w telefon, pobierze raport PDF albo doda auto do obserwowanych.</p>
        </div>
        @endif
    </div>

    <div>
        <div class="card">
            <h2>Kanały pozyskania</h2>
            <p style="font-size:11.5px;color:var(--text-3);margin:0 0 10px">Liczone z pierwszej odsłony w sesji.</p>
            @forelse($channels as $c)
            <div style="padding:7px 0">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
                    <span>{{ $c['name'] }}</span>
                    <strong>{{ $num($c['count']) }} <span style="color:var(--text-3);font-weight:500">· {{ $c['percent'] }}%</span></strong>
                </div>
                <div style="height:5px;background:var(--bg);border-radius:3px;overflow:hidden">
                    <div style="height:100%;width:{{ $c['percent'] }}%;background:var(--blue);border-radius:3px"></div>
                </div>
            </div>
            @empty
            <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
            @endforelse
        </div>

        <div class="card">
            <h2>Urządzenia</h2>
            @foreach($devices as $d)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:13px">
                <span>{{ $d['label'] }}</span>
                <strong>{{ $num($d['count']) }} <span style="color:var(--text-3);font-weight:500">· {{ $d['percent'] }}%</span></strong>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============ TOP AUTA ============ --}}
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <h2 style="margin:0">Najpopularniejsze oferty ({{ $days }} dni)</h2>
        <a href="{{ route('admin.cars.index',['sort'=>'views','dir'=>'desc']) }}" class="btn btn-outline btn-sm">Wszystkie <i data-lucide="arrow-right"></i></a>
    </div>
    <p style="font-size:12px;color:var(--text-3);margin:0 0 12px">Konwersja = zapytania podzielone przez odsłony. Wysokie odsłony i zerowa konwersja to sygnał, że oferta przyciąga, ale nie przekonuje.</p>
    @if($topCars->count())
    <table class="data-table">
        <thead><tr><th style="width:40px">#</th><th style="width:80px"></th><th>Auto</th><th>Cena</th><th style="text-align:right">Odsłony</th><th style="text-align:right">Unikalni</th><th style="text-align:right">Zapytania</th><th style="text-align:right">Konwersja</th><th></th></tr></thead>
        <tbody>
        @foreach($topCars as $i => $car)
        <tr>
            <td style="font-weight:700;color:var(--text-3)">{{ $i + 1 }}</td>
            <td>@if($car->primaryImage)<img src="{{ $car->primaryImage->url }}" class="thumb" alt="">@else<div style="width:60px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center"><i data-lucide="car" style="width:18px;height:18px;color:var(--text-4)"></i></div>@endif</td>
            <td><strong>{{ $car->title }}</strong><br><span style="font-size:11px;color:var(--text-3)">{{ $car->identifier }}</span></td>
            <td><strong>{{ $car->formatted_price }}</strong></td>
            <td style="text-align:right"><span class="badge-pill pill-blue"><i data-lucide="eye" style="width:11px;height:11px;vertical-align:-1px"></i> {{ $car->range_views }}</span></td>
            <td style="text-align:right;color:var(--text-3)">{{ $car->range_uniques }}</td>
            <td style="text-align:right">{{ $car->range_inquiries ?: '—' }}</td>
            <td style="text-align:right"><strong style="color:{{ $car->range_cvr > 0 ? '#047857' : 'var(--text-3)' }}">{{ number_format($car->range_cvr, 1, ',', ' ') }}%</strong></td>
            <td><a href="{{ route('admin.cars.edit',$car) }}" class="btn btn-outline btn-sm"><i data-lucide="edit"></i></a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state" style="padding:30px">
        <div class="ic"><i data-lucide="bar-chart-3"></i></div>
        <p style="font-size:13px">Brak odsłon ofert w tym okresie.</p>
    </div>
    @endif
</div>

{{-- ============ STRONY WEJŚCIA + TOP STRONY ============ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="card">
        <h2>Strony wejścia</h2>
        <p style="font-size:11.5px;color:var(--text-3);margin:0 0 10px">Pierwsza strona, jaką widzi odwiedzający.</p>
        @forelse($landings as $p)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:12.5px">
            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-2);font-family:ui-monospace,monospace">/{{ $p->path }}</span>
            <strong style="flex-shrink:0;margin-left:10px">{{ $num($p->count) }}</strong>
        </div>
        @empty
        <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
        @endforelse
    </div>
    <div class="card">
        <h2>Najczęściej odwiedzane</h2>
        <p style="font-size:11.5px;color:var(--text-3);margin:0 0 10px">Odsłony i unikalni odwiedzający.</p>
        @forelse($topPages as $p)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:12.5px">
            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-2);font-family:ui-monospace,monospace">/{{ $p->path }}</span>
            <strong style="flex-shrink:0;margin-left:10px">{{ $num($p->count) }} <span style="color:var(--text-3);font-weight:500">· {{ $num($p->visitors) }} os.</span></strong>
        </div>
        @empty
        <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
        @endforelse
    </div>
</div>

{{-- ============ STAN MAGAZYNU ============ --}}
<h2 style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);font-weight:700;margin:22px 0 12px">Stan magazynu</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px">
    <div class="stat">
        <div class="stat-ico"><i data-lucide="car"></i></div>
        <div class="stat-label">Wszystkich aut</div>
        <div class="stat-value">{{ $stats['total_cars'] }}</div>
        <div class="stat-sub">+{{ $stats['new_last_30'] }} w 30 dniach</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#ecfdf5;color:#047857"><i data-lucide="check-circle"></i></div>
        <div class="stat-label">Aktywne</div>
        <div class="stat-value">{{ $stats['active_cars'] }}</div>
        <div class="stat-sub">{{ $stats['draft_cars'] }} szkiców</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#fffbeb;color:#b45309"><i data-lucide="wallet"></i></div>
        <div class="stat-label">Wartość stocku</div>
        <div class="stat-value" style="font-size:22px">{{ $num($stats['stock_value']) }} zł</div>
        <div class="stat-sub">śr. {{ $num($stats['avg_price']) }} zł</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#eff6ff;color:#1e40af"><i data-lucide="inbox"></i></div>
        <div class="stat-label">Wiadomości</div>
        <div class="stat-value">{{ $stats['total_msgs'] }}</div>
        <div class="stat-sub">
            @if($stats['unread_msgs']>0)
                <a href="{{ route('admin.messages.index',['filter'=>'unread']) }}" style="color:var(--blue);font-weight:600">{{ $stats['unread_msgs'] }} nieprzeczytanych</a>
            @else
                Wszystkie przeczytane
            @endif
        </div>
    </div>
</div>

{{-- ============ OSTATNIA AKTYWNOŚĆ ============ --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:8px">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h2 style="margin:0">Ostatnie samochody</h2>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-outline btn-sm">Wszystkie <i data-lucide="arrow-right"></i></a>
        </div>
        @if($recentCars->count())
        <table class="data-table">
            <thead><tr><th></th><th>Tytuł</th><th>Cena</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($recentCars as $car)
            <tr>
                <td>@if($car->primaryImage)<img src="{{ $car->primaryImage->url }}" class="thumb" alt="">@else<div style="width:60px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center"><i data-lucide="car" style="width:18px;height:18px;color:var(--text-4)"></i></div>@endif</td>
                <td><strong>{{ $car->title }}</strong><br><span style="font-size:11px;color:var(--text-3)">{{ $car->identifier }} · {{ $car->created_at->diffForHumans() }}</span></td>
                <td><strong>{{ $car->formatted_price }}</strong></td>
                <td>
                    @if($car->is_sold)<span class="badge-pill pill-red">Sprzedane</span>
                    @elseif($car->status==='active')<span class="badge-pill pill-green">Aktywne</span>
                    @else<span class="badge-pill pill-gray">Szkic</span>@endif
                </td>
                <td><a href="{{ route('admin.cars.edit',$car) }}" class="btn btn-outline btn-sm"><i data-lucide="edit"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p style="color:var(--text-3);text-align:center;padding:28px">Brak samochodów. <a href="{{ route('admin.cars.create') }}" style="color:var(--blue);font-weight:600">Dodaj pierwszy</a></p>
        @endif
    </div>

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h2 style="margin:0">Ostatnie wiadomości</h2>
            <a href="{{ route('admin.messages.index') }}" class="btn btn-outline btn-sm"><i data-lucide="arrow-right"></i></a>
        </div>
        @if($recentMessages->count())
            @foreach($recentMessages as $m)
            <a href="{{ route('admin.messages.show',$m) }}" style="display:block;padding:12px 0;border-bottom:1px solid var(--border-l)">
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px">
                    <strong style="font-size:13px;{{ !$m->is_read ? '' : 'font-weight:500' }}">
                        @if(!$m->is_read)<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--blue);margin-right:6px;vertical-align:middle"></span>@endif
                        {{ $m->name }}
                    </strong>
                    <span style="font-size:11px;color:var(--text-3);white-space:nowrap">{{ $m->created_at->diffForHumans(null, true) }}</span>
                </div>
                <div style="font-size:12px;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">{{ $m->message }}</div>
            </a>
            @endforeach
        @else
        <p style="color:var(--text-3);text-align:center;padding:28px;font-size:13px">Brak wiadomości</p>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const ctx=document.getElementById('activityChart');
    if(!ctx)return;
    const data={!! json_encode($chart) !!};
    new Chart(ctx,{
        type:'line',
        data:{
            labels:data.map(d=>d.label),
            datasets:[
                {label:'Odsłony stron',data:data.map(d=>d.views),borderColor:'#0066ff',backgroundColor:'rgba(0,102,255,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:2},
                {label:'Odsłony ofert',data:data.map(d=>d.cars),borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:2},
                {label:'Kontakty',data:data.map(d=>d.contacts),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:2},
            ]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false},tooltip:{mode:'index',intersect:false}},
            interaction:{mode:'index',intersect:false},
            scales:{
                y:{beginAtZero:true,ticks:{precision:0,font:{size:11}},grid:{color:'#eeeef0'}},
                x:{ticks:{font:{size:11},maxTicksLimit:16},grid:{display:false}}
            }
        }
    });
})();
</script>
@endpush
@endsection
