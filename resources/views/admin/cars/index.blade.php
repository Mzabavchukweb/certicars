@extends('admin.layouts.app')
@section('title','Samochody')
@section('actions')
<a href="{{ route('admin.cars.create') }}" class="btn btn-blue"><i data-lucide="plus"></i> Dodaj samochód</a>
@endsection
@php
$sortLink = function($field) use ($sort, $dir) {
    $newDir = ($sort === $field && $dir === 'asc') ? 'desc' : 'asc';
    $params = array_merge(request()->query(), ['sort' => $field, 'dir' => $newDir]);
    return request()->url().'?'.http_build_query($params);
};
$sortIcon = fn($f) => $sort === $f ? ($dir === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down';
$chipUrl = function($remove) {
    $q = request()->except($remove);
    return route('admin.cars.index', $q);
};
$activeFilters = array_filter([
    'search'   => request('search') ? ['label' => 'Szukaj', 'val' => request('search')] : null,
    'brand_id' => request('brand_id') ? ['label' => 'Marka', 'val' => optional(\App\Models\Brand::find(request('brand_id')))->name] : null,
    'status'   => request('status') ? ['label' => 'Status', 'val' => ['active'=>'Aktywne','draft'=>'Szkic','sold'=>'Sprzedane'][request('status')] ?? request('status')] : null,
    'price_min'=> request('price_min') ? ['label' => 'Cena od', 'val' => number_format(request('price_min'), 0, ',', ' ').' zł'] : null,
    'price_max'=> request('price_max') ? ['label' => 'Cena do', 'val' => number_format(request('price_max'), 0, ',', ' ').' zł'] : null,
]);
@endphp
@section('content')
<div class="card">
    <form method="GET" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:10px;margin-bottom:14px">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Szukaj: marka, model, VIN, ID" style="padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px">
        <select name="brand_id" style="padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:#fff">
            <option value="">Wszystkie marki</option>
            @foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
        </select>
        <select name="status" style="padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:#fff">
            <option value="">Wszystkie statusy</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Aktywne</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Szkice</option>
            <option value="sold" {{ request('status')=='sold'?'selected':'' }}>Sprzedane</option>
        </select>
        <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Cena od" style="padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px">
        <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Cena do" style="padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px">
        <button type="submit" class="btn btn-dark" data-no-loading><i data-lucide="search"></i></button>
    </form>

    @if(count($activeFilters))
    <div class="filter-chips">
        @foreach($activeFilters as $key => $f)
            <span class="filter-chip"><span class="k">{{ $f['label'] }}:</span> {{ $f['val'] }} <a href="{{ $chipUrl($key) }}" title="Usuń filtr"><i data-lucide="x"></i></a></span>
        @endforeach
        <a href="{{ route('admin.cars.index') }}" class="filter-chip-clear">Wyczyść wszystkie</a>
    </div>
    @endif

    <div class="bulk-bar" id="bulkBar">
        <span><span class="count" id="bulkCount">0</span> zaznaczonych</span>
        <span class="sep">|</span>
        <form method="POST" action="{{ route('admin.cars.bulk') }}" style="display:inline-flex;gap:6px;flex-wrap:wrap" id="bulkForm">@csrf
            <input type="hidden" name="action" id="bulkAction">
            <div id="bulkIdsContainer"></div>
            <button type="button" onclick="submitBulk('featured')"><i data-lucide="star"></i> Wyróżnij</button>
            <button type="button" onclick="submitBulk('unfeatured')"><i data-lucide="star-off"></i> Usuń wyróżnienie</button>
            <button type="button" onclick="submitBulk('sold')"><i data-lucide="shopping-cart"></i> Sprzedane</button>
            <button type="button" onclick="submitBulk('active')"><i data-lucide="check-circle"></i> Aktywne</button>
            <button type="button" onclick="confirmBulkDelete()" style="background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.3)"><i data-lucide="trash-2"></i> Usuń</button>
        </form>
        <button class="close" type="button" onclick="clearSelection()"><i data-lucide="x"></i></button>
    </div>

    @if($cars->count())
    <table class="data-table responsive">
        <thead>
            <tr>
                <th style="width:28px"><input type="checkbox" id="selectAll"></th>
                <th style="width:80px"></th>
                <th><a href="{{ $sortLink('model') }}">Samochód <i data-lucide="{{ $sortIcon('model') }}"></i></a></th>
                <th><a href="{{ $sortLink('price') }}">Cena <i data-lucide="{{ $sortIcon('price') }}"></i></a></th>
                <th><a href="{{ $sortLink('mileage') }}">Przebieg <i data-lucide="{{ $sortIcon('mileage') }}"></i></a></th>
                <th><a href="{{ $sortLink('views') }}">Odsłony <i data-lucide="{{ $sortIcon('views') }}"></i></a></th>
                <th>Status</th>
                <th style="width:60px">Wyróż.</th>
                <th><a href="{{ $sortLink('created_at') }}">Data <i data-lucide="{{ $sortIcon('created_at') }}"></i></a></th>
                <th style="width:160px"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($cars as $car)
        <tr data-id="{{ $car->id }}">
            <td><input type="checkbox" class="row-check" value="{{ $car->id }}"></td>
            <td>@if($car->primaryImage)<img src="{{ $car->primaryImage->url }}" class="thumb" alt="">@else<div style="width:60px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center"><i data-lucide="car" style="width:18px;height:18px;color:var(--text-4)"></i></div>@endif</td>
            <td><strong>{{ $car->title }}</strong><br><span style="font-size:11px;color:var(--text-3)">{{ $car->identifier }} · {{ $car->first_registration ?? '—' }}</span></td>
            <td><strong>{{ $car->formatted_price }}</strong></td>
            <td>{{ $car->mileage ? number_format($car->mileage, 0, ',', ' ').' km' : '—' }}</td>
            <td><span style="display:inline-flex;align-items:center;gap:4px;color:{{ $car->views_count > 0 ? 'var(--text)' : 'var(--text-4)' }};font-weight:{{ $car->views_count > 0 ? '600' : '400' }}"><i data-lucide="eye" style="width:13px;height:13px"></i> {{ $car->views_count }}</span></td>
            <td>
                @if($car->is_sold)<span class="badge-pill pill-red">Sprzedane</span>
                @elseif($car->status==='active')<span class="badge-pill pill-green">Aktywne</span>
                @elseif($car->status==='reserved')<span class="badge-pill pill-blue">Rezerwacja</span>
                @else<span class="badge-pill pill-gray">Szkic</span>@endif
            </td>
            <td>
                <form method="POST" action="{{ route('admin.cars.toggle-featured',$car) }}" style="display:inline">@csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline" data-no-loading title="{{ $car->is_featured?'Usuń wyróżnienie':'Wyróżnij' }}"><i data-lucide="star" style="color:{{ $car->is_featured?'#f59e0b':'#b0b0b0' }};fill:{{ $car->is_featured?'#f59e0b':'none' }}"></i></button>
                </form>
            </td>
            <td style="color:var(--text-3);font-size:12px">{{ $car->created_at->format('d.m.Y') }}</td>
            <td style="text-align:right;white-space:nowrap">
                <a href="{{ route('catalog.show',$car) }}" target="_blank" class="btn btn-outline btn-sm" title="Zobacz na stronie"><i data-lucide="eye"></i></a>
                <a href="{{ route('admin.cars.pdf',$car) }}" target="_blank" class="btn btn-outline btn-sm" title="PDF"><i data-lucide="file-text"></i></a>
                <a href="{{ route('admin.cars.edit',$car) }}" class="btn btn-outline btn-sm" title="Edytuj"><i data-lucide="edit"></i></a>
                <form method="POST" action="{{ route('admin.cars.destroy',$car) }}" style="display:inline" data-confirm="Usunąć to auto wraz ze zdjęciami?" data-confirm-title="Usunąć auto" data-confirm-ok="Usuń">@csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-ghost-red" data-no-loading title="Usuń"><i data-lucide="trash-2"></i></button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Mobile cards --}}
    <div class="m-card">
        @foreach($cars as $car)
        <div style="background:#fff;border:1px solid var(--border-l);border-radius:12px;padding:14px;margin-bottom:12px;display:flex;gap:12px">
            @if($car->primaryImage)<img src="{{ $car->primaryImage->url }}" style="width:72px;height:72px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="">@else<div style="width:72px;height:72px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i data-lucide="car" style="color:var(--text-4)"></i></div>@endif
            <div style="flex:1;min-width:0">
                <div style="font-weight:700;font-size:14px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $car->title }}</div>
                <div style="font-size:12px;color:var(--text-3);margin-bottom:6px">{{ $car->identifier }} · {{ $car->first_registration ?? '—' }}</div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap">
                    <strong style="font-size:13px">{{ $car->formatted_price }}</strong>
                    @if($car->is_sold)<span class="badge-pill pill-red">Sprzedane</span>
                    @elseif($car->status==='active')<span class="badge-pill pill-green">Aktywne</span>
                    @else<span class="badge-pill pill-gray">Szkic</span>@endif
                    @if($car->is_featured)<span class="badge-pill pill-yellow">★</span>@endif
                </div>
                <div style="display:flex;gap:5px">
                    <a href="{{ route('admin.cars.edit',$car) }}" class="btn btn-outline btn-sm"><i data-lucide="edit"></i> Edytuj</a>
                    <a href="{{ route('catalog.show',$car) }}" target="_blank" class="btn btn-outline btn-sm"><i data-lucide="eye"></i></a>
                    <form method="POST" action="{{ route('admin.cars.destroy',$car) }}" style="display:inline" data-confirm="Usunąć to auto wraz ze zdjęciami?" data-confirm-title="Usunąć auto" data-confirm-ok="Usuń">@csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ghost-red" data-no-loading><i data-lucide="trash-2"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:20px;display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:var(--text-3);flex-wrap:wrap;gap:10px">
        <span>Razem: <strong style="color:var(--text)">{{ $cars->total() }}</strong> · Strona {{ $cars->currentPage() }} z {{ $cars->lastPage() }}</span>
        {{ $cars->links('pagination.custom') }}
    </div>
    @else
    <div class="empty-state">
        <div class="ic"><i data-lucide="car"></i></div>
        <h3>{{ count($activeFilters) ? 'Brak wyników' : 'Brak samochodów' }}</h3>
        <p>
            @if(count($activeFilters))
                Zmień filtry lub <a href="{{ route('admin.cars.index') }}" style="color:var(--blue);font-weight:600">wyczyść je</a>.
            @else
                Dodaj pierwsze auto, by zacząć.
            @endif
        </p>
        <a href="{{ route('admin.cars.create') }}" class="btn btn-blue"><i data-lucide="plus"></i> Dodaj samochód</a>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function(){
    const bar=document.getElementById('bulkBar'),cnt=document.getElementById('bulkCount'),all=document.getElementById('selectAll');
    const ids=()=>[...document.querySelectorAll('.row-check:checked')].map(c=>c.value);
    function refresh(){
        const s=ids();
        cnt.textContent=s.length;
        bar.classList.toggle('active',s.length>0);
        document.querySelectorAll('.row-check').forEach(c=>c.closest('tr').classList.toggle('selected',c.checked));
    }
    all?.addEventListener('change',e=>{document.querySelectorAll('.row-check').forEach(c=>c.checked=e.target.checked);refresh()});
    document.querySelectorAll('.row-check').forEach(c=>c.addEventListener('change',refresh));
    window.clearSelection=()=>{document.querySelectorAll('.row-check,#selectAll').forEach(c=>c.checked=false);refresh()};
    window.submitBulk=(action)=>{
        const s=ids();
        if(!s.length)return;
        document.getElementById('bulkAction').value=action;
        document.getElementById('bulkIdsContainer').innerHTML=s.map(id=>`<input type="hidden" name="ids[]" value="${id}">`).join('');
        document.getElementById('bulkForm').submit();
    };
    window.confirmBulkDelete=async()=>{
        const s=ids();
        if(!s.length)return;
        const ok=await confirmAction('Usunąć '+s.length+' aut?','Tej akcji nie można cofnąć. Usunięte zostaną też wszystkie zdjęcia.','Usuń '+s.length);
        if(ok)submitBulk('delete');
    };
})();
</script>
@endpush
@endsection
