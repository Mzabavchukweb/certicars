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
html{scroll-behavior:smooth}
.cs-wrap{padding:0 0 40px;background:#f5f5f7;min-height:100vh;overflow-x:hidden}
.cs-wrap .container{max-width:1200px;padding-left:24px;padding-right:24px;box-sizing:border-box;overflow:hidden}
.cs-wrap *,.cs-wrap *::before,.cs-wrap *::after{box-sizing:border-box}

/* 2-COLUMN DATA BLOCKS (COS style — side by side cards within a section) */
.cs-data-2col{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:768px){.cs-data-2col{grid-template-columns:1fr}}
.cs-data-block{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px}
.cs-data-block-title{font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #e5e7eb}


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

.cs-grid{display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:24px;margin-bottom:8px;min-width:0;align-items:start;overflow:hidden}
.cs-sidebar{position:sticky;top:92px;align-self:start;min-width:0}

/* SIDEBAR CERTICHECK BADGE (catalog card style) */
.cs-sidebar-certi{display:inline-flex;align-items:center;gap:5px;background:rgba(0,0,0,.85);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);color:#fff;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;letter-spacing:.3px;text-decoration:none;transition:all .18s;border:none;cursor:pointer}
.cs-sidebar-certi:hover{background:rgba(0,0,0,.95);transform:translateY(-1px);box-shadow:0 3px 10px rgba(0,0,0,.2)}
.cs-sidebar-certi svg{width:14px;height:14px;stroke:#4ea3ff;stroke-width:2.5;fill:none;flex-shrink:0}

/* GALLERY — edge-to-edge, no frame */
.cs-gallery{background:#e8e8ea;border-radius:14px;overflow:hidden;border:1px solid #e5e5e7;width:100%;max-width:100%;box-sizing:border-box}
.cs-gallery-stage{position:relative;width:100%;aspect-ratio:16/9;background:#e8e8ea;overflow:hidden}
.cs-gallery-main{position:absolute;inset:0;background:#e8e8ea;display:flex;align-items:center;justify-content:center;width:100%;height:100%;box-sizing:border-box}
.cs-gallery-main:not(.active){display:none!important}
.cs-gallery-main img{max-width:100%;max-height:100%}
.cs-gallery-main img{width:100%;height:100%;object-fit:cover}
.cs-gallery-main .empty{color:var(--border)}
.cs-gallery-main .empty i{width:80px;height:80px}
.cs-gallery-counter{position:absolute;bottom:12px;right:12px;background:rgba(10,10,10,.75);color:#fff;padding:6px 12px;border-radius:50px;font-size:12px;display:flex;align-items:center;gap:5px;backdrop-filter:blur(10px);font-weight:600}
.cs-gallery-counter i{width:13px;height:13px}

/* GALLERY MEDIA TABS (COS style — flat inline row ABOVE gallery) */
.cs-gallery-tabs-wrap{position:relative;overflow:hidden;max-width:100%}
.cs-gallery-tabs-wrap::after{content:'';position:absolute;right:0;top:0;bottom:0;width:48px;background:linear-gradient(to right,transparent,#f5f5f7);pointer-events:none;border-radius:0}
.cs-gallery-tabs{display:flex;align-items:center;gap:20px;padding:14px 0 10px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;max-width:100%}
.cs-gallery-tabs::-webkit-scrollbar{display:none}
.cs-gallery-tab{display:inline-flex;align-items:center;gap:7px;padding:0;background:none;border:none;font-size:13.5px;font-weight:500;color:#6b7280;cursor:pointer;transition:color .15s;white-space:nowrap;position:relative;line-height:1.4}
.cs-gallery-tab:hover{color:#1a1a1a}
.cs-gallery-tab.active{color:#0a0a0a;font-weight:600}
.cs-gallery-tab svg,.cs-gallery-tab i{width:18px;height:18px;flex-shrink:0;color:inherit}
.cs-gallery-tab .cs-tab-count{font-size:12px;font-weight:400;color:inherit}
.cs-gallery-tab.disabled{opacity:.35;pointer-events:none}

.cs-gallery-thumbs{display:flex;gap:6px;padding:10px 10px;overflow-x:auto;background:#fff}
.cs-thumb{width:96px;height:64px;object-fit:cover;cursor:pointer;border-radius:6px;flex-shrink:0;opacity:.55;border:2px solid transparent;transition:all .15s}
.cs-thumb.active,.cs-thumb:hover{opacity:1;border-color:var(--blue)}
.cs-thumb[data-hidden]{display:none}

/* SIDEBAR CARD (COS style — white) */
.cs-sidebar-card{background:#fff;border:1px solid #e5e5e7;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.06)}

/* SIDEBAR VEHICLE SUMMARY (COS-style key-value pairs) */
.cs-sidebar-summary{padding:0 22px;border-bottom:1px solid #f0f0f2}
.cs-sidebar-summary-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid #f5f5f5;font-size:13px}
.cs-sidebar-summary-row:last-child{border-bottom:none}
.cs-sidebar-summary-row .lbl{color:#6b7280;font-weight:400}
.cs-sidebar-summary-row .val{font-weight:700;color:#1a1a1a;text-align:right}

/* PRICE SECTION (inside card) */
.cs-price-section{padding:22px 22px 14px;border-bottom:1px solid #f0f0f2}
.cs-price-label{font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.cs-price-value{font-size:36px;font-weight:900;letter-spacing:-1px;color:#1a1a1a;line-height:1}
.cs-price-value small{display:block;font-size:12px;font-weight:500;color:#9ca3af;letter-spacing:0;margin-top:4px}

/* CTA BUTTONS (inside card) */
.cs-price-actions{padding:18px 22px 22px;display:flex;flex-direction:column;gap:8px}

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
.cs-calc-overlay{position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}
.cs-calc-overlay.open{opacity:1;visibility:visible}
.cs-calc-float{position:fixed;right:24px;bottom:24px;z-index:9999;width:400px;max-height:calc(100vh - 48px);overflow-y:auto;transform:translateY(20px) scale(.96);opacity:0;visibility:hidden;transition:transform .3s cubic-bezier(.22,1,.36,1),opacity .25s,visibility .25s;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.25),0 0 0 1px rgba(0,0,0,.08)}
.cs-calc-float.open{transform:translateY(0) scale(1);opacity:1;visibility:visible}
.cs-calc-float-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#fff;border-bottom:1px solid #f0f0f2}
.cs-calc-float-header h3{font-size:15px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;display:flex;align-items:center;gap:8px}
.cs-calc-float-header h3 svg{width:18px;height:18px;stroke:#0066ff}
.cs-calc-float-close{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:none;background:#f5f5f7;border-radius:8px;cursor:pointer;transition:all .15s;color:#6b7280}
.cs-calc-float-close:hover{background:#e5e5e7;color:#0a0a0a}
.cs-calc-float-close svg{width:16px;height:16px}
@media(max-width:768px){
    .cs-calc-float{right:8px;left:8px;bottom:8px;width:auto;max-height:calc(100vh - 16px);border-radius:16px}
    .cs-calc-float-header{padding:10px 16px}
    .cs-calc-float-header h3{font-size:13px;gap:6px}
    .cs-calc-float-header h3 svg{width:15px;height:15px}
    .cs-calc-float-close{width:28px;height:28px}
    .cs-calc-float-close svg{width:14px;height:14px}
    .cs-calc-tab{padding:8px 10px;font-size:11.5px;gap:5px}
    .cs-calc-tab svg{width:13px;height:13px}
    .cs-calc-body{padding:12px 16px 8px}
    .cs-calc-field{margin-bottom:10px}
    .cs-calc-field label{font-size:10px;margin-bottom:5px;letter-spacing:.2px}
    .cs-calc-range-row{gap:8px}
    .cs-calc-range-row input[type=range]{height:5px}
    .cs-calc-range-row input[type=range]::-webkit-slider-thumb{width:18px;height:18px;border-width:2px}
    .cs-calc-range-val{font-size:11.5px;min-width:56px}
    .cs-calc-range-labels{font-size:9px;margin-top:1px}
    .cs-calc-result{padding:12px 16px;border-radius:0 0 16px 16px}
    .cs-calc-result-label{font-size:9.5px;margin-bottom:3px}
    .cs-calc-result-value{font-size:24px;margin-bottom:8px}
    .cs-calc-result-details{gap:4px;margin-bottom:6px}
    .cs-calc-result-details>div{font-size:11px}
    .cs-calc-disclaimer{font-size:9px;line-height:1.3}
}

/* CALC INTERNALS (shared) */
.cs-calc{background:#fff;border-radius:0;overflow:hidden}
.cs-calc-header{padding:0;border-bottom:1px solid #e5e5e5}
.cs-calc-tabs{display:grid;grid-template-columns:1fr 1fr}
.cs-calc-tab{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 12px;font-size:13px;font-weight:700;color:#6b7280;background:#f9fafb;border:none;cursor:pointer;transition:all .2s;letter-spacing:-.1px}
.cs-calc-tab:first-child{border-right:1px solid #e5e5e5}
.cs-calc-tab.active{background:#fff;color:#0066ff}
.cs-calc-tab svg{width:16px;height:16px}
.cs-calc-body{padding:20px 22px 16px}
.cs-calc-field{margin-bottom:18px}
.cs-calc-field:last-child{margin-bottom:0}
.cs-calc-field label{display:block;font-size:12px;font-weight:700;color:#1a1a1a;margin-bottom:10px;text-transform:uppercase;letter-spacing:.3px}
.cs-calc-range-row{display:flex;align-items:center;gap:12px}
.cs-calc-range-row input[type=range]{flex:1;-webkit-appearance:none;appearance:none;height:6px;background:#e5e5e5;border-radius:3px;outline:none}
.cs-calc-range-row input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;background:#0066ff;border-radius:50%;cursor:pointer;box-shadow:0 2px 6px rgba(0,102,255,.35);border:3px solid #fff;transition:transform .15s}
.cs-calc-range-row input[type=range]::-webkit-slider-thumb:hover{transform:scale(1.15)}
.cs-calc-range-row input[type=range]::-moz-range-thumb{width:14px;height:14px;background:#0066ff;border-radius:50%;cursor:pointer;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,102,255,.35)}
.cs-calc-range-val{font-size:13px;font-weight:800;color:#1a1a1a;min-width:72px;text-align:right;white-space:nowrap}
.cs-calc-range-labels{display:flex;justify-content:space-between;font-size:10.5px;color:#9ca3af;margin-top:4px;font-weight:500}
.cs-calc-result{background:linear-gradient(135deg,#0a0a0a 0%,#1a1a2e 100%);padding:22px;color:#fff;border-radius:0 0 20px 20px}
.cs-calc-result-label{font-size:11px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.cs-calc-result-value{font-size:34px;font-weight:900;letter-spacing:-1px;line-height:1.1;margin-bottom:16px}
.cs-calc-result-details{display:flex;flex-direction:column;gap:8px;margin-bottom:14px}
.cs-calc-result-details>div{display:flex;justify-content:space-between;font-size:12.5px}
.cs-calc-result-details .lbl{color:rgba(255,255,255,.5)}
.cs-calc-result-details .val{font-weight:700;color:#fff}
.cs-calc-disclaimer{font-size:10px;color:rgba(255,255,255,.35);line-height:1.4}

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
.cs-damage-grid{display:grid;grid-template-columns:280px 1fr;gap:24px;min-height:340px}
.cs-damage-diagram{background:var(--bg);border-radius:12px;padding:20px;display:flex;align-items:center;justify-content:center;position:relative}
.cs-damage-diagram-inner{position:relative;width:200px;height:370px}
.cs-damage-marker{position:absolute;transform:translate(-50%,-50%);cursor:pointer;z-index:5;background:none;border:none;padding:0}
.cs-damage-marker-dot{width:30px;height:30px;background:var(--yellow);border:3px solid #fff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 8px rgba(245,158,11,.4);transition:transform .15s}
.cs-damage-marker:hover .cs-damage-marker-dot{transform:scale(1.15)}
.cs-damage-marker-dot i{width:14px;height:14px;stroke-width:2.6}
.cs-damage-detail{padding:20px;display:flex;flex-direction:column}
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
.cs-paint-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:0;border:1px solid #eeeef0;border-radius:12px;overflow:hidden;background:#fff}
.cs-paint-item{background:#fff;padding:14px 16px;border-right:1px solid #eeeef0;border-bottom:1px solid #eeeef0}
.cs-paint-label{font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);margin-bottom:4px}
.cs-paint-value{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.3px}
.cs-paint-warn{color:#f59e0b}
.cs-paint-danger{color:#ef4444}

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
    .cs-tire-table{grid-template-columns:180px repeat(4,1fr)}
    .cs-equipment-grid{grid-template-columns:1fr}
}
/* RELATED CARS */
.cs-related-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
@media(max-width:900px){.cs-related-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.cs-related-grid{grid-template-columns:1fr;gap:12px}}

@media(max-width:768px){
    .cs-wrap .container{padding-left:14px;padding-right:14px}
    .cs-nav-bar{flex-direction:row;flex-wrap:wrap;gap:8px;align-items:center;padding:10px 0}
    .cs-nav-bar-left{flex:0 0 auto}
    .cs-nav-bar-left .cs-nav-btn{width:auto}
    .cs-nav-bar-right{margin-left:auto;display:flex;gap:6px}
    .cs-nav-btn{padding:7px 10px;font-size:11.5px;gap:4px}
    .cs-nav-btn svg{width:12px;height:12px}
    .cs-head{flex-direction:column;gap:6px}
    .cs-head h1{font-size:22px;letter-spacing:-.4px}
    .cs-meta{font-size:11px;gap:4px}
    .cs-keyfacts{gap:6px;padding:10px 0 12px;overflow-x:auto;flex-wrap:nowrap;-webkit-overflow-scrolling:touch;scrollbar-width:none;-ms-overflow-style:none}
    .cs-keyfacts::-webkit-scrollbar{display:none}
    .cs-keyfact{padding:7px 10px;font-size:11.5px;flex-shrink:0}
    .cs-gallery{border-radius:10px}
    .cs-gallery-tabs{gap:14px;padding:10px 0 8px}
    .cs-gallery-tab{font-size:12px;gap:5px}
    .cs-gallery-tab svg,.cs-gallery-tab i{width:15px;height:15px}
    .cs-gallery-thumbs{padding:8px}
    .cs-thumb{width:72px;height:48px}
    .cs-sidebar-card{border-radius:14px}
    .cs-price-section{padding:18px 16px 12px}
    .cs-price-actions{padding:14px 16px 16px}
    .cs-price-actions .btn{padding:12px 16px;font-size:14px}
    .cs-sidebar-summary{padding:0 16px}
    .cs-sidebar-summary-row{font-size:12.5px;padding:10px 0}
    .cs-calc-trigger{padding:12px 14px;font-size:12px}
    .cs-data-header{padding:14px 16px}
    .cs-data-header h2{font-size:15px}
    .cs-data-body{padding:14px 16px 16px}
    .cs-data-row{font-size:12.5px;padding:8px 0}
    .cs-data-row .lbl{flex-shrink:1;min-width:0}
    .cs-data-row .val{max-width:55%;font-size:12.5px}
    .cs-sections-2col{padding:0 14px;gap:12px}
    .cs-damage-grid{grid-template-columns:1fr;gap:12px;min-height:auto}
    .cs-damage-diagram{padding:14px}
    .cs-damage-diagram-inner{width:160px;height:280px}
    .cs-damage-marker-dot{width:26px;height:26px}
    .cs-damage-marker-dot i{width:12px;height:12px}
    .cs-damage-detail{padding:14px}
    .cs-damage-item h3{font-size:14px}
    .cs-damage-item p{font-size:12.5px}
    .cs-damage-tags span{font-size:10px;padding:4px 9px}
    .cs-status-grid{grid-template-columns:1fr}
    .cs-svc-grid{grid-template-columns:1fr}
    .cs-tire-table{grid-template-columns:1fr}
    .cs-tire-th{display:none}
    .cs-tire-info{border-right:none;border-bottom:1px solid var(--border-l)}
    .cs-tire-col{border-right:none;border-bottom:1px solid var(--border-l);flex-direction:row;flex-wrap:wrap}
    .cs-tire-col:last-child{border-bottom:none}
    .cs-tire-col-head{border-right:none;width:100%;border-bottom:1px solid var(--border-l);padding:10px}
    .cs-tire-icon{width:56px;height:56px}
    .cs-tire-data-row{flex:1;min-width:50%;border-right:1px solid var(--border-l);padding:8px 10px;font-size:12px}
    .cs-tire-data-row:last-child{border-right:none}
    .cs-equipment-grid{grid-template-columns:1fr}
    .cs-equipment-item{padding:10px 0;font-size:12.5px}
    .cs-feat-eq{grid-template-columns:1fr}
    .cs-price-value{font-size:28px}
    .cs-fuel-grid{grid-template-columns:1fr 1fr}
    .cs-pano360-embed{aspect-ratio:4/3}
    .cs-pano360-grid{grid-template-columns:1fr!important}
    .cs-paint-grid{grid-template-columns:repeat(2,1fr)}
    .cs-paint-item{padding:12px 14px}
    .cs-paint-value{font-size:16px}
    .cs-sub-hd{padding:12px 16px}
    .cs-sub-bd{padding:2px 16px 12px}
    .cs-data-block{padding:16px}
    .cs-data-block-title{font-size:13px;margin-bottom:10px;padding-bottom:8px}
    .cs-card{padding:20px}
}
@media(max-width:500px){
    .cs-wrap .container{padding-left:12px;padding-right:12px}
    .cs-head h1{font-size:19px}
    .cs-keyfact{padding:6px 8px;font-size:10.5px}
    .cs-keyfact svg{width:13px;height:13px}
    .cs-gallery{border-radius:8px}
    .cs-sidebar-card{border-radius:12px}
    .cs-data-section{border-radius:12px}
    .cs-sidebar-summary-row{flex-direction:column;align-items:flex-start;gap:2px}
    .cs-sidebar-summary-row .val{text-align:left}
    .cs-data-row .val{max-width:50%}
    .cs-price-value{font-size:26px}
    .cs-sections-2col{padding:0 12px}
    .cs-nav-btn{padding:6px 8px;font-size:11px}
    .cs-damage-diagram-inner{width:140px;height:240px}
    .cs-damage-marker-dot{width:24px;height:24px;border-width:2px}
    .cs-damage-marker-dot i{width:11px;height:11px}
    .cs-paint-grid{grid-template-columns:repeat(2,1fr)}
    .cs-paint-item{padding:10px 12px}
    .cs-paint-value{font-size:15px}
    .cs-paint-label{font-size:9.5px}
    .cs-tire-icon{width:48px;height:48px}
    .cs-data-body{padding:12px 14px 14px}
    .cs-data-header{padding:12px 14px}
    .cs-gallery-tabs{gap:10px;padding:8px 0 6px}
    .cs-gallery-tab{font-size:11px}
}
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
            @php
                $metaParts = array_values(array_filter([
                    $car->first_registration ?: null,
                    $car->mileage ? number_format((float) $car->mileage, 0, '', ' ') . ' km' : null,
                    $car->fuel_type ?: null,
                    $car->transmission ?: null,
                    $car->power_hp ? $car->power_hp . ' KM' : null,
                ]));
            @endphp
            @if(count($metaParts))
            <div class="cs-meta">
                @foreach($metaParts as $i => $part)
                    <span>{{ $part }}</span>@if($i < count($metaParts) - 1)<span class="sep">·</span>@endif
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @if($car->mileage || $car->first_registration || $car->fuel_type || $car->transmission || $car->power_hp || $car->category)
    <div class="cs-keyfacts">
        @if($car->mileage)<div class="cs-keyfact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg><strong>{{ number_format((float) $car->mileage,0,'',' ') }} km</strong></div>@endif
        @if($car->first_registration)<div class="cs-keyfact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><strong>{{ $car->first_registration }}</strong></div>@endif
        @if($car->fuel_type)<div class="cs-keyfact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg><strong>{{ $car->fuel_type }}</strong></div>@endif
        @if($car->transmission)<div class="cs-keyfact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V8"/><path d="m5 12-3 3 3 3"/><path d="m19 12 3 3-3 3"/><path d="M2 15h20"/><path d="m5 5-3 3 3 3"/><path d="M2 8h10"/></svg><strong>{{ $car->transmission }}</strong></div>@endif
        @if($car->power_hp)<div class="cs-keyfact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg><strong>{{ $car->power_hp }} KM</strong></div>@endif
        @if($car->category)<div class="cs-keyfact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg><strong>{{ $car->category }}</strong></div>@endif
    </div>
    @endif

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
        <!-- GALLERY MEDIA TABS (COS style — above gallery, left column only) -->
        <div class="cs-gallery-tabs-wrap">
        <div class="cs-gallery-tabs">
            <button type="button" class="cs-gallery-tab active" data-gallery-filter="all" onclick="csFilterGallery(this,'all')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                Wszystkie zdjęcia
            </button>
            <button type="button" class="cs-gallery-tab {{ $damageImgList->count() ? '' : 'disabled' }}" data-gallery-filter="damage" onclick="csFilterGallery(this,'damage')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Zdjęcia przedstawiające stan pojazdu
            </button>
            <button type="button" class="cs-gallery-tab disabled" data-gallery-filter="documents">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                Dokumenty
            </button>
            <button type="button" class="cs-gallery-tab {{ $hasEngineVideo ? '' : 'disabled' }}" data-gallery-filter="video" onclick="csFilterGallery(this,'video')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Nagranie z pracy silnika
            </button>
            <button type="button" class="cs-gallery-tab {{ $car->pano360Image ? '' : 'disabled' }}" data-gallery-filter="pano360" onclick="csFilterGallery(this,'pano360')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Wnętrze 360°
            </button>
            <button type="button" class="cs-gallery-tab {{ $car->exteriorPano360Image ? '' : 'disabled' }}" data-gallery-filter="pano360ext" onclick="csFilterGallery(this,'pano360ext')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Zewnętrz 360°
            </button>
        </div>
        </div>{{-- /cs-gallery-tabs-wrap --}}
        <div class="cs-gallery">
            <div class="cs-gallery-stage">
                <div class="cs-gallery-main active" id="csGalleryStandard">
                    @if($galleryList->count())
                        <img src="{{ $galleryList->first()->url }}" id="csMainImg" alt="{{ $galleryList->first()->alt }}" style="cursor:zoom-in" onclick="openCarGallery(0)" fetchpriority="high" decoding="async">
                        <div class="cs-gallery-counter" style="cursor:zoom-in" onclick="openCarGallery(parseInt(document.getElementById('csImgCounter').textContent)-1)"><i data-lucide="maximize-2" aria-hidden="true"></i> <span id="csImgCounter">1</span>/<span id="csImgTotal">{{ $galleryList->count() }}</span></div>
                    @else
                        <div class="empty"><i data-lucide="car" aria-hidden="true"></i></div>
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
            @if($galleryList->count() > 1 || $damageImgList->count())
            <div class="cs-gallery-thumbs" id="csGalleryThumbs">
                @foreach($galleryList as $i => $img)
                    <img src="{{ $img->url }}" loading="lazy" alt="{{ $img->alt }}" class="cs-thumb {{ $i===0?'active':'' }}" data-type="gallery" data-idx="{{ $i }}" onclick="csSelImg(this,{{ $i+1 }})" ondblclick="openCarGallery({{ $i }})" tabindex="0" onkeypress="if(event.key==='Enter')csSelImg(this,{{ $i+1 }})">
                @endforeach
                @foreach($damageImgList as $j => $dimg)
                    <img src="{{ $dimg->url }}" loading="lazy" alt="{{ $dimg->alt }}" class="cs-thumb" data-type="damage" data-idx="{{ $galleryList->count() + $j }}" onclick="csSelImg(this,{{ $galleryList->count() + $j + 1 }})" tabindex="0">
                @endforeach
            </div>
            @endif
        </div>

        <!-- KALKULATOR TRIGGER (left column) -->
        <button type="button" class="cs-calc-trigger" onclick="csOpenCalc()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="8" x2="10" y1="10" y2="10"/><line x1="14" x2="16" y1="10" y2="10"/><line x1="8" x2="10" y1="14" y2="14"/><line x1="14" x2="16" y1="14" y2="14"/><line x1="8" x2="10" y1="18" y2="18"/><line x1="14" x2="16" y1="18" y2="18"/></svg>
            <span class="cs-calc-trigger-text">Oblicz ratę kredytu / leasingu<small>Kalkulator finansowania</small></span>
            <svg class="cs-calc-trigger-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>

        </div><!-- /left column: gallery -->


        <!-- PRICE + CALC SIDEBAR (sticky) -->
        <div class="cs-sidebar">
            <!-- SIDEBAR CARD (COS style) -->
            <div class="cs-sidebar-card">
                <!-- PRICE -->
                <div class="cs-price-section">
                    <div class="cs-price-label">Cena sprzedaży</div>
                    <div class="cs-price-value">{{ $car->formatted_price }}@if($car->price_type)<small>{{ $car->price_type }}</small>@endif</div>
                </div>
                <!-- VEHICLE SUMMARY (COS-style key-value pairs) -->
                <div class="cs-sidebar-summary">
                    @if($car->first_registration)<div class="cs-sidebar-summary-row"><span class="lbl">Rejestracja</span><span class="val">{{ $car->first_registration }}</span></div>@endif
                    @if($car->mileage)<div class="cs-sidebar-summary-row"><span class="lbl">Przebieg</span><span class="val">{{ number_format((float) $car->mileage,0,'',' ') }} km</span></div>@endif
                    @if($car->fuel_type)<div class="cs-sidebar-summary-row"><span class="lbl">Paliwo</span><span class="val">{{ $car->fuel_type }}</span></div>@endif
                    @if($car->transmission)<div class="cs-sidebar-summary-row"><span class="lbl">Skrzynia</span><span class="val">{{ $car->transmission }}</span></div>@endif
                    @if($car->power_hp)<div class="cs-sidebar-summary-row"><span class="lbl">Moc</span><span class="val">{{ $car->power_hp }} KM</span></div>@endif
                    @if($car->engine_capacity)<div class="cs-sidebar-summary-row"><span class="lbl">Seria</span><span class="val">{{ number_format((float) $car->engine_capacity,0,'',' ') }} cm³</span></div>@endif
                    @if($car->body_type ?? $car->category)<div class="cs-sidebar-summary-row"><span class="lbl">Nadwozie</span><span class="val">{{ $car->body_type ?? $car->category }}</span></div>@endif
                    @if($car->co2_emission)<div class="cs-sidebar-summary-row"><span class="lbl">CCL</span><span class="val">{{ $car->co2_emission }} g/km</span></div>@endif
                    @if($car->color)<div class="cs-sidebar-summary-row"><span class="lbl">Kolor</span><span class="val">{{ $car->color }}</span></div>@endif
                </div>
                <!-- CTA BUTTONS -->
                <div class="cs-price-actions">
                    <a href="tel:+48123456789" class="btn btn-blue btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Zadzwoń
                    </a>
                    <div style="display:flex;gap:8px">
                        <button type="button" class="cs-btn-secondary" id="csSidebarFav" data-id="{{ $car->id }}" onclick="toggleFav(event,{{ $car->id }});csSidebarFavUpdate()" style="flex:1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="csFavIcon"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            <span id="csFavLabel">Obserwuj</span>
                        </button>
                        @if($car->has_certicheck)
                        <a href="{{ route('car.pdf', $car->slug) }}" class="cs-sidebar-certi" title="Pobierz raport CertiCheck PDF">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                            CertiCheck
                        </a>
                        @endif
                    </div>
                </div>
            </div>


        </div>
        </div><!-- /cs-sidebar -->

    </div><!-- /cs-grid: gallery + sidebar only -->

    <!-- DATA SECTIONS (full width, below grid — COS Check style) -->
    <div class="cs-sections-2col">

    <!-- LEFT COLUMN -->
    <div class="cs-col-left">

        <!-- Dane pojazdu -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Dane pojazdu</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    @if($car->brand?->name)<div class="cs-data-row"><span class="lbl">Marka</span><span class="val">{{ $car->brand->name }}</span></div>@endif
                    @if($car->model)<div class="cs-data-row"><span class="lbl">Model</span><span class="val">{{ $car->model }}</span></div>@endif
                    @if($car->category)<div class="cs-data-row"><span class="lbl">Kategoria</span><span class="val">{{ $car->category }}</span></div>@endif
                    @if($car->color)<div class="cs-data-row"><span class="lbl">Kolor</span><span class="val">{{ $car->color }}@if($car->color_code) ({{ $car->color_code }})@endif</span></div>@endif
                    @if($car->doors)<div class="cs-data-row"><span class="lbl">Drzwi</span><span class="val">{{ $car->doors }}@if($car->seats)/{{ $car->seats }}@endif</span></div>@endif
                    @if($car->seats && !$car->doors)<div class="cs-data-row"><span class="lbl">Siedzenia</span><span class="val">{{ $car->seats }}</span></div>@endif
                    @if($car->weight)<div class="cs-data-row"><span class="lbl">Masa</span><span class="val">{{ number_format((float) $car->weight, 0, '', ' ') }} kg</span></div>@endif
                    @if($car->upholstery)<div class="cs-data-row"><span class="lbl">Tapicerka</span><span class="val">{{ $car->upholstery }}</span></div>@endif
                    @if($car->vin)<div class="cs-data-row"><span class="lbl">VIN</span><span class="val">{{ $car->vin }}</span></div>@endif
                </div>
            </div>
        </div>

        <!-- Historia pojazdu -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Historia pojazdu</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    @if($car->first_registration)<div class="cs-data-row"><span class="lbl">Pierwsza rejestracja</span><span class="val">{{ $car->first_registration }}</span></div>@endif
                    @if($car->mileage)<div class="cs-data-row"><span class="lbl">Przebieg</span><span class="val">{{ number_format((float) $car->mileage, 0, '', ' ') }} km</span></div>@endif
                    @if($car->previous_owners !== null)<div class="cs-data-row"><span class="lbl">Poprzedni właściciele</span><span class="val">{{ $car->previous_owners }}</span></div>@endif
                    @if($car->number_of_keys)<div class="cs-data-row"><span class="lbl">Liczba kluczyków</span><span class="val">{{ $car->number_of_keys }}</span></div>@endif
                    @if($car->business_use)<div class="cs-data-row"><span class="lbl">Użytkowanie</span><span class="val">{{ $car->business_use }}</span></div>@endif
                </div>
            </div>
        </div>

        <!-- Silnik -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Silnik</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    @if($car->fuel_type)<div class="cs-data-row"><span class="lbl">Paliwo</span><span class="val">{{ $car->fuel_type }}</span></div>@endif
                    @if($car->power_hp)<div class="cs-data-row"><span class="lbl">Moc</span><span class="val">{{ $car->power_hp }} KM{{ $car->power_kw ? ' ('.$car->power_kw.' kW)' : '' }}</span></div>@endif
                    @if($car->engine_capacity)<div class="cs-data-row"><span class="lbl">Pojemność silnika</span><span class="val">{{ number_format((float) $car->engine_capacity, 0, '', ' ') }} ccm</span></div>@endif
                    @if($car->transmission)<div class="cs-data-row"><span class="lbl">Skrzynia biegów</span><span class="val">{{ $car->transmission }}{{ $car->transmission_detail ? ' ('.$car->transmission_detail.')' : '' }}</span></div>@endif
                </div>
            </div>
        </div>
    </div><!-- /cs-col-left -->

    <!-- RIGHT COLUMN -->
    <div class="cs-col-right">

        <!-- Pochodzenie i podatek -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Pochodzenie i podatek</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    <div class="cs-data-row"><span class="lbl">Pojazd importowany</span><span class="val">{{ $car->is_imported ? 'Tak' : 'Nie' }}</span></div>
                    @if($car->country_registration)<div class="cs-data-row"><span class="lbl">Kraj ostatniej rejestracji</span><span class="val">{{ $car->country_registration }}</span></div>@endif
                    @if($car->taxation)<div class="cs-data-row"><span class="lbl">Opodatkowanie</span><span class="val">{{ $car->taxation }}</span></div>@endif
                </div>
            </div>
        </div>

        <!-- Serwisowanie i inspekcja -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Serwisowanie i inspekcja pojazdu</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    @if($car->last_service)<div class="cs-data-row"><span class="lbl">Ostatni serwis</span><span class="val">{{ $car->last_service }}</span></div>@endif
                    @if($car->last_service_mileage)<div class="cs-data-row"><span class="lbl">Przebieg przy serwisie</span><span class="val">{{ number_format((float) $car->last_service_mileage,0,'',' ') }} km</span></div>@endif
                    @if($car->service_documentation)<div class="cs-data-row"><span class="lbl">Dokumentacja serwisowa</span><span class="val">{{ $car->service_documentation }}</span></div>@endif
                    @if($car->next_inspection)<div class="cs-data-row"><span class="lbl">Raport HU/AU ważny do</span><span class="val">{{ $car->next_inspection }}</span></div>@endif
                </div>
            </div>
        </div>

        <!-- Zużycie paliwa -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Zużycie paliwa</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    @if($car->fuel_consumption)<div class="cs-data-row"><span class="lbl">Zużycie paliwa</span><span class="val">{{ $car->fuel_consumption }} l/100km</span></div>@endif
                    @if($car->co2_emission)<div class="cs-data-row"><span class="lbl">Emisja CO₂</span><span class="val">{{ $car->co2_emission }} g/km</span></div>@endif
                    @if($car->emission_class)<div class="cs-data-row"><span class="lbl">Klasa emisji</span><span class="val">{{ $car->emission_class }}</span></div>@endif
                </div>
            </div>
        </div>

        <!-- Dokumenty pojazdu -->
        <div class="cs-data-section">
            <div class="cs-data-header"><h2>Dokumenty pojazdu</h2></div>
            <div class="cs-data-body open" style="padding:0">
                <div class="cs-sub-bd open">
                    @if($car->service_book)<div class="cs-data-row"><span class="lbl">Książka serwisowa</span><span class="val">{{ $car->service_book }}</span></div>@endif
                    @if($car->coc_documents)<div class="cs-data-row"><span class="lbl">Dokumenty COC</span><span class="val">{{ $car->coc_documents }}</span></div>@endif
                    @if($car->vehicle_folder)<div class="cs-data-row"><span class="lbl">Teczka pojazdu</span><span class="val">{{ $car->vehicle_folder }}</span></div>@endif
                    @if($car->hu_au_report)<div class="cs-data-row"><span class="lbl">Raport HU/AU</span><span class="val">{{ $car->hu_au_report }}</span></div>@endif
                </div>
            </div>
        </div>
    </div><!-- /cs-col-right -->

    </div><!-- /cs-sections-2col -->

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
                        <svg viewBox="0 0 200 380" style="width:100%;height:100%" aria-hidden="true">
                            <path d="M60,30 Q60,10 100,10 Q140,10 140,30 L155,80 L160,120 L160,260 L155,300 L140,350 Q140,370 100,370 Q60,370 60,350 L45,300 L40,260 L40,120 L45,80 Z" fill="#e5e5e7" stroke="#cfcfd2" stroke-width="1.5"/>
                            <path d="M65,80 L135,80 L150,120 L50,120 Z" fill="#d4d4d7" stroke="#b8b8bc" stroke-width="1"/>
                            <path d="M55,280 L145,280 L140,330 L60,330 Z" fill="#d4d4d7" stroke="#b8b8bc" stroke-width="1"/>
                            <rect x="52" y="130" width="96" height="140" rx="8" fill="#d4d4d7" stroke="#b8b8bc" stroke-width="1"/>
                        </svg>
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
            <p style="font-size:12px;color:var(--text-3);margin-bottom:16px;display:flex;align-items:center;gap:6px">
                <i data-lucide="info" aria-hidden="true" style="width:14px;height:14px;flex-shrink:0"></i>
                Norma fabryczna: 80–150 µm · powyżej 200 µm — możliwa naprawa lakiernicza
            </p>
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
                    $val = is_array($value) ? ($value['value'] ?? $value[0] ?? 0) : $value;
                    $panelLabel = is_numeric($panel) ? ($paintPanelNames[$panel] ?? 'Panel '.($panel + 1)) : $panel;
                @endphp
                <div class="cs-paint-item">
                    <div class="cs-paint-label">{{ $panelLabel }}</div>
                    <div class="cs-paint-value {{ $val > 200 ? 'cs-paint-danger' : ($val > 160 ? 'cs-paint-warn' : '') }}">{{ $val }} <span style="font-size:12px;font-weight:500;color:var(--text-3)">µm</span></div>
                </div>
                @endforeach
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
                            'ac' => 'Klimatyzacja', 'tires' => 'Opony', 'lights' => 'Oświetlenie',
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
            $positions = ['Przednia lewa','Przednia prawa','Tylna lewa','Tylna prawa'];
        @endphp
        <div class="cs-tire-set">
            <h3 class="cs-tire-set-title">
                {{ $set->set_number }}. Komplet @if($set->is_mounted)(zamontowane) <i data-lucide="info" aria-hidden="true"></i>@endif
            </h3>
            <div class="cs-tire-table">
                <div class="cs-tire-th"></div>
                @foreach($setTires as $t)
                <div class="cs-tire-th" style="text-align:center">{{ $t->position }}</div>
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
                    $hasIssue = $t->condition && count($t->condition) > 0;
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
                        <div class="cs-tire-pos-name">{{ $t->position }}</div>
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
    <div style="margin-top:20px;padding:24px 24px 0;border-top:1px solid var(--border-l);max-width:1200px;margin-left:auto;margin-right:auto">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
            <div>
                <h2 style="font-size:22px;font-weight:800;letter-spacing:-.4px;color:var(--text)">Podobne pojazdy</h2>
                <p style="font-size:13px;color:var(--text-3);margin-top:4px">Inne certyfikowane auta z naszej oferty</p>
            </div>
            <a href="{{ route('catalog') }}" style="font-size:13px;font-weight:700;color:var(--blue);display:inline-flex;align-items:center;gap:5px;text-decoration:none">
                Pokaż wszystkie
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
        <div class="cs-related-grid">
            @foreach($relatedCars as $relCar)
            <a href="{{ route('catalog.show', $relCar) }}" class="vcard">
                <div class="vcard-img">
                    @if($relCar->primaryImage)
                        <img src="{{ $relCar->primaryImage->url }}" alt="{{ $relCar->primaryImage->alt }}" loading="lazy">
                    @else
                        <div class="vcard-placeholder"><svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg></div>
                    @endif
                    @if($relCar->is_featured)<div class="vcard-badge">Wyróżnione</div>@endif
                    @if($relCar->has_certicheck)<div class="certi-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg> CertiCheck</div>@endif
                </div>
                <div class="vcard-body">
                    <div class="vcard-title">{{ $relCar->title }}</div>
                    <div class="vcard-specs">
                        @if($relCar->mileage)<span>{{ number_format((float) $relCar->mileage,0,'',' ') }} km</span>@endif
                        @if($relCar->fuel_type)<span>{{ $relCar->fuel_type }}</span>@endif
                        @if($relCar->first_registration)<span>{{ $relCar->first_registration }}</span>@endif
                        @if($relCar->power_hp)<span>{{ $relCar->power_hp }} KM</span>@endif
                    </div>
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

<!-- FLOATING CALCULATOR OVERLAY -->
<div class="cs-calc-overlay" id="csCalcOverlay" onclick="csCloseCalc()"></div>
<div class="cs-calc-float" id="csCalcFloat">
    <div class="cs-calc-float-header">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="8" x2="10" y1="10" y2="10"/><line x1="14" x2="16" y1="10" y2="10"/><line x1="8" x2="10" y1="14" y2="14"/><line x1="14" x2="16" y1="14" y2="14"/><line x1="8" x2="10" y1="18" y2="18"/><line x1="14" x2="16" y1="18" y2="18"/></svg>
            Kalkulator finansowania
        </h3>
        <button type="button" class="cs-calc-float-close" onclick="csCloseCalc()" title="Zamknij">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </div>
    <div class="cs-calc" id="csCalc">
        <div class="cs-calc-header">
            <div class="cs-calc-tabs">
                <button type="button" class="cs-calc-tab active" data-calc-type="credit" onclick="csCalcTab(this,'credit')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    Kredyt
                </button>
                <button type="button" class="cs-calc-tab" data-calc-type="leasing" onclick="csCalcTab(this,'leasing')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8.3a4 4 0 0 0-1.172-2.872L3 3"/><path d="m15 9 6-6"/></svg>
                    Leasing
                </button>
            </div>
        </div>
        <div class="cs-calc-body">
            @php $carPrice = (float) ($car->price ?? 0); @endphp
            <input type="hidden" id="csCalcPrice" value="{{ $carPrice }}">

            <div class="cs-calc-field">
                <label for="csCalcDown">Wpłata własna</label>
                <div class="cs-calc-range-row">
                    <input type="range" id="csCalcDown" min="0" max="{{ $carPrice }}" step="1000" value="{{ round($carPrice * 0.2) }}" oninput="csCalcUpdate()">
                    <span class="cs-calc-range-val" id="csCalcDownVal">{{ number_format(round($carPrice * 0.2), 0, '', ' ') }} zł</span>
                </div>
                <div class="cs-calc-range-labels"><span>0 zł</span><span>{{ number_format($carPrice, 0, '', ' ') }} zł</span></div>
            </div>

            <div class="cs-calc-field">
                <label for="csCalcTerm">Okres finansowania</label>
                <div class="cs-calc-range-row">
                    <input type="range" id="csCalcTerm" min="12" max="96" step="12" value="48" oninput="csCalcUpdate()">
                    <span class="cs-calc-range-val" id="csCalcTermVal">48 mies.</span>
                </div>
                <div class="cs-calc-range-labels"><span>12 mies.</span><span>96 mies.</span></div>
            </div>

            <div class="cs-calc-field">
                <label for="csCalcRate">Oprocentowanie roczne</label>
                <div class="cs-calc-range-row">
                    <input type="range" id="csCalcRate" min="3" max="15" step="0.5" value="7.9" oninput="csCalcUpdate()">
                    <span class="cs-calc-range-val" id="csCalcRateVal">7.9%</span>
                </div>
                <div class="cs-calc-range-labels"><span>3%</span><span>15%</span></div>
            </div>

            <div class="cs-calc-field cs-calc-residual" id="csCalcResidualWrap" style="display:none">
                <label for="csCalcResidual">Wartość wykupu (% ceny)</label>
                <div class="cs-calc-range-row">
                    <input type="range" id="csCalcResidual" min="0" max="50" step="5" value="20" oninput="csCalcUpdate()">
                    <span class="cs-calc-range-val" id="csCalcResidualVal">20%</span>
                </div>
                <div class="cs-calc-range-labels"><span>0%</span><span>50%</span></div>
            </div>
        </div>

        <div class="cs-calc-result">
            <div class="cs-calc-result-label">Szacowana rata miesięczna</div>
            <div class="cs-calc-result-value" id="csCalcResult">—</div>
            <div class="cs-calc-result-details">
                <div><span class="lbl">Kwota finansowania</span><span class="val" id="csCalcFinanced">—</span></div>
                <div><span class="lbl">Łączny koszt</span><span class="val" id="csCalcTotal">—</span></div>
            </div>
            <div class="cs-calc-disclaimer">* Symulacja orientacyjna. Ostateczne warunki ustala instytucja finansowa.</div>
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
window.CAR_GALLERY=@json($galleryList->map(fn($i)=>['src'=>$i->url,'caption'=>$i->alt])->values());
window.CAR_DAMAGE_GALLERY=@json($damageImgList->map(fn($i)=>['src'=>$i->url,'caption'=>$i->alt])->values());
window.CAR_ALL_GALLERY=[...CAR_GALLERY,...CAR_DAMAGE_GALLERY];
window.openCarGallery=(i)=>{if(window.openLbAt)openLbAt(CAR_ALL_GALLERY, i||0)};

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
        const onReady = () => {
            if(typeof pannellum === 'undefined'){ setTimeout(onReady, 50); return; }
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
    document.querySelectorAll('.cs-gallery-tab').forEach(t=>t.classList.remove('active'));
    btn.classList.add('active');

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
        const vid=document.querySelector('[data-panel-engine-video]');
        if(vid)vid.scrollIntoView({behavior:'smooth',block:'center'});
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
        const onReady = () => {
            if(typeof pannellum === 'undefined'){ setTimeout(onReady, 50); return; }
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
        const onReady = () => {
            if(typeof pannellum === 'undefined'){ setTimeout(onReady, 50); return; }
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
// — Financing Calculator (floating widget) —
function csOpenCalc(){
    document.getElementById('csCalcOverlay').classList.add('open');
    document.getElementById('csCalcFloat').classList.add('open');
    document.body.style.overflow='hidden';
    csCalcUpdate();
}
function csCloseCalc(){
    document.getElementById('csCalcOverlay').classList.remove('open');
    document.getElementById('csCalcFloat').classList.remove('open');
    document.body.style.overflow='';
}
document.addEventListener('keydown',function(e){if(e.key==='Escape')csCloseCalc()});
let csCalcMode='credit';
function csCalcTab(el,mode){
    csCalcMode=mode;
    document.querySelectorAll('.cs-calc-tab').forEach(t=>t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('csCalcResidualWrap').style.display=mode==='leasing'?'':'none';
    csCalcUpdate();
}
function csFmt(n){return n.toLocaleString('pl-PL',{maximumFractionDigits:0})+' zł'}
function csCalcUpdate(){
    const price=parseFloat(document.getElementById('csCalcPrice').value)||0;
    const down=parseFloat(document.getElementById('csCalcDown').value)||0;
    const months=parseInt(document.getElementById('csCalcTerm').value)||48;
    const rate=parseFloat(document.getElementById('csCalcRate').value)||7.9;
    const residualPct=parseFloat(document.getElementById('csCalcResidual').value)||20;
    
    document.getElementById('csCalcDownVal').textContent=csFmt(down);
    document.getElementById('csCalcTermVal').textContent=months+' mies.';
    document.getElementById('csCalcRateVal').textContent=rate+'%';
    document.getElementById('csCalcResidualVal').textContent=residualPct+'%';
    
    let financed=price-down;
    let residual=0;
    if(csCalcMode==='leasing'){residual=price*(residualPct/100);financed=price-down-residual}
    if(financed<=0){
        document.getElementById('csCalcResult').textContent='0 zł';
        document.getElementById('csCalcFinanced').textContent=csFmt(0);
        document.getElementById('csCalcTotal').textContent=csFmt(0);
        return;
    }
    const mr=rate/100/12;
    let pmt;
    if(mr===0){pmt=financed/months}
    else{pmt=financed*(mr*Math.pow(1+mr,months))/(Math.pow(1+mr,months)-1)}
    const total=pmt*months+(csCalcMode==='leasing'?residual:0);
    
    document.getElementById('csCalcResult').textContent=csFmt(Math.round(pmt));
    document.getElementById('csCalcFinanced').textContent=csFmt(Math.round(financed));
    document.getElementById('csCalcTotal').textContent=csFmt(Math.round(total));
    // update slider progress fills
    document.querySelectorAll('#csCalc input[type=range]').forEach(csSliderFill);
}
function csSliderFill(el){
    const pct=((el.value-el.min)/(el.max-el.min))*100;
    el.style.background=`linear-gradient(to right,#0066ff 0%,#0066ff ${pct}%,#e5e5e5 ${pct}%,#e5e5e5 100%)`;
}
document.addEventListener('DOMContentLoaded',()=>{
    if(document.getElementById('csCalc')){
        csCalcUpdate();
        document.querySelectorAll('#csCalc input[type=range]').forEach(csSliderFill);
    }
    // Sidebar favorite button — sync initial state
    csSidebarFavUpdate();
});

// Sidebar favorite sync
function csSidebarFavUpdate(){
    const btn = document.getElementById('csSidebarFav');
    if(!btn) return;
    const favs = JSON.parse(localStorage.getItem('certicars_favs') || '[]');
    const id = +btn.dataset.id;
    const isActive = favs.includes(id);
    btn.classList.toggle('active', isActive);
    const icon = document.getElementById('csFavIcon');
    const label = document.getElementById('csFavLabel');
    if(icon) icon.style.fill = isActive ? 'var(--orange)' : 'none';
    if(icon) icon.style.stroke = isActive ? 'var(--orange)' : 'currentColor';
    if(label) label.textContent = isActive ? 'Obserwujesz' : 'Obserwuj';
}

</script>
@endpush
@endsection
