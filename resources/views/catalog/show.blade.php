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
@endsection

@section('styles')
.cs-wrap{padding:0 0 40px;background:#f5f5f7;min-height:100vh;overflow-x:hidden}
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
.cs-sidebar-certi svg{width:14px;height:14px;stroke:#4ea3ff;stroke-width:2.5;fill:none;flex-shrink:0}

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

.cs-gallery-thumbs{display:flex;gap:6px;padding:10px 10px;overflow-x:auto;background:#fff}
.cs-thumb{width:96px;height:64px;object-fit:cover;cursor:pointer;border-radius:6px;flex-shrink:0;opacity:.55;border:2px solid transparent;transition:all .15s}
.cs-thumb.active,.cs-thumb:hover{opacity:1;border-color:var(--blue)}
.cs-thumb[data-hidden]{display:none}
.cs-thumb-360{width:96px;height:64px;flex-shrink:0;border-radius:6px;border:2px solid transparent;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;background:linear-gradient(135deg,#e8f1ff,#d0e0ff);cursor:pointer;transition:all .15s;font-size:10px;font-weight:700;color:#0066ff}
.cs-thumb-360:hover,.cs-thumb-360.active{border-color:#0066ff;background:linear-gradient(135deg,#d0e0ff,#b8d4ff)}
.cs-thumb-360 svg{width:20px;height:20px;stroke:#0066ff;fill:none;stroke-width:1.8}
.cs-thumb-more{width:96px;height:64px;flex-shrink:0;border-radius:6px;border:2px solid transparent;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1px;background:#e8e8ea;cursor:pointer;transition:all .15s;font-weight:800;color:#374151;font-size:16px}
.cs-thumb-more span{font-size:9px;font-weight:600;color:#6b7280}
.cs-thumb-more:hover{background:#ddd;border-color:#bbb}

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
.cs-damages-tabs{display:flex;gap:8px;overflow-x:auto;padding-bottom:8px;margin-bottom:16px;-webkit-overflow-scrolling:touch;max-width:100%}
.cs-damage-tab{flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--yellow-bg);color:var(--yellow-dark);border:1px solid #fde68a;border-radius:50px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;transition:all .15s}
.cs-damage-tab.active{background:var(--yellow);color:#fff;border-color:var(--yellow)}
.cs-damage-tab i{width:12px;height:12px}
.cs-damage-grid{display:grid;grid-template-columns:300px 1fr;gap:0;border:1px solid #eeeef0;border-radius:14px;overflow:hidden;background:#fff}
.cs-damage-diagram{background:#f5f5f7;position:relative;overflow:hidden;min-height:500px}
.cs-damage-diagram-inner{position:absolute;inset:0;overflow:hidden}
.cs-damage-diagram-inner>img{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) scale(1.7);width:100%;height:auto;pointer-events:none}
.cs-damage-marker{position:absolute;transform:translate(-50%,-50%);cursor:pointer;z-index:5;background:none;border:none;padding:0}
.cs-damage-marker-dot{width:28px;height:28px;background:var(--yellow);border:2.5px solid #fff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 8px rgba(245,158,11,.4);transition:all .15s}
.cs-damage-marker.active .cs-damage-marker-dot{transform:scale(1.2);box-shadow:0 0 0 4px rgba(245,158,11,.25),0 2px 8px rgba(245,158,11,.4)}
.cs-damage-marker:hover .cs-damage-marker-dot{transform:scale(1.15)}
.cs-damage-marker-dot i{width:13px;height:13px;stroke-width:2.6}
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
.cs-data-header h2{font-size:17px;font-weight:700;color:#1a1a1a;letter-spacing:-.2px;display:flex;align-items:center;gap:0;margin:0;line-height:1.3;font-family:'Inter',sans-serif}
.cs-data-header h2 i,.cs-data-header h2 svg{display:none}
.cs-data-header .chev{display:none}
.cs-data-body{display:block;padding:20px 24px 24px}
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
    .cs-thumb-more{width:72px;height:48px;font-size:14px}
    .cs-thumb-more span{font-size:8px}
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
    .cs-data-section{border-radius:16px;border:none;box-shadow:0 1px 8px rgba(0,0,0,.06);overflow:hidden}
    .cs-data-header{padding:20px 20px 16px}
    .cs-data-header h2{font-size:18px;font-weight:900;letter-spacing:-.3px}
    .cs-data-body{padding:0 20px 20px}
    .cs-data-row{font-size:14px;padding:14px 0;border-bottom:1px solid #f0f0f2}
    .cs-data-row:last-child{border-bottom:none}
    .cs-data-row .lbl{font-weight:700;color:#1a1a1a;min-width:0;flex-shrink:1}
    .cs-data-row .val{font-weight:700;color:#1a1a1a;font-size:14px;max-width:55%;text-align:right}
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
    .cs-damage-grid{grid-template-columns:1fr;min-height:auto;border:none;border-radius:0}
    .cs-damage-diagram{border-radius:14px;border:1px solid #eeeef0}
    .cs-damage-diagram-inner{min-height:300px}
    .cs-damage-marker-dot{width:24px;height:24px}
    .cs-damage-marker-dot i{width:11px;height:11px}
    .cs-damage-detail{padding:16px;border-left:none;border-top:1px solid #eeeef0}
    .cs-damage-item h3{font-size:14px}
    .cs-damage-item p{font-size:12.5px}
    .cs-damage-tags span{font-size:10px;padding:4px 9px}
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
    .cs-data-section{border-radius:14px}
    .cs-data-row .val{max-width:50%}
    .cs-price-value{font-size:26px}
    .cs-sections-2col{padding:0 12px}
    .cs-nav-btn{padding:6px 8px;font-size:11px}
    .cs-damage-diagram-inner{min-height:260px}
    .cs-damage-marker-dot{width:22px;height:22px;border-width:2px}
    .cs-damage-marker-dot i{width:10px;height:10px}
    .cs-paint-item{padding:14px 16px!important}
    .cs-paint-value{font-size:15px!important}
    .cs-paint-label{font-size:13px!important}
    .cs-tire-icon{width:40px!important;height:40px!important}
    .cs-tire-col{padding:14px 10px!important}
    .cs-tire-data-row:first-of-type{font-size:16px!important}
    .cs-data-body{padding:0 16px 16px}
    .cs-data-header{padding:16px}
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
                    <div id="csPanoramaContainer" style="width:100%;height:100%;background:#000" data-pano-src="{{ $car->pano360Image->url }}"></div>
                    <div style="position:absolute;top:14px;left:50%;transform:translateX(-50%);background:rgba(10,10,10,.78);color:#fff;font-size:12px;padding:7px 14px;border-radius:50px;display:flex;align-items:center;gap:8px;backdrop-filter:blur(6px);font-weight:600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>
                        Przeciągnij, aby rozejrzeć się we wnętrzu
                    </div>
                </div>
                @endif

                {{-- ===== 360° PANORAMA VIEWER (exterior) ===== --}}
                @if($car->exteriorPano360Image)
                <div class="cs-gallery-main cs-pano360ext" id="csPano360ext" style="background:#000">
                    <div id="csPanoramaExtContainer" style="width:100%;height:100%;background:#000" data-pano-src="{{ $car->exteriorPano360Image->url }}"></div>
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
                @php $maxThumbs = 6; $showMore = $galleryList->count() > $maxThumbs; @endphp
                @foreach($galleryList->take($showMore ? $maxThumbs - 1 : $maxThumbs) as $i => $img)
                    <img src="{{ $img->url }}" loading="lazy" alt="{{ $img->alt }}" class="cs-thumb {{ $i===0 && !$car->exteriorPano360Image ? 'active' : '' }}" data-type="gallery" data-idx="{{ $i }}" onclick="csSelImg(this,{{ $i+1 }})" ondblclick="openCarGallery({{ $i }})" tabindex="0" onkeypress="if(event.key==='Enter')csSelImg(this,{{ $i+1 }})">
                @endforeach
                @if($showMore)
                <div class="cs-thumb-more" onclick="openCarGallery({{ $maxThumbs - 1 }})" title="Zobacz wszystkie zdjęcia" tabindex="0" role="button" onkeypress="if(event.key==='Enter')this.click()">
                    +{{ $galleryList->count() - $maxThumbs + 1 }}
                    <span>Zobacz więcej</span>
                </div>
                @endif
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
                    <button type="button" class="cs-calc-cta" onclick="csOpenInquiry('financing')">
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
                        @if($car->fuel_type)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg>{{ $car->fuel_type }}</span>@endif
                        @if($car->power_hp)<span style="display:inline-flex;align-items:center;gap:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:600;color:#1f2937"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>{{ $car->power_hp }} KM</span>@endif
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
                        <span class="val">{{ $car->fuel_type }}</span>
                    </div>
                    @endif
                    @if($car->transmission)
                    <div class="cs-sidebar-summary-row">
                        <svg class="cs-row-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22V8"/><path d="m5 12-3 3 3 3"/><path d="m19 12 3 3-3 3"/><path d="M2 15h20"/></svg>
                        <span class="lbl">Skrzynia biegów</span>
                        <span class="val">{{ $car->transmission }}</span>
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
                        <span class="val">{{ $car->body_type ?? $car->category }}</span>
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
                    <button type="button" class="cs-btn-message" onclick="csOpenInquiry('general')">
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
                        <a href="{{ route('car.pdf', $car->slug) }}" class="cs-sidebar-certi" title="Pobierz raport CertiCheck PDF">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                            CertiCheck
                        </a>
                        @endif
                    </div>
                </div>
                <!-- CTA BUTTONS (mobile) -->
                <div class="cs-mob-cta" style="display:none;grid-template-columns:1fr 1fr;gap:10px;padding:0 22px 16px">
                    <a href="tel:+48585586090" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#0066ff;color:#fff;padding:14px 16px;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;border:none">Zadzwoń</a>
                    <button type="button" onclick="csOpenInquiry('general')" style="display:flex;align-items:center;justify-content:center;gap:6px;background:#fff;color:#1a1a1a;padding:14px 16px;border-radius:12px;font-size:15px;font-weight:700;border:1.5px solid #e5e7eb;cursor:pointer">Napisz wiadomość</button>
                </div>



                @if($car->is_imported || $car->country_registration)
                <div style="padding:0 22px 16px;display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f8f9fb;border:1px solid #e5e7eb;border-radius:12px">
                        <span style="width:32px;height:32px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#1e40af;flex-shrink:0">DE</span>
                        <div style="font-size:13px;font-weight:700;color:#1a1a1a;line-height:1.3">Sprowadzony z {{ $car->country_registration ?? 'Niemiec' }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f8f9fb;border:1px solid #e5e7eb;border-radius:12px">
                        <span style="width:32px;height:32px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#166534;flex-shrink:0">OK</span>
                        <div style="font-size:13px;font-weight:700;color:#1a1a1a;line-height:1.3">Opłacona akcyza</div>
                    </div>
                </div>
                @endif

            </div>{{-- /cs-sidebar-card --}}

            {{-- INFO TILES — separate from main card, stretch to fill --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;flex:1;align-content:stretch">
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px 16px;text-align:center">
                    <div style="width:44px;height:44px;border-radius:12px;background:#e8f1ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#0066ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <div style="font-size:14px;font-weight:800;color:#1a1a1a;margin-bottom:4px">Dostępny od ręki</div>
                    <div style="font-size:11.5px;color:#6b7280;line-height:1.5">Auto gotowe do odbioru<br>Możliwa jazda próbna</div>
                </div>
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px 16px;text-align:center">
                    <div style="width:44px;height:44px;border-radius:12px;background:#e8f1ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#0066ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    </div>
                    <div style="font-size:14px;font-weight:800;color:#1a1a1a;margin-bottom:4px">Dostawa pod dom</div>
                    <div style="font-size:11.5px;color:#6b7280;line-height:1.5">Po wcześniejszych oględzinach<br>Cena do ustalenia</div>
                    <a href="{{ route('contact') }}" style="font-size:11.5px;font-weight:600;color:#0066ff;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-top:6px">Dowiedz się więcej <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>
            </div>
        </div>
        </div><!-- /cs-sidebar -->

    </div><!-- /cs-grid: gallery + sidebar only -->

    <!-- DATA SECTIONS (5-column horizontal layout) -->
    <div class="cs-data-section" style="max-width:calc(1200px - 48px);margin:16px auto 16px;padding:0 24px">
        <div class="cs-data-header"><h2>Dane pojazdu</h2></div>
        <div class="cs-data-columns">
            <!-- Col 1: Pochodzenie -->
            <div class="cs-data-col">
                <div class="cs-data-col-title"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>Pochodzenie</div>
                @if($car->country_registration)<div class="cs-data-row"><span class="lbl">Kraj pochodzenia</span><span class="val">{{ $car->country_registration }}</span></div>@endif
                @if($car->first_registration)<div class="cs-data-row"><span class="lbl">Pierwsza rejestracja</span><span class="val">{{ $car->first_registration }}</span></div>@endif
            </div>
            <!-- Col 2: Historia pojazdu -->
            <div class="cs-data-col">
                <div class="cs-data-col-title"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="m2 14 6-6 6 6 6-6"/></svg>Historia pojazdu</div>
                @if($car->previous_owners !== null)<div class="cs-data-row"><span class="lbl">Właściciele</span><span class="val"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px"><path d="M20 6 9 17l-5-5"/></svg>{{ $car->previous_owners == 0 ? 'Bezwypadkowy' : $car->previous_owners }}</span></div>@endif
                @if($car->service_book)<div class="cs-data-row"><span class="lbl">Sprawdzony w bazach</span><span class="val"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px"><path d="M20 6 9 17l-5-5"/></svg>{{ $car->service_book }}</span></div>@endif
                @if($car->vin)<div class="cs-data-row"><span class="lbl">VIN</span><span class="val" style="font-size:10.5px;letter-spacing:.3px">{{ $car->vin }}</span></div>@endif
            </div>
            <!-- Col 3: Serwisowanie i inspekcja -->
            <div class="cs-data-col">
                <div class="cs-data-col-title"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>Serwisowanie</div>
                @if($car->service_documentation)<div class="cs-data-row"><span class="lbl">Serwisowany w ASO</span><span class="val"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px"><path d="M20 6 9 17l-5-5"/></svg>{{ $car->service_documentation }}</span></div>@endif
                @if($car->last_service)<div class="cs-data-row"><span class="lbl">Ostatni przegląd</span><span class="val">{{ $car->last_service }}</span></div>@endif
                @if($car->next_inspection)<div class="cs-data-row"><span class="lbl">Następny przegląd</span><span class="val">{{ $car->next_inspection }}</span></div>@endif
            </div>
            <!-- Col 4: Dokumenty pojazdu -->
            <div class="cs-data-col">
                <div class="cs-data-col-title"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>Dokumenty</div>
                @if($car->coc_documents)<div class="cs-data-row"><span class="lbl">Komplet dokumentów</span><span class="val"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px"><path d="M20 6 9 17l-5-5"/></svg>{{ $car->coc_documents }}</span></div>@endif
                @if($car->vehicle_folder)<div class="cs-data-row"><span class="lbl">Dowód rejestracyjny</span><span class="val"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px"><path d="M20 6 9 17l-5-5"/></svg>{{ $car->vehicle_folder }}</span></div>@endif
                @if($car->hu_au_report)<div class="cs-data-row"><span class="lbl">Polisa OC</span><span class="val"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px"><path d="M20 6 9 17l-5-5"/></svg>{{ $car->hu_au_report }}</span></div>@endif
            </div>
            <!-- Col 5: Zużycie paliwa -->
            <div class="cs-data-col">
                <div class="cs-data-col-title"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/><path d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/></svg>Zużycie paliwa</div>
                @if($car->fuel_consumption)<div class="cs-data-row"><span class="lbl">Miasto</span><span class="val">{{ $car->fuel_consumption }} l/100km</span></div>@endif
                @if($car->co2_emission)<div class="cs-data-row"><span class="lbl">Emisja CO₂</span><span class="val">{{ $car->co2_emission }} g/km</span></div>@endif
                @if($car->emission_class)<div class="cs-data-row"><span class="lbl">Norma emisji</span><span class="val">{{ $car->emission_class }}</span></div>@endif
            </div>
        </div>
    </div>

        <!-- USZKODZENIA (CarOnSale style) -->
    @if($car->damages->count())
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="map-pin" aria-hidden="true" style="color:var(--yellow)"></i> Uszkodzenia pojazdu</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <p style="font-size:12.5px;color:var(--text-3);margin-bottom:14px">Kliknij oznaczenie, aby zobaczyć informacje</p>
            <div class="cs-damages-tabs">
                @foreach($car->damages as $d)
                    <button type="button" class="cs-damage-tab" data-damage-tab="{{ $d->id }}" onclick="csSelDamage({{ $d->id }})"><i data-lucide="alert-triangle" aria-hidden="true"></i> {{ $d->area }}</button>
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
                        <button type="button" class="cs-damage-marker" data-damage-marker="{{ $d->id }}" onclick="csSelDamage({{ $d->id }})" aria-label="Uszkodzenie: {{ $d->area }}" style="left:{{ $d->position_x ?? 50 }}%;top:{{ $d->position_y ?? 50 }}%">
                            <span class="cs-damage-marker-dot"><i data-lucide="alert-triangle" aria-hidden="true"></i></span>
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="cs-damage-detail" id="csDamageDetail">
                    <div class="cs-damage-empty" id="csDamageEmpty">
                        <i data-lucide="mouse-pointer-click" aria-hidden="true"></i>
                        Wybierz uszkodzenie z mapy
                    </div>
                    @foreach($car->damages as $d)
                    <div class="cs-damage-item" id="csDamage-{{ $d->id }}">
                        <h3><i data-lucide="alert-triangle" aria-hidden="true"></i> {{ $d->area }}</h3>
                        @if($d->tags && count($d->tags))
                        <div class="cs-damage-tags">
                            @foreach($d->tags as $t)<span>{{ $t }}</span>@endforeach
                        </div>
                        @endif
                        @if($d->description)<p>{{ $d->description }}</p>@endif
                        @if($d->image_path)
                        <a href="{{ $d->image_url }}" data-lightbox="{{ $d->image_url }}" data-gallery="damage-{{ $d->id }}" data-caption="{{ $d->area }}" style="display:block;margin-top:14px;border-radius:10px;overflow:hidden;background:var(--bg);cursor:zoom-in">
                            <img src="{{ $d->image_url }}" alt="{{ $d->area }}" loading="lazy" style="width:100%;max-height:260px;object-fit:cover;display:block">
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

        <!-- POMIARY GRUBOŚCI LAKIERU (przeniesione z CertiCheck) -->
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

    <!-- STAN TECHNICZNY + FILM SILNIKA (COS style — 2-kolumnowy grid) -->
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="shield-check" aria-hidden="true" style="color:#16a34a"></i> Stan techniczny i nagranie silnika</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-data-2col">
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Ocena stanu technicznego</div>
                    @php
                        $techLabels = [
                            'engine' => 'Silnik', 'transmission' => 'Skrzynia biegów',
                            'suspension' => 'Zawieszenie', 'electronics' => 'Elektronika',
                            'body' => 'Nadwozie', 'brakes' => 'Hamulce',
                            'steering' => 'Układ kierowniczy', 'exhaust' => 'Układ wydechowy',
                            'ac' => 'Klimatyzacja', 'air_conditioning' => 'Klimatyzacja',
                            'braking' => 'Układ hamulcowy',
                            'tires' => 'Opony', 'lights' => 'Oświetlenie',
                            'interior' => 'Wnętrze', 'underbody' => 'Podwozie',
                        ];
                    @endphp
                    @if($car->technical_conditions && count($car->technical_conditions))
                        @foreach($car->technical_conditions as $comp => $status)
                        @php
                            $st = is_array($status) ? ($status['status'] ?? $status[0] ?? 'OK') : $status;
                            $compLabel = $techLabels[strtolower($comp)] ?? ucfirst($comp);
                        @endphp
                        <div class="cs-data-row">
                            <span class="lbl" style="font-weight:600;color:#1a1a1a;display:flex;align-items:center;gap:8px">
                                <i data-lucide="check-circle" aria-hidden="true" style="width:16px;height:16px;color:#16a34a;flex-shrink:0"></i>
                                {{ $compLabel }}
                            </span>
                            <span class="val" style="color:#374151;font-weight:500">{{ $st }}</span>
                        </div>
                        @endforeach
                    @else
                        <p style="font-size:13px;color:var(--text-3)">Brak danych</p>
                    @endif
                </div>
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Nagranie pracy silnika</div>
                    @if($car->engine_video_url || $car->engine_video_path)
                        @php
                            $yt=null;$vim=null;$vidUrl=$car->engine_video_url;
                            if($vidUrl){
                                if(preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/))([\w-]{11})~', $vidUrl, $m)) $yt=$m[1];
                                elseif(preg_match('~vimeo\.com/(\d+)~', $vidUrl, $m)) $vim=$m[1];
                            }
                        @endphp
                        <div style="position:relative;border-radius:12px;overflow:hidden;background:#000;aspect-ratio:16/9">
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
                    @else
                        <p style="font-size:13px;color:var(--text-3)">Nagranie niedostępne</p>
                    @endif
                </div>
            </div>
        </div>
    </div>


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
                        <div id="csPano360Section" data-pano-src="{{ $car->pano360Image->url }}" style="width:100%;height:100%"></div>
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
                        <div id="csPano360ExtSection" data-pano-src="{{ $car->exteriorPano360Image->url }}" style="width:100%;height:100%"></div>
                    </div>
                </div>
                @endif
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

        <!-- WYPOSAŻENIE (CarOnSale style) -->
    @if($car->equipment)

    {{-- PEŁNE WYPOSAŻENIE --}}
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
window.openCarGallery=(i)=>{if(window.openLbAt)openLbAt(CAR_ALL_GALLERY, i||0)};

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

function csSelDamage(id){
    document.getElementById('csDamageEmpty').style.display='none';
    document.querySelectorAll('.cs-damage-item').forEach(d=>d.classList.remove('active'));
    const it=document.getElementById('csDamage-'+id);if(it)it.classList.add('active');
    document.querySelectorAll('[data-damage-tab]').forEach(t=>t.classList.toggle('active',t.dataset.damageTab==id));
    document.querySelectorAll('[data-damage-marker]').forEach(m=>{
        const dot=m.querySelector('.cs-damage-marker-dot');
        if(!dot)return;
        const a=m.dataset.damageMarker==id;
        dot.style.background=a?'#0a0a0a':'';
        dot.style.transform=a?'scale(1.15)':'';
    });
}
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

// Inquiry modal
function csOpenInquiry(type) {
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
    document.getElementById('csInquiryForm').style.display = 'block';
    document.getElementById('csInquiryForm').reset();
    typeInput.value = type; // reset clears it
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
        } else {
            alert(data.message || 'Wystąpił błąd. Spróbuj ponownie.');
        }
    }).catch(function() {
        alert('Wystąpił błąd połączenia. Spróbuj ponownie.');
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
    // Mobile fav button
    const mobBtn = document.getElementById('csMobFav');
    if(mobBtn){
        const id = +mobBtn.dataset.id;
        const isActive = favs.includes(id);
        const mobIcon = document.getElementById('csMobFavIcon');
        if(mobIcon){
            mobIcon.style.fill = isActive ? '#ef4444' : 'none';
            mobIcon.style.stroke = isActive ? '#ef4444' : '#9ca3af';
        }
        mobBtn.style.borderColor = isActive ? '#fca5a5' : '#e5e7eb';
        mobBtn.style.background = isActive ? '#fef2f2' : '#fff';
    }
}

</script>
@endpush
@endsection
