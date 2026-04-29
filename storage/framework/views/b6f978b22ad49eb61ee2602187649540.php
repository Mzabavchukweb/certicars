<?php $__env->startSection('meta_title_full',$car->seo_title); ?>
<?php $__env->startSection('title',$car->title); ?>
<?php $__env->startSection('description',$car->seo_description); ?>
<?php $__env->startSection('og_title',$car->seo_title); ?>
<?php $__env->startSection('og_description',$car->seo_description); ?>
<?php if($car->primaryImage): ?>
<?php $__env->startSection('og_image',$car->primaryImage->url); ?>
<?php endif; ?>
<?php $__env->startSection('og_type','product'); ?>
<?php $__env->startSection('extra_head'); ?>
    <?php if($car->noindex): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
    <?php
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
    ?>
    <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
html{scroll-behavior:smooth}
.cs-wrap{padding:0 0 80px;background:#f5f5f7;min-height:100vh}

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

.cs-grid{display:grid;grid-template-columns:1fr 380px;gap:24px;margin-bottom:20px;align-items:start}
.cs-sidebar{position:sticky;top:92px;align-self:start}

/* CERTICHECK INLINE REPORT */
.cc-report{margin-top:18px;border-radius:14px;overflow:hidden;border:1px solid #e5e5e7;background:#fff}
.cc-report-header{background:linear-gradient(135deg,#0a0a0a 0%,#1a1a2e 100%);color:#fff;padding:24px 28px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.cc-report-brand{display:flex;align-items:center;gap:10px}
.cc-report-brand .name{font-size:18px;font-weight:800;letter-spacing:-.4px}
.cc-report-brand .name span{color:#0066ff}
.cc-report-badge{background:rgba(0,102,255,.15);color:#6db3ff;font-size:9px;font-weight:700;padding:4px 10px;border-radius:50px;letter-spacing:.5px;text-transform:uppercase}
.cc-report-sub{font-size:11px;color:rgba(255,255,255,.4);font-weight:500}
.cc-report-pdf{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);color:#fff;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;border:1px solid rgba(255,255,255,.15)}
.cc-report-pdf:hover{background:rgba(255,255,255,.2)}
.cc-report-pdf svg{width:14px;height:14px}
.cc-section-hd{display:flex;align-items:center;gap:9px;padding:14px 20px;font-size:14px;font-weight:700;color:#0a0a0a;border-bottom:1px solid #f0f0f2;cursor:pointer;user-select:none;transition:background .1s}
.cc-section-hd:hover{background:#fafafa}
.cc-section-hd svg{width:17px;height:17px;color:#0066ff;flex-shrink:0}
.cc-section-hd .chv{margin-left:auto;width:16px;height:16px;color:#9ca3af;transition:transform .2s}
.cc-section-hd.open .chv{transform:rotate(180deg)}
.cc-section-bd{display:none;padding:0}
.cc-section-bd.open{display:block}
.cc-row{display:flex;justify-content:space-between;align-items:baseline;padding:9px 20px;font-size:13px;border-bottom:1px solid #f5f5f7}
.cc-row:nth-child(odd){background:#fafafa}
.cc-row .lbl{color:#6b7280}
.cc-row .val{font-weight:700;color:#1a1a1a;text-align:right}
.cc-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:0}
.cc-paint-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:1px;background:#eee;margin:12px 20px 16px;border-radius:8px;overflow:hidden}
.cc-paint-item{background:#fff;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;font-size:12px}
.cc-paint-item .panel{color:#6b7280}
.cc-paint-item .um{font-weight:800;color:#1a1a1a;font-size:13px}
.cc-paint-item .um.warn{color:#f59e0b}
.cc-paint-item .um.danger{color:#ef4444}
.cc-tech-row{display:flex;gap:14px;padding:10px 20px;font-size:13px;border-bottom:1px solid #f5f5f7;align-items:flex-start}
.cc-tech-row:nth-child(odd){background:#fafafa}
.cc-tech-row .comp{font-weight:700;color:#1a1a1a;min-width:140px;flex-shrink:0}
.cc-tech-row .st{color:#16a34a;line-height:1.5}
.cc-eq-wrap{padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:0 28px}
.cc-eq-cat{margin-bottom:16px}
.cc-eq-cat-t{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#1a1a1a;margin-bottom:8px}
.cc-eq-it{display:flex;align-items:center;gap:7px;padding:4px 0;font-size:12.5px;color:#374151}
.cc-eq-it svg{width:13px;height:13px;color:#16a34a;flex-shrink:0}
@media(max-width:768px){.cc-grid-2{grid-template-columns:1fr}.cc-eq-wrap{grid-template-columns:1fr}.cc-paint-grid{grid-template-columns:1fr 1fr;margin:8px 12px 12px}.cc-report-header{padding:18px 16px}.cc-row{padding:8px 14px}.cc-section-hd{padding:12px 14px}}

/* GALLERY — edge-to-edge, no frame */
.cs-gallery{background:#e8e8ea;border-radius:14px;overflow:hidden;border:1px solid #e5e5e7}
.cs-gallery-main{position:relative;aspect-ratio:16/9;background:#e8e8ea;display:flex;align-items:center;justify-content:center}
.cs-gallery-main img{width:100%;height:100%;object-fit:cover}
.cs-gallery-main .empty{color:var(--border)}
.cs-gallery-main .empty i{width:80px;height:80px}
.cs-gallery-counter{position:absolute;bottom:12px;right:12px;background:rgba(10,10,10,.75);color:#fff;padding:6px 12px;border-radius:50px;font-size:12px;display:flex;align-items:center;gap:5px;backdrop-filter:blur(10px);font-weight:600}
.cs-gallery-counter i{width:13px;height:13px}

/* GALLERY MEDIA TABS (COS style — flat inline row ABOVE gallery) */
.cs-gallery-tabs{display:flex;align-items:center;gap:20px;padding:14px 0 10px;overflow-x:auto;-webkit-overflow-scrolling:touch}
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

/* STICKY BOTTOM BAR (COS-style — frosted glass) */
.cs-sticky-bottom{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.95);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-top:1px solid #e5e5e7;padding:14px 0;z-index:90;transform:translateY(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);box-shadow:0 -4px 24px rgba(0,0,0,.08)}
.cs-sticky-bottom.visible{transform:translateY(0)}
.cs-sticky-bottom-in{max-width:1200px;margin:0 auto;padding:0 24px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.cs-sticky-bottom-info{display:flex;align-items:center;gap:24px}
.cs-sticky-bottom-title{font-size:15px;font-weight:700;color:#1a1a1a;letter-spacing:-.2px;max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cs-sticky-bottom-price{font-size:28px;font-weight:900;color:#0a0a0a;letter-spacing:-.8px;line-height:1;font-family:'Inter',sans-serif}
.cs-sticky-bottom-price small{font-size:11px;font-weight:500;color:#9ca3af;letter-spacing:0;margin-left:6px}
.cs-sticky-bottom-actions{display:flex;gap:10px;align-items:center}
.cs-price-actions .btn{width:100%;justify-content:center;padding:13px 20px;font-weight:700;border-radius:10px}
.cs-price-actions .cs-btn-secondary{background:#f5f5f5;color:#1a1a1a;border:1px solid #e5e5e5;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:10px;cursor:pointer;transition:all .15s;text-decoration:none}
.cs-price-actions .cs-btn-secondary:hover{background:#ebebeb}
.cs-price-actions .cs-btn-secondary svg{width:16px;height:16px;flex-shrink:0}

/* FINANCING CALCULATOR */
.cs-calc{background:#fff;border:1px solid #e5e5e7;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.06)}
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
.cs-calc-result{background:linear-gradient(135deg,#0a0a0a 0%,#1a1a2e 100%);padding:22px;color:#fff}
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
.cs-damages-tabs{display:flex;gap:8px;overflow-x:auto;padding-bottom:8px;margin-bottom:16px;-webkit-overflow-scrolling:touch}
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
.cs-paint-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1px;background:#eeeef0;border:1px solid #eeeef0;border-radius:12px;overflow:hidden}
.cs-paint-item{background:#fff;padding:14px 16px}
.cs-paint-label{font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);margin-bottom:4px}
.cs-paint-value{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.3px}

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
.cs-data-section{background:#fff;border:1px solid #e5e5e7;border-radius:16px;margin-bottom:20px;overflow:hidden}
.cs-data-header{display:flex;align-items:center;justify-content:space-between;padding:24px 28px;cursor:pointer;user-select:none;border-bottom:1px solid transparent;transition:border-color .2s,background .15s}
.cs-data-header:hover{background:#fafafa}
.cs-data-header.open{border-bottom-color:#e5e5e7}
.cs-data-header.open:hover{background:#fafafa}
.cs-data-header h2{font-size:20px;font-weight:800;color:#1a1a1a;letter-spacing:-.4px;display:flex;align-items:center;gap:12px;margin:0;line-height:1.3;font-family:'Inter',sans-serif}
.cs-data-header h2 i,.cs-data-header h2 svg{width:22px;height:22px;color:#6b7280;flex-shrink:0}
.cs-data-header .chev{width:20px;height:20px;color:#9ca3af;transition:transform .3s cubic-bezier(.4,0,.2,1);flex-shrink:0}
.cs-data-header.open .chev{transform:rotate(180deg)}
.cs-data-body{display:none;padding:28px 28px 32px}
.cs-data-body.open{display:block;animation:cos-fade .25s ease}
@keyframes cos-fade{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.cs-data-2col{display:grid;grid-template-columns:1fr 1fr;gap:0 24px}
.cs-data-block{background:#fff;border:1px solid #eeeef0;border-radius:12px;padding:24px 24px 16px}
.cs-data-block-title{font-size:18px;font-weight:800;color:#1a1a1a;margin-bottom:20px;letter-spacing:-.3px;line-height:1.3;padding-bottom:16px;border-bottom:1px solid #eeeef0}
.cs-data-row{display:flex;justify-content:space-between;align-items:baseline;padding:13px 0;font-size:14px;border-bottom:1px solid #f0f0f2}
.cs-data-row:first-child{padding-top:0}
.cs-data-row:last-child{border-bottom:none;padding-bottom:6px}
.cs-data-row .lbl{color:#6b7280;font-weight:400;line-height:1.5}
.cs-data-row .val{font-weight:700;color:#1a1a1a;text-align:right;line-height:1.5}
@media(max-width:768px){.cs-data-2col{grid-template-columns:1fr}.cs-data-block+.cs-data-block{margin-top:12px}}

/* FLOATING WIDGETS — handled by global layout */

@media(max-width:1024px){
    .cs-grid{grid-template-columns:1fr;gap:20px}
    .cs-sidebar{position:static}
    .cs-tire-table{grid-template-columns:180px repeat(4,1fr)}
    .cs-equipment-grid{grid-template-columns:1fr}
}
@media(max-width:768px){
    .cs-nav-bar{flex-direction:column;gap:10px;align-items:stretch}
    .cs-nav-bar-right{justify-content:flex-end}
    .cs-nav-btn{padding:7px 12px;font-size:12px}
    .cs-head h1{font-size:20px}
    .cs-meta{font-size:11px;gap:6px}
    .cs-keyfacts{gap:6px;padding:14px 0 16px}
    .cs-keyfact{padding:8px 12px;font-size:12px}
    .cs-gallery-tabs{gap:16px}
    .cs-gallery-tab{font-size:12.5px}
    .cs-damage-grid{grid-template-columns:1fr;gap:16px}
    .cs-status-grid{grid-template-columns:1fr}
    .cs-svc-grid{grid-template-columns:1fr}
    .cs-tire-table{grid-template-columns:1fr;}
    .cs-tire-th{display:none}
    .cs-tire-info{border-right:none;border-bottom:1px solid var(--border-l)}
    .cs-tire-col{border-right:none;border-bottom:1px solid var(--border-l);flex-direction:row;flex-wrap:wrap}
    .cs-tire-col:last-child{border-bottom:none}
    .cs-tire-col-head{border-right:none;width:100%;border-bottom:1px solid var(--border-l)}
    .cs-tire-data-row{flex:1;min-width:50%;border-right:1px solid var(--border-l)}
    .cs-price-value{font-size:28px}
    .cs-fuel-grid{grid-template-columns:1fr 1fr}
    .cs-sticky-bottom-title{display:none}
    .cs-sticky-bottom-price{font-size:22px}
    .cs-wrap{padding-bottom:70px}
}
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cs-wrap">
<div class="container" style="padding-top:16px">
    <!-- NAVIGATION BAR (COS style) -->
    <div class="cs-nav-bar">
        <div class="cs-nav-bar-left">
            <a href="<?php echo e(route('catalog')); ?>" class="cs-nav-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Powrót do ofert
            </a>
        </div>
        <div class="cs-nav-bar-right">
            <?php if($prevCar): ?>
                <a href="<?php echo e(url('/samochody/'.$prevCar->slug)); ?>" class="cs-nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Poprzedni pojazd
                </a>
            <?php else: ?>
                <span class="cs-nav-btn disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Poprzedni pojazd
                </span>
            <?php endif; ?>
            <?php if($nextCar): ?>
                <a href="<?php echo e(url('/samochody/'.$nextCar->slug)); ?>" class="cs-nav-btn">
                    Następny pojazd
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            <?php else: ?>
                <span class="cs-nav-btn disabled">
                    Następny pojazd
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="cs-head">
        <div style="min-width:0">
            <h1><?php echo e($car->title); ?></h1>
            <div class="cs-meta">
                <?php if($car->first_registration): ?><span><?php echo e($car->first_registration); ?></span><span class="sep">·</span><?php endif; ?>
                <?php if($car->mileage): ?><span><?php echo e(number_format($car->mileage,0,'',' ')); ?> km</span><span class="sep">·</span><?php endif; ?>
                <?php if($car->fuel_type): ?><span><?php echo e($car->fuel_type); ?></span><span class="sep">·</span><?php endif; ?>
                <?php if($car->transmission): ?><span><?php echo e($car->transmission); ?></span><span class="sep">·</span><?php endif; ?>
                <?php if($car->power_hp): ?><span><?php echo e($car->power_hp); ?> KM</span><?php endif; ?>
            </div>
        </div>
    </div>

    <?php
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
    ?>

    <!-- KEY FACTS STRIP (COS-style) -->
    <div class="cs-keyfacts">
        <?php if($car->mileage): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M12 2a10 10 0 0 1 7.07 17.07"/><path d="M12 2a10 10 0 0 0-7.07 17.07"/><path d="M12 8v-3"/></svg>
            <strong><?php echo e(number_format($car->mileage,0,'',' ')); ?> km</strong>
        </div>
        <?php endif; ?>
        <?php if($car->first_registration): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <?php echo e($car->first_registration); ?>

        </div>
        <?php endif; ?>
        <?php if($car->fuel_type): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 22V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17"/><path d="M15 11h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 4"/></svg>
            <?php echo e($car->fuel_type); ?>

        </div>
        <?php endif; ?>
        <?php if($car->power_hp): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            <strong><?php echo e($car->power_hp); ?> KM</strong><?php if($car->power_kw): ?> <span style="color:#9ca3af;font-weight:400;margin-left:2px">(<?php echo e($car->power_kw); ?> kW)</span><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($car->transmission): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="5" cy="6" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="19" cy="6" r="2"/><path d="M5 8v8a2 2 0 0 0 2 2h4V8"/><path d="M19 8v8a2 2 0 0 1-2 2h-4"/></svg>
            <?php echo e($car->transmission); ?>

        </div>
        <?php endif; ?>
        <?php if($car->color): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r="2.5"/><path d="M17.78 10.22a2.5 2.5 0 0 1 0 3.56L12 19.56a2.5 2.5 0 0 1-3.56 0l-5.78-5.78a2.5 2.5 0 0 1 0-3.56L8.44 4.44a2.5 2.5 0 0 1 3.56 0z"/></svg>
            <?php echo e($car->color); ?>

        </div>
        <?php endif; ?>
        <?php if($car->category): ?>
        <div class="cs-keyfact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
            <?php echo e($car->category); ?>

        </div>
        <?php endif; ?>
    </div>

    <?php
        $galleryList = $car->galleryImages->count() ? $car->galleryImages : ($car->primaryImage ? collect([$car->primaryImage]) : collect());
        $damageImgList = $car->damageImages ?? collect();
        $allMediaCount = $galleryList->count() + $damageImgList->count();
        $hasEngineVideo = $car->engine_video_url || $car->engine_video_path;
    ?>

    <div class="cs-grid">
        <!-- LEFT COLUMN: Gallery + CertiCheck -->
        <div>
        <!-- GALLERY MEDIA TABS (COS style — above gallery, left column only) -->
        <div class="cs-gallery-tabs">
            <button type="button" class="cs-gallery-tab active" data-gallery-filter="all" onclick="csFilterGallery(this,'all')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                Wszystkie zdjęcia
            </button>
            <button type="button" class="cs-gallery-tab <?php echo e($damageImgList->count() ? '' : 'disabled'); ?>" data-gallery-filter="damage" onclick="csFilterGallery(this,'damage')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Zdjęcia przedstawiające stan pojazdu
            </button>
            <button type="button" class="cs-gallery-tab disabled" data-gallery-filter="documents">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                Dokumenty
            </button>
            <button type="button" class="cs-gallery-tab <?php echo e($hasEngineVideo ? '' : 'disabled'); ?>" data-gallery-filter="video" onclick="csFilterGallery(this,'video')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Nagranie z pracy silnika
            </button>
        </div>
        <div class="cs-gallery">
            <div class="cs-gallery-main">
                <?php if($galleryList->count()): ?>
                    <img src="<?php echo e($galleryList->first()->url); ?>" id="csMainImg" alt="<?php echo e($galleryList->first()->alt); ?>" style="cursor:zoom-in" onclick="openCarGallery(0)">
                    <div class="cs-gallery-counter" style="cursor:zoom-in" onclick="openCarGallery(parseInt(document.getElementById('csImgCounter').textContent)-1)"><i data-lucide="maximize-2" aria-hidden="true"></i> <span id="csImgCounter">1</span>/<span id="csImgTotal"><?php echo e($galleryList->count()); ?></span></div>
                <?php else: ?>
                    <div class="empty"><i data-lucide="car" aria-hidden="true"></i></div>
                <?php endif; ?>
            </div>
            <?php if($galleryList->count() > 1 || $damageImgList->count()): ?>
            <div class="cs-gallery-thumbs" id="csGalleryThumbs">
                <?php $__currentLoopData = $galleryList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e($img->url); ?>" loading="lazy" alt="<?php echo e($img->alt); ?>" class="cs-thumb <?php echo e($i===0?'active':''); ?>" data-type="gallery" data-idx="<?php echo e($i); ?>" onclick="csSelImg(this,<?php echo e($i+1); ?>)" ondblclick="openCarGallery(<?php echo e($i); ?>)" tabindex="0" onkeypress="if(event.key==='Enter')csSelImg(this,<?php echo e($i+1); ?>)">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $damageImgList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j => $dimg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e($dimg->url); ?>" loading="lazy" alt="<?php echo e($dimg->alt); ?>" class="cs-thumb" data-type="damage" data-idx="<?php echo e($galleryList->count() + $j); ?>" onclick="csSelImg(this,<?php echo e($galleryList->count() + $j + 1); ?>)" tabindex="0">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
        <!-- CERTICHECK INLINE REPORT -->
        <div class="cc-report">
            <div class="cc-report-header">
                <div>
                    <div class="cc-report-brand">
                        <div class="name">Certi<span>Cars</span></div>
                        <span class="cc-report-badge">CertiCheck</span>
                    </div>
                    <div class="cc-report-sub">Pojazd zweryfikowany · <?php echo e($car->identifier); ?></div>
                </div>
                <a href="<?php echo e(route('car.pdf', $car->slug)); ?>" class="cc-report-pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Pobierz PDF
                </a>
            </div>

            
            <div class="cc-section-hd open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                Specyfikacja techniczna
                <svg class="chv" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cc-section-bd open">
                <div class="cc-grid-2">
                    <div>
                        <?php if($car->first_registration): ?><div class="cc-row"><span class="lbl">Pierwsza rejestracja</span><span class="val"><?php echo e($car->first_registration); ?></span></div><?php endif; ?>
                        <?php if($car->mileage): ?><div class="cc-row"><span class="lbl">Przebieg</span><span class="val"><?php echo e(number_format($car->mileage,0,'',' ')); ?> km</span></div><?php endif; ?>
                        <?php if($car->fuel_type): ?><div class="cc-row"><span class="lbl">Paliwo</span><span class="val"><?php echo e($car->fuel_type); ?></span></div><?php endif; ?>
                        <?php if($car->engine_capacity): ?><div class="cc-row"><span class="lbl">Pojemność silnika</span><span class="val"><?php echo e($car->engine_capacity); ?> ccm</span></div><?php endif; ?>
                        <?php if($car->power_hp): ?><div class="cc-row"><span class="lbl">Moc</span><span class="val"><?php echo e($car->power_hp); ?> KM <?php if($car->power_kw): ?>(<?php echo e($car->power_kw); ?> kW)<?php endif; ?></span></div><?php endif; ?>
                        <?php if($car->transmission): ?><div class="cc-row"><span class="lbl">Skrzynia biegów</span><span class="val"><?php echo e($car->transmission_detail ?? $car->transmission); ?></span></div><?php endif; ?>
                        <?php if($car->body_type): ?><div class="cc-row"><span class="lbl">Nadwozie</span><span class="val"><?php echo e($car->body_type); ?></span></div><?php endif; ?>
                    </div>
                    <div>
                        <?php if($car->color): ?><div class="cc-row"><span class="lbl">Kolor</span><span class="val"><?php echo e($car->color); ?><?php if($car->color_code): ?> (<?php echo e($car->color_code); ?>)<?php endif; ?></span></div><?php endif; ?>
                        <?php if($car->doors): ?><div class="cc-row"><span class="lbl">Drzwi / Miejsca</span><span class="val"><?php echo e($car->doors); ?> / <?php echo e($car->seats ?? '—'); ?></span></div><?php endif; ?>
                        <?php if($car->vin): ?><div class="cc-row"><span class="lbl">VIN</span><span class="val"><?php echo e($car->vin); ?></span></div><?php endif; ?>
                        <?php if($car->upholstery): ?><div class="cc-row"><span class="lbl">Tapicerka</span><span class="val"><?php echo e($car->upholstery); ?></span></div><?php endif; ?>
                        <?php if($car->weight): ?><div class="cc-row"><span class="lbl">Masa własna</span><span class="val"><?php echo e(number_format($car->weight,0,'',' ')); ?> kg</span></div><?php endif; ?>
                        <?php if($car->previous_owners !== null): ?><div class="cc-row"><span class="lbl">Poprzedni właściciele</span><span class="val"><?php echo e($car->previous_owners); ?></span></div><?php endif; ?>
                        <?php if($car->number_of_keys): ?><div class="cc-row"><span class="lbl">Liczba kluczyków</span><span class="val"><?php echo e($car->number_of_keys); ?></span></div><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <?php if($car->service_book || $car->last_service || $car->next_inspection || $car->coc_documents): ?>
            <div class="cc-section-hd open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                Serwis i dokumentacja
                <svg class="chv" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cc-section-bd open">
                <div class="cc-grid-2">
                    <div>
                        <?php if($car->service_book): ?><div class="cc-row"><span class="lbl">Książka serwisowa</span><span class="val"><?php echo e($car->service_book); ?></span></div><?php endif; ?>
                        <?php if($car->last_service): ?><div class="cc-row"><span class="lbl">Ostatni serwis</span><span class="val"><?php echo e($car->last_service); ?><?php if($car->last_service_mileage): ?> (<?php echo e(number_format($car->last_service_mileage,0,'',' ')); ?> km)<?php endif; ?></span></div><?php endif; ?>
                        <?php if($car->next_inspection): ?><div class="cc-row"><span class="lbl">Następny przegląd</span><span class="val"><?php echo e($car->next_inspection); ?></span></div><?php endif; ?>
                    </div>
                    <div>
                        <?php if($car->service_documentation): ?><div class="cc-row"><span class="lbl">Dokumentacja</span><span class="val"><?php echo e($car->service_documentation); ?></span></div><?php endif; ?>
                        <?php if($car->coc_documents): ?><div class="cc-row"><span class="lbl">Dokumenty CoC</span><span class="val"><?php echo e($car->coc_documents); ?></span></div><?php endif; ?>
                        <?php if($car->vehicle_folder): ?><div class="cc-row"><span class="lbl">Teczka pojazdu</span><span class="val"><?php echo e($car->vehicle_folder); ?></span></div><?php endif; ?>
                        <?php if($car->hu_au_report): ?><div class="cc-row"><span class="lbl">Raport HU/AU</span><span class="val"><?php echo e($car->hu_au_report); ?></span></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($car->co2_emission || $car->emission_class || $car->fuel_consumption): ?>
            <div class="cc-section-hd open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17"/><path d="M15 11h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 4"/></svg>
                Emisje i zużycie paliwa
                <svg class="chv" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cc-section-bd open">
                <div class="cc-grid-2">
                    <div>
                        <?php if($car->fuel_consumption): ?><div class="cc-row"><span class="lbl">Zużycie paliwa (mieszany)</span><span class="val"><?php echo e($car->fuel_consumption); ?> l/100km</span></div><?php endif; ?>
                        <?php if($car->fuel_procedure): ?><div class="cc-row"><span class="lbl">Procedura pomiaru</span><span class="val"><?php echo e($car->fuel_procedure); ?></span></div><?php endif; ?>
                    </div>
                    <div>
                        <?php if($car->co2_emission): ?><div class="cc-row"><span class="lbl">Emisja CO₂</span><span class="val"><?php echo e($car->co2_emission); ?> g/km</span></div><?php endif; ?>
                        <?php if($car->emission_class): ?><div class="cc-row"><span class="lbl">Klasa emisji</span><span class="val"><?php echo e($car->emission_class); ?></span></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($car->paint_measurements && count($car->paint_measurements)): ?>
            <div class="cc-section-hd open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="2.5"/><path d="M17.78 10.22a2.5 2.5 0 0 1 0 3.56L12 19.56a2.5 2.5 0 0 1-3.56 0l-5.78-5.78a2.5 2.5 0 0 1 0-3.56L8.44 4.44a2.5 2.5 0 0 1 3.56 0z"/></svg>
                Pomiary grubości lakieru
                <svg class="chv" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cc-section-bd open">
                <p style="font-size:11px;color:#9ca3af;padding:6px 20px 0;margin:0">Norma fabryczna: 80–150 µm · powyżej 200 µm — możliwa naprawa lakiernicza</p>
                <div class="cc-paint-grid">
                    <?php $__currentLoopData = $car->paint_measurements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $panel => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cc-paint-item"><span class="panel"><?php echo e($panel); ?></span><span class="um <?php echo e($value > 200 ? 'danger' : ($value > 160 ? 'warn' : '')); ?>"><?php echo e($value); ?> µm</span></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($car->technical_conditions && count($car->technical_conditions)): ?>
            <div class="cc-section-hd open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                Ocena stanu technicznego
                <svg class="chv" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cc-section-bd open">
                <?php $__currentLoopData = $car->technical_conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cc-tech-row"><span class="comp"><?php echo e($comp); ?></span><span class="st"><?php echo e($status); ?></span></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <?php if($car->equipment && count($car->equipment)): ?>
            <div class="cc-section-hd open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Wyposażenie pojazdu
                <svg class="chv" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cc-section-bd open">
                <div class="cc-eq-wrap">
                    <?php $__currentLoopData = $car->equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(is_array($items) && count($items)): ?>
                    <div class="cc-eq-cat">
                        <div class="cc-eq-cat-t"><?php echo e($cat); ?></div>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cc-eq-it"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?php echo e($item); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- FEATURED EQUIPMENT STRIP (COS style — always visible below gallery) -->
        <?php if(isset($featuredItems) && count($featuredItems)): ?>
        <div style="margin-top:24px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="font-size:18px;font-weight:800;color:#1a1a1a;letter-spacing:-.3px">Najważniejsze elementy wyposażenia</span>
            </div>
            <div class="cs-feat-eq">
                <?php $__currentLoopData = $featuredItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cs-feat-eq-item"><i data-lucide="check" aria-hidden="true"></i> <?php echo e($item); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        </div>

        <!-- PRICE + CALC SIDEBAR (sticky) -->
        <div class="cs-sidebar">
            <!-- SIDEBAR CARD (COS style) -->
            <div class="cs-sidebar-card" style="margin-bottom:16px">
                <!-- PRICE -->
                <div class="cs-price-section">
                    <div class="cs-price-label">Cena sprzedaży</div>
                    <div class="cs-price-value"><?php echo e($car->formatted_price); ?><?php if($car->price_type): ?><small><?php echo e($car->price_type); ?></small><?php endif; ?></div>
                </div>
                <!-- VEHICLE SUMMARY (COS-style key-value pairs) -->
                <div class="cs-sidebar-summary">
                    <?php if($car->mileage): ?><div class="cs-sidebar-summary-row"><span class="lbl">Przebieg</span><span class="val"><?php echo e(number_format($car->mileage,0,'',' ')); ?> km</span></div><?php endif; ?>
                    <?php if($car->first_registration): ?><div class="cs-sidebar-summary-row"><span class="lbl">Pierwsza rejestracja</span><span class="val"><?php echo e($car->first_registration); ?></span></div><?php endif; ?>
                    <?php if($car->power_hp): ?><div class="cs-sidebar-summary-row"><span class="lbl">Moc</span><span class="val"><?php echo e($car->power_hp); ?> KM</span></div><?php endif; ?>
                    <?php if($car->fuel_type): ?><div class="cs-sidebar-summary-row"><span class="lbl">Paliwo</span><span class="val"><?php echo e($car->fuel_type); ?></span></div><?php endif; ?>
                    <?php if($car->transmission): ?><div class="cs-sidebar-summary-row"><span class="lbl">Skrzynia</span><span class="val"><?php echo e($car->transmission); ?></span></div><?php endif; ?>
                    <?php if($car->color): ?><div class="cs-sidebar-summary-row"><span class="lbl">Kolor</span><span class="val"><?php echo e($car->color); ?></span></div><?php endif; ?>
                </div>
                <!-- CTA BUTTONS -->
                <div class="cs-price-actions">
                    <a href="tel:+48123456789" class="btn btn-blue btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Zadzwoń
                    </a>
                </div>
            </div>

            <!-- KALKULATOR FINANSOWANIA -->
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
                    <?php $carPrice = $car->price ?? 0; ?>
                    <input type="hidden" id="csCalcPrice" value="<?php echo e($carPrice); ?>">

                    <div class="cs-calc-field">
                        <label for="csCalcDown">Wpłata własna</label>
                        <div class="cs-calc-range-row">
                            <input type="range" id="csCalcDown" min="0" max="<?php echo e($carPrice); ?>" step="1000" value="<?php echo e(round($carPrice * 0.2)); ?>" oninput="csCalcUpdate()">
                            <span class="cs-calc-range-val" id="csCalcDownVal"><?php echo e(number_format(round($carPrice * 0.2), 0, '', ' ')); ?> zł</span>
                        </div>
                        <div class="cs-calc-range-labels"><span>0 zł</span><span><?php echo e(number_format($carPrice, 0, '', ' ')); ?> zł</span></div>
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
    </div>

    <!-- STAN POJAZDU (CarOnSale style) -->
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="shield-check" aria-hidden="true"></i> Stan pojazdu</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-status-grid">
                <div class="cs-status">
                    <div class="cs-status-ico"><i data-lucide="check-circle" aria-hidden="true"></i></div>
                    <div class="cs-status-body">
                        <strong>Istniejące uszkodzenia</strong>
                        <?php if($car->existing_damages_count>0): ?>
                            <span class="badge"><i data-lucide="alert-triangle" aria-hidden="true"></i> <?php echo e($car->existing_damages_count); ?> <?php echo e($car->existing_damages_count==1?'obszar':'obszary'); ?></span>
                        <?php else: ?>
                            <span style="font-size:12px;color:var(--green-dark)">Brak oznak uszkodzeń</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="cs-status">
                    <div class="cs-status-ico"><i data-lucide="wrench" aria-hidden="true"></i></div>
                    <div class="cs-status-body">
                        <strong>Naprawione uszkodzenia</strong>
                        <?php if($car->repaired_damages_count>0): ?>
                            <span class="badge"><?php echo e($car->repaired_damages_count); ?> naprawionych</span>
                        <?php else: ?>
                            <span style="font-size:12px;color:var(--green-dark)">Brak naprawionych szkód</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILM Z PRACY SILNIKA (CarOnSale style) -->
    <?php if($car->engine_video_url || $car->engine_video_path): ?>
    <div class="cs-data-section" data-panel-engine-video>
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="play-circle" aria-hidden="true"></i> Film z pracy silnika</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <?php
                $yt=null;$vim=null;$vidUrl=$car->engine_video_url;
                if($vidUrl){
                    if(preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/))([\w-]{11})~', $vidUrl, $m)) $yt=$m[1];
                    elseif(preg_match('~vimeo\.com/(\d+)~', $vidUrl, $m)) $vim=$m[1];
                }
            ?>
            <div style="position:relative;border-radius:12px;overflow:hidden;background:#000;aspect-ratio:16/9;max-width:860px">
                <?php if($yt): ?>
                    <iframe src="https://www.youtube.com/embed/<?php echo e($yt); ?>" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe>
                <?php elseif($vim): ?>
                    <iframe src="https://player.vimeo.com/video/<?php echo e($vim); ?>" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe>
                <?php elseif($car->engine_video_path): ?>
                    <video src="<?php echo e($car->engine_video_file_url); ?>" controls preload="metadata" style="width:100%;height:100%;display:block"></video>
                <?php elseif($vidUrl): ?>
                    <div style="padding:20px;color:#fff">Film dostępny pod <a href="<?php echo e($vidUrl); ?>" target="_blank" style="color:#fff;text-decoration:underline">tym linkiem</a>.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- USZKODZENIA (CarOnSale style) -->
    <?php if($car->damages->count()): ?>
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="map-pin" aria-hidden="true" style="color:var(--yellow)"></i> Uszkodzenia pojazdu</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <p style="font-size:12.5px;color:var(--text-3);margin-bottom:14px">Kliknij oznaczenie, aby zobaczyć informacje</p>
            <div class="cs-damages-tabs">
                <?php $__currentLoopData = $car->damages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="cs-damage-tab" data-damage-tab="<?php echo e($d->id); ?>" onclick="csSelDamage(<?php echo e($d->id); ?>)"><i data-lucide="alert-triangle" aria-hidden="true"></i> <?php echo e($d->area); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <?php $__currentLoopData = $car->damages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="cs-damage-marker" data-damage-marker="<?php echo e($d->id); ?>" onclick="csSelDamage(<?php echo e($d->id); ?>)" aria-label="Uszkodzenie: <?php echo e($d->area); ?>" style="left:<?php echo e($d->position_x ?? 50); ?>%;top:<?php echo e($d->position_y ?? 50); ?>%">
                            <span class="cs-damage-marker-dot"><i data-lucide="alert-triangle" aria-hidden="true"></i></span>
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="cs-damage-detail" id="csDamageDetail">
                    <div class="cs-damage-empty" id="csDamageEmpty">
                        <i data-lucide="mouse-pointer-click" aria-hidden="true"></i>
                        Wybierz uszkodzenie z mapy
                    </div>
                    <?php $__currentLoopData = $car->damages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cs-damage-item" id="csDamage-<?php echo e($d->id); ?>">
                        <h3><i data-lucide="alert-triangle" aria-hidden="true"></i> <?php echo e($d->area); ?></h3>
                        <?php if($d->tags && count($d->tags)): ?>
                        <div class="cs-damage-tags">
                            <?php $__currentLoopData = $d->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($t); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                        <?php if($d->description): ?><p><?php echo e($d->description); ?></p><?php endif; ?>
                        <?php if($d->image_path): ?>
                        <a href="<?php echo e($d->image_url); ?>" data-lightbox="<?php echo e($d->image_url); ?>" data-gallery="damage-<?php echo e($d->id); ?>" data-caption="<?php echo e($d->area); ?>" style="display:block;margin-top:14px;border-radius:10px;overflow:hidden;background:var(--bg);cursor:zoom-in">
                            <img src="<?php echo e($d->image_url); ?>" alt="<?php echo e($d->area); ?>" loading="lazy" style="width:100%;max-height:260px;object-fit:cover;display:block">
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ZUŻYCIE / EMISJA (CarOnSale style) -->
    <?php if($car->fuel_consumption || $car->co2_emission || $car->emission_class): ?>
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="leaf" aria-hidden="true"></i> Zużycie paliwa i emisja</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-data-2col">
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Zużycie</div>
                    <?php if($car->fuel_consumption): ?><div class="cs-data-row"><span class="lbl">Zużycie paliwa</span><span class="val"><?php echo e($car->fuel_consumption); ?></span></div><?php endif; ?>
                    <?php if($car->fuel_procedure): ?><div class="cs-data-row"><span class="lbl">Procedura pomiaru</span><span class="val"><?php echo e($car->fuel_procedure); ?></span></div><?php endif; ?>
                </div>
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Emisja</div>
                    <?php if($car->co2_emission): ?><div class="cs-data-row"><span class="lbl">Emisja CO₂</span><span class="val"><?php echo e($car->co2_emission); ?></span></div><?php endif; ?>
                    <?php if($car->emission_class): ?><div class="cs-data-row"><span class="lbl">Klasa emisji</span><span class="val"><?php echo e($car->emission_class); ?></span></div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- POMIARY LAKIERU (CarOnSale style) -->
    <?php if($car->paint_measurements): ?>
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="paintbrush" aria-hidden="true"></i> Grubość lakieru</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-paint-grid">
                <?php $__currentLoopData = $car->paint_measurements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cs-paint-item">
                    <div class="cs-paint-label"><?php echo e($m['label'] ?? $m['area'] ?? ''); ?></div>
                    <div class="cs-paint-value"><?php echo e($m['value'] ?? ''); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- KOŁA I OPONY (CarOnSale style) -->
    <?php if($car->tireSets->count()): ?>
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="circle-dot" aria-hidden="true"></i> Koła i opony</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
        <?php $__currentLoopData = $car->tireSets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $set): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $setTires = $set->tires;
            $positions = ['Przednia lewa','Przednia prawa','Tylna lewa','Tylna prawa'];
        ?>
        <div class="cs-tire-set">
            <h3 class="cs-tire-set-title">
                <?php echo e($set->set_number); ?>. Komplet <?php if($set->is_mounted): ?>(zamontowane) <i data-lucide="info" aria-hidden="true"></i><?php endif; ?>
            </h3>
            <div class="cs-tire-table">
                <div class="cs-tire-th"></div>
                <?php $__currentLoopData = $setTires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cs-tire-th" style="text-align:center"><?php echo e($t->position); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="cs-tire-info">
                    <?php if($set->tire_type): ?>
                    <div class="cs-tire-info-row"><div class="lbl">Rodzaj opon</div><div class="val"><?php echo e($set->tire_type); ?></div></div>
                    <?php endif; ?>
                    <?php if($set->rim): ?>
                    <div class="cs-tire-info-row"><div class="lbl">Felga</div><div class="val"><?php echo e($set->rim); ?></div></div>
                    <?php endif; ?>
                    <div class="cs-tire-info-row"><div class="lbl">Głębokość bieżnika</div></div>
                    <div class="cs-tire-info-row"><div class="lbl">Stan</div></div>
                </div>
                <?php $__currentLoopData = $setTires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $depth = (float)($t->tread_depth ?? 0);
                    $hasIssue = $t->condition && count($t->condition) > 0;
                    $statusCls = $hasIssue ? 'warn' : 'ok';
                ?>
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
                            <span class="cs-tire-status-icon <?php echo e($statusCls); ?>">
                                <?php if($hasIssue): ?><i data-lucide="alert-triangle" aria-hidden="true"></i>
                                <?php else: ?><i data-lucide="check" aria-hidden="true"></i><?php endif; ?>
                            </span>
                        </div>
                        <div class="cs-tire-pos-name"><?php echo e($t->position); ?></div>
                    </div>
                    <div class="cs-tire-data-row" style="font-weight:700"><?php echo e($t->tread_depth ?? '—'); ?></div>
                    <div class="cs-tire-data-row <?php echo e($hasIssue ? 'warn-txt' : 'ok-txt'); ?>">
                        <?php if($hasIssue): ?>
                            <i data-lucide="alert-triangle" aria-hidden="true" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:3px"></i><?php echo e(implode(', ', $t->condition)); ?>

                        <?php else: ?>
                            <i data-lucide="check" aria-hidden="true" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:3px"></i>Brak nieprawidłowości
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($set->notes): ?>
            <div style="margin-top:12px;padding:10px 14px;background:var(--yellow-bg);border-radius:10px;font-size:12.5px;color:var(--yellow-dark);display:flex;align-items:center;gap:8px">
                <i data-lucide="info" aria-hidden="true" style="width:14px;height:14px"></i> <?php echo e($set->notes); ?>

            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- DANE POJAZDU (CarOnSale style) -->
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="search" aria-hidden="true"></i> Dane pojazdu</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-data-2col">
                
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Dane ogólne</div>
                    <?php if($car->brand?->name): ?><div class="cs-data-row"><span class="lbl">Marka</span><span class="val"><?php echo e($car->brand->name); ?></span></div><?php endif; ?>
                    <?php if($car->model): ?><div class="cs-data-row"><span class="lbl">Model</span><span class="val"><?php echo e($car->model); ?></span></div><?php endif; ?>
                    <?php if($car->category): ?><div class="cs-data-row"><span class="lbl">Kategoria</span><span class="val"><?php echo e($car->category); ?></span></div><?php endif; ?>
                    <?php if($car->color): ?><div class="cs-data-row"><span class="lbl">Kolor</span><span class="val"><?php echo e($car->color); ?><?php if($car->color_code): ?> (<?php echo e($car->color_code); ?>)<?php endif; ?></span></div><?php endif; ?>
                    <?php if($car->doors): ?><div class="cs-data-row"><span class="lbl">Liczba drzwi</span><span class="val"><?php echo e($car->doors); ?></span></div><?php endif; ?>
                    <?php if($car->seats): ?><div class="cs-data-row"><span class="lbl">Liczba siedzeń</span><span class="val"><?php echo e($car->seats); ?></span></div><?php endif; ?>
                    <?php if($car->weight): ?><div class="cs-data-row"><span class="lbl">Masa</span><span class="val"><?php echo e(number_format($car->weight, 0, '', ' ')); ?> kg</span></div><?php endif; ?>
                    <?php if($car->upholstery): ?><div class="cs-data-row"><span class="lbl">Tapicerka</span><span class="val"><?php echo e($car->upholstery); ?></span></div><?php endif; ?>
                    <?php if($car->vin): ?><div class="cs-data-row"><span class="lbl">VIN</span><span class="val"><?php echo e($car->vin); ?></span></div><?php endif; ?>
                </div>
                
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Pochodzenie pojazdu</div>

                    <div class="cs-data-row"><span class="lbl">Pojazd importowany</span><span class="val"><?php echo e($car->is_imported ? 'Tak' : 'Nie'); ?></span></div>
                    <?php if($car->country_registration): ?><div class="cs-data-row"><span class="lbl">Kraj ostatniej rejestracji</span><span class="val"><?php echo e($car->country_registration); ?></span></div><?php endif; ?>
                    <?php if($car->taxation): ?><div class="cs-data-row"><span class="lbl">Opodatkowanie</span><span class="val"><?php echo e($car->taxation); ?></span></div><?php endif; ?>
                </div>
            </div>

            
            <?php if($car->fuel_type || $car->power_hp || $car->engine_capacity || $car->transmission): ?>
            <div class="cs-data-2col" style="margin-top:24px">
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Silnik i napęd</div>
                    <?php if($car->fuel_type): ?><div class="cs-data-row"><span class="lbl">Paliwo</span><span class="val"><?php echo e($car->fuel_type); ?></span></div><?php endif; ?>
                    <?php if($car->power_hp): ?><div class="cs-data-row"><span class="lbl">Moc</span><span class="val"><?php echo e($car->power_hp); ?> KM<?php echo e($car->power_kw ? ' ('.$car->power_kw.' kW)' : ''); ?></span></div><?php endif; ?>
                    <?php if($car->engine_capacity): ?><div class="cs-data-row"><span class="lbl">Pojemność</span><span class="val"><?php echo e(number_format($car->engine_capacity, 0, '', ' ')); ?> ccm</span></div><?php endif; ?>
                    <?php if($car->transmission): ?><div class="cs-data-row"><span class="lbl">Skrzynia biegów</span><span class="val"><?php echo e($car->transmission); ?><?php echo e($car->transmission_detail ? ' ('.$car->transmission_detail.')' : ''); ?></span></div><?php endif; ?>
                </div>
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Historia eksploatacji</div>
                    <?php if($car->first_registration): ?><div class="cs-data-row"><span class="lbl">Pierwsza rejestracja</span><span class="val"><?php echo e($car->first_registration); ?></span></div><?php endif; ?>
                    <?php if($car->mileage): ?><div class="cs-data-row"><span class="lbl">Przebieg</span><span class="val"><?php echo e(number_format($car->mileage, 0, '', ' ')); ?> km</span></div><?php endif; ?>
                    <?php if($car->previous_owners !== null): ?><div class="cs-data-row"><span class="lbl">Poprzedni właściciele</span><span class="val"><?php echo e($car->previous_owners); ?></span></div><?php endif; ?>
                    <?php if($car->number_of_keys): ?><div class="cs-data-row"><span class="lbl">Liczba kluczyków</span><span class="val"><?php echo e($car->number_of_keys); ?></span></div><?php endif; ?>
                    <?php if($car->business_use): ?><div class="cs-data-row"><span class="lbl">Użytkowanie</span><span class="val"><?php echo e($car->business_use); ?></span></div><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SERWISOWANIE I INSPEKCJA (CarOnSale style) -->
    <?php if($car->last_service || $car->next_inspection || $car->service_documentation || $car->service_book || $car->coc_documents || $car->last_service_mileage): ?>
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="wrench" aria-hidden="true"></i> Serwisowanie i inspekcja pojazdu</h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-data-2col">
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Serwis</div>
                    <?php if($car->last_service): ?><div class="cs-data-row"><span class="lbl">Ostatni serwis</span><span class="val"><?php echo e($car->last_service); ?></span></div><?php endif; ?>
                    <?php if($car->last_service_mileage): ?><div class="cs-data-row"><span class="lbl">Przebieg przy serwisie</span><span class="val"><?php echo e($car->last_service_mileage); ?></span></div><?php endif; ?>
                    <?php if($car->service_documentation): ?><div class="cs-data-row"><span class="lbl">Dokumentacja serwisowa</span><span class="val"><?php echo e($car->service_documentation); ?></span></div><?php endif; ?>
                    <?php if($car->next_inspection): ?><div class="cs-data-row"><span class="lbl">Raport HU/AU ważny do</span><span class="val"><?php echo e($car->next_inspection); ?></span></div><?php endif; ?>
                </div>
                <div class="cs-data-block">
                    <div class="cs-data-block-title">Dokumenty</div>
                    <?php if($car->service_book): ?><div class="cs-data-row"><span class="lbl">Książka serwisowa</span><span class="val"><?php echo e($car->service_book); ?></span></div><?php endif; ?>
                    <?php if($car->coc_documents): ?><div class="cs-data-row"><span class="lbl">Dokumenty COC</span><span class="val"><?php echo e($car->coc_documents); ?></span></div><?php endif; ?>
                    <?php if($car->vehicle_folder): ?><div class="cs-data-row"><span class="lbl">Teczka pojazdu</span><span class="val"><?php echo e($car->vehicle_folder); ?></span></div><?php endif; ?>
                    <?php if($car->hu_au_report): ?><div class="cs-data-row"><span class="lbl">Raport HU/AU</span><span class="val"><?php echo e($car->hu_au_report); ?></span></div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- WYPOSAŻENIE (CarOnSale style) -->
    <?php if($car->equipment): ?>

    
    <div class="cs-data-section">
        <div class="cs-data-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">
            <h2><i data-lucide="list-checks" aria-hidden="true"></i> Wyposażenie <span style="font-size:13px;font-weight:500;color:var(--text-3);margin-left:8px"><?php echo e($totalCount); ?> pozycji</span></h2>
            <i data-lucide="chevron-up" class="chev" aria-hidden="true"></i>
        </div>
        <div class="cs-data-body open">
            <div class="cs-equipment-grid">
                <?php $__currentLoopData = $allEquipmentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cs-equipment-item"><i data-lucide="check" aria-hidden="true"></i> <?php echo e($item); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</div>

<!-- STICKY BOTTOM BAR (COS-style) -->
<div class="cs-sticky-bottom" id="csStickyBottom">
    <div class="cs-sticky-bottom-in">
        <div class="cs-sticky-bottom-info">
            <div class="cs-sticky-bottom-title"><?php echo e($car->title); ?></div>
            <div class="cs-sticky-bottom-price"><?php echo e($car->formatted_price); ?><?php if($car->price_type): ?><small><?php echo e($car->price_type); ?></small><?php endif; ?></div>
        </div>
        <div class="cs-sticky-bottom-actions">
            <a href="tel:+48123456789" class="btn btn-blue" style="padding:11px 22px;font-size:13px;border-radius:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Zadzwoń
            </a>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function csSelImg(el,n){
    const m=document.getElementById('csMainImg');if(!m)return;
    m.src=el.src;m.alt=el.alt;
    document.getElementById('csImgCounter').textContent=n;
    document.querySelectorAll('.cs-thumb').forEach(t=>t.classList.remove('active'));
    el.classList.add('active');
}
window.CAR_GALLERY=<?php echo json_encode($galleryList->map(fn($i)=>['src'=>$i->url, 'caption'=>$i->alt])->values(), 512) ?>;
window.CAR_DAMAGE_GALLERY=<?php echo json_encode($damageImgList->map(fn($i)=>['src'=>$i->url, 'caption'=>$i->alt])->values(), 512) ?>;
window.CAR_ALL_GALLERY=[...CAR_GALLERY,...CAR_DAMAGE_GALLERY];
window.openCarGallery=(i)=>{if(window.openLbAt)openLbAt(CAR_ALL_GALLERY, i||0)};

// Gallery tab filtering (COS style)
function csFilterGallery(btn,filter){
    if(btn.classList.contains('disabled'))return;
    document.querySelectorAll('.cs-gallery-tab').forEach(t=>t.classList.remove('active'));
    btn.classList.add('active');
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
    if(filter==='video'){
        // Scroll to engine video section
        thumbs.forEach(t=>t.setAttribute('data-hidden',''));
        const vid=document.querySelector('[data-panel-engine-video]');
        if(vid)vid.scrollIntoView({behavior:'smooth',block:'center'});
        return;
    }
    if(first && !document.querySelector('#csGalleryThumbs .cs-thumb.active:not([data-hidden])')){
        first.classList.add('active');
        csSelImg(first,parseInt(first.dataset.idx)+1);
    }
    // Update counter
    const visibleCount=document.querySelectorAll('#csGalleryThumbs .cs-thumb:not([data-hidden])').length;
    const totalEl=document.getElementById('csImgTotal');
    if(totalEl)totalEl.textContent=visibleCount;
}
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
// — Financing Calculator —
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
    // Sticky bottom bar — show when sidebar price card goes off screen
    const stickyBar = document.getElementById('csStickyBottom');
    const priceCard = document.querySelector('.cs-sidebar-card');
    if(stickyBar && priceCard){
        const obs = new IntersectionObserver(([e])=>{
            stickyBar.classList.toggle('visible', !e.isIntersecting);
        },{threshold:0});
        obs.observe(priceCard);
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/catalog/show.blade.php ENDPATH**/ ?>