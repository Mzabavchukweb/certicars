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
.cat-bt-card{display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 6px 8px;border-radius:10px;background:transparent;border:1.5px solid transparent;cursor:pointer;transition:all .18s;text-decoration:none}
.cat-bt-card:hover{background:var(--blue-bg);border-color:rgba(0,102,255,.2);transform:translateY(-1px)}
.cat-bt-card.active{background:var(--blue-bg);border-color:var(--blue)}
.cat-bt-card.active .cat-bt-label{color:var(--blue);font-weight:700}
.cat-bt-icon{height:38px;width:100%;display:flex;align-items:flex-end;justify-content:center;overflow:hidden}
.cat-bt-icon img{height:34px;width:auto;object-fit:contain;mix-blend-mode:multiply;transition:transform .18s;display:block}
.cat-bt-card:hover .cat-bt-icon img,.cat-bt-card.active .cat-bt-icon img{transform:translateY(-2px)}
.cat-bt-icon svg{width:24px;height:24px;stroke:var(--text-3);fill:none;stroke-width:1.5;transition:stroke .18s}
.cat-bt-card.active .cat-bt-icon svg,.cat-bt-card:hover .cat-bt-icon svg{stroke:var(--blue)}
.cat-bt-label{font-size:11px;font-weight:600;color:var(--text-2);text-align:center;white-space:nowrap}
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

/* Cards gap — separated like Otomoto */
.cat-cards{display:flex;flex-direction:column;gap:12px}
.lcard{display:flex;background:#fff;border:1px solid var(--border-l);border-radius:12px;text-decoration:none;transition:all .18s;position:relative;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--blue);transform:scaleX(0);transform-origin:left;transition:transform .2s;border-radius:2px 0 0 2px}
.lcard:hover{background:#fafafe;border-color:#c5c5cc;box-shadow:0 4px 20px rgba(0,0,0,.1);transform:translateY(-1px)}
.lcard:hover::before{transform:scaleX(1)}

/* Image */
.lcard-img{width:260px;min-width:260px;height:190px;position:relative;overflow:hidden;flex-shrink:0;background:var(--bg)}
.lcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.lcard:hover .lcard-img img{transform:scale(1.03)}
.lcard-img-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.lcard-img-placeholder svg{width:48px;height:48px;stroke:var(--text-4);stroke-width:1.2;fill:none}
.lcard-badge-top{position:absolute;top:10px;left:10px;background:var(--orange);color:#fff;font-size:10px;font-weight:800;padding:4px 8px;border-radius:6px;letter-spacing:.5px}
.lcard-certi{position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,.7);color:#fff;font-size:10px;font-weight:700;padding:4px 8px;border-radius:6px;display:flex;align-items:center;gap:4px;backdrop-filter:blur(4px)}
.lcard-certi svg{width:10px;height:10px;stroke:#4ea3ff;fill:none;stroke-width:2.5}
.lcard-fav{position:absolute;top:8px;right:8px;width:32px;height:32px;background:rgba(255,255,255,.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.15)}
.lcard-fav:hover{background:#fff;transform:scale(1.1)}
.lcard-fav svg{width:15px;height:15px;stroke:#bbb;fill:none;stroke-width:2;transition:stroke .2s}
.lcard-fav.active svg{stroke:var(--orange);fill:var(--orange)}
/* Photo count */
.lcard-photo-count{position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:600;padding:3px 7px;border-radius:5px;display:flex;align-items:center;gap:4px}
.lcard-photo-count svg{width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2}

/* Content */
.lcard-content{flex:1;padding:16px 20px;display:flex;gap:16px;min-width:0}
.lcard-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:0}

/* Title row */
.lcard-title{font-size:17px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:6px;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lcard-subtitle{font-size:12px;color:var(--text-3);margin-bottom:12px}

/* Specs strip */
.lcard-specs{display:flex;flex-wrap:wrap;gap:4px 0;margin-bottom:14px}
.lcard-spec{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text-2);font-weight:500;padding-right:14px;margin-right:10px;border-right:1px solid var(--border-l)}
.lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
.lcard-spec svg{width:13px;height:13px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0}

/* Meta */
.lcard-meta{margin-top:auto;padding-top:12px;border-top:1px solid var(--border-l);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.lcard-meta-item{font-size:11px;color:var(--text-3);display:flex;align-items:center;gap:4px}
.lcard-meta-item svg{width:11px;height:11px;stroke:var(--text-3);fill:none;stroke-width:2}
.lcard-meta-dot{width:3px;height:3px;border-radius:50%;background:var(--border)}

/* Price column */
.lcard-price-col{display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;min-width:160px;padding-top:2px}
.lcard-price{font-size:24px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1;white-space:nowrap}
.lcard-price-label{font-size:11px;color:var(--text-3);font-weight:500;margin-top:3px}
.lcard-price-netto{font-size:12px;color:var(--text-3);font-weight:500;margin-top:2px}
.lcard-btn{margin-top:auto;background:var(--blue);color:#fff;font-size:12px;font-weight:700;padding:9px 18px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all .18s;white-space:nowrap}
.lcard-btn:hover{background:var(--blue-h);transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,102,255,.3)}
.lcard-btn svg{width:13px;height:13px;stroke:#fff;fill:none;stroke-width:2.4}

/* CertiCheck badge — COS Check style */
.cc-badge{display:inline-flex;align-items:stretch;border-radius:8px;overflow:hidden;cursor:pointer;transition:all .18s;box-shadow:0 1px 4px rgba(0,0,0,.1);text-decoration:none}
.cc-badge:hover{box-shadow:0 3px 12px rgba(0,0,0,.18);transform:translateY(-1px)}
.cc-badge-icon{display:flex;align-items:center;justify-content:center;background:rgba(0,102,255,.1);padding:6px 10px}
.cc-badge-icon svg{width:18px;height:18px}
.cc-badge-text{display:flex;align-items:center;background:#1a1a1a;color:#fff;font-size:13px;font-weight:800;letter-spacing:-.2px;padding:6px 12px 6px 10px;white-space:nowrap}
.cc-badge-text em{font-style:normal;color:var(--blue)}

/* Wrap — transparent, no background (cards are self-contained) */
.cat-cards-wrap{background:transparent}

@media(max-width:900px){
    .cat-wrap{grid-template-columns:1fr}
    .cat-sidebar{position:static;max-height:none;overflow:visible}
    .cat-mob-toggle{display:flex}
    .cat-panel{display:none}
    .cat-panel.open{display:block;animation:slideDown .2s ease}
    .lcard-img{width:180px;min-width:180px;height:160px}
    .lcard-price{font-size:20px}
    .lcard-price-col{min-width:130px}
    .cat-header-tabs{display:none}
}
@media(max-width:600px){
    .lcard{flex-direction:column}
    .lcard-img{width:100%;min-width:0;height:200px}
    .lcard-content{flex-direction:column}
    .lcard-price-col{flex-direction:row;align-items:center;min-width:0;width:100%}
    .lcard-price-col .lcard-btn{margin-top:0;margin-left:auto}
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
            <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span>Samochody osobowe</span>
        </nav>
        <div class="cat-header-row">
            <h1>Samochody osobowe <span>{{ $cars->total() }} {{ $cars->total() == 1 ? 'ogłoszenie' : ($cars->total() < 5 && $cars->total() > 1 ? 'ogłoszenia' : 'ogłoszeń') }}</span></h1>
            @if($activeFilters)
            <a href="{{ route('catalog') }}" style="font-size:13px;color:var(--text-3);display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:color .15s" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-3)'">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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

            // Domyślne typy — zawsze widoczne (z homepage)
            $defaultTypes = ['Sedan', 'SUV', 'Coupé', 'Bus', 'Kombi', 'Hatchback'];

            // Merge z DB — dodaj typy z bazy których jeszcze nie ma w domyślnych
            $dbLower = $categories->map(fn($c) => strtolower($c))->toArray();
            $defaultLower = array_map('strtolower', $defaultTypes);
            $extraFromDb = $categories->filter(fn($c) => !in_array(strtolower($c), $defaultLower))->values();
            $allTypes = collect($defaultTypes)->merge($extraFromDb);
        @endphp
        <div class="cat-bt-grid">
            {{-- Karta: Wszystkie --}}
            <a href="{{ route('catalog', request()->except('category')) }}"
               class="cat-bt-card {{ !request('category') ? 'active' : '' }}">
                <div class="cat-bt-icon">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
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
                        <svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="2" x2="6" y1="14" y2="14"/><line x1="10" x2="14" y1="8" y2="8"/><line x1="18" x2="22" y1="16" y2="16"/></svg>
                Filtry
                @if($activeFilters)<span class="cat-active-badge">{{ $activeFilters }}</span>@endif
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        </button>

        <form method="GET" action="{{ route('catalog') }}" id="catFilterForm">
            <div class="cat-panel {{ $activeFilters?'open':'' }}" id="catPanel">
                <div class="cat-panel-head">
                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="2" x2="6" y1="14" y2="14"/><line x1="10" x2="14" y1="8" y2="8"/><line x1="18" x2="22" y1="16" y2="16"/></svg>
                        Filtry
                    </h3>
                    @if($activeFilters)
                    <a href="{{ route('catalog') }}" class="cat-reset" style="margin:0;font-size:11px">
                        <svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg> Wyczyść
                    </a>
                    @endif
                </div>
                <div class="cat-panel-body">

                    <div class="cat-filter-group">
                        <div class="cat-flabel">
                            <svg viewBox="0 0 24 24"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M4 14h16"/></svg>
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
                            <svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                            Typ nadwozia
                        </div>
                        <select name="category" class="cat-select">
                            <option value="">Wszystkie typy</option>
                            @foreach($categories as $c)<option value="{{ $c }}" {{ request('category')==$c?'selected':'' }}>{{ $c }}</option>@endforeach
                        </select>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <svg viewBox="0 0 24 24"><line x1="3" x2="15" y1="22" y2="22"/><line x1="4" x2="14" y1="9" y2="9"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg>
                            Paliwo
                        </div>
                        <select name="fuel_type" class="cat-select">
                            <option value="">Wszystkie</option>
                            @foreach($fuelTypes as $f)<option value="{{ $f }}" {{ request('fuel_type')==$f?'selected':'' }}>{{ $f }}</option>@endforeach
                        </select>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <svg viewBox="0 0 24 24"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
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
                            <svg viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                            Cena (zł)
                        </div>
                        <div class="cat-row">
                            <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            Rok produkcji
                        </div>
                        <div class="cat-row">
                            <input type="number" name="year_min" value="{{ request('year_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="year_max" value="{{ request('year_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <svg viewBox="0 0 24 24"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                            Przebieg (km)
                        </div>
                        <div class="cat-row">
                            <input type="number" name="mileage_min" value="{{ request('mileage_min') }}" placeholder="Od" class="cat-input">
                            <input type="number" name="mileage_max" value="{{ request('mileage_max') }}" placeholder="Do" class="cat-input">
                        </div>
                    </div>

                    <div class="cat-filter-group" style="margin-top:14px">
                        <div class="cat-flabel">
                            <svg viewBox="0 0 24 24"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
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
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Szukaj
                    </button>
                    @if($activeFilters)
                    <a href="{{ route('catalog') }}" class="cat-reset">
                        <svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
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
            <a href="{{ route('catalog.show',$car) }}" class="lcard">
                {{-- Image --}}
                <div class="lcard-img">
                    @if($car->primaryImage)
                        <img src="{{ $car->primaryImage->url }}" alt="{{ $car->primaryImage->alt }}" loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="lcard-img-placeholder" style="display:none">
                    @else
                        <div class="lcard-img-placeholder">
                    @endif
                            <svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        </div>
                    @if($car->is_featured)<div class="lcard-badge-top">Wyróżnione</div>@endif

                    @php $imgCount = $car->images->count(); @endphp
                    @if($imgCount > 1)
                    <div class="lcard-photo-count">
                        <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        {{ $imgCount }}
                    </div>
                    @endif
                    <button class="lcard-fav" data-id="{{ $car->id }}" aria-label="Dodaj do ulubionych" onclick="toggleFav(event,{{ $car->id }})">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="lcard-content">
                    <div class="lcard-info">
                        <div class="lcard-title">{{ $car->title }}</div>
                        @if($car->category || $car->transmission)
                        <div class="lcard-subtitle">{{ implode(' · ', array_filter([$car->category, $car->transmission])) }}</div>
                        @endif

                        <div class="lcard-specs">
                            @if($car->mileage)
                            <div class="lcard-spec">
                                <svg viewBox="0 0 24 24"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                                {{ number_format((float) $car->mileage, 0, '.', ' ') }} km
                            </div>
                            @endif
                            @if($car->fuel_type)
                            <div class="lcard-spec">
                                <svg viewBox="0 0 24 24"><line x1="3" x2="15" y1="22" y2="22"/><line x1="4" x2="14" y1="9" y2="9"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/><path d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/></svg>
                                {{ $car->fuel_type }}
                            </div>
                            @endif
                            @if($car->power_hp)
                            <div class="lcard-spec">
                                <svg viewBox="0 0 24 24"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                                {{ $car->power_hp }} KM
                            </div>
                            @endif
                            @if($car->first_registration)
                            <div class="lcard-spec">
                                <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                {{ $car->first_registration }}
                            </div>
                            @endif
                        </div>

                        <div class="lcard-meta">
                            <span class="cc-badge" onclick="event.preventDefault();event.stopPropagation();var a=document.createElement('a');a.href='/samochody/{{ $car->slug }}/pdf';a.download='';document.body.appendChild(a);a.click();a.remove()">
                                <span class="cc-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#0066ff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg>
                                </span>
                                <span class="cc-badge-text"><em>Certi</em>Check</span>
                            </span>
                        </div>
                    </div>

                    {{-- Price + CTA --}}
                    <div class="lcard-price-col">
                        <div>
                            <div class="lcard-price">{{ $car->formatted_price }}</div>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="cat-empty">
                <div class="cat-empty-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                @if($activeFilters)
                    <h3>Brak wyników dla tych filtrów</h3>
                    <p>Żaden pojazd nie spełnia wszystkich wybranych kryteriów. Spróbuj rozszerzyć zakres lub wyczyść wybrane filtry.</p>
                    <div class="cat-empty-pills">
                        @if(request('brand')) <span class="cat-empty-pill"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67"/></svg>Marka wybrana</span> @endif
                        @if(request('fuel_type')) <span class="cat-empty-pill">Paliwo: {{ request('fuel_type') }}</span> @endif
                        @if(request('price_min') || request('price_max')) <span class="cat-empty-pill">Cena: {{ request('price_min','0') }}–{{ request('price_max','∞') }} zł</span> @endif
                        @if(request('year_min') || request('year_max')) <span class="cat-empty-pill">Rok: {{ request('year_min','–') }}–{{ request('year_max','–') }}</span> @endif
                        @if(request('mileage_max')) <span class="cat-empty-pill">Przebieg do: {{ number_format(request('mileage_max'),0,'.',' ') }} km</span> @endif
                        @if(request('category')) <span class="cat-empty-pill">Typ: {{ request('category') }}</span> @endif
                    </div>
                    <div class="cat-empty-actions">
                        <a href="{{ route('catalog') }}" class="btn btn-blue btn-pill">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
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
