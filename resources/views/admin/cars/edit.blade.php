@extends('admin.layouts.app')
@section('title','Edycja: '.$car->title)
@section('actions')
<a href="{{ route('catalog.show',$car) }}" target="_blank" class="btn btn-outline"><i data-lucide="eye"></i> Podgląd</a>
<a href="{{ route('admin.cars.pdf',$car) }}" target="_blank" class="btn btn-outline"><i data-lucide="file-text"></i> PDF</a>
@endsection
@section('content')

<div class="card" style="margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <h2 style="margin:0 0 4px;display:flex;align-items:center;gap:8px"><i data-lucide="bar-chart-3" style="width:18px;height:18px"></i> Statystyki wyświetleń</h2>
            <p style="font-size:12.5px;color:var(--text-3);margin:0">Unikalne sesje (1 sesja = 1 odsłona w ciągu 30 min)</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;min-width:360px">
            <div style="background:var(--bg);border-radius:10px;padding:12px;text-align:center">
                <div style="font-size:10.5px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px">Dziś</div>
                <div style="font-size:22px;font-weight:800;letter-spacing:-.4px">{{ $viewStats['today'] }}</div>
            </div>
            <div style="background:var(--bg);border-radius:10px;padding:12px;text-align:center">
                <div style="font-size:10.5px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px">7 dni</div>
                <div style="font-size:22px;font-weight:800;letter-spacing:-.4px">{{ $viewStats['last_7d'] }}</div>
            </div>
            <div style="background:var(--bg);border-radius:10px;padding:12px;text-align:center">
                <div style="font-size:10.5px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px">30 dni</div>
                <div style="font-size:22px;font-weight:800;letter-spacing:-.4px">{{ $viewStats['last_30d'] }}</div>
            </div>
            <div style="background:var(--blue-bg);border-radius:10px;padding:12px;text-align:center">
                <div style="font-size:10.5px;color:var(--blue);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px">Łącznie</div>
                <div style="font-size:22px;font-weight:800;letter-spacing:-.4px;color:var(--blue)">{{ $viewStats['total'] }}</div>
            </div>
        </div>
    </div>
    @if($viewStats['total'] > 0)
    <div style="margin-top:16px">
        <canvas id="carViewChart" height="55"></canvas>
    </div>
    @else
    <div style="margin-top:14px;background:var(--bg);border-radius:10px;padding:18px;text-align:center;color:var(--text-3);font-size:12.5px">
        <i data-lucide="bar-chart-3" style="width:18px;height:18px;vertical-align:-3px;margin-right:6px"></i>
        Brak odsłon. Statystyki pojawią się gdy użytkownicy zaczną oglądać tę ofertę.
    </div>
    @endif
</div>

<form method="POST" action="{{ route('admin.cars.update',$car) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.cars.form')
</form>

@push('scripts')
@if($viewStats['total'] > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const ctx=document.getElementById('carViewChart');
    if(!ctx)return;
    const data={!! json_encode($viewChart) !!};
    new Chart(ctx,{
        type:'bar',
        data:{labels:data.map(d=>d.label),datasets:[{data:data.map(d=>d.count),backgroundColor:'#0066ff',borderRadius:4,barThickness:'flex',maxBarThickness:14}]},
        options:{
            responsive:true,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>c.parsed.y+' '+(c.parsed.y===1?'odsłona':(c.parsed.y>=2&&c.parsed.y<=4?'odsłony':'odsłon'))}}},
            scales:{y:{beginAtZero:true,ticks:{stepSize:1,font:{size:10}},grid:{color:'#eeeef0'}},x:{ticks:{font:{size:9},maxRotation:0,autoSkip:true},grid:{display:false}}}
        }
    });
})();
</script>
@endif
@endpush
@endsection
