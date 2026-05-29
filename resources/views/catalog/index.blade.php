@extends('layouts.public')
@section('title','Oferta samochodów')
@section('description','Przeglądaj certyfikowane samochody używane z pełną inspekcją, historią lakieru i dokumentacją.')
@section('styles')
/* ===== CATALOG HEADER (Otomoto-style: compact, functional) ===== */
.cat-header{background:#fff;border-bottom:1px solid var(--border-l);padding:20px 0 0}
.cat-header-in{max-width:1200px;margin:0 auto;padding:0 24px}
.cat-breadcrumb{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-3);margin-bottom:12px}
.cat-breadcrumb a{color:var(--text-3);text-decoration:none;transition:color .15s}
.cat-breadcrumb a:hover{color:var(--blue)}
.cat-breadcrumb svg{width:10px;height:10px;stroke:var(--text-4);fill:none;stroke-width:2}
.cat-header-row{display:flex;align-items:baseline;justify-content:space-between;gap:16px;padding-bottom:16px;flex-wrap:wrap}
.cat-header-row h1{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.4px;line-height:1}
.cat-header-row h1 span{font-size:14px;font-weight:500;color:var(--text-3);margin-left:10px;letter-spacing:0}
/* Body-type card grid — compact tabs with car images */
.cat-bt-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;padding:14px 0 16px}
@media(max-width:700px){.cat-bt-grid{grid-template-columns:repeat(4,1fr)}}
@media(max-width:460px){.cat-bt-grid{display:flex;overflow-x:auto;gap:8px;padding:14px 0 14px;scrollbar-width:none;-webkit-overflow-scrolling:touch}.cat-bt-grid::-webkit-scrollbar{display:none}.cat-bt-card{flex-shrink:0;min-width:72px}}
.cat-bt-card{display:flex;flex-direction:column;align-items:center;gap:4px;padding:12px 8px 10px;border-radius:10px;background:transparent;border:1.5px solid transparent;cursor:pointer;transition:all .18s;text-decoration:none}
.cat-bt-card:hover{background:var(--blue-bg);border-color:transparent;transform:translateY(-1px)}
.cat-bt-card.active{background:var(--blue-bg);border-color:transparent}
.cat-bt-card.active .cat-bt-label{color:var(--blue);font-weight:700}
.cat-bt-icon{height:38px;width:100%;display:flex;align-items:flex-end;justify-content:center;overflow:hidden}
.cat-bt-icon img{height:34px;width:auto;object-fit:contain;mix-blend-mode:multiply;transition:transform .18s;display:block}
.cat-bt-card:hover .cat-bt-icon img,.cat-bt-card.active .cat-bt-icon img{transform:translateY(-2px)}
.cat-bt-icon svg{width:24px;height:24px;stroke:var(--text-3);fill:none;stroke-width:1.5;transition:stroke .18s}
.cat-bt-card.active .cat-bt-icon svg,.cat-bt-card:hover .cat-bt-icon svg{stroke:var(--blue)}
.cat-bt-label{font-size:13px;font-weight:600;color:var(--text-2);text-align:center;white-space:nowrap;letter-spacing:-.1px}
.cat-bt-icon img.flip{transform:scaleX(-1)}
.cat-bt-card:hover .cat-bt-icon img.flip,.cat-bt-card.active .cat-bt-icon img.flip{transform:scaleX(-1) translateY(-2px)}

/* Grid + results background */
.cat-wrap{max-width:1200px;margin:0 auto;padding:24px 24px 64px;display:grid;grid-template-columns:270px 1fr;gap:28px;align-items:start}

/* Sidebar */
.cat-sidebar{}
.cat-panel{background:#fff;border-radius:16px;border:1px solid var(--border-l);overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.05)}
.cat-panel-head{padding:18px 20px;border-bottom:1px solid var(--border-l);display:flex;align-items:center;justify-content:space-between}
.cat-panel-head h3{font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:7px}
.cat-panel-head h3 svg{width:16px;height:16px;stroke:var(--blue);fill:none;stroke-width:2;flex-shrink:0}
.cat-panel-body{padding:20px}
.cat-filter-group{margin-bottom:18px}
.cat-filter-group:last-of-type{margin-bottom:0}
.cat-flabel{font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.cat-flabel svg{width:12px;height:12px;stroke:var(--text-3);fill:none;stroke-width:2}
.cat-select{width:100%;padding:10px 36px 10px 12px;border:1.5px solid var(--border-l);border-radius:10px;font-size:13px;font-family:inherit;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;color:var(--text);appearance:none;transition:border-color .15s}
.cat-select:focus{outline:none;border-color:var(--blue)}
.cat-row{display:flex;gap:6px}
.cat-input{flex:1;padding:10px 12px;border:1.5px solid var(--border-l);border-radius:10px;font-size:12px;font-family:inherit;transition:border-color .15s;min-width:0}
.cat-input:focus{outline:none;border-color:var(--blue)}
.cat-input::placeholder{color:var(--text-4)}
.cat-sep{height:1px;background:var(--border-l);margin:16px 0}
.cat-panel-foot{padding:16px 20px;border-top:1px solid var(--border-l);background:var(--bg)}
.cat-submit{width:100%;padding:12px;border-radius:10px;background:var(--blue);color:#fff;border:none;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:7px;box-shadow:0 4px 12px rgba(0,102,255,.3)}
.cat-submit:hover{background:var(--blue-h);box-shadow:0 6px 18px rgba(0,102,255,.4);transform:translateY(-1px)}
.cat-submit svg{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5}
.cat-reset{display:flex;align-items:center;justify-content:center;gap:4px;margin-top:10px;font-size:12px;color:var(--text-3);font-weight:500;text-decoration:none;transition:color .15s}
.cat-reset:hover{color:var(--text)}
.cat-reset svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2}

/* Mobile filter toggle */
.cat-mob-toggle{display:none;width:100%;align-items:center;justify-content:space-between;padding:13px 18px;background:#fff;border:1.5px solid var(--border-l);border-radius:12px;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;margin-bottom:16px;box-shadow:0 2px 8px rgba(0,0,0,.05)}
.cat-mob-toggle svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2}
.cat-active-badge{background:var(--blue);color:#fff;font-size:10px;font-weight:700;border-radius:50px;padding:2px 8px}

/* Results area */
.cat-results-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap}
.cat-count{font-size:14px;color:var(--text-3)}
.cat-count strong{color:var(--text);font-weight:700}
.cat-sort{padding:9px 32px 9px 12px;border:1.5px solid var(--border-l);border-radius:10px;font-size:13px;font-family:inherit;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;appearance:none;cursor:pointer}
.cat-sort:focus{outline:none;border-color:var(--blue)}

/* Cards grid */
.cat-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}

/* Active filter pills */
.cat-pills{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px}
.cat-pill{display:inline-flex;align-items:center;gap:5px;background:var(--blue-bg);color:var(--blue);padding:4px 10px;border-radius:50px;font-size:12px;font-weight:600}

/* Empty state */
.cat-empty{grid-column:1/-1;background:#fff;border-radius:16px;border:1.5px dashed var(--border);padding:64px 32px;text-align:center}
.cat-empty-icon{width:80px;height:80px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;border:1.5px dashed var(--border)}
.cat-empty-icon svg{width:40px;height:40px;stroke:var(--text-4);stroke-width:1.2;fill:none}
.cat-empty h3{font-size:20px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:10px}
.cat-empty p{font-size:14px;color:var(--text-3);line-height:1.7;margin-bottom:24px;max-width:400px;margin-left:auto;margin-right:auto}
.cat-empty-pills{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:28px}
.cat-empty-pill{display:inline-flex;align-items:center;gap:6px;background:var(--bg);border:1px solid var(--border);color:var(--text-2);padding:6px 12px;border-radius:50px;font-size:12px;font-weight:600}
.cat-empty-pill svg{width:12px;height:12px;stroke:var(--text-3);fill:none;stroke-width:2}
.cat-empty-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.cat-empty-actions .btn{padding:11px 24px;font-size:13px}

/* Cards stack vertically (Otomoto-style listing rows). Each card is one
   horizontal row: image on the left, content on the right. The content area
   itself is a column so the CertiCheck pill can anchor to the bottom without
   needing a divider line above it.

   The outer .lcard is intentionally a <div>, NOT an <a>. The whole-card
   click target is a separately-stacked .lcard-link overlay sitting at z-index 1.
   This avoids the nested-<a> HTML5 violation that triggers when the
   CertiCheck pill (itself an <a download>) sits inside the card — browsers
   auto-close the outer anchor before the inner one and the card visually
   breaks apart. Bumping .lcard-fav / .lcard-footer above z-index 1 lets
   those controls receive their own clicks even though the overlay covers
   the whole card. */
.cat-cards{display:flex;flex-direction:column;gap:12px}
.lcard{position:relative;display:flex;align-items:stretch;background:#fff;border:1px solid var(--border-l);border-radius:12px;transition:all .18s;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--blue);transform:scaleX(0);transform-origin:left;transition:transform .2s;border-radius:2px 0 0 2px;z-index:3;pointer-events:none}
.lcard:hover{background:#fafafe;border-color:#c5c5cc;box-shadow:0 4px 20px rgba(0,0,0,.1);transform:translateY(-1px)}
.lcard:hover::before{transform:scaleX(1)}
.lcard-link{position:absolute;inset:0;z-index:1;text-decoration:none;color:inherit;border-radius:inherit}
.lcard-link:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* Image — fixed width on desktop, stretches to match content height so the
   right side never floats over an empty image gutter. Placeholder uses a soft
   gradient + larger icon so cars without photos don't render as a flat
   featureless 260x190 white box. */
.lcard-img{width:260px;min-width:260px;flex-shrink:0;align-self:stretch;min-height:190px;position:relative;overflow:hidden;background:linear-gradient(135deg,#eef4ff 0%,#e3ecfa 100%)}
.lcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s;display:block}
.lcard:hover .lcard-img img{transform:scale(1.03)}
.lcard-img-placeholder{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef4ff 0%,#e3ecfa 100%)}
.lcard-img-placeholder svg{width:54px;height:54px;stroke:#94a3b8;stroke-width:1.4;fill:none;opacity:.55}
.lcard-badge-top{position:absolute;top:10px;left:10px;background:var(--orange);color:#fff;font-size:10px;font-weight:800;padding:4px 8px;border-radius:6px;letter-spacing:.5px;z-index:2}
.lcard-fav{position:absolute;top:8px;right:8px;width:32px;height:32px;background:rgba(255,255,255,.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.15);z-index:2}
.lcard-fav:hover{background:#fff;transform:scale(1.1)}
.lcard-fav svg{width:15px;height:15px;stroke:#bbb;fill:none;stroke-width:2;transition:stroke .2s}
.lcard-fav.active svg{stroke:var(--orange);fill:var(--orange)}
.lcard-photo-count{position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:600;padding:3px 7px;border-radius:5px;display:flex;align-items:center;gap:4px;z-index:2}
.lcard-photo-count svg{width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2}

/* Content area — a flex COLUMN so the CertiCheck footer can sit at the
   bottom while the main row (title + specs on left, price on right) takes
   the rest of the space. Gap-based spacing (no margin-bottom on items)
   guarantees title and subtitle always have whitespace between them. */
.lcard-content{flex:1;padding:18px 22px;display:flex;flex-direction:column;gap:12px;min-width:0}

/* Main row of the content area: info column (title/subtitle/specs) on the
   left, price column on the right. Aligned to the top of the card so price
   sits at the same baseline as the title — no orphan-price effect. */
.lcard-main{display:flex;gap:20px;align-items:flex-start;min-width:0}

/* Info column — vertical stack of title / subtitle / specs with consistent
   gap. Using gap instead of margin-bottom kills any chance of two adjacent
   text nodes rendering without whitespace between them. */
.lcard-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:6px}
.lcard-title{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.3px;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0}
.lcard-subtitle{font-size:13px;color:var(--text-3);margin:0;line-height:1.3}

.lcard-specs{display:flex;flex-wrap:wrap;gap:6px 0;margin-top:6px}
.lcard-spec{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text-2);font-weight:500;padding-right:14px;margin-right:10px;border-right:1px solid var(--border-l);white-space:nowrap}
.lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
.lcard-spec svg{width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0}

/* Price column — right-aligned, top-anchored. min-width keeps it from
   collapsing when the title is long; no justify-content:space-between so a
   single price item doesn't float by itself. */
.lcard-price-col{flex-shrink:0;min-width:140px;text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:4px}
.lcard-price{font-size:24px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1;white-space:nowrap}
.lcard-price-label{font-size:11px;color:var(--text-3);font-weight:500}

/* Footer row — anchored to the bottom of the content column, used for the
   CertiCheck pill. NO border-top divider (the old design left a horizontal
   line across the card that read as visual disconnection). z-index:2
   lifts it above the whole-card .lcard-link overlay so the pill stays
   independently clickable (download CertiCheck PDF). */
.lcard-footer{margin-top:auto;display:flex;align-items:center;gap:10px;flex-wrap:wrap;min-height:32px;position:relative;z-index:2}

/* Content area inherits the lifted stacking so title/specs/price are
   selectable text and don't pass clicks through to the card link. */
.lcard-content{position:relative}

/* Wrap — transparent, no background (cards are self-contained) */
.cat-cards-wrap{background:transparent}

@media(max-width:900px){
    .cat-wrap{grid-template-columns:1fr}
    .cat-sidebar{position:static;max-height:none;overflow:visible}
    .cat-mob-toggle{display:flex}
    .cat-panel{display:none}
    .cat-panel.open{display:block;animation:slideDown .2s ease}
    .lcard-img{width:200px;min-width:200px;min-height:170px}
    .lcard-title{font-size:17px}
    .lcard-price{font-size:20px}
    .lcard-price-col{min-width:120px}
    .cat-header-tabs{display:none}
}
@media(max-width:600px){
    .lcard{flex-direction:column;align-items:stretch}
    .lcard-img{width:100%;min-width:0;height:200px;min-height:200px}
    .lcard-content{padding:16px 18px;gap:10px}
    .lcard-main{flex-direction:column;align-items:stretch;gap:10px}
    .lcard-price-col{min-width:0;text-align:left;flex-direction:row;align-items:baseline;gap:8px}
    .lcard-price{font-size:22px}
    .lcard-footer{margin-top:4px}
    .cat-header-row h1{font-size:18px}
}
@endsection
@section('content')
@php
$filterKeys = ['brand','fuel_type','category','price_min','price_max','transmission','year_min','year_max','mileage_min','mileage_max','power_min','power_max'];
$activeFilters = collect($filterKeys)->filter(fn($k)=>request()->filled($k))->count();
@endphp

{{-- Page header --}}
<div class="cat-header">
    <div class="cat-header-in">
        <nav class="cat-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Strona główna</a>
            <x-icon name="chevron-right" size="14"/>
            <span>Samochody osobowe</span>
        </nav>
        <div class="cat-header-row">
            <h1>Samochody osobowe <span>{{ $cars->total() }} {{ $cars->total() == 1 ? 'ogłoszenie' : ($cars->total() < 5 && $cars->total() > 1 ? 'ogłoszenia' : 'ogłoszeń') }}</span></h1>
            @if($activeFilters)
            <a href="{{ route('catalog') }}" style="font-size:13px;color:var(--text-3);display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:color .15s" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-3)'">
                <x-icon name="x" size="13"/>
                Wyczyść filtry ({{ $activeFilters }})
            </a>
            @endif
        </div>
        @php
            // Mapowanie nazwa → plik PNG
            $bodyTypeImages = [
                'sedan'     => 'sedan.png',
                'suv'       => 'suv.png',
                'coupe'     => 'coupe.png',
                'coupé'     => 'coupe.png',
                'kombi'     => 'kombi.png',
                'bus'       => 'van.png',
                'van'       => 'van.png',
                'hatchback' => 'hatchback.png',
                'minivan'   => 'van.png',
                'pickup'    => 'suv.png',
            ];

            // Strict whitelist — only the six body-type tiles + the leading "Wszystkie" card.
            // Catch-all DB categories like "Samochód osobowy" must not leak into the tile bar;
            // they're still selectable through the sidebar filter, just not promoted up here.
            $allTypes = collect(['Sedan', 'SUV', 'Coupé', 'Bus', 'Kombi', 'Hatchback']);
        @endphp
        <div class="cat-bt-grid">
            {{-- Karta: Wszystkie --}}
            <a href="{{ route('catalog', request()->except('category')) }}"
               class="cat-bt-card {{ !request('category') ? 'active' : '' }}">
                <div class="cat-bt-icon">
                    <x-icon name="layout-grid" size="22"/>
                </div>
                <span class="cat-bt-label">Wszystkie</span>
            </a>
            {{-- Karty: domyslne typy nadwozia + z DB --}}
            @foreach($allTypes as $cat)
            @php
                $imgKey  = strtolower($cat);
                $imgFile = $bodyTypeImages[$imgKey] ?? null;
                $isActive = strtolower(request('category', '')) === strtolower($cat);
                $isHatch = strtolower($cat) === 'hatchback';
            @endphp
            <a href="{{ route('catalog', array_merge(request()->except('category'), ['category' => $cat])) }}"
               class="cat-bt-card {{ $isActive ? 'active' : '' }}">
                <div class="cat-bt-icon">
                    @if($imgFile)
                        <img src="/img/body-types/{{ $imgFile }}" alt="{{ $cat }}" loading="lazy">
                    @else
                        <x-icon name="car" size="22"/>
                    @endif
                </div>
                <span class="cat-bt-label">{{ $cat }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Main content --}}
<div class="cat-wrap">

    {{-- Sidebar --}}
    <aside class="cat-sidebar">
        <button type="button" class="cat-mob-toggle" onclick="document.getElementById('catPanel').classList.toggle('open')">
            <span style="display:flex;align-items:center;gap:8px">
                <x-icon name="sliders-horizontal" size="16"/>
                Filtry
                @if($activeFilters)<span class="cat-active-badge">{{ $activeFilters }}</span>@endif
            </span>
            <x-icon name="chevron-down" size="16"/>
        </button>

        <form method="GET" action="{{ route('catalog') }}" id="catFilterForm">
            <div class="cat-panel {{ $activeFilters?'open':'' }}" id="catPanel">
                <div class="cat-panel-head">
                    <h3>
                        <x-icon name="sliders-horizontal" size="18"/>
                        Filtry
                    </h3>
                    @if($activeFilters)
                    <a href="{{ route('catalog') }}" class="cat-reset" style="margin:0;font-size:11px">
                        <x-icon name="x" size="14"/> Wyczyść
                    </a>
                    @endif
                </div>
                <div class="cat-panel-body">

                    <div class="cat-filter-group">
                        <div class="cat-flabel">
                            <x-icon name="rectangle-vertical" size="14"/>
                            Marka
                        </div>
                        <select name="brand" class="cat-select">
                            <option value="">Wszystkie marki</option>
                            @foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
                        </select>
                    </div>

                    <div class="cat-sep"></div>

                    <div class="cat-filter-group">
                        <div class="cat-flabel">
                            <x-icon name="car" size="22"/>
                            Typ nadwozia
                        </div>
                        <select name="category" class="cat-select">
                            <option value="">Wszystkie typy</option>
                            @foreach($categories as $c)<option value="{{ $c }}" {{ request('category')==$c?'selected':'' }}>{{ $c }}</option>@endforeach
                        </select>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <x-icon name="fuel" size="14"/>
                            Paliwo
                        </div>
                        <select name="fuel_type" class="cat-select">
                            <option value="">Wszystkie</option>
                            @foreach($fuelTypes as $f)<option value="{{ $f }}" {{ request('fuel_type')==$f?'selected':'' }}>{{ $f }}</option>@endforeach
                        </select>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <x-icon name="settings" size="14"/>
                            Skrzynia biegów
                        </div>
                        <select name="transmission" class="cat-select">
                            <option value="">Wszystkie</option>
                            <option value="Automatyczna" {{ request('transmission')=='Automatyczna'?'selected':'' }}>Automatyczna</option>
                            <option value="Manualna" {{ request('transmission')=='Manualna'?'selected':'' }}>Manualna</option>
                        </select>
                    </div>

                    <div class="cat-sep"></div>

                    <div class="cat-filter-group">
                        <div class="cat-flabel">
                            <x-icon name="car-front" size="14"/>
                            Cena (zł)
                        </div>
                        <div class="cat-row">
                            <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <x-icon name="calendar" size="14"/>
                            Rok produkcji
                        </div>
                        <div class="cat-row">
                            <input type="number" name="year_min" value="{{ request('year_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="year_max" value="{{ request('year_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <x-icon name="gauge" size="14"/>
                            Przebieg (km)
                        </div>
                        <div class="cat-row">
                            <input type="number" name="mileage_min" value="{{ request('mileage_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="mileage_max" value="{{ request('mileage_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <x-icon name="zap" size="14"/>
                            Moc silnika (KM)
                        </div>
                        <div class="cat-row">
                            <input type="number" name="power_min" value="{{ request('power_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="power_max" value="{{ request('power_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                </div>
                <div class="cat-panel-foot">
                    <button type="submit" class="cat-submit">
                        <x-icon name="search" size="16"/>
                        Szukaj
                    </button>
                    @if($activeFilters)
                    <a href="{{ route('catalog') }}" class="cat-reset">
                        <x-icon name="x" size="14"/>
                        Wyczyść wszystkie filtry
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </aside>

    {{-- Results --}}
    <div>
        @php
            $pillMap = [
                'brand'       => $brands->firstWhere('id', request('brand'))?->name,
                'fuel_type'   => request('fuel_type'),
                'category'    => request('category'),
                'transmission'=> request('transmission'),
                'price_min'   => request('price_min') ? 'Cena od ' . number_format(request('price_min'), 0, '', ' ') . ' zł' : null,
                'price_max'   => request('price_max') ? 'Cena do ' . number_format(request('price_max'), 0, '', ' ') . ' zł' : null,
                'year_min'    => request('year_min')  ? 'Rok od '   . request('year_min')  : null,
                'year_max'    => request('year_max')  ? 'Rok do '   . request('year_max')  : null,
                'mileage_min' => request('mileage_min') ? 'Przebieg od ' . number_format(request('mileage_min'), 0, '', ' ') . ' km' : null,
                'mileage_max' => request('mileage_max') ? 'Przebieg do ' . number_format(request('mileage_max'), 0, '', ' ') . ' km' : null,
                'power_min'   => request('power_min') ? 'Moc od ' . request('power_min') . ' KM' : null,
                'power_max'   => request('power_max') ? 'Moc do ' . request('power_max') . ' KM' : null,
            ];
            $activePills = array_filter($pillMap);
        @endphp
        @if(count($activePills))
        <div class="cat-pills" style="margin-bottom:16px">
            @foreach($activePills as $key => $label)
            <span class="cat-pill">
                {{ $label }}
                <a href="{{ route('catalog', request()->except($key)) }}" style="color:inherit;display:flex;align-items:center;margin-left:2px" aria-label="Usuń filtr">
                    <x-icon name="x" size="12" :strokeWidth="2.5"/>
                </a>
            </span>
            @endforeach
        </div>
        @endif
        <div class="cat-results-head">
            <p class="cat-count">Znaleziono: <strong>{{ $cars->total() }}</strong> {{ $cars->total()==1?'samochód':($cars->total()<5&&$cars->total()>0?'samochody':'samochodów') }}</p>
            <select onchange="window.location=this.value" class="cat-sort">
                <option value="{{ route('catalog',array_merge(request()->all(),['sort'=>'created_at','dir'=>'desc'])) }}" {{ request('sort','created_at')=='created_at'?'selected':'' }}>Najnowsze</option>
                <option value="{{ route('catalog',array_merge(request()->all(),['sort'=>'price','dir'=>'asc'])) }}" {{ request('sort')=='price'&&request('dir')=='asc'?'selected':'' }}>Cena: rosnąco</option>
                <option value="{{ route('catalog',array_merge(request()->all(),['sort'=>'price','dir'=>'desc'])) }}" {{ request('sort')=='price'&&request('dir')=='desc'?'selected':'' }}>Cena: malejąco</option>
                <option value="{{ route('catalog',array_merge(request()->all(),['sort'=>'mileage','dir'=>'asc'])) }}" {{ request('sort')=='mileage'?'selected':'' }}>Przebieg: rosnąco</option>
            </select>
        </div>

        <div class="cat-cards-wrap">
        <div class="cat-cards">
            @forelse($cars as $car)
            {{-- Outer wrapper is intentionally a <div>, not an <a>. The CertiCheck
                 download pill in the footer is itself an <a>, and nested anchors
                 are illegal HTML — browsers auto-close the outer one and the card
                 visually breaks apart. The .lcard-link span below is the actual
                 click target for the whole card. --}}
            <div class="lcard">
                <a href="{{ route('catalog.show',$car) }}" class="lcard-link" aria-label="{{ $car->title }}"></a>
                {{-- Image --}}
                <div class="lcard-img">
                    @if($car->primaryImage)
                        <img src="{{ $car->primaryImage->url }}" alt="{{ $car->primaryImage->alt }}" loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="lcard-img-placeholder" style="display:none">
                    @else
                        <div class="lcard-img-placeholder">
                    @endif
                            <x-icon name="car" size="22"/>
                        </div>
                    @if($car->is_featured)<div class="lcard-badge-top">Wyróżnione</div>@endif

                    @php $imgCount = $car->images->count(); @endphp
                    @if($imgCount > 1)
                    <div class="lcard-photo-count">
                        <x-icon name="image" size="14"/>
                        {{ $imgCount }}
                    </div>
                    @endif
                    <button class="lcard-fav" data-id="{{ $car->id }}" aria-label="Dodaj do ulubionych" onclick="toggleFav(event,{{ $car->id }})">
                        <x-icon name="heart" size="16"/>
                    </button>
                </div>

                {{-- Content: main row (info + price) on top, optional CertiCheck
                     footer at the bottom. The flex-column layout lets the
                     footer anchor at the bottom edge without a divider line. --}}
                <div class="lcard-content">
                    <div class="lcard-main">
                        <div class="lcard-info">
                            <div class="lcard-title">{{ $car->title }}</div>
                            @if($car->category || $car->transmission)
                            <div class="lcard-subtitle">{{ implode(' · ', array_filter([$car->category, $car->transmission])) }}</div>
                            @endif

                            <div class="lcard-specs">
                                @if($car->mileage)
                                <div class="lcard-spec">
                                    <x-icon name="gauge" size="14"/>
                                    {{ number_format((float) $car->mileage, 0, '.', ' ') }} km
                                </div>
                                @endif
                                @if($car->fuel_type)
                                <div class="lcard-spec">
                                    <x-icon name="fuel" size="14"/>
                                    {{ $car->fuel_type }}
                                </div>
                                @endif
                                @if($car->power_hp)
                                <div class="lcard-spec">
                                    <x-icon name="zap" size="14"/>
                                    {{ $car->power_hp }} KM
                                </div>
                                @endif
                                @if($car->first_registration)
                                <div class="lcard-spec">
                                    <x-icon name="calendar" size="14"/>
                                    {{ $car->first_registration }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="lcard-price-col">
                            <div class="lcard-price">{{ $car->formatted_price }}</div>
                        </div>
                    </div>

                    @if($car->has_certicheck)
                    <div class="lcard-footer">
                        <x-certicheck-cta :slug="$car->slug" size="sm"/>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="cat-empty">
                <div class="cat-empty-icon">
                    <x-icon name="car" size="22"/>
                </div>
                @if($activeFilters)
                    <h3>Brak wyników dla tych filtrów</h3>
                    <p>Żaden pojazd nie spełnia wszystkich wybranych kryteriów. Spróbuj rozszerzyć zakres lub wyczyść wybrane filtry.</p>
                    <div class="cat-empty-pills">
                        @if(request('brand')) <span class="cat-empty-pill"><x-icon name="heart" size="12"/>Marka wybrana</span> @endif
                        @if(request('fuel_type')) <span class="cat-empty-pill">Paliwo: {{ request('fuel_type') }}</span> @endif
                        @if(request('price_min') || request('price_max')) <span class="cat-empty-pill">Cena: {{ request('price_min','0') }}–{{ request('price_max','∞') }} zł</span> @endif
                        @if(request('year_min') || request('year_max')) <span class="cat-empty-pill">Rok: {{ request('year_min','–') }}–{{ request('year_max','–') }}</span> @endif
                        @if(request('mileage_max')) <span class="cat-empty-pill">Przebieg do: {{ number_format(request('mileage_max'),0,'.',' ') }} km</span> @endif
                        @if(request('category')) <span class="cat-empty-pill">Typ: {{ request('category') }}</span> @endif
                    </div>
                    <div class="cat-empty-actions">
                        <a href="{{ route('catalog') }}" class="btn btn-blue btn-pill">
                            <x-icon name="rotate-cw" size="14" :strokeWidth="2.2"/>
                            Wyczyść wszystkie filtry
                        </a>
                        <a href="{{ route('catalog') }}" class="btn btn-outline btn-pill">Przeglądaj całą ofertę</a>
                    </div>
                @else
                    <h3>Oferta jest aktualizowana</h3>
                    <p>Aktualnie brak dostępnych pojazdów. Wróć wkrótce lub skontaktuj się z nami bezpośrednio.</p>
                    <div class="cat-empty-actions">
                        <a href="{{ route('contact') }}" class="btn btn-blue btn-pill">Skontaktuj się</a>
                        <a href="{{ route('home') }}" class="btn btn-outline btn-pill">Strona główna</a>
                    </div>
                @endif
            </div>
            @endforelse
        </div>
        </div>{{-- /cat-cards-wrap --}}

        <div style="margin-top:28px;display:flex;justify-content:center">{{ $cars->links('pagination.custom') }}</div>
    </div>

</div>
@endsection
@push('scripts')
<script>
(function(){
    const form = document.getElementById('catFilterForm');
    const wrap = document.querySelector('.cat-cards-wrap');
    if(!form) return;

    function submitWithFeedback(){
        if(wrap){ wrap.style.opacity = '.45'; wrap.style.pointerEvents = 'none'; wrap.style.transition = 'opacity .15s'; }
        form.submit();
    }

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(sel=>{
        sel.addEventListener('change', submitWithFeedback);
    });
    // Debounced submit on numeric inputs (600ms)
    let timer;
    form.querySelectorAll('input[type="number"]').forEach(inp=>{
        inp.addEventListener('input',()=>{
            clearTimeout(timer);
            timer = setTimeout(submitWithFeedback, 600);
        });
    });

    // Restore opacity if page loaded from cache (bfcache)
    window.addEventListener('pageshow', ()=>{
        if(wrap){ wrap.style.opacity = ''; wrap.style.pointerEvents = ''; }
    });
})();
</script>
@endpush
