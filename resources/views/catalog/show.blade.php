@extends('layouts.public')
@section('meta_title_full',$car->seo_title)
@section('title',$car->title)
@section('description',$car->seo_description)
@section('og_title',$car->seo_title)
@section('og_description',$car->seo_description)
@if($car->primaryImage)
@section('og_image',$car->primaryImage->url)
@endif
@section('og_type','product')
@section('extra_head')
    @if($car->noindex)<meta name="robots" content="noindex,nofollow">@endif
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Vehicle',
            'name' => $car->title,
            'description' => $car->seo_description,
            'url' => url('/samochody/'.$car->slug),
            'brand' => ['@type' => 'Brand', 'name' => $car->brand?->name],
            'model' => $car->model,
            'vehicleIdentificationNumber' => $car->vin,
            'productionDate' => $car->first_registration ?: null,
            'bodyType' => $car->body_type ?? $car->category,
            'color' => $car->color,
            'fuelType' => $car->fuel_type,
            'numberOfDoors' => $car->doors ? (int) $car->doors : null,
            'seatingCapacity' => $car->seats,
            'vehicleTransmission' => $car->transmission,
            'vehicleConfiguration' => $car->transmission_detail,
            'numberOfPreviousOwners' => $car->previous_owners,
            'mileageFromOdometer' => $car->mileage ? ['@type' => 'QuantitativeValue', 'value' => $car->mileage, 'unitCode' => 'KMT'] : null,
            'vehicleEngine' => $car->power_hp ? [
                '@type' => 'EngineSpecification',
                'enginePower' => ['@type' => 'QuantitativeValue', 'value' => $car->power_hp, 'unitText' => 'HP'],
                'engineDisplacement' => $car->engine_capacity ? ['@type' => 'QuantitativeValue', 'value' => $car->engine_capacity, 'unitCode' => 'CMQ'] : null,
                'fuelType' => $car->fuel_type,
            ] : null,
            'dateModified' => $car->updated_at?->toAtomString(),
            'image' => $car->galleryImages->pluck('url')->values()->all() ?: ($car->primaryImage ? [$car->primaryImage->url] : []),
            'offers' => $car->price ? [
                '@type' => 'Offer',
                'priceCurrency' => $car->currency ?: 'PLN',
                'price' => (float) $car->price,
                'availability' => $car->is_sold ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
                'itemCondition' => 'https://schema.org/UsedCondition',
                'url' => url('/samochody/'.$car->slug),
                'seller' => ['@type' => 'AutoDealer', 'name' => 'CertiCars'],
            ] : null,
        ];
        $schema = array_filter($schema, fn($v) => $v !== null && $v !== '');
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Strona główna', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Oferta samochodów', 'item' => url('/samochody')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $car->title, 'item' => url('/samochody/'.$car->slug)],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('styles')
.cs-wrap{padding:0 0 40px;background:#f5f5f7;min-height:100vh;overflow-x:hidden}
@media(max-width:1024px){.cs-wrap{padding-bottom:calc(96px + env(safe-area-inset-bottom,0px))}}
.cs-wrap .container{max-width:1200px;padding-left:24px;padding-right:24px;box-sizing:border-box;overflow:hidden}
.cs-wrap *,.cs-wrap *::before,.cs-wrap *::after{box-sizing:border-box}
/* Focus visible */
.cs-wrap button:focus-visible,.cs-wrap a:focus-visible,.cs-wrap [tabindex]:focus-visible{outline:2px solid var(--blue);outline-offset:2px;border-radius:inherit}


/* NAVIGATION BAR (COS style — breadcrumb + prev/next) */
.cs-nav-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 0;margin-bottom:8px;border-bottom:1px solid #e5e5e7}
.cs-nav-bar-left{display:flex;align-items:center;gap:8px}
.cs-nav-bar-right{display:flex;align-items:center;gap:8px}
.cs-nav-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#fff;border:1px solid #e5e5e7;border-radius:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;transition:all .15s;text-decoration:none;white-space:nowrap;line-height:1.3}
.cs-nav-btn:hover{border-color:#0066ff;color:#0066ff;box-shadow:0 1px 4px rgba(0,102,255,.1)}
.cs-nav-btn.disabled{opacity:.4;pointer-events:none;cursor:default}
.cs-nav-btn svg{width:14px;height:14px;flex-shrink:0;stroke-width:2.2}

.cs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:4px;flex-wrap:wrap}
.cs-head h1{font-size:30px;font-weight:900;letter-spacing:-.7px;color:#0a0a0a;margin:0;line-height:1.15;font-family:'Inter',sans-serif}
.cs-meta{display:flex;flex-wrap:wrap;gap:6px;font-size:13px;color:#6b7280;font-weight:500;margin:0}
.cs-meta span{display:inline-flex;align-items:center;gap:4px}
.cs-meta svg{width:0;height:0;display:none}
.cs-meta .sep{color:#d1d5db}

/* KEY FACTS STRIP (COS-style — horizontal data pills) */
.cs-keyfacts{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:0;padding:14px 0 16px;border-bottom:1px solid #e5e5e7}
.cs-keyfact{display:flex;align-items:center;gap:6px;background:#fff;border:1px solid #e5e5e7;border-radius:8px;padding:8px 14px;font-size:13px;color:#374151;font-weight:600;white-space:nowrap;transition:all .15s;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.cs-keyfact:hover{border-color:#0066ff;box-shadow:0 1px 4px rgba(0,102,255,.12)}
.cs-keyfact svg{width:15px;height:15px;stroke:#9ca3af;stroke-width:2;fill:none;flex-shrink:0}
.cs-keyfact strong{color:#0a0a0a;font-weight:800}

.cs-grid{display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:24px;margin-bottom:8px;min-width:0;overflow:hidden;align-items:stretch}
.cs-sidebar{position:sticky;top:92px;min-width:0;display:flex;flex-direction:column}
/* Desktop hero alignment: gallery wrapper flexes vertically so its main image
   shrinks to match the sidebar's height. Tabs and thumbs keep their natural
   sizes; .cs-gallery-stage flexes to fill whatever vertical space remains.
   This guarantees the gallery's bottom edge ends at the sidebar's bottom
   regardless of how much spec data the car has. */
@media(min-width:1025px){
    .cs-grid > div:first-child{display:flex;flex-direction:column;min-height:0;height:100%}
    .cs-grid > div:first-child > .cs-gallery{flex:1;min-height:0;display:flex;flex-direction:column}
    .cs-grid > div:first-child > .cs-gallery > .cs-gallery-stage{flex:1;min-height:0;aspect-ratio:auto;height:auto}
}

/* (legacy .cs-sidebar-certi removed — sidebar CertiCheck pill now uses the shared component.) */

/* GALLERY — edge-to-edge, no frame */
.cs-gallery{background:#e8e8ea;border-radius:14px;overflow:hidden;border:1px solid #e5e5e7;width:100%;max-width:100%;box-sizing:border-box}
.cs-gallery-stage{position:relative;width:100%;aspect-ratio:16/9;background:#e8e8ea;overflow:hidden}
.cs-gallery-main{position:absolute;inset:0;background:#e8e8ea;display:flex;align-items:center;justify-content:center;width:100%;height:100%;box-sizing:border-box}
.cs-gallery-main:not(.active){display:none!important}
.cs-gallery-main img{width:100%;height:100%;object-fit:cover}
.cs-gallery-main .empty{color:#9ca3af}
.cs-gallery-main .empty i{width:80px;height:80px}
.cs-gallery-counter{position:absolute;top:12px;right:12px;background:rgba(10,10,10,.75);color:#fff;padding:6px 12px;border-radius:50px;font-size:12px;display:flex;align-items:center;gap:5px;backdrop-filter:blur(10px);font-weight:600}
.cs-gallery-counter i{width:13px;height:13px}
.cs-gallery-nav{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;background:rgba(255,255,255,.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:5;box-shadow:0 2px 8px rgba(0,0,0,.15);transition:all .15s}
.cs-gallery-nav:hover{background:#fff;box-shadow:0 4px 16px rgba(0,0,0,.2);transform:translateY(-50%) scale(1.05)}
.cs-gallery-nav svg{width:20px;height:20px;stroke:#333;fill:none;stroke-width:2.5}
.cs-gallery-nav.prev{left:12px}
.cs-gallery-nav.next{right:12px}

/* GALLERY MEDIA TABS (pill-style badges) */
.cs-gallery-tabs-wrap{position:relative;overflow:hidden;max-width:100%}
.cs-gallery-tabs-wrap::after{content:'';position:absolute;right:0;top:0;bottom:0;width:48px;background:linear-gradient(to right,transparent,#f5f5f7);pointer-events:none;border-radius:0}
.cs-gallery-tabs{display:flex;align-items:center;gap:6px;padding:14px 0 10px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;max-width:100%}
.cs-gallery-tabs::-webkit-scrollbar{display:none}
.cs-gallery-tab{display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:#f0f0f2;border:1px solid #e5e5e7;border-radius:50px;font-size:12px;font-weight:600;color:#6b7280;cursor:pointer;transition:all .15s;white-space:nowrap;line-height:1.3}
.cs-gallery-tab:hover{background:#e8e8ea;color:#374151}
.cs-gallery-tab.active{background:#0066ff;color:#fff;border-color:#0066ff}
.cs-gallery-tab svg,.cs-gallery-tab i{width:14px;height:14px;flex-shrink:0}
.cs-gallery-tab .cs-tab-count{font-size:11px;font-weight:400;color:inherit}
.cs-gallery-tab.disabled{opacity:.35;pointer-events:none}

.cs-gallery-thumbs{display:flex;gap:6px;padding:10px 10px;overflow-x:auto;overflow-y:hidden;background:#fff;-webkit-overflow-scrolling:touch;scrollbar-width:thin;overscroll-behavior-x:contain;touch-action:pan-x}
.cs-gallery-thumbs::-webkit-scrollbar{height:6px}
.cs-gallery-thumbs::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:3px}
.cs-thumb{width:96px;height:64px;object-fit:cover;cursor:pointer;border-radius:6px;flex-shrink:0;opacity:.55;border:2px solid transparent;transition:all .15s}
.cs-thumb.active,.cs-thumb:hover{opacity:1;border-color:var(--blue)}
.cs-thumb[data-hidden]{display:none}
.cs-thumb-360{width:96px;height:64px;flex-shrink:0;border-radius:6px;border:2px solid transparent;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;background:linear-gradient(135deg,#e8f1ff,#d0e0ff);cursor:pointer;transition:all .15s;font-size:10px;font-weight:700;color:#0066ff}
.cs-thumb-360:hover,.cs-thumb-360.active{border-color:#0066ff;background:linear-gradient(135deg,#d0e0ff,#b8d4ff)}
.cs-thumb-360 svg{width:20px;height:20px;stroke:#0066ff;fill:none;stroke-width:1.8}

/* SIDEBAR CARD (COS style — white) */
.cs-sidebar-card{background:#fff;border:1px solid #e5e5e7;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.06)}

/* SIDEBAR VEHICLE SUMMARY (COS-style key-value pairs) */
.cs-sidebar-summary{padding:0 22px;border-bottom:1px solid #f0f0f2}
.cs-sidebar-summary-row{display:flex;align-items:center;gap:12px;padding:13px 0;border-bottom:1px solid #f5f5f5;font-size:13.5px}
.cs-sidebar-summary-row:last-child{border-bottom:none}
.cs-sidebar-summary-row .cs-row-icon{display:inline-flex;align-items:center;justify-content:center;color:#9ca3af;flex-shrink:0}
.cs-sidebar-summary-row .cs-row-icon svg{stroke:currentColor;fill:none;stroke-width:1.8}
.cs-sidebar-summary-row .lbl{color:#6b7280;font-weight:400;flex:1}
.cs-sidebar-summary-row .val{font-weight:700;color:#1a1a1a;text-align:right}

/* PRICE SECTION (inside card) */
.cs-price-section{padding:22px 22px 14px;border-bottom:1px solid #f0f0f2}
.cs-price-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.cs-price-block{min-width:0;flex:1 1 auto}
.cs-price-label{font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.cs-price-value{font-size:36px;font-weight:900;letter-spacing:-1px;color:#1a1a1a;line-height:1}
.cs-price-value small{display:block;font-size:12px;font-weight:500;color:#9ca3af;letter-spacing:0;margin-top:4px}
.cs-price-meta{font-size:12px;color:#6b7280;font-weight:500;margin-top:6px}
/* (CertiCheck pill visuals owned by the shared component.) */
@media(max-width:520px){
    .cs-price-row{gap:10px}
    .cs-price-block{flex-basis:100%}
}

/* CTA BUTTONS (inside card) */
.cs-price-actions{padding:18px 22px 22px;display:flex;flex-direction:column;gap:8px}
.cs-btn-phone{width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:15px 24px;background:#0066ff;color:#fff;border:none;border-radius:50px;font-size:16px;font-weight:700;cursor:pointer;transition:all .2s;text-decoration:none;box-shadow:0 4px 14px rgba(0,102,255,.35)}
.cs-btn-phone:hover{background:#0052cc;color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.45);transform:translateY(-1px)}
.cs-btn-phone svg{width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2.2;flex-shrink:0}
.cs-btn-message{width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:14px 20px;background:#f8f9fa;border:1.5px solid #e5e7eb;border-radius:12px;cursor:pointer;transition:all .15s;text-decoration:none;color:#1a1a1a}
.cs-btn-message:hover{background:#f0f1f3;border-color:#d1d5db}
.cs-btn-message svg{width:20px;height:20px;stroke:#0066ff;fill:none;stroke-width:1.8;flex-shrink:0}
.cs-btn-message .cs-msg-text{text-align:center}
.cs-btn-message .cs-msg-text strong{display:block;font-size:14px;font-weight:700;color:#1a1a1a}
.cs-btn-message .cs-msg-text small{display:block;font-size:11px;font-weight:400;color:#9ca3af;margin-top:2px}

/* 360° PANORAMA */
.cs-pano360-embed{width:100%;aspect-ratio:16/9;border-radius:12px;overflow:hidden;background:#000;position:relative}
.cs-pano360-grid{display:grid;gap:16px}
.cs-price-actions .btn{width:100%;justify-content:center;padding:13px 20px;font-weight:700;border-radius:10px}
.cs-price-actions .cs-btn-secondary{background:#f5f5f5;color:#1a1a1a;border:1px solid #e5e5e5;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:10px;cursor:pointer;transition:all .15s;text-decoration:none}
.cs-price-actions .cs-btn-secondary:hover{background:#ebebeb}
.cs-price-actions .cs-btn-secondary svg{width:16px;height:16px;flex-shrink:0}

/* FINANCING CALCULATOR */
/* CALC TRIGGER BUTTON (in sidebar) */
.cs-calc-trigger{display:flex;align-items:center;gap:10px;width:100%;padding:14px 18px;background:linear-gradient(135deg,#f0f4ff 0%,#e8eeff 100%);border:1px solid #d0ddff;border-radius:12px;cursor:pointer;transition:all .22s;font-size:13px;font-weight:700;color:#0052cc;letter-spacing:-.1px;margin-top:12px}
.cs-calc-trigger:hover{background:linear-gradient(135deg,#e4ecff 0%,#d8e4ff 100%);border-color:#b0c8ff;transform:translateY(-1px);box-shadow:0 4px 14px rgba(0,102,255,.12)}
.cs-calc-trigger svg{width:20px;height:20px;flex-shrink:0;stroke:#0066ff}
.cs-calc-trigger .cs-calc-trigger-text{flex:1;text-align:left}
.cs-calc-trigger .cs-calc-trigger-text small{display:block;font-size:10.5px;font-weight:500;color:#6b7280;margin-top:2px;letter-spacing:0}
.cs-calc-trigger .cs-calc-trigger-arrow{width:16px;height:16px;stroke:#9ca3af;transition:transform .2s}
.cs-calc-trigger:hover .cs-calc-trigger-arrow{stroke:#0066ff;transform:translateX(2px)}

/* FLOATING CALC WIDGET */


/* CARD (generic) */
.cs-card{background:#fff;border:1px solid #e5e5e7;border-radius:16px;padding:28px;margin-bottom:20px}
.cs-card-title{font-size:18px;font-weight:800;color:#000;margin-bottom:18px;display:flex;align-items:center;gap:10px;letter-spacing:-.3px}
.cs-card-title svg{width:20px;height:20px;stroke:var(--blue);stroke-width:2.2;fill:none}

/* STATUS CARDS (COS style — color coded) */
.cs-status-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.cs-status{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:20px 24px;display:flex;align-items:flex-start;gap:14px}
.cs-status-ico{width:40px;height:40px;background:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--green-dark);flex-shrink:0;border:1px solid #bbf7d0}
.cs-status-ico i{width:20px;height:20px;stroke-width:2.2}
.cs-status-body strong{display:block;font-size:14px;font-weight:700;color:#166534;margin-bottom:6px}
.cs-status-body .badge{background:var(--yellow-bg);color:var(--yellow-dark);border:1px solid #fde68a;margin-top:4px}

/* DAMAGES — desktop reference rebuild */
/* Subtitle under heading */
.cs-damage-subtitle{font-size:13px;color:#6b7280;line-height:1.55;margin:-4px 0 18px;max-width:720px}
/* Category chips row — reference layout: Wszystkie + Drobne + Lekkie rysy */
.cs-damages-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;max-width:100%}
.cs-damage-tab{flex-shrink:0;display:inline-flex;align-items:center;gap:9px;padding:8px 14px;background:#fff;color:#374151;border:1.5px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .15s,color .15s,border-color .15s}
.cs-damage-tab:hover{border-color:#cbd5e1;background:#f8fafc}
.cs-damage-tab.active{background:#eff6ff;color:#0066ff;border-color:#0066ff}
.cs-damage-tab-dot{display:inline-block;width:10px;height:10px;border-radius:50%;flex-shrink:0;background:#f59e0b}
.cs-damage-tab-dot.outline{background:transparent;border:2px solid #f59e0b}
.cs-damage-tab-grid{display:inline-flex;width:14px;height:14px;flex-shrink:0;color:#6b7280}
.cs-damage-tab.active .cs-damage-tab-grid{color:#0066ff}
.cs-damage-tab-count{display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 7px;border-radius:50px;background:#f1f5f9;color:#475569;font-size:11px;font-weight:700;margin-left:2px}
.cs-damage-tab.active .cs-damage-tab-count{background:#0066ff;color:#fff}

/* Damage grid: left diagram card / right photo card */
.cs-damage-grid{display:grid;grid-template-columns:380px 1fr;gap:20px;background:transparent}
.cs-damage-diagram{position:relative;background:#fafbfc;border:1px solid #eeeef0;border-radius:14px;overflow:hidden;min-height:auto;display:flex;flex-direction:column}
.cs-damage-diagram-canvas{position:relative;width:100%;aspect-ratio:1/1;background:#fafbfc;overflow:hidden}
.cs-damage-diagram-canvas img.cs-damage-diagram-img{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;object-position:center;pointer-events:none;padding:12px}
.cs-damage-diagram-legend{padding:12px 16px 14px;border-top:1px solid #eeeef0;background:#fff;display:flex;flex-direction:column;gap:7px}
.cs-damage-diagram-legend-item{display:flex;align-items:center;gap:8px;font-size:11.5px;color:#374151;line-height:1.3}
.cs-damage-diagram-legend-item .dot{display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;flex-shrink:0}
.cs-damage-diagram-legend-item .dot.outline{background:transparent;border:2px solid #f59e0b}

/* Numbered markers on the diagram */
.cs-damage-marker{position:absolute;transform:translate(-50%,-50%);width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;padding:0;margin:0;cursor:pointer;z-index:5;-webkit-tap-highlight-color:transparent}
.cs-damage-marker-dot{width:24px;height:24px;border-radius:50%;background:#f59e0b;border:2px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;line-height:1;font-family:inherit;letter-spacing:-.2px;box-shadow:0 2px 6px rgba(245,158,11,.45);transition:transform .12s ease,box-shadow .12s ease,background .15s ease}
.cs-damage-marker.outline .cs-damage-marker-dot{background:#fff;color:#b45309;border-color:#f59e0b}
.cs-damage-marker.active .cs-damage-marker-dot{transform:scale(1.18);box-shadow:0 0 0 5px rgba(0,102,255,.18),0 2px 10px rgba(0,102,255,.35);background:#0066ff;color:#fff;border-color:#fff}
.cs-damage-marker.outline.active .cs-damage-marker-dot{background:#0066ff;color:#fff;border-color:#fff}
.cs-damage-marker:hover .cs-damage-marker-dot{transform:scale(1.1)}

/* Hide damages filtered out by the active chip category */
.cs-damage-marker[hidden]{display:none!important}

/* Right-column gallery */
.cs-damage-detail{display:flex;flex-direction:column;min-width:0}
.cs-damage-item{display:none}
.cs-damage-item.active{display:flex;flex-direction:column;width:100%}
/* Overlay info card on bottom-left of main photo */
.cs-dmg-overlay{position:absolute;left:14px;bottom:14px;background:rgba(255,255,255,.96);border-radius:10px;padding:10px 14px;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);box-shadow:0 4px 14px rgba(0,0,0,.12);z-index:2;max-width:60%}
.cs-dmg-overlay-type{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#b45309;display:inline-flex;align-items:center;gap:6px;margin-bottom:3px}
.cs-dmg-overlay-type .dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:#f59e0b}
.cs-dmg-overlay-type .dot.outline{background:transparent;border:2px solid #f59e0b}
.cs-dmg-overlay-area{font-size:14.5px;font-weight:700;color:#0a0a0a;letter-spacing:-.2px}

/* Numbered thumbnail badges */
.cs-dmg-gallery-thumb{position:relative}
.cs-dmg-thumb-num{position:absolute;top:6px;left:6px;background:rgba(10,10,10,.7);color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;letter-spacing:-.1px;backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:2}

/* "Zobacz wszystkie zdjęcia stanu pojazdu" CTA */
.cs-dmg-all-cta{display:inline-flex;align-items:center;gap:8px;margin-top:14px;font-size:13.5px;font-weight:700;color:#0066ff;text-decoration:none;cursor:pointer;background:transparent;border:none;padding:0;align-self:flex-start;font-family:inherit}
.cs-dmg-all-cta:hover{color:#0052cc}
.cs-dmg-all-cta svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.2}
.cs-dmg-all-cta .lead{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#eff6ff;color:#0066ff}
.cs-dmg-all-cta .lead svg{width:13px;height:13px}

/* Per-damage card content text below gallery (description, tags) */
.cs-damage-item-meta{margin-top:14px}
.cs-damage-item-meta h3{font-size:15px;font-weight:700;margin:0 0 8px;color:#0a0a0a;display:flex;align-items:center;gap:8px}
.cs-damage-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px}
.cs-damage-tags span{background:#fff7ed;color:#b45309;border:1px solid #fed7aa;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600}
.cs-damage-item-meta p{font-size:13.5px;color:#374151;line-height:1.6;margin:0}

/* Empty placeholder when a chip's category has no damages */
.cs-damage-empty-cat{padding:40px 20px;text-align:center;color:#9ca3af;font-size:13.5px;background:#fff;border:1px dashed #e5e7eb;border-radius:14px;grid-column:1 / -1}

/* ============ BENEFIT ROW (5 items below gallery) ============ */
.cs-benefits-row{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:0;background:#fff;border:1px solid #eeeef0;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);max-width:calc(1200px - 48px);margin:0 auto 16px;padding:0;overflow:hidden;width:100%;box-sizing:border-box}
.cs-benefit-item{display:flex;align-items:center;gap:10px;padding:14px 16px;border-right:1px solid #eef0f3;min-width:0}
.cs-benefit-item:last-child{border-right:none}
.cs-benefit-ico{flex-shrink:0;width:34px;height:34px;border-radius:10px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-benefit-ico svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
/* Country flag variant — circular, no tinted background, bordered. Used by the
   leftmost "Sprowadzony z …" tile so it visually matches the reference. */
.cs-benefit-ico.flag{width:30px;height:30px;border-radius:50%;background:#fff;border:1px solid rgba(0,0,0,.08);overflow:hidden;padding:0}
.cs-benefit-ico.flag .cs-flag{display:flex;flex-direction:column;width:100%;height:100%}
.cs-benefit-ico.flag .cs-flag span{flex:1;display:block;width:100%}
.cs-benefit-text{font-size:12.5px;font-weight:600;color:#0a0a0a;line-height:1.35;letter-spacing:-.1px;min-width:0}
@media(max-width:1024px){
    .cs-benefits-row{grid-template-columns:repeat(2,minmax(0,1fr))}
    .cs-benefit-item{border-right:1px solid #eef0f3;border-bottom:1px solid #eef0f3}
    .cs-benefit-item:nth-child(2n){border-right:none}
    .cs-benefit-item:nth-last-child(-n+1):nth-child(odd),.cs-benefit-item:nth-last-child(-n+2):nth-child(2n){border-bottom:none}
}
@media(max-width:768px){
    .cs-benefits-row{border-radius:14px;margin-top:8px;margin-bottom:20px;border-color:#e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,.06);width:auto;max-width:none}
    .cs-benefit-item{padding:16px 20px;gap:12px}
    .cs-benefit-ico{width:36px;height:36px;border-radius:10px}
    .cs-benefit-ico.flag{width:32px;height:32px}
    .cs-benefit-text{font-size:13px;line-height:1.4}
}
@media(max-width:500px){
    .cs-benefits-row{grid-template-columns:1fr;border-radius:14px;margin-bottom:18px}
    .cs-benefit-item{border-right:none;padding:14px 20px;gap:12px}
    .cs-benefit-item:last-child{border-bottom:none}
    .cs-benefit-ico{width:36px;height:36px}
    .cs-benefit-ico.flag{width:32px;height:32px}
    .cs-benefit-text{font-size:13px}
}

/* ============ FINANCING + GETHELP compact two-column row ============ */
.cs-finance-row{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:16px;max-width:calc(1200px - 48px);margin:0 auto 16px;width:100%;box-sizing:border-box;align-items:stretch}
@media(max-width:1024px){.cs-finance-row{grid-template-columns:1fr;gap:14px}}

/* Shared compact-card chrome */
.cs-finance-card,.cs-gethelp-card{background:#fff;border:1px solid #eeeef0;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 14px rgba(0,0,0,.04);padding:18px 20px;display:flex;flex-direction:column;width:100%;min-width:0;box-sizing:border-box}

/* Finansowanie pojazdu — compact */
.cs-finance-head{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.cs-finance-head-ico{flex-shrink:0;width:34px;height:34px;border-radius:9px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-finance-title{font-size:15.5px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:0;line-height:1.2}
.cs-finance-sub{font-size:12px;color:#6b7280;line-height:1.45;margin:2px 0 0}
.cs-finance-controls{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-bottom:14px}
.cs-finance-field{display:flex;flex-direction:column;min-width:0}
.cs-finance-field label{display:block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6b7280;margin-bottom:5px}
.cs-finance-readonly{height:38px;display:flex;align-items:center;padding:0 12px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-weight:700;color:#1a1a1a;letter-spacing:-.2px}
.cs-finance-input-wrap{position:relative;display:flex;align-items:center}
.cs-finance-input-wrap input{width:100%;height:38px;background:#fff;border:1.5px solid #d1d5db;border-radius:8px;padding:0 36px 0 12px;font-size:14px;font-weight:700;color:#1a1a1a;outline:none;transition:border-color .15s;min-width:0}
.cs-finance-input-wrap input:focus{border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.08)}
.cs-finance-input-wrap span{position:absolute;right:12px;font-size:12px;font-weight:600;color:#6b7280;pointer-events:none}
.cs-finance-field select{width:100%;height:38px;background:#fff;border:1.5px solid #d1d5db;border-radius:8px;padding:0 10px;font-size:14px;font-weight:700;color:#1a1a1a;cursor:pointer;outline:none;appearance:auto;transition:border-color .15s}
.cs-finance-field select:focus{border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.08)}
.cs-finance-result{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 14px;background:#eef4ff;border:1px solid #c7d8ff;border-radius:12px;margin-bottom:10px;flex-wrap:wrap}
.cs-finance-result-left{display:flex;flex-direction:column;min-width:0}
.cs-finance-result-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#0066ff;margin-bottom:3px}
.cs-finance-result-value{font-size:22px;font-weight:900;color:#0066ff;letter-spacing:-.4px;line-height:1.1}
.cs-finance-result-value .suffix{font-size:12px;font-weight:700;color:#0066ff;letter-spacing:0;opacity:.85}
.cs-finance-cta{height:40px;padding:0 18px;background:#0066ff;color:#fff;border:none;border-radius:50px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;box-shadow:0 2px 8px rgba(0,102,255,.25);transition:all .15s;flex-shrink:0}
.cs-finance-cta:hover{background:#0052cc;box-shadow:0 4px 14px rgba(0,102,255,.35);transform:translateY(-1px)}
.cs-finance-foot{font-size:10.5px;color:#9ca3af;line-height:1.5;margin-top:auto;padding-top:6px}

@media(max-width:768px){
    .cs-finance-controls{grid-template-columns:1fr 1fr}
    .cs-finance-field:first-child{grid-column:1 / -1}
    .cs-finance-result{flex-direction:column;align-items:stretch;gap:10px}
    .cs-finance-cta{width:100%;justify-content:center}
}

/* Gwarancja techniczna GetHelp — compact (3 mini cards in one row) */
.cs-gethelp-head{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.cs-gethelp-ico{flex-shrink:0;width:34px;height:34px;border-radius:9px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-gethelp-title{font-size:15.5px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:0;line-height:1.2;display:flex;align-items:center;gap:6px}
.cs-gethelp-title .info-i{display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:#eff6ff;color:#0066ff;font-size:9.5px;font-weight:700;flex-shrink:0}
.cs-gethelp-row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-bottom:12px}
.cs-gethelp-mini{background:#fff;border:1.5px solid #e5e7eb;border-radius:12px;padding:12px 10px;display:flex;flex-direction:column;align-items:center;text-align:center;gap:5px;min-width:0;transition:border-color .15s,background .15s,box-shadow .15s}
.cs-gethelp-mini.active{background:#eff6ff;border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.1)}
.cs-gethelp-mini-ico{width:32px;height:32px;border-radius:8px;background:#e8f1ff;color:#0066ff;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cs-gethelp-mini-name{font-size:13px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;line-height:1.2}
.cs-gethelp-mini-price{font-size:12px;font-weight:700;color:#0a0a0a;line-height:1.2}
.cs-gethelp-mini-price.free{color:#16a34a}
.cs-gethelp-more-link{display:inline-flex;align-items:center;gap:5px;font-size:12.5px;font-weight:700;color:#0066ff;text-decoration:none;margin-bottom:10px;align-self:flex-start}
.cs-gethelp-more-link:hover{color:#0052cc}
.cs-gethelp-helper{font-size:10.5px;color:#9ca3af;line-height:1.5;margin-top:auto}

@media(max-width:768px){
    .cs-gethelp-row{grid-template-columns:1fr;gap:8px}
}

/* ============ 3-card bottom row (Historia / Dokumenty / Formalności) ============ */
.cs-info-3row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;max-width:calc(1200px - 48px);margin:0 auto 16px;width:100%;box-sizing:border-box;align-items:stretch}
.cs-info-3card{background:#fff;border:1px solid #eeeef0;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);padding:22px 24px;display:flex;flex-direction:column;min-width:0;height:100%}
.cs-info-3card-head{display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f0f0f2}
.cs-info-3card-ico{flex-shrink:0;width:38px;height:38px;border-radius:10px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-info-3card-ico svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.cs-info-3card-title{font-size:15px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:0;line-height:1.3}
.cs-info-3card-rows{flex:1;display:flex;flex-direction:column}
.cs-info-3row-line{display:flex;justify-content:space-between;align-items:baseline;gap:12px;padding:9px 0;border-bottom:1px solid #f5f5f7;font-size:13px;line-height:1.4}
.cs-info-3row-line:last-child{border-bottom:none}
.cs-info-3row-line .lbl{color:#6b7280;font-weight:500;min-width:0;flex-shrink:1}
.cs-info-3row-line .val{font-weight:700;color:#0a0a0a;text-align:right;word-break:break-word;flex-shrink:0;max-width:60%}
.cs-info-3row-line .val.muted{color:#9ca3af;font-weight:500;font-style:italic}
.cs-info-3row-line .val.ok{color:#15803d}
@media(max-width:1024px){
    .cs-info-3row{grid-template-columns:1fr 1fr;gap:14px}
    .cs-info-3card{padding:18px 18px}
}
@media(max-width:768px){
    .cs-info-3row{grid-template-columns:1fr;width:auto;max-width:none}
    .cs-info-3card{border-radius:14px;border-color:#e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,.06);padding:18px 20px}
}

/* ============ DETAIL 3-CARD ROW (Serwisowanie / Dokumenty / Zużycie) ============ */
/* Wraps the detail blocks so they sit as equal sibling cards on desktop.
   Track count adapts to how many cards actually rendered (no awkward
   empty 3rd column when one section's data is missing). Inner 2-col
   grids collapse to 1-col since each card holds less horizontal room. */
.cs-detail-3row{display:grid;gap:16px;align-items:stretch;max-width:calc(1200px - 48px);margin:0 auto 16px;width:100%;box-sizing:border-box;grid-template-columns:repeat(3,minmax(0,1fr))}
.cs-detail-3row--n2{grid-template-columns:repeat(2,minmax(0,1fr))}
.cs-detail-3row--n1{grid-template-columns:1fr}
.cs-detail-3row > .cs-data-section{margin-bottom:0;max-width:100%;width:100%;height:100%}
.cs-detail-3row > .cs-data-section .cs-data-grid-2col{grid-template-columns:1fr;column-gap:0}
@media(max-width:1024px){
    .cs-detail-3row,.cs-detail-3row--n2,.cs-detail-3row--n1{grid-template-columns:1fr;width:auto;max-width:none;gap:14px}
}

/* ============ TECH + ENGINE-VIDEO TWO-COLUMN ROW ============ */
.cs-tech-engine-row{display:grid;grid-template-columns:1.15fr 1fr;gap:20px;margin-bottom:16px;align-items:stretch;max-width:calc(1200px - 48px);margin-left:auto;margin-right:auto;width:100%;box-sizing:border-box}
.cs-tech-engine-card{background:#fff;border:1px solid #eeeef0;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);padding:24px 26px;display:flex;flex-direction:column;min-width:0;height:100%}
.cs-tech-engine-card-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:18px}
.cs-tech-engine-card-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-tech-engine-card-ico svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.cs-tech-engine-card-titlewrap{min-width:0}
.cs-tech-engine-card-title{font-size:17px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin:0 0 6px;line-height:1.25}
.cs-tech-engine-card-sub{font-size:12.5px;color:#6b7280;line-height:1.55;margin:0}
.cs-tech-engine-card-sub + .cs-tech-engine-card-sub{margin-top:2px}

/* Tech list panel (inside left card) */
.cs-tech-list-panel{background:#f9fafb;border:1px solid #f0f0f2;border-radius:14px;padding:6px 14px;flex:1}
.cs-tech-list-row{display:flex;align-items:center;gap:12px;padding:13px 4px;border-bottom:1px solid #eef0f3}
.cs-tech-list-row:last-child{border-bottom:none}
.cs-tech-list-ico{flex-shrink:0;width:34px;height:34px;border-radius:10px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-tech-list-ico svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.cs-tech-list-name{flex:1;font-size:14px;font-weight:600;color:#0a0a0a;letter-spacing:-.1px}
.cs-tech-list-status{display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:#16a34a;flex-shrink:0;white-space:nowrap}
.cs-tech-list-status.warn{color:#d97706}
.cs-tech-list-status.fail{color:#dc2626}
.cs-tech-list-status .check{width:18px;height:18px;border-radius:50%;background:#dcfce7;color:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cs-tech-list-status.warn .check{background:#fef3c7;color:#d97706}
.cs-tech-list-status.fail .check{background:#fee2e2;color:#dc2626}
.cs-tech-list-status .check svg{width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}

/* ============ 360° CARDS SECTION ============ */
/* 360° row — always a 2-column layout on desktop. When admin has only filled
   one of the two sources, we still render the row as 2 columns and place the
   single real card in the left column with a max-width so it doesn't visually
   stretch full-width. We never invent a placeholder for the missing source. */
.cs-pano360-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px;max-width:calc(1200px - 48px);margin-left:auto;margin-right:auto;width:100%;box-sizing:border-box}
.cs-pano360-row.single{grid-template-columns:repeat(2,minmax(0,1fr));justify-content:start}
.cs-pano360-row.single .cs-pano360-card{max-width:100%}
.cs-pano360-section-card{background:#fff;border:1px solid #eeeef0;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);padding:24px 26px;max-width:calc(1200px - 48px);margin:0 auto 16px;width:100%;box-sizing:border-box}
.cs-pano360-section-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:18px}
.cs-pano360-section-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-pano360-section-ico svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.cs-pano360-section-title{font-size:17px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin:0 0 6px;line-height:1.25}
.cs-pano360-section-sub{font-size:12.5px;color:#6b7280;line-height:1.55;margin:0;max-width:640px}

.cs-pano360-card{position:relative;display:block;height:280px;border-radius:14px;overflow:hidden;background:#0a0a0a;cursor:pointer;text-decoration:none;color:#fff;transition:transform .25s ease,box-shadow .25s ease;border:none;padding:0;font:inherit;text-align:left}
.cs-pano360-card:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,0,0,.18)}
.cs-pano360-card-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease}
.cs-pano360-card:hover .cs-pano360-card-img{transform:scale(1.04)}
.cs-pano360-card-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(10,10,10,.05) 0%,rgba(10,10,10,.5) 60%,rgba(10,10,10,.85) 100%)}
.cs-pano360-card-empty{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.55);font-size:13px;letter-spacing:.3px;background:linear-gradient(135deg,#1a1a2e,#0a0a0a)}
.cs-pano360-card-mark{position:absolute;top:18px;left:18px;display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.16);color:#fff;font-size:11px;font-weight:700;padding:6px 11px;border-radius:50px;letter-spacing:.4px;text-transform:uppercase;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
.cs-pano360-card-mark svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2.2}
.cs-pano360-card-play{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:78px;height:78px;border-radius:50%;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.85);display:flex;align-items:center;justify-content:center;color:#fff;backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);transition:transform .25s ease,background .25s ease}
.cs-pano360-card:hover .cs-pano360-card-play{transform:translate(-50%,-50%) scale(1.08);background:rgba(255,255,255,.28)}
.cs-pano360-card-play svg{width:32px;height:32px;stroke:currentColor;fill:none;stroke-width:1.6}
.cs-pano360-card-text{position:absolute;left:22px;right:22px;bottom:20px;z-index:2}
.cs-pano360-card-title{font-size:18px;font-weight:800;letter-spacing:-.3px;line-height:1.2;margin:0 0 4px}
.cs-pano360-card-sub{font-size:13px;color:rgba(255,255,255,.85);line-height:1.4;margin:0}
.cs-pano360-card.disabled{cursor:not-allowed;opacity:.7}
.cs-pano360-card.disabled:hover{transform:none;box-shadow:none}
.cs-pano360-card.disabled:hover .cs-pano360-card-img{transform:none}
.cs-pano360-card.disabled:hover .cs-pano360-card-play{transform:translate(-50%,-50%);background:rgba(255,255,255,.18)}

@media(max-width:1024px){
    .cs-pano360-row{grid-template-columns:1fr;gap:14px}
    .cs-pano360-section-card{padding:20px}
    .cs-pano360-section-title{font-size:16px}
    .cs-pano360-section-sub{font-size:12px}
    .cs-pano360-card{height:220px;border-radius:12px}
    .cs-pano360-card-title{font-size:16px}
    .cs-pano360-card-sub{font-size:12.5px}
    .cs-pano360-card-play{width:64px;height:64px}
    .cs-pano360-card-play svg{width:26px;height:26px}
}
@media(max-width:768px){
    .cs-pano360-section-card{padding:18px 16px;border-radius:14px}
    .cs-pano360-card{height:200px}
    .cs-pano360-card-text{left:16px;right:16px;bottom:16px}
    .cs-pano360-card-mark{top:14px;left:14px;font-size:10px;padding:5px 9px}
}
@media(max-width:500px){
    .cs-pano360-card{height:180px}
    .cs-pano360-card-play{width:56px;height:56px}
    .cs-pano360-card-play svg{width:22px;height:22px}
}

/* ============ PAINT + TIRES TWO-CARD ROW ============ */
/* Shares the same card system as .cs-tech-engine-row (PR #25): white card,
   18px radius, blue 44px icon tile, same content container width. */
.cs-pt-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px;max-width:calc(1200px - 48px);margin-left:auto;margin-right:auto;width:100%;box-sizing:border-box;align-items:stretch}
.cs-pt-card{background:#fff;border:1px solid #eeeef0;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);padding:24px 26px;display:flex;flex-direction:column;min-width:0;height:100%}
.cs-pt-card-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:16px}
.cs-pt-card-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-pt-card-ico svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.cs-pt-card-title{font-size:17px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin:0 0 6px;line-height:1.25}
.cs-pt-card-sub{font-size:12.5px;color:#6b7280;line-height:1.55;margin:0;max-width:520px}

/* PAINT — compact grid of measurements with status colours */
.cs-pt-paint-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;flex:1}
/* Paint cell — same layout as before, refined visual styling per the reference:
   soft tinted background per status, colored dot next to the value, smaller
   muted µm unit, neutral grey label. Status modifiers .ok / .warn / .bad map
   to the three threshold buckets (≤150 / 151–300 / >300). */
.cs-pt-paint-cell{background:#f9fafb;border:1px solid #f0f0f2;border-radius:10px;padding:11px 13px;display:flex;flex-direction:column;gap:4px;min-width:0}
.cs-pt-paint-cell.ok  {background:#f0fdf4;border-color:#bbf7d0}
.cs-pt-paint-cell.warn{background:#fffbeb;border-color:#fde68a}
.cs-pt-paint-cell.bad {background:#fef2f2;border-color:#fecaca}
.cs-pt-paint-cell-label{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.45px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cs-pt-paint-cell-value{font-size:16px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;display:inline-flex;align-items:center;gap:7px;line-height:1.1}
.cs-pt-paint-cell-value .dot{display:inline-block;width:9px;height:9px;border-radius:50%;flex-shrink:0;background:#16a34a;box-shadow:0 0 0 2px rgba(22,163,74,.18)}
.cs-pt-paint-cell-value .num{display:inline-flex;align-items:baseline;gap:3px}
.cs-pt-paint-cell-value .unit{font-size:11px;font-weight:600;color:#6b7280;letter-spacing:0}
.cs-pt-paint-cell.ok   .cs-pt-paint-cell-value{color:#15803d}
.cs-pt-paint-cell.warn .cs-pt-paint-cell-value{color:#b45309}
.cs-pt-paint-cell.bad  .cs-pt-paint-cell-value{color:#b91c1c}
.cs-pt-paint-cell.ok   .cs-pt-paint-cell-value .dot{background:#16a34a;box-shadow:0 0 0 2px rgba(22,163,74,.18)}
.cs-pt-paint-cell.warn .cs-pt-paint-cell-value .dot{background:#f59e0b;box-shadow:0 0 0 2px rgba(245,158,11,.18)}
.cs-pt-paint-cell.bad  .cs-pt-paint-cell-value .dot{background:#dc2626;box-shadow:0 0 0 2px rgba(220,38,38,.18)}
.cs-pt-paint-cell.ok   .cs-pt-paint-cell-value .unit{color:#16a34a;opacity:.7}
.cs-pt-paint-cell.warn .cs-pt-paint-cell-value .unit{color:#b45309;opacity:.7}
.cs-pt-paint-cell.bad  .cs-pt-paint-cell-value .unit{color:#b91c1c;opacity:.7}
.cs-pt-paint-legend{display:flex;flex-wrap:wrap;gap:10px 16px;margin-top:12px;padding-top:12px;border-top:1px solid #f0f0f2;font-size:11px;color:#6b7280}
.cs-pt-paint-legend-item{display:inline-flex;align-items:center;gap:6px;line-height:1.3}
.cs-pt-paint-legend-item .sw{display:inline-block;width:10px;height:10px;border-radius:3px;flex-shrink:0}
.cs-pt-paint-cta{display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:12.5px;font-weight:700;color:#0066ff;text-decoration:none;align-self:flex-start;padding:8px 14px;border:1.5px solid #dbeafe;background:#eff6ff;border-radius:10px;transition:background .15s,border-color .15s}
.cs-pt-paint-cta:hover{background:#dbeafe;border-color:#bfdbfe;color:#0052cc}
.cs-pt-paint-cta svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.2}

/* TIRES — stacked-rows layout. No tire/wheel image: one card per tire set
   with four full-width rows (position + spec text on the left, green tread
   pill on the right) and a compact summary footer (rim + overall status). */
.cs-pt-tire-set{background:#fff;border:1px solid #eeeef0;border-radius:14px;padding:6px 16px 16px;flex:1;display:flex;flex-direction:column}
.cs-pt-tire-set-head{display:flex;align-items:center;gap:8px;font-size:13.5px;font-weight:700;color:#0a0a0a;letter-spacing:-.1px;padding:12px 0 8px;border-bottom:1px solid #eef0f3;margin-bottom:4px}
.cs-pt-tire-set-head .mount{color:#0066ff;font-weight:600;font-size:12.5px}
.cs-pt-tire-set-head .info{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:#eff6ff;color:#0066ff;flex-shrink:0;margin-left:2px}
.cs-pt-tire-set-head .info svg{width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2.4}

/* Single tire row */
.cs-pt-tire-row{display:flex;align-items:center;gap:14px;padding:14px 4px;border-bottom:1px solid #f0f0f2}
.cs-pt-tire-row:last-of-type{border-bottom:none}
.cs-pt-tire-row-info{flex:1;min-width:0}
.cs-pt-tire-row-pos{font-size:13.5px;font-weight:700;color:#0a0a0a;letter-spacing:-.1px;margin-bottom:2px;line-height:1.3}
.cs-pt-tire-row-spec{font-size:12.5px;color:#374151;line-height:1.35;word-break:break-word}
.cs-pt-tire-row-model{font-size:12px;color:#6b7280;margin-top:2px;line-height:1.35}
.cs-pt-tire-row-empty{font-size:12px;color:#9ca3af;font-style:italic}

/* Green tread pill (right side, premium look) */
.cs-pt-tire-tread-pill{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;padding:7px 14px;border-radius:50px;background:#dcfce7;color:#15803d;font-size:13px;font-weight:700;letter-spacing:-.1px;min-width:78px;text-align:center;line-height:1.2}
.cs-pt-tire-tread-pill.warn{background:#fef3c7;color:#b45309}
.cs-pt-tire-tread-pill.bad{background:#fee2e2;color:#b91c1c}
.cs-pt-tire-tread-pill.empty{background:#f1f5f9;color:#94a3b8;font-weight:600}

/* Bottom summary row inside the same set card */
.cs-pt-tire-summary{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:14px 4px 4px;margin-top:8px;border-top:1px solid #f0f0f2;font-size:13px;color:#374151;flex-wrap:wrap}
.cs-pt-tire-summary-left{display:inline-flex;align-items:center;gap:8px;font-weight:600;color:#0a0a0a;min-width:0}
.cs-pt-tire-summary-left .ico{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#eff6ff;color:#0066ff;flex-shrink:0}
.cs-pt-tire-summary-left .ico svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2}
.cs-pt-tire-summary-right{font-size:13px;font-weight:700;color:#15803d;display:inline-flex;align-items:center;gap:6px;flex-shrink:0}
.cs-pt-tire-summary-right.warn{color:#b45309}
.cs-pt-tire-summary-right.bad{color:#b91c1c}
.cs-pt-tire-summary-right .ico{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:#dcfce7;color:#15803d}
.cs-pt-tire-summary-right.warn .ico{background:#fef3c7;color:#b45309}
.cs-pt-tire-summary-right.bad .ico{background:#fee2e2;color:#b91c1c}
.cs-pt-tire-summary-right .ico svg{width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}

.cs-pt-tire-empty{font-size:12px;color:#9ca3af;font-style:italic}

@media(max-width:768px){
    .cs-pt-tire-set{padding:4px 14px 14px}
    .cs-pt-tire-row{padding:12px 2px;gap:10px}
    .cs-pt-tire-row-pos{font-size:13px}
    .cs-pt-tire-row-spec{font-size:12px}
    .cs-pt-tire-row-model{font-size:11.5px}
    .cs-pt-tire-tread-pill{font-size:12px;min-width:66px;padding:6px 12px}
    .cs-pt-tire-summary{flex-direction:column;align-items:flex-start;gap:8px;padding-top:12px}
}
@media(max-width:500px){
    .cs-pt-tire-set{padding:4px 12px 12px}
    .cs-pt-tire-row{padding:11px 2px}
    .cs-pt-tire-tread-pill{min-width:60px;font-size:11.5px}
}

/* Empty state shared by both cards */
.cs-pt-empty{font-size:13px;color:#9ca3af;font-style:italic;padding:24px 0;text-align:center;background:#fafbfc;border:1px dashed #e5e7eb;border-radius:10px;flex:1;display:flex;align-items:center;justify-content:center}

@media(max-width:1024px){
    .cs-pt-row{grid-template-columns:1fr;gap:14px}
    .cs-pt-card{padding:20px}
    .cs-pt-card-title{font-size:16px}
    .cs-pt-paint-grid{grid-template-columns:repeat(2,1fr)}
    .cs-pt-tire-list{grid-template-columns:1fr 1fr}
}
@media(max-width:768px){
    .cs-pt-card{padding:18px 16px;border-radius:14px}
}
@media(max-width:500px){
    .cs-pt-paint-grid{grid-template-columns:1fr 1fr}
    .cs-pt-tire-list{grid-template-columns:1fr 1fr}
}

/* Engine video panel (inside right card) */
.cs-engine-video-panel{position:relative;border-radius:14px;overflow:hidden;background:#0a0a0a;aspect-ratio:16/10;flex:1;min-height:280px}
.cs-engine-video-panel iframe,.cs-engine-video-panel video{position:absolute;inset:0;width:100%;height:100%;border:0;display:block;background:#000}
.cs-engine-video-empty{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.55);font-size:13px;letter-spacing:.3px;text-align:center;padding:30px}

/* Tablet/mobile — stack the two cards vertically */
@media(max-width:1024px){
    .cs-tech-engine-row{grid-template-columns:1fr;gap:14px}
    .cs-tech-engine-card{padding:20px}
    .cs-tech-engine-card-title{font-size:16px}
    .cs-tech-engine-card-sub{font-size:12px}
    .cs-tech-list-row{padding:12px 4px}
    .cs-tech-list-ico{width:32px;height:32px}
    .cs-tech-list-ico svg{width:16px;height:16px}
    .cs-tech-list-name{font-size:13.5px}
    .cs-tech-list-status{font-size:12px}
    .cs-engine-video-panel{aspect-ratio:16/9;min-height:0}
}
@media(max-width:500px){
    .cs-tech-engine-card{padding:18px 16px;border-radius:14px}
    .cs-tech-engine-card-ico{width:38px;height:38px}
    .cs-tech-engine-card-ico svg{width:18px;height:18px}
    .cs-tech-engine-card-title{font-size:15px}
    .cs-tech-list-panel{padding:4px 12px}
    .cs-tech-list-row{padding:11px 2px;gap:10px}
}

/* PAINT GRID */
.cs-paint-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:0;border:1px solid #eeeef0;border-radius:12px;overflow:hidden;background:#fff}
.cs-paint-item{background:#fff;padding:14px 16px;border-bottom:1px solid #f0f0f2;transition:all .15s}
.cs-paint-item.paint-ok{background:linear-gradient(90deg,#f0fdf4,#fff 60%)}
.cs-paint-item.paint-warn{background:linear-gradient(90deg,#fffbeb,#fff 60%)}
.cs-paint-item.paint-danger{background:linear-gradient(90deg,#fef2f2,#fff 60%)}
.cs-paint-label{font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);margin-bottom:4px;white-space:nowrap}
.cs-paint-value{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.3px;display:flex;align-items:center;gap:6px}
.paint-ok .cs-paint-value{color:#059669}
.paint-warn .cs-paint-value{color:#d97706}
.paint-danger .cs-paint-value{color:#dc2626}
.cs-paint-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

/* SERVICE / INSPECTION GRID */
.cs-svc-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px 40px}
.cs-svc-item{background:transparent;padding:0}
.cs-svc-label{font-size:11px;font-weight:500;color:var(--text-3);margin-bottom:5px}
.cs-svc-value{font-size:15px;font-weight:700;color:var(--text)}

/* FUEL / EMISSION SECTION */
.cs-fuel-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:28px}
.cs-fuel-item .cs-fuel-label{font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-3);margin-bottom:6px}
.cs-fuel-item .cs-fuel-value{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.4px}

/* DAMAGE / VISUAL CONDITION SECTION */
.cs-dmg-gallery{border-radius:12px;overflow:hidden}
.cs-dmg-gallery-stage{position:relative;background:#f5f5f7;overflow:hidden;border-radius:12px;aspect-ratio:16/9;touch-action:pan-y;user-select:none;-webkit-user-select:none}
/* Slides stack on top of each other and cross-fade — no display:none jump cut. */
.cs-dmg-gallery-slide{position:absolute;inset:0;opacity:0;visibility:hidden;transition:opacity .25s ease;pointer-events:none}
.cs-dmg-gallery-slide.active{opacity:1;visibility:visible;pointer-events:auto;z-index:1}
.cs-dmg-gallery-slide img{width:100%;height:100%;object-fit:cover;display:block;cursor:pointer}
.cs-dmg-gallery-label{position:absolute;bottom:14px;left:14px;background:rgba(0,0,0,.65);color:#fff;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);z-index:2}
.cs-dmg-gallery-meta{position:absolute;bottom:14px;right:14px;display:flex;align-items:center;gap:6px;z-index:2}
.cs-dmg-gallery-counter{background:rgba(0,0,0,.65);color:#fff;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:700;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
.cs-dmg-gallery-fs{width:36px;height:36px;border:none;background:rgba(0,0,0,.65);color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);transition:.15s}
.cs-dmg-gallery-fs:hover{background:rgba(0,0,0,.85)}
.cs-dmg-gallery-arrow{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:44px;border:none;background:rgba(0,0,0,.5);color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:3;transition:.15s;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}
.cs-dmg-gallery-arrow:hover{background:rgba(0,0,0,.75)}
.cs-dmg-gallery-arrow.left{left:12px}
.cs-dmg-gallery-arrow.right{right:12px}
.cs-dmg-gallery-thumbs-wrap{display:flex;align-items:center;gap:4px;margin-top:8px}
.cs-dmg-gallery-thumbs{display:flex;gap:6px;overflow-x:auto;scroll-behavior:smooth;flex:1;-ms-overflow-style:none;scrollbar-width:none}
.cs-dmg-gallery-thumbs::-webkit-scrollbar{display:none}
.cs-dmg-gallery-thumb{width:100px;height:70px;flex-shrink:0;border-radius:8px;overflow:hidden;cursor:pointer;border:3px solid transparent;transition:.15s;opacity:.6}
.cs-dmg-gallery-thumb:hover{opacity:.9}
.cs-dmg-gallery-thumb.active{border-color:#f59e0b;opacity:1}
.cs-dmg-gallery-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.cs-dmg-thumb-arrow{width:28px;height:28px;border:none;background:#f0f0f2;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;color:#374151;transition:.15s}
.cs-dmg-thumb-arrow:hover{background:#e0e0e2}


/* TIRES - reference layout */
.cs-tire-set{margin-bottom:32px}
.cs-tire-set:last-child{margin-bottom:0}
.cs-tire-set-title{font-size:15px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.cs-tire-set-title i{width:16px;height:16px;color:var(--text-3)}
.cs-tire-table{display:grid;grid-template-columns:220px repeat(4,1fr);border:1px solid var(--border-l);border-radius:12px;overflow:hidden}
.cs-tire-th{background:var(--bg);padding:14px 16px;font-size:11px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.4px;border-right:1px solid var(--border-l);border-bottom:1px solid var(--border-l)}
.cs-tire-th:last-child{border-right:none}
.cs-tire-info{border-right:1px solid var(--border-l)}
.cs-tire-info-row{padding:14px 16px;border-bottom:1px solid var(--border-l);font-size:13px}
.cs-tire-info-row:last-child{border-bottom:none}
.cs-tire-info-row .lbl{font-size:11px;color:var(--text-3);margin-bottom:3px}
.cs-tire-info-row .val{font-weight:600;color:var(--text)}
.cs-tire-col{border-right:1px solid var(--border-l);display:flex;flex-direction:column}
.cs-tire-col:last-child{border-right:none}
.cs-tire-col-head{padding:14px 12px;border-bottom:1px solid var(--border-l);text-align:center}
.cs-tire-icon{width:72px;height:72px;margin:0 auto 8px;position:relative}
.cs-tire-icon svg.wheel{width:100%;height:100%}
.cs-tire-status-icon{position:absolute;bottom:2px;right:2px;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid #fff}
.cs-tire-status-icon.ok{background:var(--green)}
.cs-tire-status-icon.warn{background:var(--yellow)}
.cs-tire-status-icon i{width:11px;height:11px;color:#fff;stroke-width:2.8}
.cs-tire-pos-name{font-size:12px;font-weight:600;color:var(--text-2)}
.cs-tire-data-row{padding:10px 12px;border-bottom:1px solid var(--border-l);text-align:center;font-size:13px;font-weight:700;color:var(--text)}
.cs-tire-data-row:last-child{border-bottom:none}
.cs-tire-data-row.ok-txt{color:var(--green-dark);font-weight:500;font-size:12px}
.cs-tire-data-row.warn-txt{color:var(--yellow-dark);font-weight:500;font-size:12px}

/* FEATURED EQUIPMENT (COS style — inline strip with icons) */
.cs-feat-eq{display:flex;flex-wrap:wrap;gap:12px 32px;padding:4px 0}
.cs-feat-eq-item{display:flex;align-items:center;gap:8px;padding:8px 0;font-size:14.5px;color:#1a1a1a;font-weight:600;white-space:nowrap}
.cs-feat-eq-item i{width:18px;height:18px;color:#16a34a;flex-shrink:0;stroke-width:2.2}

/* WYPOSAŻENIE — reference rebuild: header + 8 highlighted tiles + 6 category cards */
.cs-equip-section{max-width:calc(1200px - 48px);margin:0 auto 16px;width:100%;box-sizing:border-box}
.cs-equip-head{margin-bottom:18px}
.cs-equip-title{font-size:22px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin:0 0 4px;line-height:1.2}
.cs-equip-sub{font-size:13.5px;color:#6b7280;line-height:1.5;margin:0}
/* Top 8 highlighted tiles */
/* 8 top highlight tiles — uniform min-height so multi-line labels don't
   leave row-to-row gaps on mobile (2- and 4-col grids). */
.cs-equip-tiles{display:grid;grid-template-columns:repeat(8,minmax(0,1fr));gap:10px;margin-bottom:20px;align-items:stretch}
.cs-equip-tile{background:#fff;border:1px solid #eeeef0;border-radius:14px;padding:18px 10px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;text-align:center;gap:10px;min-width:0;min-height:112px;transition:border-color .15s,box-shadow .15s}
.cs-equip-tile:hover{border-color:#cbd5e1;box-shadow:0 4px 12px rgba(0,0,0,.06)}
.cs-equip-tile-ico{width:46px;height:46px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cs-equip-tile-label{font-size:12px;font-weight:700;color:#1a1a1a;letter-spacing:-.1px;line-height:1.3;word-break:break-word}
/* Category cards */
/* 2-column desktop grid — "Komfort | Bezpieczeństwo" row 1,
   "Światła i nadwozie | Inne" row 2. Removes the 3+1 awkward layout. */
.cs-equip-cats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;align-items:stretch}
/* Outer card unified with cs-info-3card / cs-data-section */
.cs-equip-cat{background:#fff;border:1px solid #eeeef0;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);padding:22px 24px;display:flex;flex-direction:column;min-width:0}
.cs-equip-cat-head{display:flex;align-items:center;gap:12px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f0f0f2}
.cs-equip-cat-ico{flex-shrink:0;width:38px;height:38px;border-radius:10px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-equip-cat-title{font-size:15px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:0;line-height:1.2}
.cs-equip-cat-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px}
.cs-equip-cat-list li{display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#374151;line-height:1.4}
.cs-equip-cat-list li svg{flex-shrink:0;margin-top:2px;color:#0066ff!important}
/* Collapsed: items 7+ hidden; expanded class on .cs-equip-section reveals them */
.cs-equip-cat-list li.cs-equip-extra{display:none}
.cs-equip-section.expanded .cs-equip-cat-list li.cs-equip-extra{display:flex}
/* "Pokaż pełne wyposażenie" toggle button */
.cs-equip-show-all{display:flex;align-items:center;justify-content:center;gap:8px;margin:18px auto 0;padding:13px 28px;background:transparent;border:1.5px solid #0066ff;color:#0066ff;border-radius:50px;font-size:14px;font-weight:700;cursor:pointer;transition:background .15s,color .15s,box-shadow .15s;font-family:inherit;min-height:46px}
.cs-equip-show-all:hover{background:#0066ff;color:#fff;box-shadow:0 6px 18px rgba(0,102,255,.25)}
.cs-equip-show-all .cs-equip-show-all-chev{transition:transform .2s}
.cs-equip-section.expanded .cs-equip-show-all .cs-equip-show-all-chev{transform:rotate(180deg)}

/* Tablet: 4-col top tiles, 2-col category cards */
@media(max-width:1024px){
    .cs-equip-tiles{grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
    .cs-equip-cats{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
}
/* Mobile: 2-col tiles, single-col cards */
@media(max-width:600px){
    .cs-equip-tiles{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
    .cs-equip-tile{padding:16px 10px}
    .cs-equip-tile-ico{width:42px;height:42px}
    .cs-equip-tile-label{font-size:11.5px}
    .cs-equip-cats{grid-template-columns:1fr;gap:10px}
    .cs-equip-cat{padding:16px}
    .cs-equip-title{font-size:18px}
    .cs-equip-sub{font-size:12.5px}
}
.cs-eq-count{font-size:13px;color:var(--text-3);font-weight:500;margin-left:auto}

/* DATA SECTION (CarOnSale style — pixel-perfect) */
.cs-sections-2col{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:16px;margin-bottom:16px;align-items:start;overflow:hidden;max-width:1200px;margin-left:auto;margin-right:auto;padding:0 24px}
@media(max-width:900px){.cs-sections-2col{grid-template-columns:1fr}}
.cs-sections-2col .cs-data-section{margin-bottom:0}
.cs-col-left,.cs-col-right{display:flex;flex-direction:column;gap:16px;min-width:0;align-content:start;overflow:hidden}
/* Unified section card — matches cs-info-3card and cs-equip-cat so every
   detail block on the page shares the same border/radius/shadow language. */
.cs-data-section{background:#fff;border:1px solid #eeeef0;border-radius:18px;margin-bottom:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);max-width:100%}
.cs-wrap > .cs-data-section,.cs-wrap > div:not(.container) > .cs-data-section{max-width:calc(1200px - 48px);margin-left:auto;margin-right:auto}
.cs-data-header{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;user-select:none;border-bottom:1px solid #f0f0f2;background:#fff}
.cs-data-header h2{font-size:16px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;display:flex;align-items:center;gap:12px;margin:0;line-height:1.3;font-family:'Inter',sans-serif}
.cs-data-header h2 i,.cs-data-header h2 svg{display:inline-flex;align-items:center;justify-content:center}
/* Header icon: 38x38 contained pill (matches cs-info-3card-ico / cs-equip-cat-ico) */
.cs-data-header h2 svg,.cs-data-header h2 i[data-lucide],.cs-data-header h2 i.cs-icon{width:18px;height:18px;flex-shrink:0;color:#0066ff;stroke-width:2;box-sizing:content-box;padding:10px;background:#eff6ff;border-radius:10px}
.cs-data-header .chev{display:none}
/* Collapsible accordion behavior — applied only at mobile breakpoint */
@media(max-width:1024px){
    .cs-collapsible-mobile .cs-data-header{cursor:pointer}
    .cs-collapsible-mobile .cs-data-header .chev{display:inline-flex;color:#9ca3af;transition:transform .22s ease}
    .cs-collapsible-mobile .cs-data-header.open .chev{transform:rotate(180deg)}
    .cs-collapsible-mobile .cs-data-body{display:none}
    .cs-collapsible-mobile .cs-data-header.open + .cs-data-body{display:block}
}
.cs-data-body{display:block;padding:20px 24px 24px}
.cs-data-grid-2col{display:grid;grid-template-columns:1fr 1fr;column-gap:28px;row-gap:0}
@media(max-width:768px){.cs-data-grid-2col{grid-template-columns:1fr;column-gap:0}}
/* Dane pojazdu reference 4-col layout. Tighter column-gap so the section
   reads as one structured card rather than a wide spread-out table. */
.cs-data-grid-4col{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));column-gap:28px;row-gap:0}
.cs-data-grid-4col .cs-data-col{display:flex;flex-direction:column;min-width:0}
@media(max-width:1024px){.cs-data-grid-4col{grid-template-columns:repeat(2,minmax(0,1fr));column-gap:24px}}
@media(max-width:600px){.cs-data-grid-4col{grid-template-columns:1fr;column-gap:0}}

/* Dane pojazdu — icon-led grid. Each cell pairs a small blue outline-style
   icon box with a label + value stack. Same .cs-data-section card chrome,
   but the data layout matches the reference's "icon-on-the-left" rhythm
   instead of a plain key/value table. */
.cs-dp-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px 24px}
.cs-dp-item{display:flex;align-items:center;gap:12px;min-width:0}
.cs-dp-ico{flex-shrink:0;width:38px;height:38px;border-radius:10px;background:#eff6ff;border:1px solid #dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-dp-ico svg,.cs-dp-ico i[data-lucide]{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8}
.cs-dp-text{display:flex;flex-direction:column;gap:2px;min-width:0}
.cs-dp-lbl{font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.4px;line-height:1.3}
.cs-dp-val{font-size:14px;color:#0a0a0a;font-weight:700;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
@media(max-width:1024px){.cs-dp-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 20px}}
@media(max-width:520px){.cs-dp-grid{grid-template-columns:1fr;gap:12px}}

/* TECH CONDITION LIST — fixed icon column, no text-icon collision */
.cs-tech-list{display:flex;flex-direction:column}
.cs-tech-row{display:grid;grid-template-columns:32px 1fr auto;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f2;font-size:14px}
.cs-tech-row:last-child{border-bottom:none}
.cs-tech-icon{width:32px;height:32px;border-radius:50%;background:#dcfce7;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
.cs-tech-icon i,.cs-tech-icon svg{width:18px;height:18px;color:#16a34a;stroke:#16a34a}
.cs-tech-name{font-weight:600;color:#1a1a1a;line-height:1.4;min-width:0;overflow-wrap:break-word}
.cs-tech-status{font-weight:600;color:#16a34a;font-size:13px;text-align:right;flex-shrink:0;padding-left:12px}
@media(max-width:480px){
    .cs-tech-row{grid-template-columns:28px 1fr auto;gap:10px;font-size:13.5px;padding:10px 0}
    .cs-tech-icon{width:28px;height:28px}
    .cs-tech-icon i,.cs-tech-icon svg{width:16px;height:16px}
}
.cs-data-2col{display:grid;grid-template-columns:1fr 1fr;gap:0 24px}
.cs-data-block{background:#fff;border:1px solid #eeeef0;border-radius:12px;padding:22px 22px 14px}
.cs-data-block-title{font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:14px;letter-spacing:-.2px;line-height:1.3;padding-bottom:12px;border-bottom:1px solid #eeeef0}
/* Row typography unified with .cs-info-3row-line — same separator, same
   font, same colour scheme. Lets every detail block read as part of the
   same component system. */
.cs-data-row{display:flex;justify-content:space-between;align-items:baseline;padding:9px 0;font-size:13px;line-height:1.4;border-bottom:1px solid #f5f5f7;gap:12px}
.cs-data-row:first-child{padding-top:0}
.cs-data-row:last-child{border-bottom:none;padding-bottom:0}
.cs-data-row .lbl{color:#6b7280;font-weight:500;line-height:1.4;flex-shrink:1;min-width:0}
.cs-data-row .val{font-weight:700;color:#0a0a0a;text-align:right;line-height:1.4;overflow:hidden;text-overflow:ellipsis;word-break:break-word;min-width:0;flex-shrink:0;max-width:60%}
@media(max-width:768px){.cs-data-2col{grid-template-columns:1fr}.cs-data-block+.cs-data-block{margin-top:12px}}

/* DATA COLUMNS (5-col horizontal layout) */
.cs-data-columns{display:grid;grid-template-columns:repeat(5,1fr);gap:0}
.cs-data-col{padding:20px 18px;border-right:1px solid #eeeef0}
.cs-data-col:last-child{border-right:none}
.cs-data-col-title{font-size:13.5px;font-weight:800;color:#1a1a1a;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #eeeef0;letter-spacing:-.1px}
.cs-data-col .cs-data-row{display:flex;flex-direction:column;gap:2px;padding:7px 0;border-bottom:none;font-size:12px}
.cs-data-col .cs-data-row .lbl{font-size:12px;color:#6b7280;font-weight:500;line-height:1.4}
.cs-data-col .cs-data-row .val{font-size:13.5px;font-weight:700;color:#1a1a1a;text-align:left;max-width:none;word-break:break-word;overflow-wrap:break-word;line-height:1.4}
@media(max-width:1024px){.cs-data-columns{grid-template-columns:repeat(3,1fr)}.cs-data-col{border-bottom:1px solid #eeeef0;padding:16px}.cs-data-col:nth-child(3){border-right:none}}
@media(max-width:768px){.cs-data-columns{grid-template-columns:repeat(2,1fr)}.cs-data-col:nth-child(2n){border-right:none}}
@media(max-width:500px){.cs-data-columns{grid-template-columns:1fr}.cs-data-col{border-right:none}}

/* SUB-ACCORDION inside cs-data-section (Dane pojazdu sub-sections) */
.cs-sub-hd{display:flex;align-items:center;justify-content:space-between;padding:13px 24px;user-select:none;border-top:1px solid #f0f0f2;font-size:13.5px;font-weight:700;color:#374151;background:#fafafa}
.cs-sub-chev{display:none}
.cs-sub-bd{display:block;padding:6px 24px 14px}
.cs-sub-bd .cs-data-row{font-size:13px;padding:9px 0;border-bottom:1px solid #f5f5f7}
.cs-sub-bd .cs-data-row:first-child{padding-top:4px}
.cs-sub-bd .cs-data-row:last-child{border-bottom:none;padding-bottom:0}
@media(max-width:768px){.cs-sub-hd{padding:12px 18px}.cs-sub-bd{padding:2px 18px 12px}}

/* FLOATING WIDGETS — handled by global layout */

@media(max-width:1024px){
    .cs-grid{grid-template-columns:minmax(0,1fr);gap:20px}
    .cs-grid > div{min-width:0;max-width:100%}
    .cs-sidebar{position:static}
    .cs-head{display:none}
    .cs-mob-head{display:block!important}
    .cs-sidebar-summary{display:none!important}
    .cs-mob-pills{display:flex!important}
    .cs-price-actions{display:none!important}
    .cs-mob-cta{display:grid!important}
    .cs-tire-table{grid-template-columns:180px repeat(4,1fr)}
    .cs-equipment-grid{grid-template-columns:1fr}
}
/* RELATED CARS */
/* ============ PODOBNE POJAZDY (section wrapper + nav + fuel badge) ============ */
/* Reuses the .cs-tech-engine-card system for the wrapper so the section sits
   inside the same content-container card as the rest of the redesign. */
/* ============ JAK WYGLĄDA ZAKUP — purchase process section ============
   Dark premium block placed BEFORE the CertiCheck section. Five
   step-cards in one row on desktop, a benefits strip below, and a
   primary CTA at the bottom. Real component, not a screenshot block. */
.cs-jwz{position:relative;background:linear-gradient(180deg,#0a1a3c 0%,#11264f 100%);color:#fff;border-radius:20px;padding:48px 32px 40px;margin:0 auto 16px;max-width:calc(1200px - 48px);width:100%;box-sizing:border-box;overflow:hidden;isolation:isolate}
.cs-jwz::before{content:'';position:absolute;top:-40%;right:-12%;width:60%;height:120%;background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(0,102,255,.28) 0%,rgba(0,102,255,.08) 40%,rgba(0,102,255,0) 70%);pointer-events:none;z-index:-1}
.cs-jwz::after{content:'';position:absolute;bottom:-30%;left:-8%;width:55%;height:90%;background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(78,163,255,.18) 0%,rgba(78,163,255,.06) 45%,rgba(78,163,255,0) 70%);pointer-events:none;z-index:-1}
.cs-jwz-head{text-align:center;margin-bottom:32px;position:relative;z-index:1}
.cs-jwz-kicker{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;color:#7eb3ff;text-transform:uppercase;letter-spacing:1.6px;margin-bottom:14px}
.cs-jwz-kicker::before,.cs-jwz-kicker::after{content:'';width:24px;height:1.5px;background:#7eb3ff;border-radius:1px}
.cs-jwz-head h2{font-size:30px;font-weight:900;color:#fff;letter-spacing:-.6px;line-height:1.15;margin:0 0 12px;max-width:680px;margin-left:auto;margin-right:auto}
.cs-jwz-head p{font-size:14px;color:rgba(255,255,255,.7);line-height:1.6;margin:0;max-width:600px;margin-left:auto;margin-right:auto}

.cs-jwz-steps{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:32px;position:relative;z-index:1}
.cs-jwz-step{background:#fff;border-radius:14px;padding:22px 18px 20px;display:flex;flex-direction:column;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.18);transition:transform .18s ease,box-shadow .18s ease;position:relative}
.cs-jwz-step:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(0,0,0,.25)}
.cs-jwz-step-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
.cs-jwz-step-ico{flex-shrink:0;width:42px;height:42px;border-radius:11px;background:#eff6ff;border:1px solid #dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-jwz-step-ico svg,.cs-jwz-step-ico i[data-lucide]{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8}
.cs-jwz-step-num{font-size:13px;font-weight:800;color:#0066ff;background:#eff6ff;border-radius:6px;padding:3px 9px;letter-spacing:.4px}
.cs-jwz-step h3{font-size:15px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:6px 0 2px;line-height:1.25}
.cs-jwz-step p{font-size:12.5px;color:#4b5563;line-height:1.55;margin:0}

.cs-jwz-benefits{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:28px;position:relative;z-index:1}
.cs-jwz-benefit{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(126,179,255,.18);border-radius:12px;padding:12px 16px;color:#fff;font-size:13px;font-weight:600}
.cs-jwz-benefit-ico{flex-shrink:0;width:30px;height:30px;border-radius:8px;background:rgba(78,163,255,.15);border:1px solid rgba(78,163,255,.3);color:#7eb3ff;display:flex;align-items:center;justify-content:center}
.cs-jwz-benefit-ico svg,.cs-jwz-benefit-ico i[data-lucide]{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2}

.cs-jwz-cta-wrap{text-align:center;position:relative;z-index:1}
.cs-jwz-cta{display:inline-flex;align-items:center;gap:9px;background:#0066ff;color:#fff;padding:14px 32px;border-radius:50px;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s ease;box-shadow:0 6px 20px rgba(0,102,255,.4)}
.cs-jwz-cta:hover{background:#0052cc;box-shadow:0 8px 26px rgba(0,102,255,.5);transform:translateY(-1px);color:#fff}
.cs-jwz-cta svg,.cs-jwz-cta i[data-lucide]{width:17px;height:17px;stroke:currentColor;fill:none;stroke-width:2.2}

@media(max-width:1024px){
    .cs-jwz{padding:40px 24px 36px}
    .cs-jwz-steps{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .cs-jwz-benefits{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
    .cs-jwz-head h2{font-size:26px}
}
@media(max-width:600px){
    .cs-jwz{padding:32px 20px 28px;border-radius:16px}
    .cs-jwz-steps{grid-template-columns:1fr;gap:12px}
    .cs-jwz-benefits{grid-template-columns:1fr;gap:8px}
    .cs-jwz-head h2{font-size:22px}
    .cs-jwz-head p{font-size:13.5px}
    .cs-jwz-cta{padding:12px 26px;font-size:14px}
}

/* ============ CERTICHECK SECTION (single car page) ============
   Light premium section after Jak wygląda zakup. Left content stack
   with kicker / heading / paragraph / buttons / info box; right side a
   2×2 grid of feature cards with circular icon medallions. */
.cs-cc{position:relative;background:#fafbff;border-radius:20px;padding:48px;margin:0 auto 16px;max-width:calc(1200px - 48px);width:100%;box-sizing:border-box;overflow:hidden;isolation:isolate;border:1px solid #eaf0fc}
.cs-cc::before{content:'';position:absolute;top:-30%;right:-10%;width:55%;height:120%;background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(0,102,255,.12) 0%,rgba(0,102,255,.04) 45%,rgba(0,102,255,0) 70%);pointer-events:none;z-index:-1}
.cs-cc-grid{display:grid;grid-template-columns:1fr 1.1fr;gap:48px;align-items:center;position:relative;z-index:1}
.cs-cc-kicker{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;color:#0066ff;text-transform:uppercase;letter-spacing:1.6px;margin-bottom:14px}
.cs-cc-kicker::before{content:'';width:22px;height:1.5px;background:#0066ff;border-radius:1px}
.cs-cc-left h2{font-size:36px;font-weight:900;color:#0a0a0a;letter-spacing:-.7px;line-height:1.1;margin:0 0 18px}
.cs-cc-left p{font-size:15px;color:#475569;line-height:1.7;margin:0 0 24px;max-width:480px}
.cs-cc-ctas{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px}
.cs-cc-cta-primary{display:inline-flex;align-items:center;gap:8px;background:#0066ff;color:#fff;padding:13px 24px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;transition:all .18s ease;box-shadow:0 4px 16px rgba(0,102,255,.3)}
.cs-cc-cta-primary:hover{background:#0052cc;box-shadow:0 6px 20px rgba(0,102,255,.42);transform:translateY(-1px);color:#fff}
.cs-cc-cta-secondary{display:inline-flex;align-items:center;gap:6px;color:#0066ff;padding:13px 18px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;border:1.5px solid transparent;transition:color .15s,border-color .15s}
.cs-cc-cta-secondary:hover{color:#0052cc;border-color:#dbeafe}
.cs-cc-ctas svg,.cs-cc-ctas i[data-lucide]{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}
.cs-cc-info{display:inline-flex;align-items:center;gap:9px;background:#fff;border:1px solid #dbeafe;border-radius:10px;padding:10px 14px;font-size:12.5px;color:#475569;line-height:1.5}
.cs-cc-info-ico{flex-shrink:0;width:24px;height:24px;border-radius:6px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-cc-info-ico svg,.cs-cc-info-ico i[data-lucide]{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2}

.cs-cc-cards{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.cs-cc-card{position:relative;background:#fff;border:1px solid #eaf0fc;border-radius:16px;padding:22px 20px 24px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 6px 20px rgba(15,32,80,.05);transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
.cs-cc-card:hover{transform:translateY(-2px);box-shadow:0 4px 8px rgba(0,0,0,.04),0 12px 32px rgba(15,32,80,.1);border-color:#cfdcf5}
.cs-cc-card-ico{width:44px;height:44px;border-radius:50%;background:#eff6ff;border:1px solid #dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center;margin-bottom:14px}
.cs-cc-card-ico svg,.cs-cc-card-ico i[data-lucide]{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8}
.cs-cc-card h3{font-size:15px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:0 0 6px;line-height:1.25}
.cs-cc-card p{font-size:13px;color:#4b5563;line-height:1.55;margin:0}
.cs-cc-card-arrow{position:absolute;top:20px;right:20px;width:26px;height:26px;border-radius:50%;background:#fff;border:1px solid #dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center;opacity:.55;transition:opacity .18s ease,transform .18s ease}
.cs-cc-card-arrow svg,.cs-cc-card-arrow i[data-lucide]{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.4}
.cs-cc-card:hover .cs-cc-card-arrow{opacity:1;transform:translateX(2px) translateY(-2px)}

@media(max-width:1024px){
    .cs-cc{padding:36px 32px}
    .cs-cc-grid{grid-template-columns:1fr;gap:32px}
    .cs-cc-left h2{font-size:30px}
    .cs-cc-cards{grid-template-columns:1fr 1fr;gap:12px}
}
@media(max-width:600px){
    .cs-cc{padding:28px 20px;border-radius:16px}
    .cs-cc-left h2{font-size:24px}
    .cs-cc-left p{font-size:14px}
    .cs-cc-cards{grid-template-columns:1fr}
    .cs-cc-ctas{flex-direction:column;align-items:stretch}
    .cs-cc-cta-primary,.cs-cc-cta-secondary{justify-content:center}
}

.cs-related-section{background:#fff;border:1px solid #eeeef0;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);padding:24px 26px;max-width:calc(1200px - 48px);margin:0 auto 16px;width:100%;box-sizing:border-box}
.cs-related-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:18px;flex-wrap:wrap}
.cs-related-head-left{display:flex;align-items:flex-start;gap:14px;flex:1;min-width:240px}
.cs-related-head-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-related-head-ico svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.cs-related-head-title{font-size:17px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin:0 0 6px;line-height:1.25}
.cs-related-head-sub{font-size:12.5px;color:#6b7280;line-height:1.55;margin:0;max-width:520px}
.cs-related-controls{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-left:auto}
.cs-related-all{font-size:13px;font-weight:700;color:#0066ff;display:inline-flex;align-items:center;gap:4px;text-decoration:none}
.cs-related-all:hover{color:#0052cc}
.cs-related-all svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
.cs-related-nav{display:flex;gap:6px}
.cs-related-nav button{width:34px;height:34px;border-radius:50%;border:1.5px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:border-color .15s,color .15s;color:#374151;padding:0}
.cs-related-nav button:hover{border-color:#0066ff;color:#0066ff}
.cs-related-nav button:disabled{opacity:.4;cursor:not-allowed;border-color:#e5e7eb;color:#374151}
.cs-related-nav button svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}

/* Fuel badge on each vcard (top-left). Heart stays on the right via existing
   .lcard-fav class. */
.vcard-fuel-badge{position:absolute;top:12px;left:12px;background:rgba(255,255,255,.96);color:#0a0a0a;font-size:11px;font-weight:700;padding:4px 10px;border-radius:50px;letter-spacing:.3px;display:inline-flex;align-items:center;gap:5px;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);box-shadow:0 1px 4px rgba(0,0,0,.1);z-index:2}
.vcard-fuel-badge svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2}

/* Legal disclaimer bar — sits below the related cars section. */
.cs-legal-bar{display:flex;align-items:flex-start;gap:10px;background:#eff6ff;border:1px solid #dbeafe;border-radius:12px;padding:12px 16px;max-width:calc(1200px - 48px);margin:0 auto 28px;font-size:12px;line-height:1.55;color:#475569}
.cs-legal-bar-ico{flex-shrink:0;width:28px;height:28px;border-radius:8px;background:#dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center;margin-top:1px}
.cs-legal-bar-ico svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2}
.cs-legal-bar p{margin:0}
.cs-legal-bar strong{color:#0a0a0a;font-weight:700}

@media(max-width:1024px){
    .cs-related-section{padding:20px}
    .cs-related-head-title{font-size:16px}
}
@media(max-width:768px){
    .cs-related-section{padding:18px 20px;border-radius:14px;border-color:#e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,.06);width:auto;max-width:none}
    .cs-related-controls{width:100%;margin-left:0;justify-content:space-between}
    .vcard-fuel-badge{font-size:10px;padding:3px 8px;top:10px;left:10px}
}

.cs-related-grid{display:flex;gap:20px;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding-bottom:4px}
.cs-related-grid::-webkit-scrollbar{display:none}
.cs-related-grid>*{flex:0 0 calc(33.333% - 14px);scroll-snap-align:start;min-width:260px}

@media(max-width:768px){
    /* Mobile gutter: every section sits at the same left/right edge as
       the vehicle summary card. The HTML structure puts most sections as
       direct children of .cs-wrap (not .container — close-tag balance
       drifted long ago), so we align them with auto-margins + a max-width
       that matches the container's content area. The summary card lives
       inside .container so this rule keeps it untouched.

       Result: at any mobile width, every section card has identical
       left/right gutters. No section touches the viewport edge. */
    .cs-wrap .container{padding-left:24px;padding-right:24px}
    .cs-wrap > *:not(.container){
        max-width:calc(100% - 48px);
        width:auto;
        margin-left:auto;
        margin-right:auto;
        box-sizing:border-box;
    }
    .cs-pt-row,
    .cs-legal-bar,
    .cs-data-section{width:auto;max-width:none}
    .cs-sections-2col{padding:0;margin:0}
    .cs-nav-bar{flex-direction:row;flex-wrap:wrap;gap:8px;align-items:center;padding:10px 0}
    .cs-nav-bar-left{flex:0 0 auto}
    .cs-nav-bar-left .cs-nav-btn{width:auto}
    .cs-nav-bar-right{margin-left:auto;display:flex;gap:6px}
    .cs-nav-btn{padding:7px 10px;font-size:11.5px;gap:4px}
    .cs-nav-btn svg{width:12px;height:12px}
    .cs-head{flex-direction:column;gap:6px}
    .cs-head h1{font-size:22px;letter-spacing:-.4px}
    .cs-gallery{border-radius:14px}
    .cs-gallery-tabs{gap:5px;padding:10px 0 8px}
    .cs-gallery-tab{font-size:11px;gap:4px;padding:5px 10px}
    .cs-gallery-tab svg,.cs-gallery-tab i{width:12px;height:12px}
    .cs-gallery-thumbs{padding:8px}
    .cs-thumb{width:72px;height:48px}
    .cs-thumb-360{width:72px;height:48px;font-size:9px}
    .cs-thumb-360 svg{width:16px;height:16px}
    .cs-gallery-nav{width:34px;height:34px}
    .cs-gallery-nav svg{width:16px;height:16px}
    .cs-gallery-nav.prev{left:8px}
    .cs-gallery-nav.next{right:8px}
    /* Sidebar */
    .cs-sidebar-card{border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
    .cs-price-section{padding:20px 20px 14px}
    .cs-price-value{font-size:30px}
    .cs-price-actions{padding:14px 16px 16px}
    .cs-price-actions .btn{padding:12px 16px;font-size:14px}
    .cs-sidebar-summary{padding:0 16px}
    .cs-sidebar-summary-row{font-size:12.5px;padding:10px 0}
    .cs-calc-trigger{padding:12px 14px;font-size:12px}
    /* DATA SECTIONS — rounded card. Mobile keeps the same border/shadow
       language as desktop so the visual system stays coherent. */
    .cs-data-section{border-radius:16px;border:1px solid #eeeef0;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 12px rgba(0,0,0,.04);overflow:hidden;margin-bottom:16px}
    .cs-data-header{padding:18px 20px;text-align:left;border-bottom:1px solid #f0f0f2}
    .cs-data-header h2{font-size:16px;font-weight:800;letter-spacing:-.2px;text-align:left;gap:12px}
    .cs-data-body{padding:14px 20px 18px}
    /* Row pattern matches cs-info-3row-line so mobile rows feel like the
       same component family as the 3-card summary row above. */
    .cs-data-row{font-size:13.5px;padding:11px 0;border-bottom:1px solid #f5f5f7;gap:14px;align-items:baseline;line-height:1.45}
    .cs-data-row:last-child{border-bottom:none;padding-bottom:0}
    .cs-data-row .lbl{font-weight:500;color:#6b7280;min-width:0;flex:1 1 auto;line-height:1.45;word-break:normal;overflow-wrap:break-word}
    .cs-data-row .val{font-weight:700;color:#0a0a0a;font-size:13.5px;max-width:58%;text-align:right;line-height:1.45;word-break:break-word;overflow-wrap:break-word;white-space:normal;overflow:visible;text-overflow:clip;flex-shrink:0}
    .cs-sections-2col{gap:22px}
    /* STAN TECHNICZNY — separate blocks stacked */
    .cs-data-2col{grid-template-columns:1fr!important;gap:28px}
    .cs-data-block{padding:0!important;border:none!important;background:transparent!important;box-shadow:none!important}
    .cs-data-block-title{font-size:18px!important;font-weight:900!important;margin-bottom:4px!important;padding-bottom:0!important;border-bottom:none!important}
    .cs-data-block .cs-data-row{padding:16px 0;border-bottom:1px solid #f0f0f2;font-size:14px}
    .cs-data-block .cs-data-row .lbl{font-size:14px;font-weight:700;gap:10px}
    .cs-data-block .cs-data-row .lbl i[data-lucide="check-circle"]{width:24px;height:24px;color:#16a34a;background:#dcfce7;border-radius:50%;padding:3px;flex-shrink:0}
    .cs-data-block .cs-data-row .val{font-size:14px;font-weight:700;color:#1a1a1a}
    /* DAMAGE — mobile (compact, photo-first) */
    .cs-sections-2col{padding:0;gap:12px}
    .cs-damage-subtitle{font-size:12.5px;margin-bottom:14px}
    .cs-damages-tabs{gap:6px;padding-bottom:4px;margin-bottom:14px}
    .cs-damage-grid{grid-template-columns:1fr;min-height:auto;border:none;border-radius:0;background:transparent;row-gap:14px;gap:0}
    /* Compact diagram on mobile — square, height-capped, contain so the silhouette never crops. */
    .cs-damage-diagram{border-radius:14px;border:1px solid #eeeef0;background:#f7f8fa;width:100%;max-width:300px;margin-left:auto;margin-right:auto}
    .cs-damage-diagram-canvas{aspect-ratio:1/1;max-height:260px}
    .cs-damage-diagram-canvas img.cs-damage-diagram-img{padding:6px}
    .cs-damage-diagram-legend{padding:10px 14px 12px}
    .cs-damage-diagram-legend-item{font-size:11px}
    .cs-damage-marker{width:40px;height:40px}
    .cs-damage-marker-dot{width:22px;height:22px;border-width:2px;font-size:10px}
    .cs-damage-item h3{font-size:14px;margin-bottom:8px}
    .cs-damage-item p{font-size:12.5px}
    .cs-damage-tags{margin-bottom:10px}
    .cs-damage-tags span{font-size:10px;padding:4px 9px}
    .cs-damage-tab{font-size:12px;padding:7px 12px}
    .cs-damage-tab-count{min-width:20px;height:18px;font-size:10.5px}
    .cs-dmg-overlay{left:10px;bottom:10px;padding:8px 12px;max-width:70%}
    .cs-dmg-overlay-type{font-size:9px}
    .cs-dmg-overlay-area{font-size:12.5px}
    .cs-dmg-thumb-num{width:16px;height:16px;font-size:9px;top:4px;left:4px}
    .cs-dmg-all-cta{font-size:12.5px;margin-top:10px}
    .cs-dmg-gallery{border-radius:14px;background:#fff;border:1px solid #eeeef0;padding:8px}
    .cs-dmg-gallery-stage{border-radius:10px;aspect-ratio:4/3}
    .cs-dmg-gallery-slide img{border-radius:10px}
    .cs-dmg-gallery-thumbs-wrap{margin-top:8px}
    .cs-dmg-gallery-thumb{width:64px;height:48px;border-width:2px;border-radius:6px}
    .cs-dmg-gallery-arrow{width:36px;height:36px}
    .cs-dmg-gallery-label{font-size:12px;padding:6px 12px;bottom:10px;left:10px}
    .cs-dmg-gallery-meta{bottom:10px;right:10px}
    .cs-dmg-gallery-counter{font-size:11px;padding:5px 10px}
    .cs-dmg-gallery-fs{width:32px;height:32px}
    .cs-status-grid{grid-template-columns:1fr}
    .cs-svc-grid{grid-template-columns:1fr}
    /* TIRES — 2×2 card grid */
    .cs-tire-set-title{font-size:18px;font-weight:900;margin-bottom:12px}
    .cs-tire-table{display:grid!important;grid-template-columns:1fr 1fr!important;gap:10px;border:none!important;border-radius:0!important;overflow:visible!important}
    .cs-tire-th{display:none!important}
    .cs-tire-info{display:none!important}
    .cs-tire-col{border:1.5px solid #e5e7eb!important;border-radius:14px!important;flex-direction:column!important;align-items:center!important;text-align:center;padding:18px 12px!important;background:#fff}
    .cs-tire-col-head{border:none!important;padding:0!important;width:auto!important;background:transparent!important;flex-direction:column;align-items:center;gap:4px}
    .cs-tire-icon{width:48px!important;height:48px!important;margin:0 auto 6px}
    .cs-tire-pos-name{font-size:13px!important;font-weight:600;color:#6b7280}
    .cs-tire-data-row{border:none!important;padding:2px 0!important;font-size:18px!important;font-weight:800!important;text-align:center}
    .cs-tire-data-row:first-of-type{color:#059669}
    .cs-tire-data-row.ok-txt,.cs-tire-data-row.warn-txt{font-size:0!important;line-height:0;height:0;padding:0!important;overflow:hidden}
    /* EQUIPMENT */
    .cs-equipment-grid{grid-template-columns:1fr}
    .cs-equipment-item{padding:10px 0;font-size:12.5px}
    .cs-feat-eq{flex-direction:column}
    /* FUEL */
    .cs-fuel-grid{grid-template-columns:1fr 1fr}
    /* 360° */
    .cs-pano360-embed{aspect-ratio:4/3;border-radius:14px}
    .cs-pano360-grid{grid-template-columns:1fr!important}
    /* PAINT — clean table (Element | Wynik) */
    .cs-paint-grid{grid-template-columns:1fr!important;gap:0;border-radius:14px;border:1px solid #eeeef0}
    .cs-paint-item{padding:16px 20px!important;display:flex!important;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f0f2;border-right:none!important;background:#fff!important;flex-direction:row!important}
    .cs-paint-item:last-child{border-bottom:none}
    .cs-paint-label{font-size:15px!important;font-weight:700!important;color:#1a1a1a!important;text-transform:none!important;letter-spacing:0!important;margin-bottom:0!important;order:0;white-space:nowrap}
    .cs-paint-value{font-size:16px!important;font-weight:800!important;text-align:right;letter-spacing:0!important}
    .cs-paint-dot{display:none!important}
    /* ACCORDION */
    .cs-sub-hd{padding:14px 20px}
    .cs-sub-bd{padding:2px 20px 14px}
    .cs-card{padding:20px}
}
@media(max-width:500px){
    /* Mobile gutter tightens to 20 px on small phones. Keep the
       cs-wrap > * direct-child sections aligned with the container. */
    .cs-wrap .container{padding-left:20px;padding-right:20px}
    .cs-wrap > *:not(.container){max-width:calc(100% - 40px)}
    .cs-sections-2col{padding:0}
    .cs-head h1{font-size:19px}
    .cs-gallery{border-radius:10px}
    .cs-sidebar-card{border-radius:14px}
    .cs-data-section{border-radius:14px;margin-bottom:14px}
    .cs-data-row{padding:10px 0;gap:12px}
    .cs-data-row .val{max-width:55%}
    .cs-price-value{font-size:26px}
    .cs-sections-2col{padding:0}
    .cs-nav-btn{padding:6px 8px;font-size:11px}
    .cs-damage-diagram{max-height:260px;max-width:300px}
    .cs-damage-marker{width:42px;height:42px}
    .cs-damage-marker-dot{width:20px;height:20px;border-width:2px}
    .cs-damage-marker-dot svg{width:10px;height:10px}
    .cs-paint-item{padding:14px 16px!important}
    .cs-paint-value{font-size:15px!important}
    .cs-paint-label{font-size:13px!important}
    .cs-tire-icon{width:40px!important;height:40px!important}
    .cs-tire-col{padding:14px 10px!important}
    .cs-tire-data-row:first-of-type{font-size:16px!important}
    .cs-data-body{padding:12px 18px 16px}
    .cs-data-header{padding:16px 18px}
    .cs-gallery-tabs{gap:4px;padding:8px 0 6px}
    .cs-gallery-tab{font-size:10px;padding:4px 8px;gap:3px}
    .cs-gallery-tab svg,.cs-gallery-tab i{width:11px;height:11px}
    .cs-gallery-nav{width:30px;height:30px}
    .cs-gallery-nav svg{width:14px;height:14px}
    .cs-gallery-nav.prev{left:6px}
    .cs-gallery-nav.next{right:6px}
}
/* INQUIRY MODAL */
.cs-inquiry-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.cs-inquiry-overlay.open{display:flex}
.cs-inquiry-panel{background:#fff;border-radius:16px;width:100%;max-width:440px;max-height:90vh;overflow-y:auto;padding:32px;position:relative;animation:csSlideUp .25s ease}
@keyframes csSlideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.cs-inquiry-close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:24px;color:#9ca3af;cursor:pointer;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;transition:all .15s}
.cs-inquiry-close:hover{background:#f5f5f5;color:#1a1a1a}
.cs-inquiry-panel h3{font-size:20px;font-weight:800;color:#1a1a1a;margin:0 0 4px}
.cs-inquiry-car{font-size:13px;color:#6b7280;margin:0 0 20px;padding-bottom:16px;border-bottom:1px solid #f0f0f2}
.cs-inquiry-field{margin-bottom:14px}
.cs-inquiry-field label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px}
.cs-inquiry-field input,.cs-inquiry-field textarea{width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#1a1a1a;outline:none;transition:border-color .15s;font-family:inherit;box-sizing:border-box}
.cs-inquiry-field input:focus,.cs-inquiry-field textarea:focus{border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.1)}
.cs-inquiry-submit{width:100%;padding:14px;background:#0066ff;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .15s;margin-top:8px}
.cs-inquiry-submit:hover{background:#0052cc}
.cs-inquiry-submit:disabled{opacity:.6;cursor:not-allowed}
.cs-inquiry-legal{font-size:10px;color:#9ca3af;text-align:center;margin-top:12px;line-height:1.4}
.cs-inquiry-consent{display:flex;align-items:flex-start;gap:10px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 13px;margin:6px 0 4px;transition:border-color .15s,background .15s}
.cs-inquiry-consent:focus-within{border-color:#0066ff;background:#f5f8ff}
.cs-inquiry-consent.has-error{border-color:#ef4444;background:#fef2f2}
.cs-inquiry-consent input[type=checkbox]{flex-shrink:0;width:18px;height:18px;margin:1px 0 0;accent-color:#0066ff;cursor:pointer}
.cs-inquiry-consent label{font-size:12px;line-height:1.45;color:#374151;cursor:pointer;font-weight:500}
.cs-inquiry-consent label .req{color:#ef4444;margin-left:2px}
.cs-inquiry-success{text-align:center;padding:20px 0}
.cs-inquiry-success h4{font-size:20px;font-weight:800;color:#1a1a1a;margin:16px 0 6px}
.cs-inquiry-success p{font-size:14px;color:#6b7280;margin:0 0 20px}
.cs-inquiry-success button{padding:10px 28px;background:#f5f5f7;border:none;border-radius:10px;font-size:14px;font-weight:600;color:#374151;cursor:pointer}
.cs-inquiry-success button:hover{background:#e8e8ea}
/* Inquiry modal — tighten on mobile so it doesn't fill the whole screen */
@media(max-width:768px){
    .cs-inquiry-overlay{padding:18px;align-items:flex-end}
    .cs-inquiry-panel{max-width:none;padding:22px 20px;border-radius:18px;max-height:88vh;animation:csSlideUp .22s ease}
    .cs-inquiry-panel h3{font-size:18px}
    .cs-inquiry-car{font-size:12.5px;margin:0 0 16px;padding-bottom:12px}
    .cs-inquiry-field{margin-bottom:12px}
    .cs-inquiry-field label{font-size:11.5px;margin-bottom:4px}
    .cs-inquiry-field input,.cs-inquiry-field textarea{padding:10px 12px;font-size:14px;border-radius:9px}
    .cs-inquiry-consent{padding:10px 12px;gap:9px}
    .cs-inquiry-consent label{font-size:11.5px;line-height:1.4}
    .cs-inquiry-submit{padding:13px;font-size:14px;border-radius:11px}
    .cs-inquiry-legal{font-size:9.5px;margin-top:10px}
    .cs-inquiry-close{top:10px;right:10px;width:34px;height:34px;font-size:22px}
}
@media(max-width:500px){
    .cs-inquiry-overlay{padding:14px}
    .cs-inquiry-panel{padding:20px 16px}
}
/* Hide the floating phone widget on this single-car page only (mobile)
   so it doesn't double up with the sticky Zadzwoń / Napisz bar below. */
@media(max-width:1024px){
    body .float-call{display:none!important}
}

/* STICKY MOBILE CTA BAR */
.cs-sticky-cta{display:none;position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e5e7eb;padding:10px 14px calc(10px + env(safe-area-inset-bottom,0px));z-index:999;box-shadow:0 -4px 20px rgba(0,0,0,.08);gap:8px;grid-template-columns:1fr 1fr;transform:translateY(100%);transition:transform .25s ease}
.cs-sticky-cta.visible{transform:translateY(0)}
.cs-sticky-cta a,.cs-sticky-cta button{display:flex;align-items:center;justify-content:center;gap:6px;padding:13px 16px;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;border:none}
.cs-sticky-cta .cs-sticky-call{background:#0066ff;color:#fff;box-shadow:0 2px 10px rgba(0,102,255,.3)}
.cs-sticky-cta .cs-sticky-msg{background:#f3f4f6;color:#1a1a1a;border:none}

/* MOBILE TOP BAR (Wróć / Udostępnij) — above gallery on mobile only */
.cs-mob-topbar{display:none}
.cs-mob-topbar-btn{display:inline-flex;align-items:center;gap:6px;min-height:44px;padding:8px 12px;border-radius:10px;background:transparent;border:none;color:#0a0a0a;font-size:13px;font-weight:600;font-family:inherit;text-decoration:none;cursor:pointer;-webkit-tap-highlight-color:transparent}
.cs-mob-topbar-btn:active{background:#f3f4f6}
.cs-mob-topbar-btn svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.2;flex-shrink:0}
@media(max-width:1024px){
    .cs-mob-topbar{display:flex;align-items:center;justify-content:space-between;padding:4px 0 6px}
    /* Mobile: hide the wider COS-style nav-bar (Wróć/Udostępnij replaces its left, prev/next not needed on phone) */
    .cs-nav-bar{display:none!important}
    /* Mobile: reorder gallery — image first, tabs below */
    .cs-grid > div:first-child{display:flex;flex-direction:column}
    .cs-grid > div:first-child > .cs-mob-topbar{order:0}
    .cs-grid > div:first-child > .cs-gallery{order:1}
    .cs-grid > div:first-child > .cs-gallery-tabs-wrap{order:2;margin-top:8px}
    /* Connector-line fix: drop the bottom hairline under price (made buttons below look attached) */
    .cs-price-section{border-bottom:none}
}
/* Share toast (fallback when navigator.share unavailable) */
#csShareToast{position:fixed;left:50%;bottom:84px;transform:translateX(-50%) translateY(8px);background:#1a1a1a;color:#fff;padding:10px 18px;border-radius:50px;font-size:13.5px;font-weight:600;z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s,transform .22s;box-shadow:0 6px 20px rgba(0,0,0,.25)}
#csShareToast.visible{opacity:1;transform:translateX(-50%) translateY(0)}

.cs-sticky-cta svg{width:16px;height:16px;flex-shrink:0}
@media(max-width:1024px){.cs-sticky-cta{display:grid}}

/* FULLSCREEN LIGHTBOX */
.cs-lightbox{display:none;position:fixed;inset:0;background:rgba(0,0,0,.95);z-index:10000;flex-direction:column;align-items:center;justify-content:center}
.cs-lightbox.open{display:flex}
.cs-lightbox-close{position:absolute;top:16px;right:16px;width:44px;height:44px;background:rgba(255,255,255,.1);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:3;transition:background .15s}
.cs-lightbox-close:hover{background:rgba(255,255,255,.2)}
.cs-lightbox-close svg{width:24px;height:24px;stroke:#fff;fill:none;stroke-width:2}
.cs-lightbox-img{max-width:90vw;max-height:80vh;object-fit:contain;border-radius:8px;user-select:none;-webkit-user-drag:none}
.cs-lightbox-counter{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.7);font-size:14px;font-weight:600}
.cs-lightbox-nav{position:absolute;top:50%;transform:translateY(-50%);width:48px;height:48px;background:rgba(255,255,255,.1);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:3;transition:background .15s}
.cs-lightbox-nav:hover{background:rgba(255,255,255,.2)}
.cs-lightbox-nav svg{width:24px;height:24px;stroke:#fff;fill:none;stroke-width:2.5}
.cs-lightbox-prev{left:16px}
.cs-lightbox-next{right:16px}

/* CERTICHECK TRUST BANNER */
.cs-trust-banner{background:linear-gradient(135deg,#f0fdf4 0%,#ecfdf5 100%);border-bottom:1px solid #bbf7d0;padding:16px 22px;display:flex;align-items:center;gap:12px}
.cs-trust-badge{width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cs-trust-badge svg{width:22px;height:22px;stroke:#16a34a;fill:none;stroke-width:2.2}
.cs-trust-text{flex:1;min-width:0}
.cs-trust-text strong{display:block;font-size:13.5px;font-weight:800;color:#166534;letter-spacing:-.1px}
.cs-trust-text small{display:block;font-size:11px;color:#4ade80;font-weight:500;margin-top:2px}
.cs-trust-dl{font-size:11px;font-weight:700;color:#16a34a;background:#fff;border:1px solid #bbf7d0;border-radius:8px;padding:6px 12px;text-decoration:none;white-space:nowrap;transition:all .15s;display:inline-flex;align-items:center;gap:4px;flex-shrink:0}
.cs-trust-dl:hover{background:#dcfce7;color:#166534}
.cs-trust-dl svg{width:12px;height:12px}
@endsection

@section('content')
<div class="cs-wrap">
<div class="container" style="padding-top:16px">
    <!-- NAVIGATION BAR (COS style) -->
    <div class="cs-nav-bar">
        <div class="cs-nav-bar-left">
            <a href="{{ route('catalog') }}" class="cs-nav-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Powrót do ofert
            </a>
        </div>
        <div class="cs-nav-bar-right">
            <button type="button" class="cs-nav-btn" onclick="csShare()" aria-label="Udostępnij ofertę">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Udostępnij
            </button>
            @if($prevCar)
                <a href="{{ url('/samochody/'.$prevCar->slug) }}" class="cs-nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Poprzedni pojazd
                </a>
            @else
                <span class="cs-nav-btn disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Poprzedni pojazd
                </span>
            @endif
            @if($nextCar)
                <a href="{{ url('/samochody/'.$nextCar->slug) }}" class="cs-nav-btn">
                    Następny pojazd
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            @else
                <span class="cs-nav-btn disabled">
                    Następny pojazd
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </span>
            @endif
        </div>
    </div>

    <div class="cs-head">
        <div style="min-width:0">
            <h1>{{ $car->title }}</h1>
        </div>
    </div>



    @php
        $allEquipmentItems = [];
        if($car->equipment) {
            foreach($car->equipment as $cat => $items) {
                if(is_array($items)) {
                    foreach($items as $item) {
                        $allEquipmentItems[] = $item;
                    }
                }
            }
        }
        $featuredItems = array_slice($allEquipmentItems, 0, 6);
        $totalCount = count($allEquipmentItems);
    @endphp



    @php
        $galleryList = $car->galleryImages->count() ? $car->galleryImages : ($car->primaryImage ? collect([$car->primaryImage]) : collect());
        $damageImgList = $car->damageImages ?? collect();
        $allMediaCount = $galleryList->count() + $damageImgList->count();
        $hasEngineVideo = $car->engine_video_url || $car->engine_video_path;
    @endphp

    <div class="cs-grid">
        <div>
        <!-- MOBILE-ONLY TOP BAR: Wróć + Udostępnij (hidden on desktop via CSS) -->
        <div class="cs-mob-topbar">
            <a href="{{ route('catalog') }}" class="cs-mob-topbar-btn" aria-label="Powrót do ofert">
                <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                Wróć
            </a>
            <button type="button" class="cs-mob-topbar-btn" onclick="csShare()" aria-label="Udostępnij ofertę">
                <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Udostępnij
            </button>
        </div>
        <!-- GALLERY MEDIA TABS (pill-style badges) -->
        <div class="cs-gallery-tabs-wrap" role="tablist">
        <div class="cs-gallery-tabs">
            <button type="button" class="cs-gallery-tab active" data-gallery-filter="all" onclick="csFilterGallery(this,'all')" role="tab" aria-selected="true">
                <x-icon name="image" size="14" :strokeWidth="1.8"/>
                Wszystkie zdjęcia
            </button>
            <button type="button" class="cs-gallery-tab {{ $car->exteriorPano360Image ? '' : 'disabled' }}" data-gallery-filter="pano360ext" onclick="csFilterGallery(this,'pano360ext')" role="tab" aria-selected="false">
                <x-icon name="rotate-3d" size="14" :strokeWidth="1.8"/>
                360° z zewnątrz
            </button>
            <button type="button" class="cs-gallery-tab {{ $car->pano360Image ? '' : 'disabled' }}" data-gallery-filter="pano360" onclick="csFilterGallery(this,'pano360')" role="tab" aria-selected="false">
                <x-icon name="rotate-3d" size="14" :strokeWidth="1.8"/>
                360° wnętrza
            </button>
            <button type="button" class="cs-gallery-tab {{ $damageImgList->count() ? '' : 'disabled' }}" data-gallery-filter="damage" onclick="csFilterGallery(this,'damage')" role="tab" aria-selected="false">
                <x-icon name="search" size="14" :strokeWidth="1.8"/>
                Zdjęcia stanu pojazdu
            </button>
            <button type="button" class="cs-gallery-tab" data-gallery-filter="documents" onclick="csFilterGallery(this,'documents')" role="tab" aria-selected="false">
                <x-icon name="file-text" size="14" :strokeWidth="1.8"/>
                Dokumenty
            </button>
            <button type="button" class="cs-gallery-tab {{ $hasEngineVideo ? '' : 'disabled' }}" data-gallery-filter="video" onclick="csFilterGallery(this,'video')" role="tab" aria-selected="false">
                <x-icon name="play" size="14" :strokeWidth="1.8"/>
                Wideo pracy silnika
            </button>
            <button type="button" class="cs-gallery-tab" data-gallery-filter="paint" onclick="csFilterGallery(this,'paint')" role="tab" aria-selected="false">
                <x-icon name="paintbrush" size="14" :strokeWidth="1.8"/>
                Pomiary lakieru
            </button>
        </div>
        </div>{{-- /cs-gallery-tabs-wrap --}}
        <div class="cs-gallery">
            <div class="cs-gallery-stage">
                <button type="button" class="cs-gallery-nav prev" onclick="csGalleryPrev()" aria-label="Poprzednie zdjęcie"><x-icon name="chevron-left" size="24"/></button>
                <button type="button" class="cs-gallery-nav next" onclick="csGalleryNext()" aria-label="Następne zdjęcie"><x-icon name="chevron-right" size="24"/></button>
                <div class="cs-gallery-main active" id="csGalleryStandard">
                    @if($galleryList->count())
                        <img src="{{ $galleryList->first()->url }}" id="csMainImg" alt="{{ $galleryList->first()->alt }}" style="cursor:zoom-in" onclick="openCarGallery(0)" fetchpriority="high" decoding="async">
                        <div class="cs-gallery-counter" style="cursor:zoom-in" onclick="openCarGallery(parseInt(document.getElementById('csImgCounter').textContent)-1)"><span id="csImgCounter">1</span> / <span id="csImgTotal">{{ $galleryList->count() }}</span></div>
                    @else
                        <div class="empty"><i data-lucide="car" aria-hidden="true"></i></div>
                    @endif
                    @if($car->available_now || $car->home_delivery || $car->has_gethelp)
                    <div style="position:absolute;bottom:12px;left:12px;display:flex;flex-wrap:wrap;gap:5px;z-index:4">
                        @if($car->available_now)<span style="background:rgba(16,185,129,.92);color:#fff;padding:5px 10px;border-radius:50px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(6px)"><x-icon name="zap" size="12" :strokeWidth="2.5"/>Od ręki</span>@endif
                        @if($car->home_delivery)<span style="background:rgba(99,102,241,.92);color:#fff;padding:5px 10px;border-radius:50px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(6px)"><x-icon name="truck" size="12" :strokeWidth="2.5"/>Dostawa</span>@endif
                        @if($car->has_gethelp)<span style="background:rgba(217,119,6,.92);color:#fff;padding:5px 10px;border-radius:50px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(6px)"><x-icon name="shield-check" size="12" :strokeWidth="2.5"/>GetHelp {{ $car->gethelp_package ?? 'Classic' }} w cenie</span>@endif
                    </div>
                    @endif
                </div>

                {{-- ===== 360° PANORAMA VIEWER (interior) ===== --}}
                @if($car->pano360Image)
                <div class="cs-gallery-main cs-pano360" id="csPano360" style="background:#000">
                    <div id="csPanoramaContainer" style="width:100%;height:100%;background:#000" data-pano-src="{{ route('panorama.stream', $car->pano360Image) }}"></div>
                    <div style="position:absolute;top:14px;left:50%;transform:translateX(-50%);background:rgba(10,10,10,.78);color:#fff;font-size:12px;padding:7px 14px;border-radius:50px;display:flex;align-items:center;gap:8px;backdrop-filter:blur(6px);font-weight:600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>
                        Przeciągnij, aby rozejrzeć się we wnętrzu
                    </div>
                </div>
                @endif

                {{-- ===== 360° PANORAMA VIEWER (exterior) ===== --}}
                @if($car->exteriorPano360Image)
                <div class="cs-gallery-main cs-pano360ext" id="csPano360ext" style="background:#000">
                    <div id="csPanoramaExtContainer" style="width:100%;height:100%;background:#000" data-pano-src="{{ route('panorama.stream', $car->exteriorPano360Image) }}"></div>
                    <div style="position:absolute;top:14px;left:50%;transform:translateX(-50%);background:rgba(10,10,10,.78);color:#fff;font-size:12px;padding:7px 14px;border-radius:50px;display:flex;align-items:center;gap:8px;backdrop-filter:blur(6px);font-weight:600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Przeciągnij, aby obejrzeć auto z zewnątrz
                    </div>
                </div>
                @endif
            </div>
            @if($galleryList->count() > 0 || $car->exteriorPano360Image)
            <div class="cs-gallery-thumbs" id="csGalleryThumbs">
                @if($car->exteriorPano360Image)
                <div class="cs-thumb-360" onclick="csFilterGallery(document.querySelector('[data-gallery-filter=pano360ext]'),'pano360ext')" title="Widok 360°" tabindex="0" role="button" onkeypress="if(event.key==='Enter')this.click()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    360°
                </div>
                @endif
                @foreach($galleryList as $i => $img)
                    <img src="{{ $img->url }}" loading="lazy" alt="{{ $img->alt }}" class="cs-thumb {{ $i===0 && !$car->exteriorPano360Image ? 'active' : '' }}" data-type="gallery" data-idx="{{ $i }}" onclick="csSelImg(this,{{ $i+1 }})" ondblclick="openCarGallery({{ $i }})" tabindex="0" onkeypress="if(event.key==='Enter')csSelImg(this,{{ $i+1 }})">
                @endforeach
                @foreach($damageImgList as $j => $dimg)
                    <img src="{{ $dimg->url }}" loading="lazy" alt="{{ $dimg->alt }}" class="cs-thumb" data-type="damage" data-idx="{{ $galleryList->count() + $j }}" onclick="csSelImg(this,{{ $galleryList->count() + $j + 1 }})" tabindex="0" data-hidden>
                @endforeach
            </div>
            @endif
        </div>

        </div><!-- /left column: gallery -->


        <!-- PRICE + SIDEBAR (sticky) -->
        <div class="cs-sidebar">
            <div class="cs-sidebar-card">
                <!-- MOBILE-ONLY: Title + Heart + Pills -->
                <div class="cs-mob-head" style="display:none;padding:20px 22px 0">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                        <h2 style="font-size:22px;font-weight:900;color:#0a0a0a;letter-spacing:-.4px;line-height:1.2;margin:0">{{ $car->title }}</h2>
                        <button type="button" id="csMobFav" data-id="{{ $car->id }}" onclick="toggleFav(event,{{ $car->id }});csSidebarFavUpdate()" style="width:40px;height:40px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0">
                            <x-icon name="heart" size="20" id="csMobFavIcon" style="color:#9ca3af"/>
                        </button>
                    </div>
                    <div class="cs-mob-pills" style="display:none;flex-wrap:wrap;gap:6px;margin-top:12px">
                        @if($car->mileage)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>{{ number_format((float) $car->mileage,0,'',' ') }} km</span>@endif
                        @if($car->first_registration)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>{{ $car->first_registration }}</span>@endif
                        @if($car->fuel_type)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg>{{ \App\Helpers\CarLabels::fuelType($car->fuel_type) }}</span>@endif
                        @if($car->power_hp)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>{{ $car->power_hp }} KM</span>@endif
                        {{-- CertiCheck moved to the price row (cs-price-section) so it sits next to the price on both desktop and mobile. --}}
                    </div>
                </div>
                <!-- PRICE + CertiCheck action -->
                <div class="cs-price-section">
                    <div class="cs-price-row">
                        <div class="cs-price-block">
                            <div class="cs-price-value">{{ $car->formatted_price }}</div>
                            <div class="cs-price-meta">Cena brutto / VAT-Marża</div>
                        </div>
                        @if($car->has_certicheck)
                            <x-certicheck-cta :slug="$car->slug" :ready="$car->brochureIsReady()"/>
                        @endif
                    </div>
                </div>
                <!-- VEHICLE SUMMARY with icons -->
                <div class="cs-sidebar-summary">
                    @if($car->mileage)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="gauge" size="16"/></span>
                        <span class="lbl">Przebieg</span>
                        <span class="val">{{ number_format((float) $car->mileage,0,'',' ') }} km</span>
                    </div>
                    @endif
                    @if($car->first_registration)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="calendar" size="16"/></span>
                        <span class="lbl">Rok produkcji</span>
                        <span class="val">{{ $car->first_registration }}</span>
                    </div>
                    @endif
                    @if($car->fuel_type)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="fuel" size="16"/></span>
                        <span class="lbl">Paliwo</span>
                        <span class="val">{{ \App\Helpers\CarLabels::fuelType($car->fuel_type) }}</span>
                    </div>
                    @endif
                    @if($car->transmission)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="settings-2" size="16"/></span>
                        <span class="lbl">Skrzynia biegów</span>
                        <span class="val">{{ \App\Helpers\CarLabels::transmission($car->transmission) }}</span>
                    </div>
                    @endif
                    @if($car->power_hp)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="zap" size="16"/></span>
                        <span class="lbl">Moc</span>
                        <span class="val">{{ $car->power_hp }} KM</span>
                    </div>
                    @endif
                    @if($car->body_type ?? $car->category)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="car" size="16"/></span>
                        <span class="lbl">Nadwozie</span>
                        <span class="val">{{ \App\Helpers\CarLabels::bodyType($car->body_type ?? $car->category) }}</span>
                    </div>
                    @endif
                    @if($car->seats)
                    <div class="cs-sidebar-summary-row">
                        <span class="cs-row-icon"><x-icon name="users" size="16"/></span>
                        <span class="lbl">Liczba miejsc</span>
                        <span class="val">{{ $car->seats }}</span>
                    </div>
                    @endif
                </div>
                <!-- CTA BUTTONS (desktop) -->
                <div class="cs-price-actions">
                    <a href="tel:+48515440623" class="cs-btn-phone">
                        <x-icon name="phone" size="18"/>
                        Zadzwoń
                        <span style="font-weight:400;opacity:.85">+48 515 440 623</span>
                    </a>
                    <button type="button" class="cs-btn-message" onclick="csOpenInquiry('general','main_car_cta')">
                        <x-icon name="mail" size="18"/>
                        <span class="cs-msg-text">
                            <strong>Napisz wiadomość</strong>
                            <small>Odpowiadamy na każde pytanie</small>
                        </span>
                    </button>
                    <div style="display:flex;gap:8px;margin-top:4px">
                        <button type="button" class="cs-btn-secondary" id="csSidebarFav" data-id="{{ $car->id }}" onclick="toggleFav(event,{{ $car->id }});csSidebarFavUpdate()" style="flex:1">
                            <x-icon name="heart" size="16" id="csFavIcon"/>
                            <span id="csFavLabel" style="visibility:hidden">Dodaj do ulubionych</span>
                        </button>
                    </div>
                </div>
                <!-- CTA BUTTONS (mobile) -->
                <div class="cs-mob-cta" style="display:none;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:8px;padding:0 16px 16px">
                    <a href="tel:+48515440623" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#0066ff;color:#fff;padding:13px 10px;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;border:none;min-width:0;white-space:nowrap">Zadzwoń</a>
                    <button type="button" onclick="csOpenInquiry('general','trust_banner_cta')" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#f3f4f6;color:#1a1a1a;padding:13px 10px;border-radius:12px;font-size:14px;font-weight:700;border:none;cursor:pointer;min-width:0;white-space:nowrap">Napisz</button>
                </div>



            </div>{{-- /cs-sidebar-card --}}
        </div>
        </div><!-- /cs-sidebar -->

    </div><!-- /cs-grid: gallery + sidebar only -->

    @php
        use App\Helpers\CarLabels;
        // Helper to render a row only when the value is meaningful (non-empty after translation).
        $rowOk = fn($v) => $v !== null && $v !== '' && $v !== false;
        // Pre-compute display values once.
        $dispFuel = CarLabels::fuelType($car->fuel_type);
        $dispTransmission = CarLabels::transmission($car->transmission);
        $dispBody = CarLabels::bodyType($car->body_type ?? $car->category);
        $dispCountry = CarLabels::country($car->country_registration);
        $dispImportedFrom = CarLabels::country($car->imported_from);

        // Benefit row — 5 short reassurance tiles. Items that depend on car
        // fields render dynamic text; the rest are universal CertiCars offers.
        // Resolve the imported-from country once so the leftmost tile can always
        // render with the matching national flag. Falls back to Germany — the
        // CertiCars default sourcing country — when admin hasn't filled the
        // country fields yet (so the row stays at 5 cells like the reference).
        $countryRaw    = $car->country_registration ?: $car->imported_from ?: 'Niemcy';
        $countryName   = CarLabels::country($countryRaw) ?? $countryRaw;
        $importedText  = CarLabels::importedFromStatement($car)
                         ?? ('Sprowadzony z ' . match ($countryName) {
                             'Niemcy' => 'Niemiec', 'Włochy' => 'Włoch', 'Czechy' => 'Czech',
                             'Francja' => 'Francji', 'Hiszpania' => 'Hiszpanii', 'Holandia' => 'Holandii',
                             'Belgia' => 'Belgii', 'Austria' => 'Austrii', 'Szwajcaria' => 'Szwajcarii',
                             'Dania' => 'Danii', 'Szwecja' => 'Szwecji', 'Norwegia' => 'Norwegii',
                             'Japonia' => 'Japonii', 'Polska' => 'Polski',
                             'USA' => 'USA', 'Wielka Brytania' => 'Wielkiej Brytanii',
                             default => $countryName,
                         });
        // Horizontal-stripe palette per country (top → bottom). Keys match the
        // values returned by CarLabels::country(). Fallback is the German flag.
        $flagPalettes = [
            'Niemcy'           => ['#000000', '#dd0000', '#ffce00'],
            'Polska'           => ['#ffffff', '#dc143c', '#dc143c'],
            'Francja'          => ['#0055a4', '#ffffff', '#ef4135'],
            'Włochy'           => ['#009246', '#ffffff', '#ce2b37'],
            'Hiszpania'        => ['#aa151b', '#f1bf00', '#aa151b'],
            'Holandia'         => ['#ae1c28', '#ffffff', '#21468b'],
            'Belgia'           => ['#000000', '#fdda24', '#ef3340'],
            'Austria'          => ['#ed2939', '#ffffff', '#ed2939'],
            'Szwajcaria'       => ['#d52b1e', '#ffffff', '#d52b1e'],
            'Dania'            => ['#c8102e', '#ffffff', '#c8102e'],
            'Szwecja'          => ['#006aa7', '#fecc00', '#006aa7'],
            'Norwegia'         => ['#ef2b2d', '#ffffff', '#002868'],
            'Czechy'           => ['#ffffff', '#11457e', '#d7141a'],
            'USA'              => ['#b22234', '#ffffff', '#3c3b6e'],
            'Wielka Brytania'  => ['#012169', '#ffffff', '#c8102e'],
            'Japonia'          => ['#ffffff', '#bc002d', '#ffffff'],
        ];
        $flagStripes = $flagPalettes[$countryName] ?? $flagPalettes['Niemcy'];

        $exciseLine   = CarLabels::exciseStatement($car);
        $benefits = [];
        $benefits[] = ['ico' => 'flag',            'text' => $importedText, 'stripes' => $flagStripes];
        if ($exciseLine || $car->taxation === null) $benefits[] = ['ico' => 'badge-check', 'text' => $exciseLine ?: 'Akcyza opłacona'];
        $benefits[] = ['ico' => 'clipboard-check', 'text' => 'Przygotowany do rejestracji'];
        $benefits[] = ['ico' => 'percent',         'text' => 'Kupujący zwolniony z PCC 2%'];
        $benefits[] = ['ico' => 'search-check',    'text' => 'Możliwość sprawdzenia auta przed zakupem'];
        $benefits = array_slice($benefits, 0, 5);
    @endphp

    {{-- =================== BENEFIT ROW (5 items) =================== --}}
    {{-- Icon system: Lucide via <x-icon>. Flag tile keeps its CSS stripes — no
         Lucide flag glyph, and the colours are the country flag itself. --}}
    <div class="cs-benefits-row">
        @foreach($benefits as $b)
        <div class="cs-benefit-item">
            <span class="cs-benefit-ico{{ $b['ico'] === 'flag' ? ' flag' : '' }}" aria-hidden="true">
                @if($b['ico'] === 'flag')
                    <span class="cs-flag">
                        <span style="background:{{ $b['stripes'][0] }}"></span>
                        <span style="background:{{ $b['stripes'][1] }}"></span>
                        <span style="background:{{ $b['stripes'][2] }}"></span>
                    </span>
                @else
                    <x-icon :name="$b['ico']" size="18"/>
                @endif
            </span>
            <span class="cs-benefit-text">{{ $b['text'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- =================== FINANSOWANIE + GETHELP (two-column row) =================== --}}
    @php
        $activePkg = strtolower((string) ($car->gethelp_package ?? 'classic'));
        $gethelpPackages = [
            ['key' => 'classic', 'name' => 'GetHelp Classic', 'badge' => '12 mies.', 'price' => '1 000 zł / rok', 'desc' => 'Podstawowa ochrona układu napędowego oraz wsparcie assistance 24/7.'],
            ['key' => 'optimum', 'name' => 'GetHelp Optimum', 'badge' => '24 mies.', 'price' => '1 495 zł / rok', 'desc' => 'Rozszerzony zakres ochrony, w tym elektronika, klimatyzacja i komfort jazdy.'],
            ['key' => 'grand',   'name' => 'GetHelp Grand',   'badge' => '36 mies.', 'price' => '1 995 zł / rok', 'desc' => 'Najszersza ochrona z assistance, autem zastępczym i zakresem niemal pełnym.'],
        ];
    @endphp
    <div class="cs-finance-row">
        {{-- LEFT: Finansowanie pojazdu (compact) --}}
        <div class="cs-finance-card">
            <div class="cs-finance-head">
                <div class="cs-finance-head-ico" aria-hidden="true">
                    <x-icon name="wallet" size="18"/>
                </div>
                <div>
                    <h3 class="cs-finance-title">Finansowanie pojazdu</h3>
                    <p class="cs-finance-sub">Sprawdź orientacyjną ratę dla tego auta.</p>
                </div>
            </div>
            <div class="cs-finance-controls">
                <div class="cs-finance-field">
                    <label>Cena pojazdu</label>
                    <div class="cs-finance-readonly" id="csCalcPrice" data-price="{{ $car->price ?? 0 }}">{{ $car->price ? number_format((float) $car->price, 0, '', ' ') . ' zł' : '—' }}</div>
                </div>
                <div class="cs-finance-field">
                    <label>Wpłata własna</label>
                    <div class="cs-finance-input-wrap">
                        <input type="number" id="csCalcDp" value="{{ $car->price ? round($car->price * 0.2) : 0 }}" min="0" max="{{ $car->price ?? 0 }}" step="1000">
                        <span>zł</span>
                    </div>
                </div>
                <div class="cs-finance-field">
                    <label>Okres finansowania</label>
                    <select id="csCalcTerm">
                        <option value="12">12 mies.</option>
                        <option value="24">24 mies.</option>
                        <option value="36">36 mies.</option>
                        <option value="48" selected>48 mies.</option>
                        <option value="60">60 mies.</option>
                        <option value="72">72 mies.</option>
                        <option value="84">84 mies.</option>
                        <option value="96">96 mies.</option>
                    </select>
                </div>
            </div>
            <div class="cs-finance-result">
                <div class="cs-finance-result-left">
                    <span class="cs-finance-result-label">Orientacyjna rata miesięczna</span>
                    <div class="cs-finance-result-value"><span id="csCalcInlineRate">—</span> <span class="suffix">/ mies.</span></div>
                </div>
                <button type="button" class="cs-finance-cta" onclick="csOpenInquiry('financing','financing_form')">
                    Zapytaj o finansowanie
                    <x-icon name="arrow-right" size="14" :strokeWidth="2.5"/>
                </button>
            </div>
            <div class="cs-finance-foot">
                <span>*Przykładowa rata przy RRSO 7,9%. Nie stanowi oferty w rozumieniu prawa.</span>
            </div>
        </div>

        {{-- RIGHT: Gwarancja techniczna GetHelp (compact) --}}
        <div class="cs-gethelp-card">
            <div class="cs-gethelp-head">
                <div class="cs-gethelp-ico" aria-hidden="true">
                    <x-icon name="shield-check" size="18"/>
                </div>
                <h3 class="cs-gethelp-title">Gwarancja techniczna GetHelp <span class="info-i" title="Wybierz pakiet ochrony pojazdu po zakupie">i</span></h3>
            </div>
            <div class="cs-gethelp-row">
                @foreach($gethelpPackages as $pkg)
                <div class="cs-gethelp-mini{{ $pkg['key'] === $activePkg ? ' active' : '' }}">
                    <div class="cs-gethelp-mini-ico" aria-hidden="true">
                        <x-icon name="shield-check" size="18"/>
                    </div>
                    <div class="cs-gethelp-mini-name">{{ $pkg['name'] }}</div>
                    <div class="cs-gethelp-mini-price">{{ $pkg['price'] }}</div>
                </div>
                @endforeach
            </div>
            <a href="{{ url('/o-nas#gethelp') }}" class="cs-gethelp-more-link">
                Dowiedz się więcej o gwarancji
                <x-icon name="arrow-right" size="14" :strokeWidth="2.5"/>
            </a>
            <div class="cs-gethelp-helper">
                Dostępność i zakres gwarancji zależy od pojazdu, jego wieku, przebiegu oraz historii serwisowej. Szczegóły u naszego doradcy.
            </div>
        </div>
    </div>

    {{-- A. DANE POJAZDU — expanded by default on both desktop and mobile --}}
    <div class="cs-data-section cs-collapsible-mobile">
        <div class="cs-data-header open" onclick="csToggleAccordion(this)">
            <h2><x-icon name="car" size="20"/>Dane pojazdu</h2>
            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <div class="cs-data-body">
            {{-- 14-field icon-led grid. Order is fixed; each row only
                 renders when the backend has a real value, so cars with
                 missing fields gracefully collapse (no empty cells). --}}
            @php
                $emissionDisp = $rowOk($car->emission_class)
                    ? preg_replace('/^(euro)\s*(\d.*)$/i', 'Euro $2', trim((string) $car->emission_class))
                    : null;
                $dpRows = [];
                if ($car->brand?->name)              $dpRows[] = ['badge-check',   'Marka',                $car->brand->name];
                if ($rowOk($car->first_registration))$dpRows[] = ['calendar',      'Rok produkcji',        $car->first_registration];
                if ($rowOk($car->model))             $dpRows[] = ['car',           'Model',                $car->model];
                if ($rowOk($dispFuel))               $dpRows[] = ['fuel',          'Paliwo',               $dispFuel];
                if ($rowOk($dispTransmission))       $dpRows[] = ['settings',      'Skrzynia biegów',      $dispTransmission];
                if ($rowOk($car->mileage))           $dpRows[] = ['gauge',         'Przebieg',             number_format((float) $car->mileage, 0, '', ' ') . ' km'];
                if ($rowOk($car->engine_capacity))   $dpRows[] = ['activity',      'Pojemność skokowa',    number_format((float) $car->engine_capacity, 0, '', ' ') . ' cm³'];
                if ($emissionDisp)                   $dpRows[] = ['leaf',          'Norma emisji spalin',  $emissionDisp];
                if ($rowOk($car->power_hp))          $dpRows[] = ['zap',           'Moc',                  $car->power_hp . ' KM' . ($rowOk($car->power_kw) ? ' / ' . $car->power_kw . ' kW' : '')];
                if ($rowOk($car->seats))             $dpRows[] = ['users',         'Liczba miejsc',        $car->seats];
                if ($rowOk($dispBody))               $dpRows[] = ['car-front',     'Typ nadwozia',         $dispBody];
                if ($rowOk($car->doors))             $dpRows[] = ['door-open',     'Liczba drzwi',         $car->doors];
                if ($rowOk($car->color))             $dpRows[] = ['palette',       'Kolor nadwozia',       $car->color];
                if ($rowOk($dispCountry))            $dpRows[] = ['globe',         'Kraj pochodzenia',     $dispCountry];
            @endphp
            <div class="cs-dp-grid">
                @foreach($dpRows as [$ico, $label, $value])
                    <div class="cs-dp-item">
                        <span class="cs-dp-ico" aria-hidden="true"><x-icon :name="$ico" size="18" :strokeWidth="1.8"/></span>
                        <span class="cs-dp-text">
                            <span class="cs-dp-lbl">{{ $label }}</span>
                            <span class="cs-dp-val" title="{{ $value }}">{{ $value }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- =================== 3-CARD SUMMARY ROW (Historia / Dokumenty / Formalności) =================== --}}
    @php
        // Pull the same labelled values used by the deeper accordion sections so
        // the summary row stays in sync without re-deriving anything. All helpers
        // are null-safe; muted "—" placeholders appear only when the field is
        // genuinely empty in admin.
        $svc3      = CarLabels::bool($car->service_book) ?: CarLabels::bool($car->service_documentation);
        $regCert3  = CarLabels::bool($car->registration_cert);
        $manual3   = CarLabels::bool($car->owners_manual);
        $bookSt3   = CarLabels::status($car->service_book_status) ?? $car->service_book_status;
        $exciseSh  = match (strtolower((string) $car->taxation)) {
            'paid', 'oplacona', 'opłacona' => 'Opłacona',
            'unpaid', 'nieoplacona', 'nieopłacona' => 'Nieopłacona',
            'na', 'nie_dotyczy', 'nie dotyczy' => 'Nie dotyczy',
            default => $car->taxation ? ucfirst((string) $car->taxation) : null,
        };
        $hasMuted = fn($v) => $v ?: '—';
    @endphp
    <div class="cs-info-3row">
        {{-- A. HISTORIA POJAZDU --}}
        <div class="cs-info-3card">
            <div class="cs-info-3card-head">
                <div class="cs-info-3card-ico" aria-hidden="true">
                    <x-icon name="history" size="18"/>
                </div>
                <h3 class="cs-info-3card-title">Historia pojazdu</h3>
            </div>
            <div class="cs-info-3card-rows">
                <div class="cs-info-3row-line"><span class="lbl">Pochodzenie</span><span class="val {{ $dispCountry ? '' : 'muted' }}">{{ $hasMuted($dispCountry) }}</span></div>
                @php
                    // Show specific import country when admin filled it AND it differs from
                    // country_registration; otherwise fall back to the Tak/Nie summary.
                    $importedSpecific = ($rowOk($dispImportedFrom) && $car->imported_from !== $car->country_registration) ? $dispImportedFrom : null;
                @endphp
                <div class="cs-info-3row-line"><span class="lbl">Importowany</span><span class="val">{{ $importedSpecific ?? (($car->is_imported || $rowOk($car->imported_from)) ? 'Tak' : 'Nie') }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Liczba właścicieli</span><span class="val {{ $car->previous_owners === null ? 'muted' : '' }}">{{ $car->previous_owners === null ? '—' : ($car->previous_owners == 0 ? 'Pierwszy' : $car->previous_owners) }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Historia serwisowa</span><span class="val {{ $svc3 ? '' : 'muted' }}">{{ $hasMuted($svc3) }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Ostatni przegląd</span><span class="val {{ $rowOk($car->last_service) ? '' : 'muted' }}">{{ $hasMuted($car->last_service) }}</span></div>
                @if($rowOk($car->vehicle_history))
                <div class="cs-info-3row-line" style="flex-direction:column;align-items:flex-start;gap:2px"><span class="lbl">Opis historii</span><span class="val" style="text-align:left;max-width:100%;font-weight:600;color:#374151">{{ $car->vehicle_history }}</span></div>
                @endif
            </div>
        </div>

        {{-- B. DOKUMENTY --}}
        <div class="cs-info-3card">
            <div class="cs-info-3card-head">
                <div class="cs-info-3card-ico" aria-hidden="true">
                    <x-icon name="file-text" size="18"/>
                </div>
                <h3 class="cs-info-3card-title">Dokumenty</h3>
            </div>
            <div class="cs-info-3card-rows">
                <div class="cs-info-3row-line"><span class="lbl">Faktura</span><span class="val ok">VAT-marża</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Dowód rejestracyjny</span><span class="val ok">{{ $regCert3 ?: 'Dostępny' }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Liczba kluczyków</span><span class="val {{ $rowOk($car->number_of_keys) ? '' : 'muted' }}">{{ $rowOk($car->number_of_keys) ? $car->number_of_keys : '2' }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Książka serwisowa</span><span class="val ok">{{ $bookSt3 ?: 'Dostępna' }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Instrukcja obsługi</span><span class="val ok">{{ $manual3 ?: 'Jest' }}</span></div>
            </div>
        </div>

        {{-- C. FORMALNOŚCI --}}
        <div class="cs-info-3card">
            <div class="cs-info-3card-head">
                <div class="cs-info-3card-ico" aria-hidden="true">
                    <x-icon name="shield-check" size="18"/>
                </div>
                <h3 class="cs-info-3card-title">Formalności</h3>
            </div>
            <div class="cs-info-3card-rows">
                <div class="cs-info-3row-line"><span class="lbl">Akcyza</span><span class="val ok">{{ $exciseSh ?: 'Opłacona' }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Przegląd techniczny</span><span class="val ok">{{ $rowOk($car->next_inspection) ? $car->next_inspection : 'Wykonany' }}</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Przygotowany do rejestracji</span><span class="val ok">Tak</span></div>
                <div class="cs-info-3row-line"><span class="lbl">PCC 2%</span><span class="val ok">Kupujący zwolniony</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Koszt rejestracji</span><span class="val ok">Po stronie kupującego</span></div>
                <div class="cs-info-3row-line"><span class="lbl">Możliwość transportu</span><span class="val ok">Dostępna po wcześniejszym ustaleniu</span></div>
            </div>
        </div>
    </div>

    {{-- (Historia pojazdu accordion removed — its content is already shown in the
         3-card summary row above. The unique fields below were merged into the
         summary card's "Historia pojazdu" tile (free-text + specific import
         country). Removed at render source, not hidden with CSS. --}}

    {{-- =================== DETAIL 3-CARD ROW =================== --}}
    {{-- Serwisowanie · Dokumenty · Zużycie paliwa as 3 equal sibling cards
         on desktop (1·1·1) instead of stacked full-width strips.
         Each remains a collapsible accordion on mobile. --}}
    @php
        // SERWISOWANIE visibility
        $svcDoc = CarLabels::bool($car->service_documentation);
        $asoSvc = CarLabels::bool($car->aso_serviced);
        $showSvc = $rowOk($svcDoc) || $rowOk($asoSvc) || $rowOk($car->service_history) || $rowOk($car->last_service) || $rowOk($car->next_inspection);

        // DOKUMENTY visibility
        $cocDocs        = CarLabels::bool($car->coc_documents);
        $svcBookStatus  = CarLabels::status($car->service_book_status) ?? $car->service_book_status;
        $regCert        = CarLabels::bool($car->registration_cert);
        $ownersManual   = CarLabels::bool($car->owners_manual);
        $vehicleFolder  = CarLabels::bool($car->vehicle_folder);
        $huAuReport     = CarLabels::bool($car->hu_au_report);
        $showDocs       = $rowOk($cocDocs) || $rowOk($svcBookStatus) || $rowOk($regCert) || $rowOk($ownersManual) || $rowOk($vehicleFolder) || $rowOk($huAuReport);

        // ZUŻYCIE PALIWA visibility
        $showFuel = $rowOk($car->fuel_consumption) || $rowOk($car->co2_emission) || $rowOk($car->emission_class);

        // Visible-card count drives the grid track count so missing data
        // never leaves an awkward empty column. 3 = three even cards,
        // 2 = two half-width cards, 1 = full-width card.
        $detailCount = ($showSvc ? 1 : 0) + ($showDocs ? 1 : 0) + ($showFuel ? 1 : 0);
    @endphp
    @if($detailCount > 0)
    <div class="cs-detail-3row cs-detail-3row--n{{ $detailCount }}">
        @if($showSvc)
        {{-- C. SERWISOWANIE --}}
        <div class="cs-data-section cs-collapsible-mobile">
            <div class="cs-data-header" onclick="csToggleAccordion(this)">
                <h2><x-icon name="wrench" size="20"/>Serwisowanie</h2>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="cs-data-body">
                <div class="cs-data-grid-2col">
                    @if($rowOk($asoSvc))
                        <div class="cs-data-row"><span class="lbl">Serwis ASO</span><span class="val">{{ $asoSvc }}</span></div>
                    @endif
                    @if($rowOk($svcDoc))
                        <div class="cs-data-row"><span class="lbl">Dokumentacja serwisowa</span><span class="val">{{ $svcDoc }}</span></div>
                    @endif
                    @if($rowOk($car->service_history))
                        <div class="cs-data-row"><span class="lbl">Historia serwisowa</span><span class="val">{{ $car->service_history }}</span></div>
                    @endif
                    @if($rowOk($car->last_service))
                        <div class="cs-data-row"><span class="lbl">Ostatni przegląd</span><span class="val">{{ $car->last_service }}@if($rowOk($car->last_service_mileage)) · {{ number_format((float) $car->last_service_mileage, 0, '', ' ') }} km @endif</span></div>
                    @endif
                    @if($rowOk($car->next_inspection))
                        <div class="cs-data-row"><span class="lbl">Następny przegląd</span><span class="val">{{ $car->next_inspection }}</span></div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($showDocs)
        {{-- D. DOKUMENTY --}}
        <div class="cs-data-section cs-collapsible-mobile">
            <div class="cs-data-header" onclick="csToggleAccordion(this)">
                <h2><x-icon name="file-text" size="20"/>Dokumenty</h2>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="cs-data-body">
                <div class="cs-data-grid-2col">
                    @if($rowOk($cocDocs))
                        <div class="cs-data-row"><span class="lbl">Komplet dokumentów</span><span class="val">{{ $cocDocs }}</span></div>
                    @endif
                    @if($rowOk($svcBookStatus))
                        <div class="cs-data-row"><span class="lbl">Książka serwisowa</span><span class="val">{{ $svcBookStatus }}</span></div>
                    @endif
                    @if($rowOk($regCert))
                        <div class="cs-data-row"><span class="lbl">Dowód rejestracyjny</span><span class="val">{{ $regCert }}</span></div>
                    @endif
                    @if($rowOk($ownersManual))
                        <div class="cs-data-row"><span class="lbl">Instrukcja obsługi</span><span class="val">{{ $ownersManual }}</span></div>
                    @endif
                    @if($rowOk($vehicleFolder))
                        <div class="cs-data-row"><span class="lbl">Teczka pojazdu</span><span class="val">{{ $vehicleFolder }}</span></div>
                    @endif
                    @if($rowOk($huAuReport))
                        <div class="cs-data-row"><span class="lbl">Raport HU/AU</span><span class="val">{{ $huAuReport }}</span></div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($showFuel)
        {{-- E. ZUŻYCIE PALIWA --}}
        <div class="cs-data-section cs-collapsible-mobile">
            <div class="cs-data-header" onclick="csToggleAccordion(this)">
                <h2><x-icon name="fuel" size="20"/>Zużycie paliwa</h2>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="cs-data-body">
                <div class="cs-data-grid-2col">
                    @if($rowOk($car->fuel_consumption))
                        @php
                            // Strip any embedded "l/100 km" / "L/100km" the admin may have already typed,
                            // and normalise decimal separator. Render the unit exactly once below.
                            $fcRaw = trim(preg_replace('/\s*[lL]\s*\/\s*100\s*km\.?/u', '', (string) $car->fuel_consumption));
                            $fcRaw = trim(str_replace(',', '.', $fcRaw));
                        @endphp
                        <div class="cs-data-row"><span class="lbl">Średnie zużycie</span><span class="val">{{ $fcRaw }} l/100 km</span></div>
                    @endif
                    @if($rowOk($car->co2_emission))
                        <div class="cs-data-row"><span class="lbl">Emisja CO₂</span><span class="val">{{ $car->co2_emission }} g/km</span></div>
                    @endif
                    @if($rowOk($car->emission_class))
                        <div class="cs-data-row"><span class="lbl">Norma emisji</span><span class="val">{{ $car->emission_class }}</span></div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- E. WYPOSAŻENIE — rebuilt to match reference structure --}}
    @php
        $hl = collect($car->highlighted_equipment ?? [])
            ->filter(fn($k) => \App\Helpers\EquipmentCatalog::option($k) !== null)
            ->take(8)
            ->values();
        $eqGrouped = \App\Helpers\EquipmentCatalog::groupEquipmentForDisplay($car->equipment);
        $hasEqSection = $hl->isNotEmpty() || $totalCount > 0;
    @endphp
    @if($hasEqSection)
    <div class="cs-equip-section">
        <div class="cs-equip-head">
            <h2 class="cs-equip-title">Wyposażenie</h2>
            <p class="cs-equip-sub">Najważniejsze elementy wyposażenia tego egzemplarza</p>
        </div>

        @if($hl->isNotEmpty())
        {{-- Top 8 highlighted tiles. Admin picks them from EquipmentCatalog options. --}}
        <div class="cs-equip-tiles">
            @foreach($hl as $key)
                @php $opt = \App\Helpers\EquipmentCatalog::option($key); @endphp
                <div class="cs-equip-tile">
                    <div class="cs-equip-tile-ico" aria-hidden="true">
                        <x-icon :name="$opt['icon']" size="22" :strokeWidth="1.8"/>
                    </div>
                    <div class="cs-equip-tile-label">{{ $opt['label'] }}</div>
                </div>
            @endforeach
        </div>
        @endif

        @if(!empty($eqGrouped))
        {{-- Category cards. Items are real equipment data grouped/categorized via
             EquipmentCatalog::groupEquipmentForDisplay. Empty categories are
             dropped by the helper. --}}
        @php
            // Show first 6 items per category in the collapsed view. If any
            // category exceeds 6 items the "Pokaż pełne wyposażenie" button
            // toggles the rest into view.
            $eqHasOverflow = collect($eqGrouped)->contains(fn($items) => count($items) > 6);
        @endphp
        <div class="cs-equip-cats" id="csEquipCats">
            @foreach($eqGrouped as $catKey => $items)
            @php $catMeta = \App\Helpers\EquipmentCatalog::CATEGORIES[$catKey] ?? null; @endphp
            @if($catMeta)
            <div class="cs-equip-cat">
                <div class="cs-equip-cat-head">
                    <div class="cs-equip-cat-ico" aria-hidden="true">
                        <x-icon :name="$catMeta['icon']" size="18" :strokeWidth="1.8"/>
                    </div>
                    <h3 class="cs-equip-cat-title">{{ $catMeta['label'] }}</h3>
                </div>
                <ul class="cs-equip-cat-list">
                    @foreach($items as $idx => $item)
                    <li @if($idx >= 6) class="cs-equip-extra" @endif><x-icon name="check" size="14" tone="blue" :strokeWidth="2.5"/><span>{{ $item }}</span></li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endforeach
        </div>
        @if($eqHasOverflow)
        <button type="button" class="cs-equip-show-all" id="csEquipShowAll" onclick="csEquipToggle(this)" aria-controls="csEquipCats" aria-expanded="false">
            <span class="cs-equip-show-all-text">Pokaż pełne wyposażenie</span>
            <x-icon name="chevron-down" size="16" :strokeWidth="2.4" class="cs-equip-show-all-chev"/>
        </button>
        <script>
        function csEquipToggle(btn){
            const sec = btn.closest('.cs-equip-section');
            if(!sec) return;
            const expanded = sec.classList.toggle('expanded');
            btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            btn.querySelector('.cs-equip-show-all-text').textContent = expanded ? 'Pokaż mniej' : 'Pokaż pełne wyposażenie';
        }
        </script>
        @endif
        @endif
    </div>
    @endif

    {{-- STAN WIZUALNY I ŚLADY EKSPLOATACJI --}}
    @if($car->damages->count())
    @php
        // Classify each damage into one of two reference categories. Severity drives it:
        // low or empty → 'minor' (filled orange dot · "Drobne ślady eksploatacji")
        // anything else → 'scratch' (outlined orange dot · "Lekkie rysy i otarcia")
        // 'category' is stamped on every damage for the JS chip filter.
        $damagesNumbered = $car->damages->values();
        $catMinor   = collect();
        $catScratch = collect();
        foreach ($damagesNumbered as $idx => $d) {
            $sev = strtolower((string) ($d->severity ?? ''));
            $cat = ($sev === '' || $sev === 'low' || $sev === 'minor' || $sev === 'niski') ? 'minor' : 'scratch';
            $d->__cat = $cat;          // used as data attribute
            $d->__num = $idx + 1;       // 1-based marker / thumb number
            if ($cat === 'minor') $catMinor->push($d); else $catScratch->push($d);
        }
        $countAll     = $damagesNumbered->count();
        $countMinor   = $catMinor->count();
        $countScratch = $catScratch->count();
    @endphp
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="map-pin" aria-hidden="true" style="color:#0066ff"></i> Stan wizualny i ślady eksploatacji</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <p class="cs-damage-subtitle">Delikatnie oznaczone miejsca pokazują elementy, na które warto zwrócić uwagę podczas oględzin pojazdu.</p>

            {{-- Category chips: Wszystkie / Drobne ślady eksploatacji / Lekkie rysy i otarcia --}}
            <div class="cs-damages-tabs" role="tablist">
                <button type="button" class="cs-damage-tab active" data-damage-cat="all" onclick="csFilterDamageCat(this,'all')" role="tab" aria-selected="true">
                    <span class="cs-damage-tab-grid" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
                    Wszystkie
                    <span class="cs-damage-tab-count">{{ $countAll }}</span>
                </button>
                @if($countMinor > 0)
                <button type="button" class="cs-damage-tab" data-damage-cat="minor" onclick="csFilterDamageCat(this,'minor')" role="tab" aria-selected="false">
                    <span class="cs-damage-tab-dot" aria-hidden="true"></span>
                    Drobne ślady eksploatacji
                    <span class="cs-damage-tab-count">{{ $countMinor }}</span>
                </button>
                @endif
                @if($countScratch > 0)
                <button type="button" class="cs-damage-tab" data-damage-cat="scratch" onclick="csFilterDamageCat(this,'scratch')" role="tab" aria-selected="false">
                    <span class="cs-damage-tab-dot outline" aria-hidden="true"></span>
                    Lekkie rysy i otarcia
                    <span class="cs-damage-tab-count">{{ $countScratch }}</span>
                </button>
                @endif
            </div>

            <div class="cs-damage-grid">
                {{-- Left: car top-view diagram with numbered markers + legend --}}
                <div class="cs-damage-diagram">
                    <div class="cs-damage-diagram-canvas">
                        @php
                            $bodyTypeMap = [
                                'sedan' => 'sedan', 'suv' => 'suv', 'coupé' => 'coupe', 'coupe' => 'coupe',
                                'bus' => 'van', 'van' => 'van', 'kombi' => 'kombi', 'hatchback' => 'hatchback',
                                'kabriolet' => 'sedan', 'cabriolet' => 'sedan', 'pickup' => 'suv',
                            ];
                            $btKey = strtolower($car->body_type ?? $car->category ?? 'sedan');
                            $topImg = $bodyTypeMap[$btKey] ?? 'sedan';
                        @endphp
                        <img class="cs-damage-diagram-img" src="/img/body-types-top/{{ $topImg }}.png" alt="" aria-hidden="true" draggable="false">
                        @foreach($damagesNumbered as $d)
                        <button type="button"
                                class="cs-damage-marker{{ $loop->first ? ' active' : '' }}{{ $d->__cat === 'scratch' ? ' outline' : '' }}"
                                data-damage-marker="{{ $d->id }}"
                                data-damage-cat="{{ $d->__cat }}"
                                onclick="csSelDamage({{ $d->id }})"
                                aria-label="{{ $d->area }}"
                                style="left:{{ $d->position_x ?? 50 }}%;top:{{ $d->position_y ?? 50 }}%">
                            <span class="cs-damage-marker-dot">{{ $d->__num }}</span>
                        </button>
                        @endforeach
                    </div>
                    <div class="cs-damage-diagram-legend">
                        @if($countMinor > 0)
                        <div class="cs-damage-diagram-legend-item"><span class="dot"></span>Drobne ślady eksploatacji</div>
                        @endif
                        @if($countScratch > 0)
                        <div class="cs-damage-diagram-legend-item"><span class="dot outline"></span>Lekkie rysy i otarcia</div>
                        @endif
                    </div>
                </div>

                {{-- Right: selected damage photo + thumbnails + bottom CTA --}}
                <div class="cs-damage-detail" id="csDamageDetail">
                    @foreach($damagesNumbered as $d)
                    @php
                        // R2-aware photo URLs (PR #7). Damage main image first, then per-damage photos.
                        $dmgPhotos = [];
                        if ($d->image_url) {
                            $dmgPhotos[] = $d->image_url;
                        }
                        foreach ($d->photos ?? [] as $dp) {
                            $url = $dp->path ? $dp->url : null;
                            if ($url && !in_array($url, $dmgPhotos)) $dmgPhotos[] = $url;
                        }
                        $catLabel = $d->__cat === 'scratch' ? 'Lekkie rysy i otarcia' : 'Drobne ślady eksploatacji';
                    @endphp
                    <div class="cs-damage-item{{ $loop->first ? ' active' : '' }}" id="csDamage-{{ $d->id }}" data-damage-cat="{{ $d->__cat }}">
                        @if(count($dmgPhotos))
                        <div class="cs-dmg-gallery" data-dmg-id="{{ $d->id }}">
                            {{-- Main image stage --}}
                            <div class="cs-dmg-gallery-stage">
                                @foreach($dmgPhotos as $pi => $pUrl)
                                <div class="cs-dmg-gallery-slide{{ $pi === 0 ? ' active' : '' }}" data-slide="{{ $pi }}">
                                    <img src="{{ $pUrl }}" alt="{{ $d->area }}" {{ $pi === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' }} decoding="async" onclick="csDmgLightbox(this)" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                                </div>
                                @endforeach
                                {{-- Bottom-left overlay card: category + area --}}
                                <div class="cs-dmg-overlay">
                                    <div class="cs-dmg-overlay-type"><span class="dot{{ $d->__cat === 'scratch' ? ' outline' : '' }}"></span>{{ $catLabel }}</div>
                                    <div class="cs-dmg-overlay-area">{{ $d->area }}</div>
                                </div>
                                {{-- Top-right counter + fullscreen --}}
                                <div class="cs-dmg-gallery-meta">
                                    <span class="cs-dmg-gallery-counter"><span class="cs-dmg-gallery-cur">1</span>/{{ count($dmgPhotos) }}</span>
                                    <button type="button" class="cs-dmg-gallery-fs" onclick="csDmgLightbox(this.closest('.cs-dmg-gallery-stage').querySelector('.cs-dmg-gallery-slide.active img'))" title="Pełny ekran"><i data-lucide="maximize-2" style="width:16px;height:16px"></i></button>
                                </div>
                                @if(count($dmgPhotos) > 1)
                                <button type="button" class="cs-dmg-gallery-arrow left" onclick="csDmgSlide(this,-1)"><i data-lucide="chevron-left" style="width:22px;height:22px"></i></button>
                                <button type="button" class="cs-dmg-gallery-arrow right" onclick="csDmgSlide(this,1)"><i data-lucide="chevron-right" style="width:22px;height:22px"></i></button>
                                @endif
                            </div>

                            {{-- Numbered thumbnails — only when >1 photo --}}
                            @if(count($dmgPhotos) > 1)
                            <div class="cs-dmg-gallery-thumbs-wrap">
                                <button type="button" class="cs-dmg-thumb-arrow left" onclick="csDmgScrollThumbs(this,-1)"><i data-lucide="chevron-left" style="width:14px;height:14px"></i></button>
                                <div class="cs-dmg-gallery-thumbs">
                                    @foreach($dmgPhotos as $pi => $pUrl)
                                    <div class="cs-dmg-gallery-thumb{{ $pi === 0 ? ' active' : '' }}" data-slide="{{ $pi }}" onclick="csDmgGoSlide(this,{{ $pi }})">
                                        <span class="cs-dmg-thumb-num">{{ $pi + 1 }}</span>
                                        <img src="{{ $pUrl }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="cs-dmg-thumb-arrow right" onclick="csDmgScrollThumbs(this,1)"><i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>
                            </div>
                            @endif

                            {{-- Bottom CTA: "Zobacz wszystkie zdjęcia stanu pojazdu" — opens lightbox at this damage's first photo --}}
                            <button type="button" class="cs-dmg-all-cta" onclick="csDmgLightbox(this.closest('.cs-dmg-gallery').querySelector('.cs-dmg-gallery-slide.active img'))">
                                <span class="lead"><svg viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18"/></svg></span>
                                Zobacz wszystkie zdjęcia stanu pojazdu
                                <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                        @else
                        <div style="padding:40px;text-align:center;color:var(--text-3);background:#fff;border:1px dashed #e5e7eb;border-radius:14px">
                            <i data-lucide="image-off" style="width:32px;height:32px;margin-bottom:8px"></i>
                            <div style="font-size:13px">Brak zdjęć dla tego elementu</div>
                        </div>
                        @endif

                        @if($d->description || ($d->tags && count($d->tags)))
                        <div class="cs-damage-item-meta">
                            @if($d->tags && count($d->tags))
                            <div class="cs-damage-tags">@foreach($d->tags as $t)<span>{{ $t }}</span>@endforeach</div>
                            @endif
                            @if($d->description)<p>{{ $d->description }}</p>@endif
                        </div>
                        @endif
                    </div>
                    @endforeach

                    {{-- Empty state when no damages match the active category chip --}}
                    <div class="cs-damage-empty-cat" id="csDamageEmptyCat" style="display:none">
                        Brak zdjęć dla wybranej kategorii.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- STAN TECHNICZNY PODCZAS OGLĘDZIN + NAGRANIE PRACY SILNIKA — two-column row --}}
    @php
        // Build the reference 8-item ordered list. Map admin-stored statuses onto
        // the three visual buckets (ok / warn / fail) so the same card structure
        // can carry real per-car data later without re-tagging icons.
        $techRows = [
            ['key' => 'engine',        'label' => 'Silnik',             'icon' => 'engine'],
            ['key' => 'transmission',  'label' => 'Skrzynia biegów',    'icon' => 'gear'],
            ['key' => 'suspension',    'label' => 'Zawieszenie',        'icon' => 'suspension'],
            ['key' => 'brakes',        'label' => 'Hamulce',            'icon' => 'brakes'],
            ['key' => 'steering',      'label' => 'Układ kierowniczy',  'icon' => 'steering'],
            ['key' => 'ac',            'label' => 'Klimatyzacja',       'icon' => 'ac'],
            ['key' => 'electronics',   'label' => 'Elektronika',        'icon' => 'electronics'],
            ['key' => 'lights',        'label' => 'Oświetlenie',        'icon' => 'lights'],
        ];
        // Legacy admin keys (air_conditioning, braking) map to the canonical bucket
        // so old saves still surface against the right item without admin re-save.
        $techLegacyAliases = ['ac' => 'air_conditioning', 'brakes' => 'braking'];
        $techStatusFor = function (string $key) use ($car, $techLegacyAliases): array {
            $raw = $car->technical_conditions[$key] ?? null;
            if ($raw === null && isset($techLegacyAliases[$key])) {
                $raw = $car->technical_conditions[$techLegacyAliases[$key]] ?? null;
            }
            // Single source of truth — same helper the admin form pre-resolves with.
            $resolved = \App\Helpers\CarLabels::techStatus($raw);
            $clsMap = ['ok' => 'ok', 'attention' => 'warn', 'bad' => 'fail'];
            return [
                'cls'   => $clsMap[$resolved['status']],
                'label' => $resolved['label'],
                'note'  => $resolved['note'],
            ];
        };

        // Engine video — derive embed type once.
        $hasEngineVideoLocal = $car->engine_video_url || $car->engine_video_path;
        $ytId = null; $vimId = null; $rawVidUrl = $car->engine_video_url;
        if ($rawVidUrl) {
            if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/))([\w-]{11})~', $rawVidUrl, $m)) $ytId = $m[1];
            elseif (preg_match('~vimeo\.com/(\d+)~', $rawVidUrl, $m)) $vimId = $m[1];
        }
        // Hide the whole tech+video row when admin hasn't filled either side
        // (no per-item status entries AND no engine recording of any form).
        $hasAnyTechStatus = !empty(array_filter((array) ($car->technical_conditions ?? []), function ($v) {
            if (is_array($v)) {
                $s = $v['status'] ?? null;
                $n = trim((string) ($v['note'] ?? ''));
                return ($s && $s !== 'ok') || $n !== '';
            }
            return is_string($v) && trim($v) !== '';
        }));
        $showTechEngineRow = $hasAnyTechStatus || $hasEngineVideoLocal;
    @endphp
    @if($showTechEngineRow)
    <div class="cs-tech-engine-row">
        {{-- LEFT — Stan techniczny podczas oględzin --}}
        <div class="cs-tech-engine-card">
            <div class="cs-tech-engine-card-head">
                <div class="cs-tech-engine-card-ico">
                    <x-icon name="shield-check" size="22"/>
                </div>
                <div class="cs-tech-engine-card-titlewrap">
                    <h3 class="cs-tech-engine-card-title">Stan techniczny podczas oględzin</h3>
                    <p class="cs-tech-engine-card-sub">Sprawdziliśmy kluczowe elementy techniczne pojazdu podczas oględzin i jazdy próbnej.</p>
                    <p class="cs-tech-engine-card-sub">Poniżej znajdziesz ich stan oraz nagranie pracy silnika.</p>
                </div>
            </div>
            <div class="cs-tech-list-panel">
                @foreach($techRows as $row)
                @php $status = $techStatusFor($row['key']); @endphp
                @php
                    // Map our internal part keys → Lucide icon names. Keeps one
                    // consistent visual family for all 8 tech-status rows.
                    $techIconMap = [
                        'engine'      => 'cog',
                        'gear'        => 'settings-2',
                        'suspension'  => 'activity',
                        'brakes'      => 'disc-3',
                        'steering'    => 'circle-dot',
                        'ac'          => 'snowflake',
                        'electronics' => 'cpu',
                        'lights'      => 'lightbulb',
                    ];
                    $statusIconMap = ['ok' => 'check', 'warn' => 'alert-triangle', 'fail' => 'x'];
                @endphp
                <div class="cs-tech-list-row">
                    <span class="cs-tech-list-ico" aria-hidden="true">
                        <x-icon :name="$techIconMap[$row['icon']] ?? 'circle'" size="20"/>
                    </span>
                    <span class="cs-tech-list-name">{{ $row['label'] }}</span>
                    <span class="cs-tech-list-status {{ $status['cls'] === 'ok' ? '' : $status['cls'] }}">
                        <span class="check">
                            <x-icon :name="$statusIconMap[$status['cls']] ?? 'check'" size="11"/>
                        </span>
                        {{ $status['label'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT — Nagranie pracy silnika --}}
        <div class="cs-tech-engine-card">
            <div class="cs-tech-engine-card-head">
                <div class="cs-tech-engine-card-ico">
                    <x-icon name="video" size="22"/>
                </div>
                <div class="cs-tech-engine-card-titlewrap">
                    <h3 class="cs-tech-engine-card-title">Nagranie pracy silnika</h3>
                    <p class="cs-tech-engine-card-sub">Krótki film z uruchomienia i pracy silnika nagrany podczas inspekcji.</p>
                </div>
            </div>
            <div class="cs-engine-video-panel">
                @if($ytId)
                    <iframe src="https://www.youtube.com/embed/{{ $ytId }}" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                @elseif($vimId)
                    <iframe src="https://player.vimeo.com/video/{{ $vimId }}" allowfullscreen loading="lazy"></iframe>
                @elseif($car->engine_video_path)
                    <video src="{{ $car->engine_video_file_url }}" controls preload="metadata" playsinline></video>
                @elseif($rawVidUrl)
                    <div class="cs-engine-video-empty">
                        Pełne nagranie dostępne pod adresem:<br>
                        <a href="{{ $rawVidUrl }}" target="_blank" rel="noopener" style="color:#fff;text-decoration:underline">{{ $rawVidUrl }}</a>
                    </div>
                @else
                    <div class="cs-engine-video-empty">Nagranie zostanie dodane wkrótce.</div>
                @endif
            </div>
        </div>
    </div>
    @endif


    {{-- WIDOK 360° POJAZDU — two clickable cards that jump to the gallery's 360° tab.
         Replaces the old in-place Pannellum embed; the interactive viewer still lives
         in the main gallery (lines ~949+) so we reuse csFilterGallery + csScrollGalleryIntoView
         instead of duplicating the iframe. --}}
    @if($car->pano360Image || $car->exteriorPano360Image)
    <div class="cs-pano360-section-card">
        <div class="cs-pano360-section-head">
            <div class="cs-pano360-section-ico">
                <x-icon name="rotate-3d" size="22"/>
            </div>
            <div>
                <h3 class="cs-pano360-section-title">Widok 360° pojazdu</h3>
                <p class="cs-pano360-section-sub">Obejrzyj pojazd z każdej strony. Przesuwaj, obracaj i przybliżaj obraz, aby dokładnie zapoznać się z autem.</p>
            </div>
        </div>
        @php $singleCard = !($car->pano360Image && $car->exteriorPano360Image); @endphp
        <div class="cs-pano360-row{{ $singleCard ? ' single' : '' }}">
            @if($car->exteriorPano360Image)
            <button type="button" class="cs-pano360-card" onclick="csOpenPano360('pano360ext')" aria-label="Otwórz widok 360° z zewnątrz">
                @if($car->exteriorPano360Image->url)
                    <img class="cs-pano360-card-img" src="{{ $car->exteriorPano360Image->url }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                @else
                    <div class="cs-pano360-card-empty">Podgląd niedostępny</div>
                @endif
                <div class="cs-pano360-card-overlay"></div>
                <span class="cs-pano360-card-mark">
                    <x-icon name="map-pin" size="12"/>
                    360° · Z ZEWNĄTRZ
                </span>
                <span class="cs-pano360-card-play">
                    <x-icon name="rotate-3d" size="32"/>
                </span>
                <div class="cs-pano360-card-text">
                    <h4 class="cs-pano360-card-title">360° z zewnątrz</h4>
                    <p class="cs-pano360-card-sub">Obejrzyj auto dookoła</p>
                </div>
            </button>
            @endif
            @if($car->pano360Image)
            <button type="button" class="cs-pano360-card" onclick="csOpenPano360('pano360')" aria-label="Otwórz widok 360° wnętrza">
                @if($car->pano360Image->url)
                    <img class="cs-pano360-card-img" src="{{ $car->pano360Image->url }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                @else
                    <div class="cs-pano360-card-empty">Podgląd niedostępny</div>
                @endif
                <div class="cs-pano360-card-overlay"></div>
                <span class="cs-pano360-card-mark">
                    <x-icon name="armchair" size="12"/>
                    360° · WNĘTRZE
                </span>
                <span class="cs-pano360-card-play">
                    <x-icon name="rotate-3d" size="32"/>
                </span>
                <div class="cs-pano360-card-text">
                    <h4 class="cs-pano360-card-title">360° wnętrza</h4>
                    <p class="cs-pano360-card-sub">Zobacz kabinę kierowcy i przestrzeń pasażerską</p>
                </div>
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- POMIARY LAKIERU + KOŁA I OPONY — two-card row directly below the 360 section.
         Both cards use the same .cs-pt-card system (matches PR #25 tech+engine row).
         Each card stays graceful with a "Brak danych" empty state. --}}
    @php
        $paintMeasurements = collect($car->paint_measurements ?? [])->filter(function ($v) {
            $val = is_array($v) ? ($v['value'] ?? $v[0] ?? null) : $v;
            return (int) preg_replace('/[^0-9]/', '', (string) $val) > 0;
        });
        $hasPaintData = $paintMeasurements->isNotEmpty();
        $hasTireData  = $car->tireSets && $car->tireSets->count() > 0;
    @endphp
    @if($hasPaintData || $hasTireData)
    <div class="cs-pt-row">

        {{-- LEFT — Pomiary grubości lakieru --}}
        <div class="cs-pt-card">
            <div class="cs-pt-card-head">
                <div class="cs-pt-card-ico" aria-hidden="true">
                    <x-icon name="paintbrush" size="22"/>
                </div>
                <div>
                    <h3 class="cs-pt-card-title">Pomiary grubości lakieru</h3>
                    <p class="cs-pt-card-sub">Pomiary wykonane profesjonalnym czujnikiem podczas oględzin.</p>
                </div>
            </div>
            @if($hasPaintData)
                @php
                    $paintPanelNames = [
                        0 => 'Dach', 1 => 'Maska', 2 => 'Błotnik P-L', 3 => 'Błotnik P-P',
                        4 => 'Drzwi P-L', 5 => 'Drzwi P-P', 6 => 'Błotnik T-L', 7 => 'Błotnik T-P',
                        8 => 'Drzwi T-L', 9 => 'Drzwi T-P', 10 => 'Klapa bagażnika',
                        11 => 'Zderzak P', 12 => 'Zderzak T', 13 => 'Próg lewy', 14 => 'Próg prawy',
                    ];
                @endphp
                <div class="cs-pt-paint-grid">
                    @foreach($paintMeasurements as $panel => $value)
                    @php
                        $val = is_array($value) ? ($value['value'] ?? $value[0] ?? '') : $value;
                        $numVal = (int) preg_replace('/[^0-9]/', '', (string) $val);
                        $panelLabel = is_array($value) && isset($value['area'])
                            ? $value['area']
                            : (is_numeric($panel) ? ($paintPanelNames[$panel] ?? 'Panel '.($panel + 1)) : $panel);
                        // Reference thresholds:
                        //   90–150  µm → green  (fabryczna powłoka)
                        //   150–300 µm → orange (ponownie lakierowany)
                        //   >300    µm → red    (szpachla / naprawa)
                        // Values below 90 µm are not in the reference colour map; treat
                        // them as factory-range (green) since they represent an unusually
                        // thin original coat rather than damage.
                        $cls = $numVal > 300 ? 'bad' : ($numVal > 150 ? 'warn' : 'ok');
                    @endphp
                    <div class="cs-pt-paint-cell {{ $cls }}">
                        <span class="cs-pt-paint-cell-label">{{ $panelLabel }}</span>
                        <span class="cs-pt-paint-cell-value">
                            <span class="dot" aria-hidden="true"></span>
                            <span class="num">{{ $numVal }}<span class="unit">µm</span></span>
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="cs-pt-paint-legend">
                    <span class="cs-pt-paint-legend-item"><span class="sw" style="background:#16a34a"></span>Lakier fabryczny (90–150 µm)</span>
                    <span class="cs-pt-paint-legend-item"><span class="sw" style="background:#f59e0b"></span>Ponownie lakierowany (150–300 µm)</span>
                    <span class="cs-pt-paint-legend-item"><span class="sw" style="background:#dc2626"></span>Naprawa / szpachla (powyżej 300 µm)</span>
                </div>
                @if($car->has_certicheck)
                <a href="{{ route('catalog.certicheck', $car->slug) }}" class="cs-pt-paint-cta">
                    Zobacz szczegółowy raport
                    <x-icon name="arrow-right" size="14"/>
                </a>
                @endif
            @else
                <div class="cs-pt-empty">Brak danych o pomiarach lakieru.</div>
            @endif
        </div>

        {{-- RIGHT — Koła i opony --}}
        <div class="cs-pt-card">
            <div class="cs-pt-card-head">
                <div class="cs-pt-card-ico" aria-hidden="true">
                    <x-icon name="circle-dot" size="22"/>
                </div>
                <div>
                    <h3 class="cs-pt-card-title">Koła i opony</h3>
                    <p class="cs-pt-card-sub">Informacje o stanie opon i felg.</p>
                </div>
            </div>
            @if($hasTireData)
                @php
                    // Mounted set first, fall back to the first set otherwise.
                    $primarySet = $car->tireSets->firstWhere('is_mounted', true) ?? $car->tireSets->first();
                    $tiresByPos = $primarySet->tires->keyBy('position');
                    $positions  = [
                        'front_left'  => 'Przód lewy',
                        'front_right' => 'Przód prawy',
                        'rear_left'   => 'Tył lewy',
                        'rear_right'  => 'Tył prawy',
                    ];
                    // Per-tire status from condition[] → ok / warn / bad enum.
                    $resolveWheelStatus = function ($t) {
                        if (!$t) return 'empty';
                        $cond = is_array($t->condition) ? $t->condition : [];
                        if (count($cond) === 0) return 'ok';
                        $joined = mb_strtolower(implode(' ', $cond));
                        if (str_contains($joined, 'zużyt') || str_contains($joined, 'uszkodz') || str_contains($joined, 'wymian') || str_contains($joined, 'pęknięt')) return 'bad';
                        return 'warn';
                    };
                    $anyIssue = false;
                    foreach ($tiresByPos as $t) {
                        $s = $resolveWheelStatus($t);
                        if ($s === 'warn' || $s === 'bad') { $anyIssue = true; break; }
                    }
                @endphp
                <div class="cs-pt-tire-set">
                    <div class="cs-pt-tire-set-head">
                        <span>{{ $primarySet->set_number ?? 1 }}. Komplet</span>
                        @if($primarySet->is_mounted)<span class="mount">(zamontowane)</span>@endif
                        <span class="info" aria-hidden="true" title="Dane pomiarowe z inspekcji pojazdu">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </span>
                    </div>

                    {{-- Four stacked rows — one per wheel position. No tire/wheel image. --}}
                    @foreach($positions as $key => $label)
                    @php
                        $t = $tiresByPos->get($key);
                        $statusKey = $resolveWheelStatus($t);
                        // tread_depth is stored as a single numeric value; render with the
                        // Polish decimal separator (6,0 mm) when there's a fractional part.
                        $treadDisplay = null;
                        if ($t && $t->tread_depth !== null) {
                            $td = (float) $t->tread_depth;
                            $treadDisplay = (floor($td) == $td)
                                ? number_format($td, 1, ',', ' ') . ' mm'
                                : number_format($td, 1, ',', ' ') . ' mm';
                        }
                    @endphp
                    <div class="cs-pt-tire-row">
                        <div class="cs-pt-tire-row-info">
                            <div class="cs-pt-tire-row-pos">{{ $label }}</div>
                            @if($t)
                                @if($primarySet->tire_type)
                                    <div class="cs-pt-tire-row-spec">{{ $primarySet->tire_type }}</div>
                                @endif
                                @if($t->condition && is_array($t->condition) && count($t->condition))
                                    <div class="cs-pt-tire-row-model">{{ implode(', ', $t->condition) }}</div>
                                @endif
                            @else
                                <div class="cs-pt-tire-row-empty">Brak danych</div>
                            @endif
                        </div>
                        @if($treadDisplay !== null)
                            <span class="cs-pt-tire-tread-pill {{ $statusKey === 'ok' || $statusKey === 'empty' ? '' : $statusKey }}">{{ $treadDisplay }}</span>
                        @else
                            <span class="cs-pt-tire-tread-pill empty">— mm</span>
                        @endif
                    </div>
                    @endforeach

                    {{-- Bottom summary: rim/felga left, overall status right --}}
                    <div class="cs-pt-tire-summary">
                        <span class="cs-pt-tire-summary-left">
                            <span class="ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                            </span>
                            {{ $primarySet->rim ?: 'Brak informacji o felgach' }}
                        </span>
                        <span class="cs-pt-tire-summary-right {{ $anyIssue ? 'warn' : '' }}">
                            <span class="ico" aria-hidden="true">
                                @if($anyIssue)
                                    <svg viewBox="0 0 24 24"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                @endif
                            </span>
                            {{ $anyIssue ? 'Wymaga uwagi' : 'Stan bardzo dobry' }}
                        </span>
                    </div>

                    @if($primarySet->notes)
                    <div style="margin-top:8px;padding:9px 12px;background:#fef3c7;border:1px solid #fde68a;color:#92400e;border-radius:8px;font-size:12px;line-height:1.5">{{ $primarySet->notes }}</div>
                    @endif
                </div>
            @else
                <div class="cs-pt-empty">Brak danych o stanie opon.</div>
            @endif
        </div>

    </div>
    @endif

    {{-- =================== JAK WYGLĄDA ZAKUP =================== --}}
    {{-- Dark premium section: 5 step cards + benefits strip + CTA.
         Real component, dynamic CTA href falls back to the canonical
         CertiCars phone number used everywhere else on the site. --}}
    <section class="cs-jwz" id="jak-wyglada-zakup">
        <div class="cs-jwz-head">
            <div class="cs-jwz-kicker">Jak wygląda zakup</div>
            <h2>Prosty proces. Więcej spokoju przed zakupem.</h2>
            <p>Od wyboru samochodu do odbioru auta prowadzimy Cię krok po kroku — jasno, konkretnie i bez zbędnych komplikacji.</p>
        </div>

        <div class="cs-jwz-steps">
            @php
                $jwzSteps = [
                    ['search',         '01', 'Wybierasz samochód',           'Przeglądasz ofertę, zdjęcia, dane techniczne, wyposażenie oraz opis stanu auta. Przy wybranych pojazdach dostępny jest dodatkowy raport CertiCheck.'],
                    ['message-square', '02', 'Kontaktujesz się z nami',      'Potwierdzamy dostępność auta, odpowiadamy na pytania i umawiamy dogodny termin oględzin.'],
                    ['clipboard-check','03', 'Oglądasz i sprawdzasz auto',   'Na miejscu możesz dokładnie obejrzeć samochód, odbyć jazdę próbną oraz sprawdzić auto przed zakupem — na stacji diagnostycznej lub w wybranym serwisie.'],
                    ['file-text',      '04', 'Finalizujesz zakup',           'Wyjaśniamy formalności, dokumenty oraz koszty. Wiesz, co jest w cenie auta i co pozostaje po stronie kupującego.'],
                    ['truck',          '05', 'Możliwy transport auta',       'Po wcześniejszym ustaleniu warunków i kosztów możemy przywieźć samochód pod wskazany adres.'],
                ];
            @endphp
            @foreach($jwzSteps as [$ico, $num, $title, $desc])
                <div class="cs-jwz-step">
                    <div class="cs-jwz-step-head">
                        <span class="cs-jwz-step-ico" aria-hidden="true"><x-icon :name="$ico" size="20" :strokeWidth="1.8"/></span>
                        <span class="cs-jwz-step-num">{{ $num }}</span>
                    </div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        <div class="cs-jwz-benefits">
            @php
                $jwzBenefits = [
                    ['shield-check', 'Jasne zasady'],
                    ['wallet',       'Brak ukrytych kosztów'],
                    ['search',       'Możliwość sprawdzenia auta'],
                    ['phone-call',   'Jesteśmy do Twojej dyspozycji'],
                ];
            @endphp
            @foreach($jwzBenefits as [$ico, $label])
                <div class="cs-jwz-benefit">
                    <span class="cs-jwz-benefit-ico" aria-hidden="true"><x-icon :name="$ico" size="15" :strokeWidth="2"/></span>
                    {{ $label }}
                </div>
            @endforeach
        </div>

        <div class="cs-jwz-cta-wrap">
            <a class="cs-jwz-cta" href="tel:+48515440623">
                <x-icon name="phone" size="17" :strokeWidth="2.2"/>
                Zadzwoń i zapytaj o auto
            </a>
        </div>
    </section>

    {{-- =================== CERTICHECK SECTION (single car) =================== --}}
    {{-- Left content + right 2×2 feature cards. Buttons point to the
         existing catalog + about routes, no dead URLs. --}}
    <section class="cs-cc" id="certicheck-info">
        <div class="cs-cc-grid">
            <div class="cs-cc-left">
                <div class="cs-cc-kicker">CertiCheck — dla wybranych aut</div>
                <h2>Wiesz więcej<br>przed przyjazdem</h2>
                <p>Przy wybranych autach przygotowujemy rozszerzony opis CertiCheck z dodatkowymi informacjami o stanie pojazdu, lakierze, śladach użytkowania i dokumentach. Dzięki temu łatwiej oceniasz auto jeszcze przed wizytą.</p>
                <div class="cs-cc-ctas">
                    <a class="cs-cc-cta-primary" href="{{ route('catalog') }}">
                        <x-icon name="search" size="15" :strokeWidth="2.2"/>
                        Zobacz auta z CertiCheck
                    </a>
                    <a class="cs-cc-cta-secondary" href="{{ route('about') }}">
                        Jak działa CertiCheck?
                        <x-icon name="arrow-right" size="14" :strokeWidth="2.2"/>
                    </a>
                </div>
                <div class="cs-cc-info">
                    <span class="cs-cc-info-ico" aria-hidden="true"><x-icon name="shield-check" size="13" :strokeWidth="2"/></span>
                    CertiCheck dotyczy wybranych pojazdów w naszej ofercie.
                </div>
            </div>

            <div class="cs-cc-cards">
                @php
                    $ccCards = [
                        ['scan-line',     'Pomiary lakieru',   'Wykryjemy ponowne malowania i grubsze powłoki w punktach kontrolnych.'],
                        ['wrench',        'Stan techniczny',   'Sprawdzamy kluczowe elementy mechaniczne i eksploatacyjne pojazdu.'],
                        ['search',        'Ślady użytkowania', 'Wskazujemy widoczne ślady eksploatacji i ich dokładną lokalizację.'],
                        ['file-text',     'Raport PDF',        'Czytelne podsumowanie ze zdjęciami i danymi do pobrania.'],
                    ];
                @endphp
                @foreach($ccCards as [$ico, $title, $desc])
                    <div class="cs-cc-card">
                        <div class="cs-cc-card-ico" aria-hidden="true"><x-icon :name="$ico" size="20" :strokeWidth="1.8"/></div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                        <span class="cs-cc-card-arrow" aria-hidden="true"><x-icon name="arrow-up-right" size="13" :strokeWidth="2.4"/></span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PODOBNE POJAZDY — wrapped in the same .cs-related-section card system as
         the other redesigned sections (blue icon tile, title, subtitle, content
         container). Cards use the global .vcard system + a new fuel badge. --}}
    @if($relatedCars->count())
    <div class="cs-related-section">
        <div class="cs-related-head">
            <div class="cs-related-head-left">
                <div class="cs-related-head-ico" aria-hidden="true">
                    <x-icon name="car" size="22"/>
                </div>
                <div>
                    <h3 class="cs-related-head-title">Podobne pojazdy</h3>
                    <p class="cs-related-head-sub">Inne samochody, które mogą Cię zainteresować.</p>
                </div>
            </div>
            <div class="cs-related-controls">
                <a href="{{ route('catalog') }}" class="cs-related-all">
                    Zobacz wszystkie
                    <x-icon name="arrow-right" size="14"/>
                </a>
                <div class="cs-related-nav">
                    <button type="button" onclick="csRelScroll(-1)" aria-label="Przewiń w lewo">
                        <x-icon name="chevron-left" size="18"/>
                    </button>
                    <button type="button" onclick="csRelScroll(1)" aria-label="Przewiń w prawo">
                        <x-icon name="chevron-right" size="18"/>
                    </button>
                </div>
            </div>
        </div>
        <div class="cs-related-grid" id="csRelatedGrid">
            @foreach($relatedCars as $relCar)
            <a href="{{ route('catalog.show', $relCar) }}" class="vcard">
                <div class="vcard-img">
                    @if($relCar->primaryImage)
                        <img src="{{ $relCar->primaryImage->url }}" alt="{{ $relCar->primaryImage->alt }}" loading="lazy" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                    @else
                        <div class="vcard-placeholder"><svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg></div>
                    @endif
                    {{-- Fuel badge top-left (matches reference layout) --}}
                    @if($relCar->fuel_type)
                    <span class="vcard-fuel-badge">
                        <svg viewBox="0 0 24 24"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/><path d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2 2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/></svg>
                        {{ \App\Helpers\CarLabels::fuelType($relCar->fuel_type) ?? $relCar->fuel_type }}
                    </span>
                    @endif
                    @if($relCar->is_featured)<div class="vcard-badge">Wyróżnione</div>@endif
                    {{-- Favorite heart (top-right) — same handler as the catalog --}}
                    <button class="lcard-fav" data-id="{{ $relCar->id }}" aria-label="Dodaj do ulubionych" onclick="event.preventDefault();event.stopPropagation();toggleFav(event,{{ $relCar->id }})">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>
                <div class="vcard-body">
                    <div class="vcard-title">{{ $relCar->title }}</div>
                    <div class="vcard-specs">
                        @if($relCar->first_registration)<span><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> {{ $relCar->first_registration }}</span>@endif
                        @if($relCar->mileage)<span>{{ number_format((float) $relCar->mileage,0,'',' ') }} km</span>@endif
                        @if($relCar->transmission)<span>{{ \App\Helpers\CarLabels::transmission($relCar->transmission) ?? $relCar->transmission }}</span>@endif
                        @if($relCar->power_hp)<span>{{ $relCar->power_hp }} KM</span>@endif
                    </div>
                    <div class="vcard-bottom">
                        <div class="vcard-price">{{ $relCar->formatted_price }}</div>
                        <span class="vcard-link">Zobacz szczegóły <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Legal disclaimer bar — sits below all single-car content. --}}
    <div class="cs-legal-bar">
        <div class="cs-legal-bar-ico" aria-hidden="true">
            <x-icon name="shield" size="14"/>
        </div>
        <p>
            <strong>Informacje zawarte na tej stronie</strong> zostały przygotowane z najwyższą starannością.
            Nie stanowią jednak oferty handlowej w rozumieniu art. 66 §1 Kodeksu cywilnego.
        </p>
    </div>

</div>
</div>

<!-- INQUIRY MODAL -->
<div id="csInquiryOverlay" class="cs-inquiry-overlay" onclick="if(event.target===this)csCloseInquiry()">
    <div class="cs-inquiry-panel" id="csInquiryPanel">
        <button type="button" class="cs-inquiry-close" onclick="csCloseInquiry()">&times;</button>
        <h3 id="csInquiryTitle">Napisz wiadomość</h3>
        <p id="csInquiryCarInfo" class="cs-inquiry-car">{{ $car->brand?->name }} {{ $car->model }} {{ $car->year }}</p>
        <form id="csInquiryForm" onsubmit="return csSubmitInquiry(event)">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="car_id" value="{{ $car->id }}">
            <input type="hidden" id="csInquiryType" name="type" value="general">
            <input type="hidden" id="csInquiryFormSource" name="form_source" value="">
            <input type="hidden" id="csInquirySourcePage" name="source_page" value="">
            <input type="hidden" id="csInquiryReferrer" name="referrer" value="">
            <input type="hidden" id="csInquiryUtmSource" name="utm_source" value="">
            <input type="hidden" id="csInquiryUtmMedium" name="utm_medium" value="">
            <input type="hidden" id="csInquiryUtmCampaign" name="utm_campaign" value="">
            <input type="hidden" id="csInquiryUtmContent" name="utm_content" value="">
            <input type="hidden" id="csInquiryUtmTerm" name="utm_term" value="">
            <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
            <div class="cs-inquiry-field">
                <label>Imię i nazwisko *</label>
                <input type="text" name="name" required placeholder="Jan Kowalski" maxlength="100">
            </div>
            <div class="cs-inquiry-field">
                <label>Numer telefonu *</label>
                <input type="tel" name="phone" required placeholder="+48 600 000 000" maxlength="30">
            </div>
            <div class="cs-inquiry-field">
                <label>E-mail (opcjonalnie)</label>
                <input type="email" name="email" placeholder="jan@example.pl" maxlength="200">
            </div>
            <div class="cs-inquiry-field">
                <label id="csInquiryMsgLabel">Wiadomość</label>
                <textarea name="message" rows="3" placeholder="Twoje pytanie..." maxlength="2000"></textarea>
            </div>
            <div class="cs-inquiry-consent" id="csInquiryConsentBox">
                <input type="checkbox" id="csInquiryConsent" name="consent" value="1" required>
                <label for="csInquiryConsent">Wyrażam zgodę na przetwarzanie moich danych osobowych w celu obsługi zapytania.<span class="req">*</span></label>
            </div>
            <button type="submit" class="cs-inquiry-submit" id="csInquirySubmitBtn">
                <span id="csInquirySubmitText">Wyślij zapytanie</span>
                <span id="csInquirySubmitLoader" style="display:none">Wysyłanie...</span>
            </button>
            <p class="cs-inquiry-legal">Administratorem danych jest CertiCars. Dane będą wykorzystywane wyłącznie w celu obsługi tego zapytania.</p>
        </form>
        <div id="csInquirySuccess" style="display:none" class="cs-inquiry-success">
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#10b981" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <h4>Dziękujemy!</h4>
            <p>Odezwiemy się jak najszybciej.</p>
            <button type="button" onclick="csCloseInquiry()">Zamknij</button>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="cs-lightbox" id="csLightbox" onclick="if(event.target===this)csCloseLb()">
    <button class="cs-lightbox-close" onclick="csCloseLb()"><svg viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    <button class="cs-lightbox-nav cs-lightbox-prev" onclick="csLbNav(-1)"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
    <img class="cs-lightbox-img" id="csLbImg" src="" alt="">
    <button class="cs-lightbox-nav cs-lightbox-next" onclick="csLbNav(1)"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
    <div class="cs-lightbox-counter" id="csLbCounter"></div>
</div>

{{-- STICKY MOBILE CTA --}}
<div class="cs-sticky-cta" id="csStickyBar">
    <a href="tel:+48515440623" class="cs-sticky-call">
        <x-icon name="phone" size="16"/>
        Zadzwoń
    </a>
    <button type="button" class="cs-sticky-msg" onclick="csOpenInquiry('general','sticky_mobile_cta')">
        <x-icon name="mail" size="16"/>
        Napisz
    </button>
</div>


@push('scripts')
<script>
function csSelImg(el,n){
    const m=document.getElementById('csMainImg');if(!m)return;
    m.src=el.src;m.alt=el.alt;
    document.getElementById('csImgCounter').textContent=n;
    document.querySelectorAll('.cs-thumb').forEach(t=>t.classList.remove('active'));
    el.classList.add('active');
}
function csGalleryPrev(){
    const thumbs=Array.from(document.querySelectorAll('#csGalleryThumbs .cs-thumb:not([style*="display:none"]):not([data-hidden])'));
    if(!thumbs.length)return;
    let idx=thumbs.findIndex(t=>t.classList.contains('active'));
    idx=(idx<=0)?thumbs.length-1:idx-1;
    csSelImg(thumbs[idx],parseInt(thumbs[idx].dataset.idx)+1);
}
function csGalleryNext(){
    const thumbs=Array.from(document.querySelectorAll('#csGalleryThumbs .cs-thumb:not([style*="display:none"]):not([data-hidden])'));
    if(!thumbs.length)return;
    let idx=thumbs.findIndex(t=>t.classList.contains('active'));
    idx=(idx>=thumbs.length-1)?0:idx+1;
    csSelImg(thumbs[idx],parseInt(thumbs[idx].dataset.idx)+1);
}
window.CAR_GALLERY=@json($galleryList->map(fn($i)=>['src'=>$i->url,'caption'=>$i->alt])->values());
window.CAR_DAMAGE_GALLERY=@json($damageImgList->map(fn($i)=>['src'=>$i->url,'caption'=>$i->alt])->values());
window.CAR_ALL_GALLERY=[...CAR_GALLERY,...CAR_DAMAGE_GALLERY];
window.openCarGallery=(i)=>{csOpenLb(i||0)};

// ==== iOS-SAFE BODY SCROLL LOCK ====
// document.body.style.overflow='hidden' is broken on iOS Safari — the page still
// scrolls behind the modal. The reliable cross-browser pattern is to freeze the
// body at its current scroll position with position:fixed + negative top, then
// restore the exact scroll position on unlock. The lock counter lets multiple
// modals (e.g. inquiry + lightbox) stack without one closing unlocking the other.
//
// CRITICAL: every csLockBody() MUST be paired with exactly one csUnlockBody().
// A double-tap on the gallery image used to fire two open paths, locking the
// depth to 2; one close left it at 1 and the page stayed frozen. The open
// functions below now guard against that by only locking when the lightbox
// transitions from closed → open. csForceReleaseLock() is the emergency exit
// for any path we haven't anticipated (bfcache restore, leftover state).
var _csLockY=0, _csLockDepth=0, _csLockPrev={};
function csLockBody(){
    if(_csLockDepth++>0)return;
    _csLockY=window.pageYOffset||document.documentElement.scrollTop||0;
    var b=document.body, s=b.style;
    _csLockPrev={position:s.position,top:s.top,left:s.left,right:s.right,width:s.width,overflow:s.overflow};
    s.position='fixed';
    s.top=(-_csLockY)+'px';
    s.left='0';
    s.right='0';
    s.width='100%';
    s.overflow='hidden';
}
function csUnlockBody(){
    if(_csLockDepth<=0)return;
    if(--_csLockDepth>0)return;
    csRestoreBodyStyles();
}
function csRestoreBodyStyles(){
    var b=document.body, s=b.style;
    s.position=_csLockPrev.position||'';
    s.top=_csLockPrev.top||'';
    s.left=_csLockPrev.left||'';
    s.right=_csLockPrev.right||'';
    s.width=_csLockPrev.width||'';
    s.overflow=_csLockPrev.overflow||'';
    window.scrollTo(0,_csLockY);
}
// Emergency cleanup: nukes the lock state regardless of how many times
// csLockBody was called. Used by csCloseLb defensively + by the pageshow
// handler so a back/forward navigation never leaves the page frozen.
function csForceReleaseLock(){
    if(_csLockDepth<=0)return;
    _csLockDepth=0;
    csRestoreBodyStyles();
}

// ==== FULLSCREEN LIGHTBOX ====
var _lbIdx=0;
function csOpenLb(idx){
    _lbIdx=idx||0;
    const lb=document.getElementById('csLightbox');
    const img=document.getElementById('csLbImg');
    if(!lb||!CAR_ALL_GALLERY.length)return;
    img.src=CAR_ALL_GALLERY[_lbIdx].src;
    img.alt=CAR_ALL_GALLERY[_lbIdx].caption||'';
    document.getElementById('csLbCounter').textContent=(_lbIdx+1)+' / '+CAR_ALL_GALLERY.length;
    // Idempotent: only acquire a body lock on the closed→open transition.
    // Mobile double-tap + delayed-click can fire this twice in a row; the
    // second call must be a no-op for scroll-lock accounting, otherwise
    // a single close leaves depth=1 and the body stays frozen.
    if(!lb.classList.contains('open')){
        lb.classList.add('open');
        csLockBody();
    }
}
function csCloseLb(){
    const lb=document.getElementById('csLightbox');
    if(lb && lb.classList.contains('open')){
        lb.classList.remove('open');
        csUnlockBody();
    } else {
        // Lightbox already closed but we may still hold a stale lock from a
        // previous mis-paired open. Release everything so the page is
        // guaranteed scrollable.
        csForceReleaseLock();
    }
}
function csLbNav(dir){
    _lbIdx=(_lbIdx+dir+CAR_ALL_GALLERY.length)%CAR_ALL_GALLERY.length;
    document.getElementById('csLbImg').src=CAR_ALL_GALLERY[_lbIdx].src;
    document.getElementById('csLbImg').alt=CAR_ALL_GALLERY[_lbIdx].caption||'';
    document.getElementById('csLbCounter').textContent=(_lbIdx+1)+' / '+CAR_ALL_GALLERY.length;
}
document.addEventListener('keydown',function(e){
    if(!document.getElementById('csLightbox').classList.contains('open'))return;
    if(e.key==='Escape')csCloseLb();
    if(e.key==='ArrowLeft')csLbNav(-1);
    if(e.key==='ArrowRight')csLbNav(1);
});
// bfcache restore safety: if a user opens the lightbox, leaves the tab or
// navigates away, then comes back, the browser may restore the DOM with the
// body still position:fixed from the prior lock. Force-release on pageshow
// so the page is always scrollable when it becomes visible again.
window.addEventListener('pageshow',function(){
    csForceReleaseLock();
    var lb=document.getElementById('csLightbox');
    if(lb) lb.classList.remove('open');
});
// Click main image → open lightbox
document.addEventListener('DOMContentLoaded',function(){
    var mainImg=document.getElementById('csMainImg');
    if(mainImg) mainImg.style.cursor='zoom-in';
    if(mainImg) mainImg.addEventListener('click',function(){
        var thumbs=Array.from(document.querySelectorAll('#csGalleryThumbs .cs-thumb:not([data-hidden])'));
        var active=thumbs.findIndex(t=>t.classList.contains('active'));
        csOpenLb(active>=0?active:0);
    });
});
// Lightbox swipe — horizontal only
(function(){
    var lb=document.getElementById('csLightbox'),sx=0,sy=0;
    if(!lb)return;
    lb.addEventListener('touchstart',function(e){sx=e.touches[0].clientX;sy=e.touches[0].clientY;},{passive:true});
    lb.addEventListener('touchend',function(e){
        var dx=e.changedTouches[0].clientX-sx;
        var dy=e.changedTouches[0].clientY-sy;
        if(Math.abs(dx)>50 && Math.abs(dx)>Math.abs(dy)*1.5){dx<0?csLbNav(1):csLbNav(-1);}
    });
})();

// ==== GALLERY SWIPE — horizontal only, ignore vertical scrolls ====
(function(){
    var stage=document.querySelector('.cs-gallery-stage'),sx=0,sy=0;
    if(!stage)return;
    stage.addEventListener('touchstart',function(e){sx=e.touches[0].clientX;sy=e.touches[0].clientY;},{passive:true});
    stage.addEventListener('touchend',function(e){
        var dx=e.changedTouches[0].clientX-sx;
        var dy=e.changedTouches[0].clientY-sy;
        if(Math.abs(dx)>50 && Math.abs(dx)>Math.abs(dy)*1.5){dx<0?csGalleryNext():csGalleryPrev();}
    });
})();

// ==== STICKY CTA BAR ====
(function(){
    var bar=document.getElementById('csStickyBar');
    var mobCta=document.querySelector('.cs-mob-cta');
    if(!bar)return;
    var check=function(){
        if(window.innerWidth>1024){bar.classList.remove('visible');return;}
        var ref=mobCta||document.querySelector('.cs-price-section');
        if(!ref){bar.classList.add('visible');return;}
        var rect=ref.getBoundingClientRect();
        bar.classList.toggle('visible',rect.bottom<0);
    };
    window.addEventListener('scroll',check,{passive:true});
    window.addEventListener('resize',check,{passive:true});
    check();
})();

// ==== RELATED CARS SCROLL ====
function csRelScroll(dir){
    const g=document.getElementById('csRelatedGrid');
    if(!g)return;
    const card=g.querySelector('.vcard');
    if(!card)return;
    const w=card.offsetWidth+20;
    g.scrollBy({left:dir*w,behavior:'smooth'});
}

// ==== 360° SECTION (panorama embed — Pannellum) ====
function cs360InitPano(){
    document.querySelectorAll('.cs-pano360-embed').forEach(function(embed){
        const container = embed.querySelector('[data-pano-src]');
        if(!container || container.dataset.panoReady) return;
        container.dataset.panoReady = '1';
        const src = container.dataset.panoSrc;
        if(!src) return; // no image — do not init (prevents pannellum loading its own demo images)
        // Lazy-load Pannellum
        if(!document.querySelector('link[data-pannellum]')){
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
            css.dataset.pannellum = '1';
            document.head.appendChild(css);
        }
        var retries = 0;
        const onReady = () => {
            if(typeof pannellum === 'undefined'){ if(++retries > 200) { console.warn('Pannellum CDN failed to load'); return; } setTimeout(onReady, 50); return; }
            pannellum.viewer(container, {
                type: 'equirectangular',
                panorama: src,
                autoLoad: true,
                showZoomCtrl: true,
                showFullscreenCtrl: true,
                compass: false,
                hfov: 100,
                minHfov: 50,
                maxHfov: 120,
                autoRotate: -2,
                autoRotateInactivityDelay: 3000,
            });
            // Hide hint after interaction
            const hint = embed.querySelector('.cs-pano360-hint');
            if(hint) container.addEventListener('mousedown', ()=>{ hint.style.opacity='0'; setTimeout(()=>hint.remove(),300); }, {once:true});
        };
        if(typeof pannellum === 'undefined'){
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
            s.onerror = function(){ console.warn('Pannellum script failed to load'); };
            s.onload = onReady;
            document.body.appendChild(s);
        } else { onReady(); }
    });
}
// Auto-init panorama on page load if 360 section is visible
document.addEventListener('DOMContentLoaded', function(){ if(document.querySelector('.cs-pano360-embed')) cs360InitPano(); });

// Cards in the "Widok 360° pojazdu" section: scroll up to the main gallery and
// click the matching 360° tab. Reuses the existing csFilterGallery so the
// Pannellum viewer lives in exactly one place.
function csOpenPano360(filter){
    var tab = document.querySelector('[data-gallery-filter="' + filter + '"]');
    var gallery = document.querySelector('.cs-gallery-stage');
    if (gallery) {
        gallery.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    if (tab && typeof csFilterGallery === 'function') {
        // Delay so the scroll has time to start before the tab swap (Pannellum
        // initialises lazily and we want it visible when it boots).
        setTimeout(function(){ csFilterGallery(tab, filter); }, 200);
    }
}

// Gallery tab filtering (COS style)
function csFilterGallery(btn,filter){
    if(btn.classList.contains('disabled'))return;
    document.querySelectorAll('.cs-gallery-tab').forEach(t=>{t.classList.remove('active');t.setAttribute('aria-selected','false')});
    btn.classList.add('active');
    btn.setAttribute('aria-selected','true');

    // Toggle gallery panes (active class controls visibility — see .cs-gallery-main:not(.active){display:none})
    const std     = document.getElementById('csGalleryStandard');
    const pano    = document.getElementById('csPano360');
    const panoExt = document.getElementById('csPano360ext');
    const thumbsWrap = document.getElementById('csGalleryThumbs');
    [std, pano, panoExt].forEach(el => el && el.classList.remove('active'));
    if(filter === 'pano360' && pano) pano.classList.add('active');
    else if(filter === 'pano360ext' && panoExt) panoExt.classList.add('active');
    else if(std) std.classList.add('active');
    if(thumbsWrap) thumbsWrap.style.display = (filter==='pano360'||filter==='pano360ext') ? 'none' : '';

    if(filter==='pano360'){ if(window.csPano360Init)csPano360Init(); return; }
    if(filter==='pano360ext'){ if(window.csPano360ExtInit)csPano360ExtInit(); return; }

    if(filter==='video'){
        var vid=document.querySelector('[data-panel-engine-video]');
        if(vid)vid.scrollIntoView({behavior:'smooth',block:'center'});
        return;
    }
    if(filter==='paint'){
        var paintSec=document.querySelector('.cs-paint-grid');
        if(paintSec)paintSec.scrollIntoView({behavior:'smooth',block:'center'});
        return;
    }
    if(filter==='documents'){
        var docSec=document.querySelector('[data-panel-documents]');
        if(docSec)docSec.scrollIntoView({behavior:'smooth',block:'center'});
        else{ var dataSec=document.querySelector('.cs-data-columns'); if(dataSec)dataSec.scrollIntoView({behavior:'smooth',block:'center'}); }
        return;
    }

    const thumbs=document.querySelectorAll('#csGalleryThumbs .cs-thumb');
    let first=null;
    thumbs.forEach(t=>{
        const type=t.dataset.type;
        let show=false;
        if(filter==='all')show=true;
        else if(filter==='gallery')show=(type==='gallery');
        else if(filter==='damage')show=(type==='damage');
        if(show){t.removeAttribute('data-hidden');if(!first)first=t;}
        else{t.setAttribute('data-hidden','');t.classList.remove('active');}
    });
    if(first && !document.querySelector('#csGalleryThumbs .cs-thumb.active:not([data-hidden])')){
        first.classList.add('active');
        csSelImg(first,parseInt(first.dataset.idx)+1);
    }
    const visibleCount=document.querySelectorAll('#csGalleryThumbs .cs-thumb:not([data-hidden])').length;
    const totalEl=document.getElementById('csImgTotal');
    if(totalEl)totalEl.textContent=visibleCount;
}

// ==== 360° PANORAMA VIEWER (gallery tab) ====
@if($car->pano360Image)
window.csPano360Init = (function(){
    let initialized = false;
    return function(){
        if(initialized) return;
        initialized = true;
        const container = document.getElementById('csPanoramaContainer');
        if(!container) return;
        const src = container.dataset.panoSrc;

        // Lazy-load Pannellum from CDN
        if(!document.querySelector('link[data-pannellum]')){
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
            css.dataset.pannellum = '1';
            document.head.appendChild(css);
        }
        var retries = 0;
        const onReady = () => {
            if(typeof pannellum === 'undefined'){ if(++retries > 200) { console.warn('Pannellum CDN failed to load'); return; } setTimeout(onReady, 50); return; }
            pannellum.viewer(container, {
                type: 'equirectangular',
                panorama: src,
                autoLoad: true,
                showZoomCtrl: true,
                showFullscreenCtrl: true,
                compass: false,
                hfov: 100,
                minHfov: 50,
                maxHfov: 120,
                autoRotate: -2,
                autoRotateInactivityDelay: 3000,
            });
        };
        if(typeof pannellum === 'undefined'){
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
            s.onerror = function(){ console.warn('Pannellum script failed to load'); };
            s.onload = onReady;
            document.body.appendChild(s);
        } else {
            onReady();
        }
    };
})();
@endif

// ==== 360° PANORAMA VIEWER (exterior gallery tab) ====
@if($car->exteriorPano360Image)
window.csPano360ExtInit = (function(){
    let initialized = false;
    return function(){
        if(initialized) return;
        initialized = true;
        const container = document.getElementById('csPanoramaExtContainer');
        if(!container) return;
        const src = container.dataset.panoSrc;

        if(!document.querySelector('link[data-pannellum]')){
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
            css.dataset.pannellum = '1';
            document.head.appendChild(css);
        }
        var retries = 0;
        const onReady = () => {
            if(typeof pannellum === 'undefined'){ if(++retries > 200) { console.warn('Pannellum CDN failed to load'); return; } setTimeout(onReady, 50); return; }
            pannellum.viewer(container, {
                type: 'equirectangular',
                panorama: src,
                autoLoad: true,
                showZoomCtrl: true,
                showFullscreenCtrl: true,
                compass: false,
                hfov: 100,
                minHfov: 50,
                maxHfov: 120,
                autoRotate: -2,
                autoRotateInactivityDelay: 3000,
            });
        };
        if(typeof pannellum === 'undefined'){
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
            s.onerror = function(){ console.warn('Pannellum script failed to load'); };
            s.onload = onReady;
            document.body.appendChild(s);
        } else {
            onReady();
        }
    };
})();
@endif

// Damage section — select damage by ID. Markers and items now sync via data-damage-marker
// / data-damage-cat. Chips are filtered by csFilterDamageCat (below).
function csSelDamage(id){
    document.querySelectorAll('.cs-damage-item').forEach(d=>d.classList.remove('active'));
    const it=document.getElementById('csDamage-'+id);
    if(it)it.classList.add('active');
    document.querySelectorAll('[data-damage-marker]').forEach(m=>m.classList.toggle('active',m.dataset.damageMarker==id));
    var emptyCat=document.getElementById('csDamageEmptyCat');
    if(emptyCat)emptyCat.style.display='none';
    // Reset gallery to first slide; force-eager-load on tap for instant paint.
    if(it){
        const g=it.querySelector('.cs-dmg-gallery');
        if(g){
            _csDmgSetSlide(g,0);
            const firstImg=g.querySelector('.cs-dmg-gallery-slide img');
            if(firstImg&&firstImg.loading==='lazy')firstImg.loading='eager';
        }
    }
}

// Category chip filter — hides markers + items outside the selected category,
// auto-selects the first damage that matches, or shows an empty-state if none.
function csFilterDamageCat(btn,cat){
    document.querySelectorAll('.cs-damage-tab').forEach(function(t){
        var on=(t.dataset.damageCat===cat);
        t.classList.toggle('active',on);
        t.setAttribute('aria-selected',on?'true':'false');
    });
    var markers=document.querySelectorAll('[data-damage-marker]');
    var firstId=null;
    markers.forEach(function(m){
        var match=(cat==='all'||m.dataset.damageCat===cat);
        m.hidden=!match;
        if(match&&firstId===null)firstId=m.dataset.damageMarker;
    });
    document.querySelectorAll('.cs-damage-item').forEach(function(it){
        var match=(cat==='all'||it.dataset.damageCat===cat);
        if(!match)it.classList.remove('active');
    });
    var emptyCat=document.getElementById('csDamageEmptyCat');
    if(firstId){
        if(emptyCat)emptyCat.style.display='none';
        csSelDamage(firstId);
    } else if(emptyCat){
        emptyCat.style.display='block';
    }
}

// Preload every damage's primary photo as soon as the page is interactive so
// switching markers/chips never blocks on a network round-trip.
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('.cs-dmg-gallery .cs-dmg-gallery-slide[data-slide="0"] img').forEach(function(img){
        if(!img.src)return;
        const pre=new Image();pre.decoding='async';pre.src=img.src;
    });
});

// Damage gallery — slide prev/next
function csDmgSlide(btn,dir){
    const g=btn.closest('.cs-dmg-gallery');
    const slides=g.querySelectorAll('.cs-dmg-gallery-slide');
    let cur=0;
    slides.forEach((s,i)=>{if(s.classList.contains('active'))cur=i;});
    let next=cur+dir;
    if(next<0)next=slides.length-1;
    if(next>=slides.length)next=0;
    _csDmgSetSlide(g,next);
}

// Damage gallery — go to specific slide
function csDmgGoSlide(thumb,idx){
    const g=thumb.closest('.cs-dmg-gallery');
    _csDmgSetSlide(g,idx);
}

// Internal — set active slide
function _csDmgSetSlide(g,idx){
    g.querySelectorAll('.cs-dmg-gallery-slide').forEach((s,i)=>s.classList.toggle('active',i===idx));
    g.querySelectorAll('.cs-dmg-gallery-thumb').forEach((t,i)=>t.classList.toggle('active',i===idx));
    const cur=g.querySelector('.cs-dmg-gallery-cur');
    if(cur)cur.textContent=idx+1;
    // Scroll active thumb into view
    const activeThumb=g.querySelector('.cs-dmg-gallery-thumb.active');
    if(activeThumb)activeThumb.scrollIntoView({behavior:'smooth',block:'nearest',inline:'center'});
}

// Damage gallery — scroll thumbnails
function csDmgScrollThumbs(btn,dir){
    const wrap=btn.closest('.cs-dmg-gallery-thumbs-wrap');
    const strip=wrap.querySelector('.cs-dmg-gallery-thumbs');
    strip.scrollBy({left:dir*220,behavior:'smooth'});
}

// Damage gallery — open lightbox scoped to this damage only
var _dmgLbPhotos=null, _dmgLbIdx=0;
function csDmgLightbox(imgEl){
    if(!imgEl)return;
    var gallery=imgEl.closest('.cs-dmg-gallery');
    if(!gallery)return;
    var slides=Array.from(gallery.querySelectorAll('.cs-dmg-gallery-slide img'));
    _dmgLbPhotos=slides.map(function(img){return{src:img.src,caption:img.alt||''};});
    _dmgLbIdx=Math.max(0,slides.indexOf(imgEl));
    if(!_dmgLbPhotos.length)return;
    _dmgLbShow();
}
function _dmgLbShow(){
    var lb=document.getElementById('csLightbox');
    var img=document.getElementById('csLbImg');
    if(!lb||!_dmgLbPhotos||!_dmgLbPhotos.length)return;
    img.src=_dmgLbPhotos[_dmgLbIdx].src;
    img.alt=_dmgLbPhotos[_dmgLbIdx].caption||'';
    document.getElementById('csLbCounter').textContent=(_dmgLbIdx+1)+' / '+_dmgLbPhotos.length;
    if(!lb.classList.contains('open')){
        lb.classList.add('open');
        csLockBody();
    }
}
// Override nav/close when damage lightbox is open
(function(){
    var origNav=window.csLbNav, origClose=window.csCloseLb;
    window.csLbNav=function(dir){
        if(_dmgLbPhotos){
            _dmgLbIdx=(_dmgLbIdx+dir+_dmgLbPhotos.length)%_dmgLbPhotos.length;
            _dmgLbShow();
        } else {
            origNav(dir);
        }
    };
    window.csCloseLb=function(){
        _dmgLbPhotos=null;
        origClose();
    };
})();

// ==== TOUCH SWIPE — damage gallery stages + main gallery + lightbox ====
// Simple horizontal-swipe detector. Threshold of 40px filters out scroll jitter.
// pan-y in CSS keeps vertical page scroll responsive while we own X swipes.
(function(){
    function bindSwipe(el, onPrev, onNext){
        if(!el)return;
        var sx=0, sy=0, ex=0, ey=0, active=false;
        el.addEventListener('touchstart', function(e){
            var t=e.changedTouches[0];
            sx=ex=t.clientX; sy=ey=t.clientY; active=true;
        }, {passive:true});
        el.addEventListener('touchmove', function(e){
            if(!active)return;
            var t=e.changedTouches[0];
            ex=t.clientX; ey=t.clientY;
        }, {passive:true});
        el.addEventListener('touchend', function(){
            if(!active)return;
            active=false;
            var dx=ex-sx, dy=ey-sy;
            // horizontal swipe wins over vertical pan and clears the noise floor
            if(Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)){
                (dx<0 ? onNext : onPrev)();
            }
        }, {passive:true});
    }
    document.addEventListener('DOMContentLoaded', function(){
        // Damage gallery: swipe between slides
        document.querySelectorAll('.cs-dmg-gallery-stage').forEach(function(stage){
            bindSwipe(stage,
                function(){ var g=stage.closest('.cs-dmg-gallery'); if(!g)return;
                    var slides=g.querySelectorAll('.cs-dmg-gallery-slide'); var cur=0;
                    slides.forEach(function(s,i){ if(s.classList.contains('active'))cur=i; });
                    _csDmgSetSlide(g, (cur-1+slides.length)%slides.length);
                },
                function(){ var g=stage.closest('.cs-dmg-gallery'); if(!g)return;
                    var slides=g.querySelectorAll('.cs-dmg-gallery-slide'); var cur=0;
                    slides.forEach(function(s,i){ if(s.classList.contains('active'))cur=i; });
                    _csDmgSetSlide(g, (cur+1)%slides.length);
                });
        });
        // Main car gallery: swipe between thumbnails (drives csSelImg)
        var mainStage=document.querySelector('.cs-gallery-stage');
        if(mainStage) bindSwipe(mainStage,
            function(){ if(typeof csGalleryPrev==='function')csGalleryPrev(); },
            function(){ if(typeof csGalleryNext==='function')csGalleryNext(); });
        // Lightbox: swipe between photos
        var lb=document.getElementById('csLightbox');
        if(lb) bindSwipe(lb,
            function(){ csLbNav(-1); },
            function(){ csLbNav(1); });
    });
})();

// Inline calculator
function csCalcInlineUpdate() {
    var price = {{ $car->price ?? 0 }};
    var dp = parseInt(document.getElementById('csCalcDp').value) || 0;
    var term = parseInt(document.getElementById('csCalcTerm').value) || 48;
    var apr = 0.079;
    var principal = Math.max(price - dp, 0);
    var mr = apr / 12;
    var pmt = principal > 0 ? Math.round(principal * mr / (1 - Math.pow(1 + mr, -term))) : 0;
    document.getElementById('csCalcInlineRate').textContent = pmt > 0 ? pmt.toLocaleString('pl-PL') + ' zł' : '—';
}
document.getElementById('csCalcDp').addEventListener('input', csCalcInlineUpdate);
document.getElementById('csCalcTerm').addEventListener('change', csCalcInlineUpdate);
csCalcInlineUpdate();

// Inquiry attribution — populated once on page load
(function() {
    var p = new URLSearchParams(window.location.search);
    var set = function(id, val) { var el = document.getElementById(id); if (el && val) el.value = val; };
    set('csInquirySourcePage', window.location.href.substring(0, 500));
    set('csInquiryReferrer',   document.referrer.substring(0, 500));
    set('csInquiryUtmSource',   p.get('utm_source')   || '');
    set('csInquiryUtmMedium',   p.get('utm_medium')   || '');
    set('csInquiryUtmCampaign', p.get('utm_campaign') || '');
    set('csInquiryUtmContent',  p.get('utm_content')  || '');
    set('csInquiryUtmTerm',     p.get('utm_term')     || '');
})();

// Inquiry modal
function csOpenInquiry(type, source) {
    var overlay = document.getElementById('csInquiryOverlay');
    var title = document.getElementById('csInquiryTitle');
    var typeInput = document.getElementById('csInquiryType');
    var msgLabel = document.getElementById('csInquiryMsgLabel');
    typeInput.value = type;
    if (type === 'financing') {
        title.textContent = 'Zapytaj o finansowanie';
        msgLabel.textContent = 'Dodatkowe informacje';
    } else {
        title.textContent = 'Napisz wiadomość';
        msgLabel.textContent = 'Wiadomość';
    }
    document.getElementById('csInquirySuccess').style.display = 'none';
    var form = document.getElementById('csInquiryForm');
    form.style.display = 'block';
    form.reset();
    typeInput.value = type; // reset clears it
    document.getElementById('csInquiryFormSource').value = source || '';
    // clear any previous field errors
    form.querySelectorAll('.cs-field-error').forEach(function(el){ el.remove(); });
    form.querySelectorAll('input, textarea').forEach(function(el){ el.style.borderColor = ''; });
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function csCloseInquiry() {
    document.getElementById('csInquiryOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
function csSubmitInquiry(e) {
    e.preventDefault();
    var form = document.getElementById('csInquiryForm');
    // clear previous errors
    form.querySelectorAll('.cs-field-error').forEach(function(el){ el.remove(); });
    form.querySelectorAll('input, textarea').forEach(function(el){ el.style.borderColor = ''; });
    var consentBox = document.getElementById('csInquiryConsentBox');
    if (consentBox) consentBox.classList.remove('has-error');
    // Client-side consent guard — abort before any network hit if unchecked.
    var consent = document.getElementById('csInquiryConsent');
    if (consent && !consent.checked) {
        if (consentBox) consentBox.classList.add('has-error');
        var prev = consentBox && consentBox.querySelector('.cs-field-error');
        if (prev) prev.remove();
        var err = document.createElement('span');
        err.className = 'cs-field-error';
        err.style.cssText = 'display:block;color:#ef4444;font-size:11.5px;margin-top:6px;font-weight:500';
        err.textContent = 'Aby wysłać zapytanie, musisz wyrazić zgodę na przetwarzanie danych osobowych.';
        consentBox.appendChild(err);
        consentBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        consent.focus();
        return false;
    }
    var btn = document.getElementById('csInquirySubmitBtn');
    btn.disabled = true;
    document.getElementById('csInquirySubmitText').style.display = 'none';
    document.getElementById('csInquirySubmitLoader').style.display = 'inline';
    fetch('{{ route("inquiry.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(Object.fromEntries(new FormData(form)))
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success) {
            form.style.display = 'none';
            document.getElementById('csInquirySuccess').style.display = 'block';
        } else if (data.errors) {
            Object.entries(data.errors).forEach(function([field, messages]) {
                var input = form.querySelector('[name="' + field + '"]');
                if (input && messages[0]) {
                    if (field === 'consent') {
                        var box = document.getElementById('csInquiryConsentBox');
                        if (box) box.classList.add('has-error');
                    } else {
                        input.style.borderColor = '#ef4444';
                    }
                    var err = document.createElement('span');
                    err.className = 'cs-field-error';
                    err.style.cssText = 'display:block;color:#ef4444;font-size:11.5px;margin-top:4px;font-weight:500';
                    err.textContent = messages[0];
                    input.parentNode.appendChild(err);
                }
            });
            // scroll first error into view
            var first = form.querySelector('.cs-field-error');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            var errDiv = document.getElementById('csInquiryFormError');
            if (!errDiv) {
                errDiv = document.createElement('div');
                errDiv.id = 'csInquiryFormError';
                errDiv.style.cssText = 'background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:13px;color:#b91c1c;margin-bottom:12px';
                form.insertBefore(errDiv, form.firstChild);
            }
            errDiv.textContent = data.message || 'Wystąpił błąd. Spróbuj ponownie.';
        }
    }).catch(function() {
        var errDiv = document.getElementById('csInquiryFormError');
        if (!errDiv) {
            errDiv = document.createElement('div');
            errDiv.id = 'csInquiryFormError';
            errDiv.style.cssText = 'background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:13px;color:#b91c1c;margin-bottom:12px';
            form.insertBefore(errDiv, form.firstChild);
        }
        errDiv.textContent = 'Wystąpił błąd połączenia. Spróbuj ponownie.';
    }).finally(function() {
        btn.disabled = false;
        document.getElementById('csInquirySubmitText').style.display = 'inline';
        document.getElementById('csInquirySubmitLoader').style.display = 'none';
    });
    return false;
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') csCloseInquiry(); });
document.addEventListener('DOMContentLoaded',()=>{
    // Sidebar favorite button — sync initial state
    csSidebarFavUpdate();
});

// Sidebar favorite sync
function csSidebarFavUpdate(){
    const favs = JSON.parse(localStorage.getItem('certicars_favs') || '[]');
    // Desktop fav button
    const btn = document.getElementById('csSidebarFav');
    if(btn){
        const id = +btn.dataset.id;
        const isActive = favs.includes(id);
        btn.classList.toggle('active', isActive);
        const icon = document.getElementById('csFavIcon');
        const label = document.getElementById('csFavLabel');
        if(icon){ icon.style.fill = isActive ? 'var(--orange)' : 'none'; icon.style.stroke = isActive ? 'var(--orange)' : 'currentColor'; }
        if(label){ label.textContent = isActive ? 'Obserwujesz' : 'Obserwuj'; label.style.visibility = 'visible'; }
    }
    // (see csShare below)
    // Mobile fav button — same color system as desktop + Oferta cards (orange)
    const mobBtn = document.getElementById('csMobFav');
    if(mobBtn){
        const id = +mobBtn.dataset.id;
        const isActive = favs.includes(id);
        const mobIcon = document.getElementById('csMobFavIcon');
        if(mobIcon){
            mobIcon.style.fill = isActive ? 'var(--orange)' : 'none';
            mobIcon.style.stroke = isActive ? 'var(--orange)' : '#9ca3af';
        }
        mobBtn.style.borderColor = isActive ? 'var(--orange)' : '#e5e7eb';
        mobBtn.style.background = '#fff';
    }
}

// Accordion toggle — used by the upper detail sections (Dane, Historia, Serwis, Dokumenty, Zużycie)
window.csToggleAccordion = function(headerEl) {
    if (!headerEl) return;
    headerEl.classList.toggle('open');
};

// SHARE: native share with clipboard fallback + toast
window.csShare = async function() {
    var url = window.location.href;
    var title = document.title;
    if (navigator.share) {
        try { await navigator.share({ title: title, url: url }); return; }
        catch (e) { if (e && e.name === 'AbortError') return; /* fall through to clipboard */ }
    }
    var copied = false;
    if (navigator.clipboard && window.isSecureContext) {
        try { await navigator.clipboard.writeText(url); copied = true; } catch (e) {}
    }
    if (!copied) {
        try {
            var ta = document.createElement('textarea');
            ta.value = url;
            ta.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            copied = true;
        } catch (e) {}
    }
    csShareToast(copied ? 'Link skopiowany' : 'Nie udało się skopiować');
};
function csShareToast(msg) {
    var t = document.getElementById('csShareToast');
    if (!t) { t = document.createElement('div'); t.id = 'csShareToast'; document.body.appendChild(t); }
    t.textContent = msg;
    t.classList.add('visible');
    clearTimeout(window._csShareToastT);
    window._csShareToastT = setTimeout(function(){ t.classList.remove('visible'); }, 2200);
}


</script>
@endpush
@endsection
