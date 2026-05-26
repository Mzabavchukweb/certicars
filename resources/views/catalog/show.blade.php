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

.cs-grid{display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:24px;margin-bottom:8px;min-width:0;overflow:hidden}
.cs-sidebar{position:sticky;top:92px;min-width:0;display:flex;flex-direction:column}

/* SIDEBAR CERTICHECK BADGE (catalog card style) */
.cs-sidebar-certi{display:inline-flex;align-items:center;gap:5px;background:rgba(0,0,0,.85);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);color:#fff;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;letter-spacing:.3px;text-decoration:none;transition:all .18s;border:none;cursor:pointer}
.cs-sidebar-certi:hover{background:rgba(0,0,0,.95);transform:translateY(-1px);box-shadow:0 3px 10px rgba(0,0,0,.2)}
.cs-sidebar-certi svg{width:16px;height:16px;flex-shrink:0;display:block}

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
.cs-sidebar-summary-row .cs-row-icon{width:18px;height:18px;stroke:#9ca3af;fill:none;stroke-width:1.8;flex-shrink:0}
.cs-sidebar-summary-row .lbl{color:#6b7280;font-weight:400;flex:1}
.cs-sidebar-summary-row .val{font-weight:700;color:#1a1a1a;text-align:right}

/* PRICE SECTION (inside card) */
.cs-price-section{padding:22px 22px 14px;border-bottom:1px solid #f0f0f2}
.cs-price-label{font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.cs-price-value{font-size:36px;font-weight:900;letter-spacing:-1px;color:#1a1a1a;line-height:1}
.cs-price-value small{display:block;font-size:12px;font-weight:500;color:#9ca3af;letter-spacing:0;margin-top:4px}

/* CTA BUTTONS (inside card) */
.cs-price-actions{padding:18px 22px 22px;display:flex;flex-direction:column;gap:8px}
.cs-btn-phone{width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:15px 24px;background:#0066ff;color:#fff;border:none;border-radius:50px;font-size:16px;font-weight:700;cursor:pointer;transition:all .2s;text-decoration:none;box-shadow:0 4px 14px rgba(0,102,255,.35)}
.cs-btn-phone:hover{background:#0052cc;color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.45);transform:translateY(-1px)}
.cs-btn-phone svg{width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2.2;flex-shrink:0}
.cs-btn-message{width:100%;display:flex;align-items:center;gap:10px;padding:14px 20px;background:#f8f9fa;border:1.5px solid #e5e7eb;border-radius:12px;cursor:pointer;transition:all .15s;text-decoration:none;color:#1a1a1a}
.cs-btn-message:hover{background:#f0f1f3;border-color:#d1d5db}
.cs-btn-message svg{width:20px;height:20px;stroke:#0066ff;fill:none;stroke-width:1.8;flex-shrink:0}
.cs-btn-message .cs-msg-text{text-align:left}
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

/* DAMAGES */
.cs-damages-tabs{display:flex;gap:8px;overflow-x:auto;padding-bottom:8px;margin-bottom:16px;-webkit-overflow-scrolling:touch;max-width:100%;scrollbar-width:none}
.cs-damages-tabs::-webkit-scrollbar{display:none}
.cs-damage-tab{flex-shrink:0;display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:#fff7ed;color:#b45309;border:1px solid #fed7aa;border-radius:50px;font-size:12.5px;font-weight:700;cursor:pointer;white-space:nowrap;transition:background .15s,color .15s,border-color .15s,transform .12s}
.cs-damage-tab:hover{background:#ffedd5}
.cs-damage-tab.active{background:#0a0a0a;color:#fff;border-color:#0a0a0a}
.cs-damage-tab-ico{display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;flex-shrink:0}
.cs-damage-tab-ico svg{width:16px;height:16px;display:block}
.cs-damage-grid{display:grid;grid-template-columns:300px 1fr;gap:0;border:1px solid #eeeef0;border-radius:14px;overflow:hidden;background:#fff}
.cs-damage-diagram{background:#f5f5f7;position:relative;overflow:hidden;min-height:500px}
.cs-damage-diagram-inner{position:absolute;inset:0;overflow:hidden}
.cs-damage-diagram-inner>img{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) scale(1.7);width:100%;height:auto;pointer-events:none}
.cs-damage-marker{position:absolute;transform:translate(-50%,-50%);width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;padding:0;margin:0;cursor:pointer;z-index:5;-webkit-tap-highlight-color:transparent}
.cs-damage-marker-dot{width:26px;height:26px;border-radius:50%;background:#f59e0b;border:2px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 8px rgba(245,158,11,.4);transition:transform .12s ease,box-shadow .12s ease}
.cs-damage-marker-dot svg{width:14px;height:14px;display:block}
.cs-damage-marker.active .cs-damage-marker-dot{transform:scale(1.15);box-shadow:0 0 0 6px rgba(245,158,11,.18),0 2px 10px rgba(245,158,11,.5)}
.cs-damage-marker:hover .cs-damage-marker-dot{transform:scale(1.1)}
.cs-damage-detail{padding:24px;display:flex;flex-direction:column;border-left:1px solid #eeeef0}
.cs-damage-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;text-align:center;color:var(--text-4);font-size:13.5px;gap:10px}
.cs-damage-empty i{width:36px;height:36px}
.cs-damage-item{display:none}
.cs-damage-item.active{display:block}
.cs-damage-item h3{font-size:16px;font-weight:700;margin-bottom:10px;display:flex;align-items:center;gap:7px}
.cs-damage-item h3 i{width:18px;height:18px;color:var(--yellow)}
.cs-damage-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px}
.cs-damage-tags span{background:#0a0a0a;color:#fff;padding:5px 11px;border-radius:6px;font-size:11px;font-weight:500}
.cs-damage-item p{font-size:13.5px;color:var(--text-2);line-height:1.65}

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
.cs-dmg-gallery-stage{position:relative;background:#f5f5f7;overflow:hidden;border-radius:12px}
.cs-dmg-gallery-slide{display:none}
.cs-dmg-gallery-slide.active{display:block}
.cs-dmg-gallery-slide img{width:100%;aspect-ratio:16/9;object-fit:cover;display:block;cursor:pointer}
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

/* FULL EQUIPMENT (COS style — clean grid) */
.cs-equipment-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 32px;padding:4px 0}
.cs-equipment-item{display:flex;align-items:center;gap:10px;padding:12px 0;font-size:13.5px;color:#374151;border-bottom:1px solid #f0f0f2}
.cs-equipment-item:last-child{border-bottom:none}
.cs-equipment-item i{width:14px;height:14px;color:#16a34a;flex-shrink:0;stroke-width:2.5}
.cs-eq-count{font-size:13px;color:var(--text-3);font-weight:500;margin-left:auto}

/* DATA SECTION (CarOnSale style — pixel-perfect) */
.cs-sections-2col{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:16px;margin-bottom:16px;align-items:start;overflow:hidden;max-width:1200px;margin-left:auto;margin-right:auto;padding:0 24px}
@media(max-width:900px){.cs-sections-2col{grid-template-columns:1fr}}
.cs-sections-2col .cs-data-section{margin-bottom:0}
.cs-col-left,.cs-col-right{display:flex;flex-direction:column;gap:16px;min-width:0;align-content:start;overflow:hidden}
.cs-data-section{background:#fff;border:none;border-radius:16px;margin-bottom:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.04);max-width:100%}
.cs-wrap > .cs-data-section,.cs-wrap > div:not(.container) > .cs-data-section{max-width:calc(1200px - 48px);margin-left:auto;margin-right:auto}
.cs-data-header{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;user-select:none;border-bottom:1px solid #eeeef0}
.cs-data-header h2{font-size:17px;font-weight:700;color:#1a1a1a;letter-spacing:-.2px;display:flex;align-items:center;gap:8px;margin:0;line-height:1.3;font-family:'Inter',sans-serif}
.cs-data-header h2 i,.cs-data-header h2 svg{display:inline-flex;align-items:center;justify-content:center}
.cs-data-header h2 svg{width:18px;height:18px;flex-shrink:0;color:#10b981}
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
.cs-data-grid-2col{display:grid;grid-template-columns:1fr 1fr;column-gap:32px;row-gap:0}
@media(max-width:768px){.cs-data-grid-2col{grid-template-columns:1fr;column-gap:0}}

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
.cs-data-row{display:flex;justify-content:space-between;align-items:baseline;padding:10px 0;font-size:13.5px;border-bottom:1px solid #f0f0f2;gap:8px}
.cs-data-row:first-child{padding-top:0}
.cs-data-row:last-child{border-bottom:none;padding-bottom:4px}
.cs-data-row .lbl{color:#6b7280;font-weight:400;line-height:1.5;flex-shrink:0;margin-right:4px}
.cs-data-row .val{font-weight:600;color:#1a1a1a;text-align:right;line-height:1.5;overflow:hidden;text-overflow:ellipsis;word-break:break-word;min-width:0;flex-shrink:1;max-width:60%}
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
    .cs-grid{grid-template-columns:1fr;gap:20px}
    .cs-grid > div{min-width:0;max-width:100%}
    .cs-sidebar{position:static}
    .cs-calc-inline{display:none}
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
.cs-related-grid{display:flex;gap:20px;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding-bottom:4px}
.cs-related-grid::-webkit-scrollbar{display:none}
.cs-related-grid>*{flex:0 0 calc(33.333% - 14px);scroll-snap-align:start;min-width:260px}

@media(max-width:768px){
    .cs-wrap .container{padding-left:14px;padding-right:14px}
    /* Unify ALL section side margins to match sidebar card */
    .cs-wrap > .cs-data-section,.cs-wrap > div:not(.container) > .cs-data-section{max-width:none!important;margin-left:14px!important;margin-right:14px!important;padding-left:0!important;padding-right:0!important}
    .cs-wrap > .cs-data-section[style],.cs-data-section[style]{margin-left:14px!important;margin-right:14px!important;padding-left:0!important;padding-right:0!important;max-width:none!important}
    .cs-sections-2col{padding:0 14px!important;margin:0!important}
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
    /* DATA SECTIONS — rounded card */
    .cs-data-section{border-radius:16px;border:none;box-shadow:0 1px 8px rgba(0,0,0,.06);overflow:hidden;margin-bottom:22px}
    .cs-data-header{padding:22px 22px 14px;text-align:left}
    .cs-data-header h2{font-size:18px;font-weight:900;letter-spacing:-.3px;text-align:left}
    .cs-data-body{padding:4px 22px 22px}
    .cs-data-row{font-size:14px;padding:16px 0;border-bottom:1px solid #f0f0f2;gap:16px;align-items:flex-start}
    .cs-data-row:last-child{border-bottom:none}
    .cs-data-row .lbl{font-weight:700;color:#1a1a1a;min-width:0;flex:1 1 auto;line-height:1.45;word-break:normal;overflow-wrap:break-word}
    .cs-data-row .val{font-weight:700;color:#1a1a1a;font-size:14px;max-width:58%;text-align:right;line-height:1.45;word-break:break-word;overflow-wrap:break-word;white-space:normal;overflow:visible;text-overflow:clip;flex-shrink:0}
    /* Stack 2-col section grid (Damage etc.) with a healthier gap on mobile */
    .cs-sections-2col{gap:22px!important}
    /* STAN TECHNICZNY — separate blocks stacked */
    .cs-data-2col{grid-template-columns:1fr!important;gap:28px}
    .cs-data-block{padding:0!important;border:none!important;background:transparent!important;box-shadow:none!important}
    .cs-data-block-title{font-size:18px!important;font-weight:900!important;margin-bottom:4px!important;padding-bottom:0!important;border-bottom:none!important}
    .cs-data-block .cs-data-row{padding:16px 0;border-bottom:1px solid #f0f0f2;font-size:14px}
    .cs-data-block .cs-data-row .lbl{font-size:14px;font-weight:700;gap:10px}
    .cs-data-block .cs-data-row .lbl i[data-lucide="check-circle"]{width:24px;height:24px;color:#16a34a;background:#dcfce7;border-radius:50%;padding:3px;flex-shrink:0}
    .cs-data-block .cs-data-row .val{font-size:14px;font-weight:700;color:#1a1a1a}
    /* DAMAGE */
    .cs-sections-2col{padding:0 14px;gap:12px}
    .cs-damage-grid{grid-template-columns:1fr;min-height:auto;border:none;border-radius:0;background:transparent;row-gap:14px}
    /* Square 1:1 diagram, capped height, object-fit:contain — whole car silhouette visible, no zoom-crop. */
    .cs-damage-diagram{border-radius:14px;border:1px solid #eeeef0;background:#f7f8fa;min-height:0;width:100%;max-width:340px;margin-left:auto;margin-right:auto;aspect-ratio:1/1;max-height:300px}
    .cs-damage-diagram-inner{min-height:0;inset:0}
    .cs-damage-diagram-inner>img{top:0;left:0;transform:none;width:100%;height:100%;object-fit:contain;object-position:center}
    .cs-damage-marker{width:44px;height:44px}
    .cs-damage-marker-dot{width:22px;height:22px;border-width:2px}
    .cs-damage-marker-dot svg{width:11px;height:11px}
    .cs-damage-detail{padding:0;border-left:none;border-top:none}
    .cs-damage-item h3{font-size:14px}
    .cs-damage-item p{font-size:12.5px}
    .cs-damage-tags span{font-size:10px;padding:4px 9px}
    .cs-damage-tab{font-size:12px;padding:7px 12px}
    .cs-damage-tab-ico,.cs-damage-tab-ico svg{width:14px;height:14px}
    .cs-dmg-gallery-slide img{aspect-ratio:4/3}
    .cs-dmg-gallery-thumb{width:78px;height:54px;border-width:2px}
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
    .cs-wrap .container{padding-left:12px;padding-right:12px}
    .cs-wrap > .cs-data-section,.cs-wrap > div:not(.container) > .cs-data-section,.cs-data-section[style]{margin-left:12px!important;margin-right:12px!important}
    .cs-sections-2col{padding:0 12px!important}
    .cs-head h1{font-size:19px}
    .cs-gallery{border-radius:10px}
    .cs-sidebar-card{border-radius:14px}
    .cs-data-section{border-radius:14px;margin-bottom:18px}
    .cs-data-row{padding:15px 0;gap:14px}
    .cs-data-row .val{max-width:55%}
    .cs-price-value{font-size:26px}
    .cs-sections-2col{padding:0 12px}
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
    .cs-data-body{padding:4px 18px 18px}
    .cs-data-header{padding:18px 18px 12px}
    .cs-gallery-tabs{gap:4px;padding:8px 0 6px}
    .cs-gallery-tab{font-size:10px;padding:4px 8px;gap:3px}
    .cs-gallery-tab svg,.cs-gallery-tab i{width:11px;height:11px}
    .cs-gallery-nav{width:30px;height:30px}
    .cs-gallery-nav svg{width:14px;height:14px}
    .cs-gallery-nav.prev{left:6px}
    .cs-gallery-nav.next{right:6px}
}
/* INLINE CALCULATOR */
.cs-calc-inline{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-top:12px}
.cs-calc-result{padding:20px 20px 16px;border-bottom:1px solid #f0f0f2}
.cs-calc-rate-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#6b7280;margin-bottom:6px}
.cs-calc-rate-value{font-size:30px;font-weight:800;color:#1a1a1a;letter-spacing:-.5px;line-height:1}
.cs-calc-rate-suffix{font-size:14px;font-weight:500;color:#6b7280}
.cs-calc-rate-sub{font-size:11.5px;color:#9ca3af;margin-top:8px}
.cs-calc-controls{display:flex;align-items:flex-end;gap:10px;padding:16px 20px;flex-wrap:wrap}
.cs-calc-field{flex:1;min-width:130px}
.cs-calc-field label{display:block;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:#6b7280;margin-bottom:5px}
.cs-calc-input-wrap{display:flex;align-items:center;background:#fff;border:1.5px solid #d1d5db;border-radius:8px;padding:0 12px;height:42px;transition:border-color .15s}
.cs-calc-input-wrap:focus-within{border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.08)}
.cs-calc-input-wrap input{border:none;background:none;font-size:15px;font-weight:700;color:#1a1a1a;width:100%;outline:none;padding:0}
.cs-calc-input-wrap span{font-size:12px;font-weight:600;color:#9ca3af;margin-left:4px;white-space:nowrap}
.cs-calc-field select{width:100%;height:42px;background:#fff;border:1.5px solid #d1d5db;border-radius:8px;padding:0 12px;font-size:15px;font-weight:700;color:#1a1a1a;cursor:pointer;outline:none;appearance:auto;transition:border-color .15s}
.cs-calc-field select:focus{border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.08)}
.cs-calc-cta{height:42px;padding:0 22px;background:#0066ff;color:#fff;border:none;border-radius:8px;font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;white-space:nowrap;transition:all .15s;box-shadow:0 2px 8px rgba(0,102,255,.25)}
.cs-calc-cta:hover{background:#0052cc;box-shadow:0 4px 14px rgba(0,102,255,.35)}
.cs-calc-footer{padding:12px 20px;background:#fafafa;border-top:1px solid #f0f0f2}
.cs-calc-disclaimer{font-size:9.5px;color:#b0b0b4;line-height:1.4}
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
.cs-inquiry-success{text-align:center;padding:20px 0}
.cs-inquiry-success h4{font-size:20px;font-weight:800;color:#1a1a1a;margin:16px 0 6px}
.cs-inquiry-success p{font-size:14px;color:#6b7280;margin:0 0 20px}
.cs-inquiry-success button{padding:10px 28px;background:#f5f5f7;border:none;border-radius:10px;font-size:14px;font-weight:600;color:#374151;cursor:pointer}
.cs-inquiry-success button:hover{background:#e8e8ea}

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
    .cs-grid > div:first-child > .cs-calc-inline{order:3}
    /* Connector-line fix: drop the bottom hairline under price (made buttons below look attached) */
    .cs-price-section{border-bottom:none}
}
/* Share toast (fallback when navigator.share unavailable) */
#csShareToast{position:fixed;left:50%;bottom:84px;transform:translateX(-50%) translateY(8px);background:#1a1a1a;color:#fff;padding:10px 18px;border-radius:50px;font-size:13.5px;font-weight:600;z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s,transform .22s;box-shadow:0 6px 20px rgba(0,0,0,.25)}
#csShareToast.visible{opacity:1;transform:translateX(-50%) translateY(0)}

/* INFO BENEFIT TILES under sidebar card — equal height, aligned rows */
.cs-info-tiles{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;align-items:stretch}
.cs-info-tile{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px 16px;text-align:center;display:flex;flex-direction:column;align-items:center;height:100%;min-height:0}
.cs-info-tile-icon{width:44px;height:44px;border-radius:12px;background:#e8f1ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;flex-shrink:0}
.cs-info-tile-title{font-size:14px;font-weight:800;color:#1a1a1a;margin:0 0 6px;letter-spacing:-.1px;flex-shrink:0}
.cs-info-tile-body{font-size:11.5px;color:#6b7280;line-height:1.5;flex:1 1 auto}
.cs-info-tile-link{display:inline-flex;align-items:center;gap:4px;margin-top:10px;font-size:11.5px;font-weight:600;color:#0066ff;text-decoration:none;flex-shrink:0}
.cs-info-tile-link:hover{color:#0052cc}
.cs-info-tile-link svg{flex-shrink:0}
@media(max-width:480px){
    .cs-info-tile{padding:16px 12px}
    .cs-info-tile-title{font-size:13.5px}
}
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
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                Wszystkie zdjęcia
            </button>
            <button type="button" class="cs-gallery-tab {{ $car->exteriorPano360Image ? '' : 'disabled' }}" data-gallery-filter="pano360ext" onclick="csFilterGallery(this,'pano360ext')" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                360° z zewnątrz
            </button>
            <button type="button" class="cs-gallery-tab {{ $car->pano360Image ? '' : 'disabled' }}" data-gallery-filter="pano360" onclick="csFilterGallery(this,'pano360')" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                360° wnętrza
            </button>
            <button type="button" class="cs-gallery-tab {{ $damageImgList->count() ? '' : 'disabled' }}" data-gallery-filter="damage" onclick="csFilterGallery(this,'damage')" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Zdjęcia stanu pojazdu
            </button>
            <button type="button" class="cs-gallery-tab" data-gallery-filter="documents" onclick="csFilterGallery(this,'documents')" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                Dokumenty
            </button>
            <button type="button" class="cs-gallery-tab {{ $hasEngineVideo ? '' : 'disabled' }}" data-gallery-filter="video" onclick="csFilterGallery(this,'video')" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Wideo pracy silnika
            </button>
            <button type="button" class="cs-gallery-tab" data-gallery-filter="paint" onclick="csFilterGallery(this,'paint')" role="tab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m19 11-8-8-8.6 8.6a2 2 0 0 0 0 2.8l5.2 5.2c.8.8 2 .8 2.8 0L19 11Z"/><path d="m5 2 5 5"/><path d="M2 13h15"/><path d="M22 20a2 2 0 1 1-4 0c0-1.6 1.7-2.4 2-4 .3 1.6 2 2.4 2 4Z"/></svg>
                Pomiary lakieru
            </button>
        </div>
        </div>{{-- /cs-gallery-tabs-wrap --}}
        <div class="cs-gallery">
            <div class="cs-gallery-stage">
                <button type="button" class="cs-gallery-nav prev" onclick="csGalleryPrev()" aria-label="Poprzednie zdjęcie"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                <button type="button" class="cs-gallery-nav next" onclick="csGalleryNext()" aria-label="Następne zdjęcie"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
                <div class="cs-gallery-main active" id="csGalleryStandard">
                    @if($galleryList->count())
                        <img src="{{ $galleryList->first()->url }}" id="csMainImg" alt="{{ $galleryList->first()->alt }}" style="cursor:zoom-in" onclick="openCarGallery(0)" fetchpriority="high" decoding="async">
                        <div class="cs-gallery-counter" style="cursor:zoom-in" onclick="openCarGallery(parseInt(document.getElementById('csImgCounter').textContent)-1)"><span id="csImgCounter">1</span> / <span id="csImgTotal">{{ $galleryList->count() }}</span></div>
                    @else
                        <div class="empty"><i data-lucide="car" aria-hidden="true"></i></div>
                    @endif
                    @if($car->available_now || $car->home_delivery || $car->has_gethelp)
                    <div style="position:absolute;bottom:12px;left:12px;display:flex;flex-wrap:wrap;gap:5px;z-index:4">
                        @if($car->available_now)<span style="background:rgba(16,185,129,.92);color:#fff;padding:5px 10px;border-radius:50px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(6px)"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>Od ręki</span>@endif
                        @if($car->home_delivery)<span style="background:rgba(99,102,241,.92);color:#fff;padding:5px 10px;border-radius:50px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(6px)"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 13.52 9H12v9"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>Dostawa</span>@endif
                        @if($car->has_gethelp)<span style="background:rgba(217,119,6,.92);color:#fff;padding:5px 10px;border-radius:50px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(6px)"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>GetHelp {{ $car->gethelp_package ?? 'Classic' }} w cenie</span>@endif
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

        <!-- KALKULATOR KREDYTU (inline) -->
        <div class="cs-calc-inline">
            <div class="cs-calc-result">
                <div class="cs-calc-rate-label">Rata od</div>
                <div class="cs-calc-rate-value"><span id="csCalcInlineRate">—</span> <span class="cs-calc-rate-suffix">/ mies.*</span></div>
                <div class="cs-calc-rate-sub">Szacowana rata dla wybranych parametrów</div>
            </div>
            <div class="cs-calc-controls">
                <div class="cs-calc-field">
                    <label>Wpłata własna</label>
                    <div class="cs-calc-input-wrap">
                        <input type="number" id="csCalcDp" value="{{ $car->price ? round($car->price * 0.2) : 0 }}" min="0" max="{{ $car->price ?? 0 }}" step="1000">
                        <span>zł</span>
                    </div>
                </div>
                <div class="cs-calc-field">
                    <label>Okres finansowania</label>
                    <select id="csCalcTerm">
                        <option value="12">12 miesięcy</option>
                        <option value="24">24 miesiące</option>
                        <option value="36">36 miesięcy</option>
                        <option value="48" selected>48 miesięcy</option>
                        <option value="60">60 miesięcy</option>
                        <option value="72">72 miesiące</option>
                        <option value="84">84 miesiące</option>
                        <option value="96">96 miesięcy</option>
                    </select>
                </div>
                <div class="cs-calc-field" style="flex:0 0 auto;min-width:auto">
                    <label>&nbsp;</label>
                    <button type="button" class="cs-calc-cta" onclick="csOpenInquiry('financing','financing_form')">
                        Zapytaj o finansowanie
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
            <div class="cs-calc-footer">
                <span class="cs-calc-disclaimer">*Przykładowa rata przy RRSO 7,9%. Nie stanowi oferty w rozumieniu prawa.</span>
            </div>
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
                            <svg id="csMobFavIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        </button>
                    </div>
                    <div class="cs-mob-pills" style="display:none;flex-wrap:wrap;gap:6px;margin-top:12px">
                        @if($car->mileage)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>{{ number_format((float) $car->mileage,0,'',' ') }} km</span>@endif
                        @if($car->first_registration)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>{{ $car->first_registration }}</span>@endif
                        @if($car->fuel_type)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg>{{ \App\Helpers\CarLabels::fuelType($car->fuel_type) }}</span>@endif
                        @if($car->power_hp)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>{{ $car->power_hp }} KM</span>@endif
                        @if($car->has_certicheck)<a href="{{ route('catalog.certicheck', $car->slug) }}" class="cs-sidebar-certi" title="Sprawdź raport CertiCheck"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/><path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>CertiCheck</a>@endif
                    </div>
                </div>
                <!-- PRICE -->
                <div class="cs-price-section">
                    <div class="cs-price-value">{{ $car->formatted_price }}</div>
                    <div style="font-size:12px;color:#6b7280;font-weight:500;margin-top:6px">Cena brutto @if($car->price_type)· {{ $car->price_type }}@endif</div>
                </div>
                <!-- VEHICLE SUMMARY with icons -->
                <div class="cs-sidebar-summary">
                    @if($car->mileage)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                        <span class="lbl">Przebieg</span>
                        <span class="val">{{ number_format((float) $car->mileage,0,'',' ') }} km</span>
                    </div>
                    @endif
                    @if($car->first_registration)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <span class="lbl">Rok produkcji</span>
                        <span class="val">{{ $car->first_registration }}</span>
                    </div>
                    @endif
                    @if($car->fuel_type)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/><path d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/></svg>
                        <span class="lbl">Paliwo</span>
                        <span class="val">{{ \App\Helpers\CarLabels::fuelType($car->fuel_type) }}</span>
                    </div>
                    @endif
                    @if($car->transmission)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22V8"/><path d="m5 12-3 3 3 3"/><path d="m19 12 3 3-3 3"/><path d="M2 15h20"/></svg>
                        <span class="lbl">Skrzynia biegów</span>
                        <span class="val">{{ \App\Helpers\CarLabels::transmission($car->transmission) }}</span>
                    </div>
                    @endif
                    @if($car->power_hp)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                        <span class="lbl">Moc</span>
                        <span class="val">{{ $car->power_hp }} KM</span>
                    </div>
                    @endif
                    @if($car->body_type ?? $car->category)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        <span class="lbl">Nadwozie</span>
                        <span class="val">{{ \App\Helpers\CarLabels::bodyType($car->body_type ?? $car->category) }}</span>
                    </div>
                    @endif
                </div>
                <!-- CTA BUTTONS (desktop) -->
                <div class="cs-price-actions">
                    <a href="tel:+48585586090" class="cs-btn-phone">
                        <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Zadzwoń
                        <span style="font-weight:400;opacity:.85">+48 58 558 60 90</span>
                    </a>
                    <button type="button" class="cs-btn-message" onclick="csOpenInquiry('general','main_car_cta')">
                        <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <span class="cs-msg-text">
                            <strong>Napisz wiadomość</strong>
                            <small>Odpowiadamy na każde pytanie</small>
                        </span>
                    </button>
                    <div style="display:flex;gap:8px;margin-top:4px">
                        <button type="button" class="cs-btn-secondary" id="csSidebarFav" data-id="{{ $car->id }}" onclick="toggleFav(event,{{ $car->id }});csSidebarFavUpdate()" style="flex:1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="csFavIcon"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            <span id="csFavLabel" style="visibility:hidden">Dodaj do ulubionych</span>
                        </button>
                        @if($car->has_certicheck)
                        <a href="{{ route('catalog.certicheck', $car->slug) }}" class="cs-sidebar-certi" title="Sprawdź raport CertiCheck">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/><path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            CertiCheck
                        </a>
                        @endif
                    </div>
                </div>
                <!-- CTA BUTTONS (mobile) -->
                <div class="cs-mob-cta" style="display:none;grid-template-columns:1fr 1fr;gap:10px;padding:0 22px 16px">
                    <a href="tel:+48585586090" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#0066ff;color:#fff;padding:14px 16px;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;border:none">Zadzwoń</a>
                    <button type="button" onclick="csOpenInquiry('general','trust_banner_cta')" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#f3f4f6;color:#1a1a1a;padding:14px 16px;border-radius:12px;font-size:15px;font-weight:700;border:none;cursor:pointer">Napisz wiadomość</button>
                </div>



            </div>{{-- /cs-sidebar-card --}}

            {{-- BENEFIT TILES — below the sidebar card --}}
            <div class="cs-info-tiles">
                <div class="cs-info-tile">
                    <div class="cs-info-tile-icon">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#0066ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <div class="cs-info-tile-title">Dostępny od ręki</div>
                    <div class="cs-info-tile-body">Auto gotowe do odbioru<br>Możliwa jazda próbna</div>
                </div>
                <div class="cs-info-tile">
                    <div class="cs-info-tile-icon">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#0066ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    </div>
                    <div class="cs-info-tile-title">Dostawa pod dom</div>
                    <div class="cs-info-tile-body">Po wcześniejszych oględzinach<br>Cena do ustalenia</div>
                    <a href="{{ route('contact') }}" class="cs-info-tile-link">Dowiedz się więcej <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>
            </div>
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
    @endphp

    {{-- A. DANE POJAZDU — expanded by default on both desktop and mobile --}}
    <div class="cs-data-section cs-collapsible-mobile">
        <div class="cs-data-header open" onclick="csToggleAccordion(this)">
            <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>Dane pojazdu</h2>
            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <div class="cs-data-body">
            <div class="cs-data-grid-2col">
                @if($car->brand?->name || $car->model)
                    <div class="cs-data-row"><span class="lbl">Marka i model</span><span class="val">{{ trim(($car->brand?->name ?? '') . ' ' . ($car->model ?? '')) }}</span></div>
                @endif
                @if($rowOk($car->first_registration))
                    <div class="cs-data-row"><span class="lbl">Rok produkcji</span><span class="val">{{ $car->first_registration }}</span></div>
                @endif
                @if($rowOk($car->mileage))
                    <div class="cs-data-row"><span class="lbl">Przebieg</span><span class="val">{{ number_format((float) $car->mileage, 0, '', ' ') }} km</span></div>
                @endif
                @if($rowOk($dispFuel))
                    <div class="cs-data-row"><span class="lbl">Paliwo</span><span class="val">{{ $dispFuel }}</span></div>
                @endif
                @if($rowOk($dispTransmission))
                    <div class="cs-data-row"><span class="lbl">Skrzynia biegów</span><span class="val">{{ $dispTransmission }}</span></div>
                @endif
                @if($rowOk($car->power_hp))
                    <div class="cs-data-row"><span class="lbl">Moc</span><span class="val">{{ $car->power_hp }} KM @if($rowOk($car->power_kw))· {{ $car->power_kw }} kW @endif</span></div>
                @endif
                @if($rowOk($car->engine_capacity))
                    <div class="cs-data-row"><span class="lbl">Pojemność</span><span class="val">{{ number_format((float) $car->engine_capacity, 0, '', ' ') }} cm³</span></div>
                @endif
                @if($rowOk($dispBody))
                    <div class="cs-data-row"><span class="lbl">Nadwozie</span><span class="val">{{ $dispBody }}</span></div>
                @endif
                @if($rowOk($car->color))
                    <div class="cs-data-row"><span class="lbl">Kolor</span><span class="val">{{ $car->color }}</span></div>
                @endif
                @if($rowOk($car->doors))
                    <div class="cs-data-row"><span class="lbl">Liczba drzwi</span><span class="val">{{ $car->doors }}</span></div>
                @endif
                @if($rowOk($car->seats))
                    <div class="cs-data-row"><span class="lbl">Liczba miejsc</span><span class="val">{{ $car->seats }}</span></div>
                @endif
                @if($rowOk($car->vin))
                    <div class="cs-data-row"><span class="lbl">VIN</span><span class="val" style="font-family:'SF Mono',Menlo,monospace;font-size:12px;letter-spacing:.4px">{{ $car->vin }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- B. HISTORIA POJAZDU — collapsed on mobile by default --}}
    @if($rowOk($dispCountry) || $rowOk($dispImportedFrom) || $rowOk($car->first_registration) || $car->previous_owners !== null || $rowOk($car->vehicle_history) || $rowOk(CarLabels::bool($car->service_book)))
    <div class="cs-data-section cs-collapsible-mobile">
        <div class="cs-data-header" onclick="csToggleAccordion(this)">
            <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="m2 14 6-6 6 6 6-6"/></svg>Historia pojazdu</h2>
            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <div class="cs-data-body">
            <div class="cs-data-grid-2col">
                @if($rowOk($dispCountry))
                    <div class="cs-data-row"><span class="lbl">Kraj rejestracji</span><span class="val">{{ $dispCountry }}</span></div>
                @endif
                @if($rowOk($dispImportedFrom) && $car->imported_from !== $car->country_registration)
                    <div class="cs-data-row"><span class="lbl">Importowany z</span><span class="val">{{ $dispImportedFrom }}</span></div>
                @endif
                @if($rowOk($car->first_registration))
                    <div class="cs-data-row"><span class="lbl">Pierwsza rejestracja</span><span class="val">{{ $car->first_registration }}</span></div>
                @endif
                @if($car->previous_owners !== null)
                    <div class="cs-data-row"><span class="lbl">Właściciele</span><span class="val">{{ $car->previous_owners == 0 ? 'Pierwszy właściciel' : $car->previous_owners }}</span></div>
                @endif
                @if($rowOk($car->vehicle_history))
                    <div class="cs-data-row"><span class="lbl">Historia pojazdu</span><span class="val">{{ $car->vehicle_history }}</span></div>
                @endif
                @php $svcBook = CarLabels::bool($car->service_book); @endphp
                @if($rowOk($svcBook))
                    <div class="cs-data-row"><span class="lbl">Sprawdzony w bazach</span><span class="val">{{ $svcBook }}</span></div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- C. SERWISOWANIE — collapsed on mobile by default --}}
    @php
        $svcDoc = CarLabels::bool($car->service_documentation);
        $asoSvc = CarLabels::bool($car->aso_serviced);
    @endphp
    @if($rowOk($svcDoc) || $rowOk($asoSvc) || $rowOk($car->service_history) || $rowOk($car->last_service) || $rowOk($car->next_inspection))
    <div class="cs-data-section cs-collapsible-mobile">
        <div class="cs-data-header" onclick="csToggleAccordion(this)">
            <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>Serwisowanie</h2>
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

    {{-- D. DOKUMENTY — collapsed on mobile by default --}}
    @php
        $cocDocs = CarLabels::bool($car->coc_documents);
        $svcBookStatus = CarLabels::status($car->service_book_status) ?? $car->service_book_status;
        $regCert = CarLabels::bool($car->registration_cert);
        $ownersManual = CarLabels::bool($car->owners_manual);
        $vehicleFolder = CarLabels::bool($car->vehicle_folder);
        $huAuReport = CarLabels::bool($car->hu_au_report);
    @endphp
    @if($rowOk($cocDocs) || $rowOk($svcBookStatus) || $rowOk($regCert) || $rowOk($ownersManual) || $rowOk($vehicleFolder) || $rowOk($huAuReport))
    <div class="cs-data-section cs-collapsible-mobile">
        <div class="cs-data-header" onclick="csToggleAccordion(this)">
            <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>Dokumenty</h2>
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

    {{-- Zużycie paliwa — collapsed on mobile by default --}}
    @if($rowOk($car->fuel_consumption) || $rowOk($car->co2_emission) || $rowOk($car->emission_class))
    <div class="cs-data-section cs-collapsible-mobile">
        <div class="cs-data-header" onclick="csToggleAccordion(this)">
            <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/><path d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/></svg>Zużycie paliwa</h2>
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

    {{-- E. WYPOSAŻENIE --}}
    @if($totalCount > 0)
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="list-checks" aria-hidden="true"></i> Wyposażenie <span style="font-size:13px;font-weight:500;color:var(--text-3);margin-left:8px">{{ $totalCount }} pozycji</span></h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-equipment-grid">
                @foreach($allEquipmentItems as $item)
                <div class="cs-equipment-item"><i data-lucide="check" aria-hidden="true"></i> {{ $item }}</div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- STAN WIZUALNY I ŚLADY UŻYTKOWANIA --}}
    @if($car->damages->count())
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="map-pin" aria-hidden="true" style="color:var(--yellow)"></i> Stan wizualny i ślady użytkowania</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <p style="font-size:12.5px;color:var(--text-3);margin-bottom:14px">Kliknij oznaczenie, aby zobaczyć informacje</p>
            <div class="cs-damages-tabs">
                @foreach($car->damages as $d)
                @php
                    $tabColor = match($d->type) { 'accident' => '#dc2626', 'repaired' => '#9ca3af', default => '#f59e0b' };
                @endphp
                    <button type="button" class="cs-damage-tab{{ $loop->first ? ' active' : '' }}" data-damage-tab="{{ $d->id }}" onclick="csSelDamage({{ $d->id }})"><span class="cs-damage-tab-ico" style="color:{{ $tabColor }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>{{ $d->area }}</button>
                @endforeach
            </div>
            <div class="cs-damage-grid">
                <div class="cs-damage-diagram">
                    <div class="cs-damage-diagram-inner">
                        @php
                            $bodyTypeMap = [
                                'sedan' => 'sedan', 'suv' => 'suv', 'coupé' => 'coupe', 'coupe' => 'coupe',
                                'bus' => 'van', 'van' => 'van', 'kombi' => 'kombi', 'hatchback' => 'hatchback',
                                'kabriolet' => 'sedan', 'cabriolet' => 'sedan', 'pickup' => 'suv',
                            ];
                            $btKey = strtolower($car->body_type ?? $car->category ?? 'sedan');
                            $topImg = $bodyTypeMap[$btKey] ?? 'sedan';
                        @endphp
                        <img src="/img/body-types-top/{{ $topImg }}.png" alt="" aria-hidden="true" draggable="false">
                        @foreach($car->damages as $d)
                        @php
                            $mColor = match($d->type) { 'accident' => '#dc2626', 'repaired' => '#9ca3af', default => '#f59e0b' };
                        @endphp
                        <button type="button" class="cs-damage-marker{{ $loop->first ? ' active' : '' }}" data-damage-marker="{{ $d->id }}" onclick="csSelDamage({{ $d->id }})" aria-label="{{ $d->area }}" style="left:{{ $d->position_x ?? 50 }}%;top:{{ $d->position_y ?? 50 }}%">
                            <span class="cs-damage-marker-dot" style="background:{{ $mColor }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="cs-damage-detail" id="csDamageDetail">
                    <div class="cs-damage-empty" id="csDamageEmpty" style="display:none">
                        <i data-lucide="mouse-pointer-click" aria-hidden="true"></i>
                        Wybierz element z mapy
                    </div>
                    @foreach($car->damages as $d)
                    @php
                        // Build image array: damage's own image first, then per-damage gallery photos.
                        // P1 fix: use R2-aware model accessors (CarDamage::image_url, CarImage::url)
                        // instead of asset('storage/'.path) — the latter returns
                        // https://certicars.pl/storage/... which 404s on production where
                        // FILESYSTEM_DISK=s3 (R2). The accessors handle local vs s3 disk +
                        // placeholder fallback for missing paths.
                        $dmgPhotos = [];
                        if ($d->image_url) {
                            $dmgPhotos[] = $d->image_url;
                        }
                        foreach ($d->photos ?? [] as $dp) {
                            $url = $dp->path ? $dp->url : null;
                            if ($url && !in_array($url, $dmgPhotos)) $dmgPhotos[] = $url;
                        }
                    @endphp
                    <div class="cs-damage-item{{ $loop->first ? ' active' : '' }}" id="csDamage-{{ $d->id }}">
                        @if(count($dmgPhotos))
                        <div class="cs-dmg-gallery" data-dmg-id="{{ $d->id }}">
                            {{-- Main image --}}
                            <div class="cs-dmg-gallery-stage">
                                @foreach($dmgPhotos as $pi => $pUrl)
                                <div class="cs-dmg-gallery-slide{{ $pi === 0 ? ' active' : '' }}" data-slide="{{ $pi }}">
                                    <img src="{{ $pUrl }}" alt="{{ $d->area }}" {{ $pi === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' }} decoding="async" onclick="csDmgLightbox(this)" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                                </div>
                                @endforeach
                                {{-- Label --}}
                                <div class="cs-dmg-gallery-label">{{ $d->area }}</div>
                                {{-- Counter + fullscreen --}}
                                <div class="cs-dmg-gallery-meta">
                                    <span class="cs-dmg-gallery-counter"><span class="cs-dmg-gallery-cur">1</span>/{{ count($dmgPhotos) }}</span>
                                    <button type="button" class="cs-dmg-gallery-fs" onclick="csDmgLightbox(this.closest('.cs-dmg-gallery-stage').querySelector('.cs-dmg-gallery-slide.active img'))" title="Pełny ekran"><i data-lucide="maximize-2" style="width:16px;height:16px"></i></button>
                                </div>
                                @if(count($dmgPhotos) > 1)
                                {{-- Arrows --}}
                                <button type="button" class="cs-dmg-gallery-arrow left" onclick="csDmgSlide(this,-1)"><i data-lucide="chevron-left" style="width:22px;height:22px"></i></button>
                                <button type="button" class="cs-dmg-gallery-arrow right" onclick="csDmgSlide(this,1)"><i data-lucide="chevron-right" style="width:22px;height:22px"></i></button>
                                @endif
                            </div>
                            {{-- Thumbnails — always shown --}}
                            <div class="cs-dmg-gallery-thumbs-wrap">
                                @if(count($dmgPhotos) > 1)
                                <button type="button" class="cs-dmg-thumb-arrow left" onclick="csDmgScrollThumbs(this,-1)"><i data-lucide="chevron-left" style="width:14px;height:14px"></i></button>
                                @endif
                                <div class="cs-dmg-gallery-thumbs">
                                    @foreach($dmgPhotos as $pi => $pUrl)
                                    <div class="cs-dmg-gallery-thumb{{ $pi === 0 ? ' active' : '' }}" data-slide="{{ $pi }}" onclick="csDmgGoSlide(this,{{ $pi }})">
                                        <img src="{{ $pUrl }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/images/placeholder-car.svg'">
                                    </div>
                                    @endforeach
                                </div>
                                @if(count($dmgPhotos) > 1)
                                <button type="button" class="cs-dmg-thumb-arrow right" onclick="csDmgScrollThumbs(this,1)"><i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>
                                @endif
                            </div>
                        </div>
                        @else
                        <div style="padding:40px;text-align:center;color:var(--text-3)">
                            <i data-lucide="image-off" style="width:32px;height:32px;margin-bottom:8px"></i>
                            <div style="font-size:13px">Brak zdjęć dla tego elementu</div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- G. STAN TECHNICZNY --}}
    @if($car->technical_conditions && count($car->technical_conditions))
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="shield-check" aria-hidden="true" style="color:#16a34a"></i> Stan techniczny</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            @php
                $techLabels = [
                    'engine' => 'Silnik', 'transmission' => 'Skrzynia biegów',
                    'suspension' => 'Zawieszenie', 'electronics' => 'Elektronika',
                    'body' => 'Nadwozie', 'brakes' => 'Układ hamulcowy', 'braking' => 'Układ hamulcowy',
                    'steering' => 'Układ kierowniczy', 'exhaust' => 'Układ wydechowy',
                    'ac' => 'Klimatyzacja', 'air_conditioning' => 'Klimatyzacja', 'aircon' => 'Klimatyzacja',
                    'airbags' => 'Poduszki powietrzne', 'air_bags' => 'Poduszki powietrzne',
                    'tires' => 'Opony', 'lights' => 'Oświetlenie',
                    'interior' => 'Wnętrze', 'underbody' => 'Podwozie',
                ];
                $techStatusLabels = [
                    'ok' => 'Sprawny', 'good' => 'Sprawny', 'sprawny' => 'Sprawny',
                    'fair' => 'Dostateczny', 'warning' => 'Wymaga uwagi',
                    'bad' => 'Niesprawny', 'broken' => 'Niesprawny',
                    'true' => 'Sprawny', '1' => 'Sprawny',
                ];
            @endphp
            <div class="cs-tech-list">
                @foreach($car->technical_conditions as $comp => $status)
                @php
                    $raw = is_array($status) ? ($status['status'] ?? $status[0] ?? 'OK') : $status;
                    $st = $techStatusLabels[mb_strtolower((string) $raw)] ?? $raw;
                    $compLabel = $techLabels[strtolower((string) $comp)] ?? ucfirst((string) $comp);
                @endphp
                <div class="cs-tech-row">
                    <span class="cs-tech-icon"><i data-lucide="check-circle" aria-hidden="true"></i></span>
                    <span class="cs-tech-name">{{ $compLabel }}</span>
                    <span class="cs-tech-status">{{ $st }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- H. NAGRANIE PRACY SILNIKA --}}
    @if($car->engine_video_url || $car->engine_video_path)
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="play-circle" aria-hidden="true" style="color:#0066ff"></i> Nagranie pracy silnika</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            @php
                $yt=null;$vim=null;$vidUrl=$car->engine_video_url;
                if($vidUrl){
                    if(preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/))([\w-]{11})~', $vidUrl, $m)) $yt=$m[1];
                    elseif(preg_match('~vimeo\.com/(\d+)~', $vidUrl, $m)) $vim=$m[1];
                }
            @endphp
            <div style="position:relative;border-radius:12px;overflow:hidden;background:#000;aspect-ratio:16/9;max-width:820px;margin:0 auto">
                @if($yt)
                    <iframe src="https://www.youtube.com/embed/{{ $yt }}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe>
                @elseif($vim)
                    <iframe src="https://player.vimeo.com/video/{{ $vim }}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe>
                @elseif($car->engine_video_path)
                    <video src="{{ $car->engine_video_file_url }}" controls preload="metadata" style="width:100%;height:100%;display:block"></video>
                @else
                    <div style="padding:20px;color:#fff">Film: <a href="{{ $vidUrl }}" target="_blank" style="color:#fff;text-decoration:underline">link</a></div>
                @endif
            </div>
        </div>
    </div>
    @endif


        <!-- WIDOK 360° (wnętrze + zewnętrze w jednej sekcji) -->
    @if($car->pano360Image || $car->exteriorPano360Image)
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> Widok 360°</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <p style="font-size:12.5px;color:var(--text-3);margin-bottom:16px;display:flex;align-items:center;gap:6px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 9l-3 3 3 3"/><path d="M2 12h20"/><path d="M19 15l3-3-3-3"/></svg>
                Przeciągnij, aby rozejrzeć się w 360°. Scroll = zoom.
            </p>
            <div class="cs-pano360-grid" style="grid-template-columns:{{ ($car->pano360Image && $car->exteriorPano360Image) ? '1fr 1fr' : '1fr' }}">
                @if($car->pano360Image)
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        Wnętrze
                    </div>
                    <div class="cs-pano360-embed" style="position:relative;aspect-ratio:16/9;background:#000;border-radius:12px;overflow:hidden">
                        <div id="csPano360Section" data-pano-src="{{ route('panorama.stream', $car->pano360Image) }}" style="width:100%;height:100%"></div>
                    </div>
                </div>
                @endif
                @if($car->exteriorPano360Image)
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Zewnętrze
                    </div>
                    <div class="cs-pano360-embed" style="position:relative;aspect-ratio:16/9;background:#000;border-radius:12px;overflow:hidden">
                        <div id="csPano360ExtSection" data-pano-src="{{ route('panorama.stream', $car->exteriorPano360Image) }}" style="width:100%;height:100%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif



    {{-- J. POMIARY GRUBOŚCI LAKIERU --}}
    @if($car->paint_measurements && count($car->paint_measurements))
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="paintbrush" aria-hidden="true"></i> Pomiary grubości lakieru</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:8px">
                <p style="font-size:12px;color:var(--text-3);display:flex;align-items:center;gap:6px;margin:0">
                    <i data-lucide="info" aria-hidden="true" style="width:14px;height:14px;flex-shrink:0"></i>
                    Pomiary wykonane profesjonalnym czujnikiem lakieru. Normy fabryczne: 90–150 µm.
                </p>
                <a href="#" style="font-size:12px;font-weight:700;color:var(--blue);display:inline-flex;align-items:center;gap:4px;text-decoration:none;white-space:nowrap">Jak czytać pomiary? <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
            </div>
            <div class="cs-paint-grid">
                @php
                    $paintPanelNames = [
                        0 => 'Dach', 1 => 'Maska', 2 => 'Błotnik P-L', 3 => 'Błotnik P-P',
                        4 => 'Drzwi P-L', 5 => 'Drzwi P-P', 6 => 'Błotnik T-L', 7 => 'Błotnik T-P',
                        8 => 'Drzwi T-L', 9 => 'Drzwi T-P', 10 => 'Klapa bagażnika',
                        11 => 'Zderzak przód', 12 => 'Zderzak tył', 13 => 'Próg lewy', 14 => 'Próg prawy',
                    ];
                @endphp
                @foreach($car->paint_measurements as $panel => $value)
                @php
                    $val = is_array($value) ? ($value['value'] ?? $value[0] ?? '') : $value;
                    $numVal = (int) preg_replace('/[^0-9]/', '', $val);
                    if($numVal <= 0) continue; // skip empty measurements
                    $panelLabel = is_array($value) && isset($value['area']) ? $value['area'] : (is_numeric($panel) ? ($paintPanelNames[$panel] ?? 'Panel '.($panel + 1)) : $panel);
                    $paintClass = $numVal > 300 ? 'paint-danger' : ($numVal > 150 ? 'paint-warn' : 'paint-ok');
                    $dotColor = $numVal > 300 ? '#ef4444' : ($numVal > 150 ? '#f59e0b' : '#10b981');
                @endphp
                <div class="cs-paint-item {{ $paintClass }}">
                    <div class="cs-paint-label">{{ $panelLabel }}</div>
                    <div class="cs-paint-value"><span class="cs-paint-dot" style="background:{{ $dotColor }}"></span>{{ $val }} <span style="font-size:12px;font-weight:500;color:var(--text-3)">µm</span></div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:16px;padding-top:14px;border-top:1px solid #eeeef0;font-size:11px;font-weight:600">
                <span style="display:flex;align-items:center;gap:6px"><span style="width:12px;height:12px;border-radius:3px;background:#10b981"></span> Lakier fabryczny (90–150 µm)</span>
                <span style="display:flex;align-items:center;gap:6px"><span style="width:12px;height:12px;border-radius:3px;background:#f59e0b"></span> Ponownie lakierowany (150–300 µm)</span>
                <span style="display:flex;align-items:center;gap:6px"><span style="width:12px;height:12px;border-radius:3px;background:#ef4444"></span> Naprawa / szpachla (powyżej 300 µm)</span>
            </div>
        </div>
    </div>
    @endif

        <!-- KOŁA I OPONY (CarOnSale style) -->
    @if($car->tireSets->count())
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="circle-dot" aria-hidden="true"></i> Koła i opony</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
        @foreach($car->tireSets as $set)
        @php
            $setTires = $set->tires;
            $positionLabels = [
                'front_left' => 'Przednia lewa',
                'front_right' => 'Przednia prawa',
                'rear_left' => 'Tylna lewa',
                'rear_right' => 'Tylna prawa',
            ];
        @endphp
        <div class="cs-tire-set">
            <h3 class="cs-tire-set-title">
                {{ $set->set_number }}. Komplet @if($set->is_mounted)(zamontowane) <i data-lucide="info" aria-hidden="true"></i>@endif
            </h3>
            <div class="cs-tire-table">
                <div class="cs-tire-th"></div>
                @foreach($setTires as $t)
                <div class="cs-tire-th" style="text-align:center">{{ $positionLabels[$t->position] ?? $t->position }}</div>
                @endforeach
                <div class="cs-tire-info">
                    @if($set->tire_type)
                    <div class="cs-tire-info-row"><div class="lbl">Rodzaj opon</div><div class="val">{{ $set->tire_type }}</div></div>
                    @endif
                    @if($set->rim)
                    <div class="cs-tire-info-row"><div class="lbl">Felga</div><div class="val">{{ $set->rim }}</div></div>
                    @endif
                    <div class="cs-tire-info-row"><div class="lbl">Głębokość bieżnika</div></div>
                    <div class="cs-tire-info-row"><div class="lbl">Stan</div></div>
                </div>
                @foreach($setTires as $t)
                @php
                    $depth = (float)($t->tread_depth ?? 0);
                    $hasIssue = $t->condition && is_array($t->condition) && count($t->condition) > 0;
                    $statusCls = $hasIssue ? 'warn' : 'ok';
                @endphp
                <div class="cs-tire-col">
                    <div class="cs-tire-col-head">
                        <div class="cs-tire-icon">
                            <svg class="wheel" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="36" cy="36" r="33" stroke="#d4d4d8" stroke-width="2" fill="#f4f4f5"/>
                                <circle cx="36" cy="36" r="24" stroke="#d4d4d8" stroke-width="1.5" fill="white"/>
                                <circle cx="36" cy="36" r="6" fill="#e4e4e7" stroke="#d4d4d8" stroke-width="1"/>
                                <line x1="36" y1="12" x2="36" y2="24" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="36" y1="48" x2="36" y2="60" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="12" y1="36" x2="24" y2="36" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="48" y1="36" x2="60" y2="36" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="19.8" y1="19.8" x2="27.5" y2="27.5" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="44.5" y1="44.5" x2="52.2" y2="52.2" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="52.2" y1="19.8" x2="44.5" y2="27.5" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="27.5" y1="44.5" x2="19.8" y2="52.2" stroke="#d4d4d8" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span class="cs-tire-status-icon {{ $statusCls }}">
                                @if($hasIssue)<i data-lucide="alert-triangle" aria-hidden="true"></i>
                                @else<i data-lucide="check" aria-hidden="true"></i>@endif
                            </span>
                        </div>
                        <div class="cs-tire-pos-name">{{ $positionLabels[$t->position] ?? $t->position }}</div>
                    </div>
                    <div class="cs-tire-data-row" style="font-weight:700">{{ $t->tread_depth ?? '—' }}</div>
                    <div class="cs-tire-data-row {{ $hasIssue ? 'warn-txt' : 'ok-txt' }}">
                        @if($hasIssue)
                            <i data-lucide="alert-triangle" aria-hidden="true" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:3px"></i>{{ implode(', ', $t->condition) }}
                        @else
                            <i data-lucide="check" aria-hidden="true" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:3px"></i>Brak nieprawidłowości
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @if($set->notes)
            <div style="margin-top:12px;padding:10px 14px;background:var(--yellow-bg);border-radius:10px;font-size:12.5px;color:var(--yellow-dark);display:flex;align-items:center;gap:8px">
                <i data-lucide="info" aria-hidden="true" style="width:14px;height:14px"></i> {{ $set->notes }}
            </div>
            @endif
        </div>
        @endforeach
        </div>
    </div>
    @endif

    @if($relatedCars->count())
    <div style="margin-top:20px;padding:24px 24px 32px;border-top:1px solid var(--border-l);max-width:1200px;margin-left:auto;margin-right:auto">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
            <div>
                <h2 style="font-size:22px;font-weight:800;letter-spacing:-.4px;color:var(--text)">Podobne pojazdy</h2>
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                <a href="{{ route('catalog') }}" style="font-size:13px;font-weight:700;color:var(--blue);display:inline-flex;align-items:center;gap:5px;text-decoration:none">
                    Zobacz wszystkie
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
                <div style="display:flex;gap:6px">
                    <button type="button" onclick="csRelScroll(-1)" style="width:36px;height:36px;border-radius:50%;border:1.5px solid #e5e5e7;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s" onmouseover="this.style.borderColor='#0066ff'" onmouseout="this.style.borderColor='#e5e5e7'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button type="button" onclick="csRelScroll(1)" style="width:36px;height:36px;border-radius:50%;border:1.5px solid #e5e5e7;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s" onmouseover="this.style.borderColor='#0066ff'" onmouseout="this.style.borderColor='#e5e5e7'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="cs-related-grid" id="csRelatedGrid">
            @foreach($relatedCars as $relCar)
            <a href="{{ route('catalog.show', $relCar) }}" class="vcard">
                <div class="vcard-img">
                    @if($relCar->primaryImage)
                        <img src="{{ $relCar->primaryImage->url }}" alt="{{ $relCar->primaryImage->alt }}" loading="lazy">
                    @else
                        <div class="vcard-placeholder"><svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg></div>
                    @endif
                    @if($relCar->is_featured)<div class="vcard-badge">Wyróżnione</div>@endif
                    <button class="lcard-fav" data-id="{{ $relCar->id }}" aria-label="Dodaj do ulubionych" onclick="event.preventDefault();event.stopPropagation();toggleFav(event,{{ $relCar->id }})">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>
                <div class="vcard-body">
                    <div class="vcard-title">{{ $relCar->title }}</div>
                    <div class="vcard-specs">
                        @if($relCar->first_registration)<span><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> {{ $relCar->first_registration }}</span>@endif
                        @if($relCar->fuel_type)<span>{{ $relCar->fuel_type }}</span>@endif
                        @if($relCar->transmission)<span>{{ $relCar->transmission }}</span>@endif
                        @if($relCar->power_hp)<span>{{ $relCar->power_hp }} KM</span>@endif
                    </div>
                    @if($relCar->mileage)<div style="font-size:12px;color:#6b7280;margin-bottom:8px">{{ number_format((float) $relCar->mileage,0,'',' ') }} km</div>@endif
                    <div class="vcard-bottom">
                        <div class="vcard-price">{{ $relCar->formatted_price }}</div>
                        <span class="vcard-link">Sprawdź <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

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
            <button type="submit" class="cs-inquiry-submit" id="csInquirySubmitBtn">
                <span id="csInquirySubmitText">Wyślij zapytanie</span>
                <span id="csInquirySubmitLoader" style="display:none">Wysyłanie...</span>
            </button>
            <p class="cs-inquiry-legal">Wysyłając formularz, wyrażasz zgodę na przetwarzanie danych osobowych w celu obsługi zapytania.</p>
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
<div class="cs-lightbox" id="csLightbox">
    <button class="cs-lightbox-close" onclick="csCloseLb()"><svg viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
    <button class="cs-lightbox-nav cs-lightbox-prev" onclick="csLbNav(-1)"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
    <img class="cs-lightbox-img" id="csLbImg" src="" alt="">
    <button class="cs-lightbox-nav cs-lightbox-next" onclick="csLbNav(1)"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
    <div class="cs-lightbox-counter" id="csLbCounter"></div>
</div>

{{-- STICKY MOBILE CTA --}}
<div class="cs-sticky-cta" id="csStickyBar">
    <a href="tel:+48585586090" class="cs-sticky-call">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        Zadzwoń
    </a>
    <button type="button" class="cs-sticky-msg" onclick="csOpenInquiry('general','sticky_mobile_cta')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
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
    lb.classList.add('open');
    document.body.style.overflow='hidden';
}
function csCloseLb(){
    document.getElementById('csLightbox').classList.remove('open');
    document.body.style.overflow='';
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

// Damage section — select damage by ID
function csSelDamage(id){
    const empty=document.getElementById('csDamageEmpty');if(empty)empty.style.display='none';
    document.querySelectorAll('.cs-damage-item').forEach(d=>d.classList.remove('active'));
    const it=document.getElementById('csDamage-'+id);if(it)it.classList.add('active');
    document.querySelectorAll('[data-damage-tab]').forEach(t=>t.classList.toggle('active',t.dataset.damageTab==id));
    document.querySelectorAll('[data-damage-marker]').forEach(m=>m.classList.toggle('active',m.dataset.damageMarker==id));
    // Reset gallery to first slide; force-eager-load it on tap so first paint is instant
    if(it){
        const g=it.querySelector('.cs-dmg-gallery');
        if(g){
            _csDmgSetSlide(g,0);
            const firstImg=g.querySelector('.cs-dmg-gallery-slide img');
            if(firstImg&&firstImg.loading==='lazy')firstImg.loading='eager';
        }
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
    lb.classList.add('open');
    document.body.style.overflow='hidden';
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
                    input.style.borderColor = '#ef4444';
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
