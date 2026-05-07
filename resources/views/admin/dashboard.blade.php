@extends('admin.layouts.app')
@section('title','Dashboard')
@section('actions')
<a href="{{ route('admin.cars.create') }}" class="btn btn-blue"><i data-lucide="plus"></i> Dodaj samochód</a>
@endsection
@section('content')

{{-- Traffic stats --}}
<h2 style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);font-weight:700;margin-bottom:12px">Ruch na stronie</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px">
    <div class="stat">
        <div class="stat-ico" style="background:var(--blue-bg);color:var(--blue)"><i data-lucide="eye"></i></div>
        <div class="stat-label">Wejścia dziś</div>
        <div class="stat-value">{{ $stats['views_today'] }}</div>
        <div class="stat-sub">{{ $stats['views_7d'] }} w ciągu 7 dni</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#ecfdf5;color:#047857"><i data-lucide="trending-up"></i></div>
        <div class="stat-label">Wejścia 30 dni</div>
        <div class="stat-value">{{ number_format($stats['views_30d'], 0, ',', ' ') }}</div>
        <div class="stat-sub">{{ number_format($stats['views_total'], 0, ',', ' ') }} łącznie</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#fffbeb;color:#b45309"><i data-lucide="car"></i></div>
        <div class="stat-label">Odsłony ofert (7 dni)</div>
        <div class="stat-value">{{ $stats['car_views_7d'] }}</div>
        <div class="stat-sub">{{ number_format($stats['car_views_total'], 0, ',', ' ') }} łącznie</div>
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

{{-- Inventory stats --}}
<h2 style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);font-weight:700;margin-bottom:12px">Stan magazynu</h2>
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
        <div class="stat-value" style="font-size:22px">{{ number_format($stats['stock_value'], 0, ',', ' ') }} zł</div>
        <div class="stat-sub">śr. {{ number_format($stats['avg_price'], 0, ',', ' ') }} zł</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#fef2f2;color:#b91c1c"><i data-lucide="shopping-cart"></i></div>
        <div class="stat-label">Sprzedane</div>
        <div class="stat-value">{{ $stats['sold_cars'] }}</div>
        <div class="stat-sub">{{ $stats['featured_cars'] }} wyróżnionych</div>
    </div>
</div>

{{-- Chart --}}
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0">Aktywność (ostatnie 14 dni)</h2>
        <div style="display:flex;gap:14px;font-size:12px;flex-wrap:wrap">
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:var(--blue);border-radius:2px"></span> Wejścia</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:#f59e0b;border-radius:2px"></span> Odsłony ofert</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:#10b981;border-radius:2px"></span> Wiadomości</span>
        </div>
    </div>
    <canvas id="activityChart" height="70"></canvas>
</div>

{{-- Top cars + referers --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h2 style="margin:0">🔥 Najpopularniejsze oferty (wszystko)</h2>
            <a href="{{ route('admin.cars.index',['sort'=>'views','dir'=>'desc']) }}" class="btn btn-outline btn-sm">Wszystkie <i data-lucide="arrow-right"></i></a>
        </div>
        @if($topCars->count())
        <table class="data-table">
            <thead><tr><th style="width:40px">#</th><th style="width:80px"></th><th>Auto</th><th>Cena</th><th style="text-align:right">Odsłony</th><th></th></tr></thead>
            <tbody>
            @foreach($topCars as $i => $car)
            <tr>
                <td style="font-weight:700;color:var(--text-3)">{{ $i + 1 }}</td>
                <td>@if($car->primaryImage)<img src="{{ $car->primaryImage->url }}" class="thumb" alt="">@else<div style="width:60px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center"><i data-lucide="car" style="width:18px;height:18px;color:var(--text-4)"></i></div>@endif</td>
                <td><strong>{{ $car->title }}</strong><br><span style="font-size:11px;color:var(--text-3)">{{ $car->identifier }}</span></td>
                <td><strong>{{ $car->formatted_price }}</strong></td>
                <td style="text-align:right"><span class="badge-pill pill-blue"><i data-lucide="eye" style="width:11px;height:11px;vertical-align:-1px"></i> {{ $car->views_count }}</span></td>
                <td><a href="{{ route('admin.cars.edit',$car) }}" class="btn btn-outline btn-sm"><i data-lucide="edit"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="padding:30px">
            <div class="ic"><i data-lucide="bar-chart-3"></i></div>
            <p style="font-size:13px">Brak zarejestrowanych odsłon. Statystyki pojawią się gdy użytkownicy zaczną przeglądać oferty.</p>
        </div>
        @endif
    </div>

    <div>
        <div class="card">
            <h2>Źródła ruchu (30 dni)</h2>
            @if($topReferers->count())
                @foreach($topReferers as $r)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:13px">
                    <span style="display:flex;align-items:center;gap:8px;min-width:0">
                        <i data-lucide="{{ $r->display === 'Bezpośrednio' ? 'arrow-right-circle' : 'globe' }}" style="width:14px;height:14px;color:var(--text-3);flex-shrink:0"></i>
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $r->display }}</span>
                    </span>
                    <strong style="flex-shrink:0;margin-left:10px">{{ $r->count }}</strong>
                </div>
                @endforeach
            @else
                <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
            @endif
        </div>
        <div class="card">
            <h2>Top strony (30 dni)</h2>
            @if($topPages->count())
                @foreach($topPages as $p)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:12.5px">
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-2);font-family:ui-monospace,monospace">/{{ $p->path }}</span>
                    <strong style="flex-shrink:0;margin-left:10px">{{ $p->count }}</strong>
                </div>
                @endforeach
            @else
                <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
            @endif
        </div>
    </div>
</div>

{{-- Recent activity --}}
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
                {label:'Wejścia',data:data.map(d=>d.views),borderColor:'#0066ff',backgroundColor:'rgba(0,102,255,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:3},
                {label:'Odsłony ofert',data:data.map(d=>d.cars),borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:3},
                {label:'Wiadomości',data:data.map(d=>d.msgs),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:3},
            ]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false},tooltip:{mode:'index',intersect:false}},
            interaction:{mode:'index',intersect:false},
            scales:{
                y:{beginAtZero:true,ticks:{stepSize:1,font:{size:11}},grid:{color:'#eeeef0'}},
                x:{ticks:{font:{size:11}},grid:{display:false}}
            }
        }
    });
})();
</script>
@endpush
@endsection
