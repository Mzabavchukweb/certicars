<?php $__env->startSection('title','Certyfikowane samochody używane'); ?>
<?php $__env->startSection('description','CertiCars — komis premium z pełną inspekcją techniczną. '.$totalCars.' certyfikowanych pojazdów w ofercie.'); ?>

<?php $__env->startSection('styles'); ?>
/* HERO — inset card with rounded corners */
.hero-wrap{background:var(--bg);padding:20px}
.hero{position:relative;background:#0a0a0a;color:#fff;overflow:hidden;margin-bottom:0;min-height:580px;border-radius:20px}
.hero::before{content:'';position:absolute;inset:0;background-image:url('/img/hero-car.jpg');background-size:cover;background-position:center center;background-repeat:no-repeat;z-index:1}
.hero::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(10,10,10,1) 0%,rgba(10,10,10,.97) 22%,rgba(10,10,10,.82) 38%,rgba(10,10,10,.38) 58%,rgba(10,10,10,.08) 80%,rgba(10,10,10,.25) 100%);z-index:2}
.hero-in{position:relative;z-index:3;padding:100px 24px 80px;max-width:1200px;margin:0 auto;min-height:580px;display:flex;flex-direction:column;justify-content:center}
.hero-text{max-width:560px}
.hero-text h1{font-size:72px;font-weight:900;line-height:.95;letter-spacing:-2.5px;margin-bottom:24px}
.hero-text h1 .line1{color:#fff;display:block}
.hero-text h1 .line2{color:var(--blue);display:block}
.hero-text .lead{font-size:17px;color:rgba(255,255,255,.7);max-width:400px;margin-bottom:36px;line-height:1.6;font-weight:400}
.hero-text .hero-ctas{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.hero-text .btn{padding:16px 30px;font-size:14px;border-radius:50px;box-shadow:0 8px 24px rgba(0,102,255,.4);font-weight:700;letter-spacing:.1px;display:inline-flex;align-items:center;gap:8px}
.hero-text .btn:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,102,255,.5)}
.hero-text .btn svg{width:16px;height:16px;stroke-width:2.4}
.hero-secondary-link{color:rgba(255,255,255,.75);font-size:14px;font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .15s}
.hero-secondary-link:hover{color:#fff}
.hero-secondary-link svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.2}
.hero-trust{display:flex;gap:28px;margin-top:28px;padding-top:28px;border-top:1px solid rgba(255,255,255,.12)}
.hero-trust-item{display:flex;flex-direction:column;gap:2px}
.hero-trust-item strong{font-size:22px;font-weight:800;color:#fff;letter-spacing:-.5px;line-height:1}
.hero-trust-item span{font-size:12px;color:rgba(255,255,255,.55);font-weight:400}




/* SEARCH FORM */
.hero-search-wrap{position:relative;margin-top:-72px;z-index:4;padding-bottom:0}
.hero-search{background:#fff;border-radius:22px;box-shadow:0 24px 64px rgba(0,0,0,.16),0 4px 16px rgba(0,0,0,.06);padding:32px 40px 32px;max-width:1200px;margin:0 auto}

/* Header row */
.hero-search-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.hero-search-title{font-family:var(--font-body);font-size:20px;font-weight:800;color:#000;letter-spacing:-.3px;display:flex;align-items:center;gap:10px}
.hero-search-badge{background:var(--blue);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.2px}
.hero-search-nav{display:flex;gap:4px}
.hero-search-nav a,.hero-search-nav button{font-family:var(--font-body);font-size:13px;font-weight:600;color:var(--text-3);background:none;border:none;cursor:pointer;padding:6px 14px;border-radius:8px;text-decoration:none;transition:all .15s}
.hero-search-nav a:hover,.hero-search-nav button:hover{color:var(--text);background:var(--bg)}
.hero-search-nav a.active,.hero-search-nav button.active{color:var(--blue);background:var(--blue-bg)}

/* Fields row */
.hero-search-fields{display:grid;grid-template-columns:repeat(4,1fr);border:1.5px solid var(--border-l);border-radius:14px;overflow:hidden;margin-bottom:16px}
.hero-search-field{padding:14px 20px;border-right:1.5px solid var(--border-l);position:relative}
.hero-search-field:last-child{border-right:none}
.hero-search-field:focus-within{background:var(--bg)}
.hero-search-field label{display:block;font-family:var(--font-body);font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:5px}
.hero-search-field select,.hero-search-field input{width:100%;border:none;outline:none;font-family:var(--font-body);font-size:14px;font-weight:600;color:var(--text);background:transparent;padding:0;appearance:none;line-height:1.3}
.hero-search-field select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23aaa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 0 center;padding-right:18px}
.hero-search-field input::placeholder{color:#9CA3AF;font-weight:400}

/* Bottom row */
.hero-search-bottom{display:flex;align-items:center;justify-content:space-between;padding-top:4px}
.hero-search-reset{font-family:var(--font-body);font-size:13px;color:var(--text-3);font-weight:500;text-decoration:none;transition:color .15s;background:none;border:none;cursor:pointer;padding:0}
.hero-search-reset:hover{color:var(--text)}
.hero-search-submit{height:48px;padding:0 32px;border-radius:50px;background:var(--blue);color:#fff;border:none;display:inline-flex;align-items:center;gap:9px;cursor:pointer;transition:all .2s;font-family:var(--font-body);font-size:14px;font-weight:700;white-space:nowrap;letter-spacing:.1px}
.hero-search-submit:hover{background:var(--blue-h);box-shadow:0 8px 24px rgba(0,102,255,.35);transform:translateY(-1px)}
.hero-search-submit svg{width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2.5}

/* CERTICHECK SECTION */
.certicheck-section{background:#0a0a0a;padding:96px 0}
.certicheck-inner{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center}
.certicheck-left .cc-label{font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--orange);margin-bottom:16px}
.certicheck-left h2{font-size:40px;font-weight:900;color:#fff;letter-spacing:-.8px;line-height:1.1;margin-bottom:20px}
.certicheck-left p{font-size:15px;color:rgba(255,255,255,.55);line-height:1.7;margin-bottom:36px}
.certicheck-cta{display:inline-flex;align-items:center;gap:9px;background:var(--blue);color:#fff;padding:14px 28px;border-radius:50px;font-weight:700;font-size:14px;text-decoration:none;transition:all .2s}
.certicheck-cta:hover{background:var(--blue-h);box-shadow:0 8px 24px rgba(0,102,255,.4);transform:translateY(-1px)}
.certicheck-cta svg{width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2.4}
.certicheck-cards{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.cc-card{background:rgba(255,255,255,.04);padding:32px 28px;display:flex;flex-direction:column;gap:16px;transition:background .2s;border-radius:0}
.cc-card:first-child{border-radius:16px 0 0 0}
.cc-card:nth-child(2){border-radius:0 16px 0 0}
.cc-card:nth-child(3){border-radius:0 0 0 16px}
.cc-card:last-child{border-radius:0 0 16px 0}
.cc-card:hover{background:rgba(255,255,255,.07)}
.cc-card .cc-num{font-size:28px;font-weight:900;color:rgba(255,255,255,.15);letter-spacing:-1px;line-height:1}
.cc-card .cc-ico{width:44px;height:44px;background:rgba(0,102,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center}
.cc-card .cc-ico svg{width:22px;height:22px;stroke:var(--blue);fill:none;stroke-width:1.8}
.cc-card .cc-title{font-size:16px;font-weight:700;color:#fff;letter-spacing:-.2px}
.cc-card .cc-desc{font-size:13px;color:rgba(255,255,255,.62);line-height:1.65;margin-top:2px}

.section{padding:80px 0}
.section-inner{max-width:1200px;margin:0 auto;padding:0 24px}
.section-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;gap:20px;flex-wrap:wrap}
.section-head h2{font-size:32px;font-weight:900;letter-spacing:-.6px;color:#000;margin-bottom:4px;line-height:1.1}
.section-head p{font-size:14px;color:var(--text-3)}
.section-head-link{color:var(--blue);font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:5px}
.section-head-link svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
/* Home listings (Otomoto horizontal) */
/* Home listings — transparent container, cards self-contained */
.home-listings{background:transparent;border:none;border-radius:0;overflow:visible;box-shadow:none}
.home-lcard{display:flex;text-decoration:none;border-bottom:1px solid var(--border-l);transition:background .15s;position:relative;overflow:hidden}
.home-lcard:last-child{border-bottom:none}
.home-lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--blue);transform:scaleY(0);transform-origin:center;transition:transform .2s}
.home-lcard:hover{background:#fafafa}
.home-lcard:hover::before{transform:scaleY(1)}
.home-lcard-img{width:280px;min-width:280px;height:200px;position:relative;overflow:hidden;flex-shrink:0;background:var(--bg)}
.home-lcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.home-lcard:hover .home-lcard-img img{transform:scale(1.03)}
.home-lcard-img-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.home-lcard-img-placeholder svg{width:48px;height:48px;stroke:var(--text-4);stroke-width:1.2;fill:none}
.home-lcard-badge{position:absolute;top:10px;left:10px;background:var(--orange);color:#fff;font-size:10px;font-weight:800;padding:4px 10px;border-radius:6px;letter-spacing:.5px}
.home-lcard-certi{position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,.72);color:#fff;font-size:10px;font-weight:700;padding:4px 9px;border-radius:6px;display:flex;align-items:center;gap:4px;backdrop-filter:blur(4px)}
.home-lcard-certi svg{width:10px;height:10px;stroke:#4ea3ff;fill:none;stroke-width:2.5}
.home-lcard-fav{position:absolute;top:8px;right:8px;width:30px;height:30px;background:rgba(255,255,255,.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.1)}
.home-lcard-fav:hover{background:#fff;transform:scale(1.1)}
.home-lcard-fav svg{width:14px;height:14px;stroke:#bbb;fill:none;stroke-width:2;transition:all .2s}
.home-lcard-fav.active svg{stroke:var(--orange);fill:var(--orange)}
/* Each card: self-contained, separated, with gap */
.home-listings{display:flex;flex-direction:column;gap:12px}
.home-lcard{display:flex;text-decoration:none;border:1px solid var(--border-l);border-radius:12px;background:#fff;transition:all .18s;position:relative;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.home-lcard:hover{background:#fafafe;border-color:#c5c5cc;box-shadow:0 4px 20px rgba(0,0,0,.1);transform:translateY(-1px)}
.home-lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--orange);transform:scaleX(0);transform-origin:left;transition:transform .2s;border-radius:2px 0 0 2px}
.home-lcard:hover::before{transform:scaleX(1)}
.home-lcard-content{flex:1;padding:18px 22px;display:flex;gap:16px;min-width:0}
.home-lcard-info{flex:1;min-width:0;display:flex;flex-direction:column}
.home-lcard-title{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:4px;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.home-lcard-sub{font-size:12px;color:var(--text-3);margin-bottom:14px}
.home-lcard-specs{display:flex;flex-wrap:wrap;gap:4px 0;margin-bottom:16px}
.home-lcard-spec{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text-2);font-weight:500;padding-right:14px;margin-right:10px;border-right:1px solid var(--border-l)}
.home-lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
.home-lcard-spec svg{width:13px;height:13px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0}
.home-lcard-cta-wrap{margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--border-l)}
.home-lcard-loc{font-size:11px;color:var(--text-3);display:flex;align-items:center;gap:4px}
.home-lcard-loc svg{width:11px;height:11px;stroke:var(--text-3);fill:none;stroke-width:2}
.home-lcard-price-col{display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;min-width:170px;padding-top:2px}
.home-lcard-price{font-size:26px;font-weight:900;color:#000;letter-spacing:-.6px;line-height:1;white-space:nowrap}
.home-lcard-price-lbl{font-size:11px;color:var(--text-3);font-weight:500;margin-top:3px;margin-bottom:14px}
.home-lcard-btn{display:inline-flex;align-items:center;gap:7px;background:var(--blue);color:#fff;font-size:13px;font-weight:700;padding:11px 22px;border-radius:9px;text-decoration:none;transition:all .18s;white-space:nowrap;width:100%;justify-content:center}
.home-lcard-btn:hover{background:var(--blue-h);box-shadow:0 6px 18px rgba(0,102,255,.35);transform:translateY(-1px)}
.home-lcard-btn svg{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.4}
.home-listings-cta{margin-top:20px;text-align:center}
.home-listings-cta-btn{display:inline-flex;align-items:center;gap:9px;border:2px solid var(--blue);color:var(--blue);font-size:14px;font-weight:700;padding:13px 32px;border-radius:50px;text-decoration:none;transition:all .2s}
.home-listings-cta-btn:hover{background:var(--blue);color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.25)}
.home-listings-cta-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}
@media(max-width:900px){
    .home-lcard-img{width:200px;min-width:200px;height:170px}
    .home-lcard-price{font-size:22px}
    .home-lcard-price-col{min-width:140px}
}
@media(max-width:600px){
    .home-lcard{flex-direction:column}
    .home-lcard-img{width:100%;min-width:0;height:200px}
    .home-lcard-content{flex-direction:column}
    .home-lcard-price-col{flex-direction:row;align-items:center;min-width:0;width:100%}
    .home-lcard-price-col .home-lcard-btn{width:auto;margin-left:auto}
}

.body-types{background:var(--bg);border-top:1px solid var(--border-l);border-bottom:1px solid var(--border-l);padding:60px 0 56px}
.body-types-inner{max-width:1200px;margin:0 auto;padding:0 24px}
.body-types-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;gap:20px}
.body-types-head h2{font-size:32px;font-weight:900;color:#000;letter-spacing:-.6px;line-height:1.1;margin:0}
.body-types-head p{font-size:14px;color:var(--text-3);margin-top:6px}
.body-types-head-link{font-size:13px;font-weight:700;color:var(--blue);display:inline-flex;align-items:center;gap:5px;white-space:nowrap;text-decoration:none;flex-shrink:0}
.body-types-head-link svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
.body-types-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:8px}
.body-type-card{display:flex;flex-direction:column;align-items:center;gap:10px;padding:16px 8px 12px;border-radius:12px;background:transparent;border:none;cursor:pointer;transition:all .2s;text-decoration:none}
.body-type-card:hover{background:rgba(0,0,0,.03)}
.body-type-card:active{transform:scale(.96)}
.body-type-card .bt-icon{width:100%;display:flex;align-items:flex-end;justify-content:center;position:relative;padding-bottom:6px}
.body-type-card .bt-icon::before{content:'';position:absolute;bottom:0;left:50%;transform:translateX(-50%) scaleX(1);width:85%;height:10px;background:rgba(0,0,0,.06);border-radius:50%;filter:blur(4px);transition:all .2s;opacity:.7}
.body-type-card:hover .bt-icon::before{width:95%;opacity:1;filter:blur(5px)}
.body-type-card .bt-icon img{width:100%;height:auto;object-fit:contain;mix-blend-mode:multiply;transition:transform .2s;display:block}
.body-type-card:hover .bt-icon img{transform:translateY(-3px)}
.body-type-card .bt-icon img.flip{transform:scaleX(-1)}
.body-type-card:hover .bt-icon img.flip{transform:scaleX(-1) translateY(-3px)}
.body-type-card .bt-label{font-size:13px;font-weight:700;color:#444;letter-spacing:-.1px}

@media(max-width:1024px){
    .hero-text h1{font-size:56px}
    .hero-in{padding:80px 24px 70px;min-height:480px}
    .hero{min-height:480px}
}
@media(max-width:900px){
    .hero-wrap{padding:12px}
    .hero{border-radius:16px;min-height:420px}
    .hero::before{background-position:center}
    .hero::after{background:linear-gradient(180deg,rgba(10,10,10,.7) 0%,rgba(10,10,10,.85) 60%,#0a0a0a 100%)}
    .hero-in{padding:64px 24px 60px;min-height:420px}
    .hero-text{max-width:none;text-align:center;margin:0 auto}
    .hero-text h1{font-size:44px}
    .hero-text .lead{margin-left:auto;margin-right:auto}
    .hero-search-wrap{margin-top:-50px}
    .hero-search-fields{grid-template-columns:1fr 1fr}
    .body-types-grid{grid-template-columns:repeat(3,1fr)}
    .body-types{padding:28px 24px 24px}
    .feature-strip-in{grid-template-columns:1fr 1fr;gap:20px}
    .section{padding:56px 0}
    .section-head h2{font-size:24px}
}
@media(max-width:560px){
    .hero-wrap{padding:10px}
    .hero{border-radius:14px;min-height:380px}
    .hero-in{padding:48px 18px 50px;min-height:380px}
    .hero-text h1{font-size:36px;letter-spacing:-1.2px}
    .hero-text .lead{font-size:15px}
    .hero-search{padding:18px 18px 20px}
    .hero-search-head{flex-direction:column;align-items:flex-start;gap:10px}
    .hero-search-fields{grid-template-columns:1fr}
    .hero-search-field{border-right:none;border-bottom:1.5px solid var(--border-l)}
    .hero-search-field:last-child{border-bottom:none}
    .hero-search-bottom{flex-direction:column;gap:12px}
    .hero-search-submit{width:100%;justify-content:center}
    .body-types-grid{grid-template-columns:repeat(2,1fr)}
    .body-types{padding:22px 16px 20px;border-radius:14px}
    .body-types h3{font-size:16px}
    .feature-strip-in{grid-template-columns:1fr}
    .cat-cards{grid-template-columns:1fr}
}
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="hero-wrap">
    <section class="hero">
        <div class="hero-in">
            <div class="hero-text">
                <h1>
                    <span class="line1">Pewne auta.</span>
                    <span class="line2">Pełna historia.</span>
                </h1>
                <p class="lead">Każdy pojazd z certyfikatem inspekcji technicznej i pełną dokumentacją stanu.</p>
                <div class="hero-ctas">
                    <a href="<?php echo e(route('catalog')); ?>" class="btn btn-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Przeglądaj ofertę
                    </a>
                    <a href="#jak-dzialamy" class="hero-secondary-link">
                        Jak weryfikujemy?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="hero-search-wrap">
    <div class="container">
        <form class="hero-search" method="GET" action="<?php echo e(route('catalog')); ?>">

            <div class="hero-search-header">
                <div class="hero-search-title">
                    Znajdź sprawdzony samochód
                    <span class="hero-search-badge"><?php echo e($totalCars); ?> ofert</span>
                </div>
                <nav class="hero-search-nav">
                    <button type="button" class="active">Samochody</button>
                    <a href="<?php echo e(route('catalog')); ?>">Oferta</a>
                </nav>
            </div>

            <div class="hero-search-fields">
                <div class="hero-search-field">
                    <label>Marka</label>
                    <select name="brand">
                        <option value="">Wszystkie marki</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($b->id); ?>"><?php echo e($b->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="hero-search-field">
                    <label>Rok od</label>
                    <select name="year_min">
                        <option value="">Dowolny</option>
                        <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                        <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="hero-search-field">
                    <label>Cena do</label>
                    <input type="number" name="price_max" placeholder="50 000 zł" min="0">
                </div>
                <div class="hero-search-field">
                    <label>Przebieg do</label>
                    <input type="number" name="mileage_max" placeholder="150 000 km" min="0">
                </div>
            </div>

            <div class="hero-search-bottom">
                <a href="<?php echo e(route('catalog')); ?>" class="hero-search-reset">Wyczyść filtry</a>
                <button type="submit" class="hero-search-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Szukaj samochodów
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Body type selector -->
<div class="body-types">
    <div class="body-types-inner">
        <div class="body-types-head">
            <div>
                <h2>Szukaj po typie nadwozia</h2>
                <p>Wybierz rodzaj auta który Cię interesuje</p>
            </div>
            <a href="<?php echo e(route('catalog')); ?>" class="body-types-head-link">Pokaż wszystkie <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
        </div>
        <div class="body-types-grid">
            <a href="<?php echo e(route('catalog', ['category' => 'Sedan'])); ?>" class="body-type-card">
                <div class="bt-icon"><img src="/img/body-types/sedan.png" alt="" aria-hidden="true" loading="lazy"></div>
                <span class="bt-label">Sedan</span>
            </a>
            <a href="<?php echo e(route('catalog', ['category' => 'SUV'])); ?>" class="body-type-card">
                <div class="bt-icon"><img src="/img/body-types/suv.png" alt="" aria-hidden="true" loading="lazy"></div>
                <span class="bt-label">SUV</span>
            </a>
            <a href="<?php echo e(route('catalog', ['category' => 'Coupé'])); ?>" class="body-type-card">
                <div class="bt-icon"><img src="/img/body-types/coupe.png" alt="" aria-hidden="true" loading="lazy"></div>
                <span class="bt-label">Coupé</span>
            </a>
            <a href="<?php echo e(route('catalog', ['category' => 'Bus'])); ?>" class="body-type-card">
                <div class="bt-icon"><img src="/img/body-types/van.png" alt="" aria-hidden="true" loading="lazy"></div>
                <span class="bt-label">Bus</span>
            </a>
            <a href="<?php echo e(route('catalog', ['category' => 'Kombi'])); ?>" class="body-type-card">
                <div class="bt-icon"><img src="/img/body-types/kombi.png" alt="" aria-hidden="true" loading="lazy"></div>
                <span class="bt-label">Kombi</span>
            </a>
            <a href="<?php echo e(route('catalog', ['category' => 'Hatchback'])); ?>" class="body-type-card">
                <div class="bt-icon"><img src="/img/body-types/hatchback.png" alt="" aria-hidden="true" loading="lazy"></div>
                <span class="bt-label">Hatchback</span>
            </a>
        </div>
    </div>
</div>




<?php if($featuredCars->count()): ?>
<section class="body-types" style="border:none">
    <div class="body-types-inner">
        <div class="body-types-head">
            <div>
                <h2>Wyróżnione pojazdy</h2>
                <p>Starannie dobrane auta — sprawdzone, wyszukane, gotowe do odbioru</p>
            </div>
            <a href="<?php echo e(route('catalog')); ?>" class="body-types-head-link">Pełna oferta (<?php echo e($totalCars); ?>) <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
        </div>

        <div class="home-listings">
            <?php $__currentLoopData = $featuredCars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('catalog.show',$car)); ?>" class="home-lcard">
                
                <div class="home-lcard-img">
                    <?php if($car->primaryImage): ?>
                        <img src="<?php echo e($car->primaryImage->url); ?>" alt="<?php echo e($car->primaryImage->alt); ?>" loading="lazy">
                    <?php else: ?>
                        <div class="home-lcard-img-placeholder">
                            <svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        </div>
                    <?php endif; ?>
                    <div class="home-lcard-certi">
                        <svg viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                        CertiCheck
                    </div>
                    <?php if($car->is_featured): ?><div class="home-lcard-badge">Wyróżnione</div><?php endif; ?>
                    <button class="home-lcard-fav" data-id="<?php echo e($car->id); ?>" aria-label="Dodaj do ulubionych" onclick="toggleFav(event,<?php echo e($car->id); ?>)">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>

                
                <div class="home-lcard-content">
                    <div class="home-lcard-info">
                        <div class="home-lcard-title"><?php echo e($car->title); ?></div>
                        <div class="home-lcard-sub"><?php echo e(implode(' · ', array_filter([$car->category, $car->transmission])) ?: 'Certyfikowany pojazd używany'); ?></div>

                        <div class="home-lcard-specs">
                            <?php if($car->mileage): ?>
                            <div class="home-lcard-spec">
                                <svg viewBox="0 0 24 24"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                                <?php echo e(number_format($car->mileage, 0, '.', ' ')); ?> km
                            </div>
                            <?php endif; ?>
                            <?php if($car->fuel_type): ?>
                            <div class="home-lcard-spec">
                                <svg viewBox="0 0 24 24"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg>
                                <?php echo e($car->fuel_type); ?>

                            </div>
                            <?php endif; ?>
                            <?php if($car->power_hp): ?>
                            <div class="home-lcard-spec">
                                <svg viewBox="0 0 24 24"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                                <?php echo e($car->power_hp); ?> KM
                            </div>
                            <?php endif; ?>
                            <?php if($car->first_registration): ?>
                            <div class="home-lcard-spec">
                                <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                <?php echo e($car->first_registration); ?>

                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="home-lcard-cta-wrap">
                            <div class="home-lcard-loc">
                                <svg viewBox="0 0 24 24"><path d="M20 10c0 5-7 13-8 13S4 15 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                Warszawa · Salon CertiCars
                            </div>
                        </div>
                    </div>

                    
                    <div class="home-lcard-price-col">
                        <div>
                            <div class="home-lcard-price"><?php echo e($car->formatted_price); ?></div>
                            <div class="home-lcard-price-lbl"><?php echo e($car->price_type ?? 'brutto'); ?></div>
                        </div>
                        <span class="home-lcard-btn">
                            Sprawdź auto
                            <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="home-listings-cta">
            <a href="<?php echo e(route('catalog')); ?>" class="home-listings-cta-btn">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Zobacz wszystkie <?php echo e($totalCars); ?> oferty
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="certicheck-section" id="jak-dzialamy">
    <div class="container">
        <div class="certicheck-inner">
            <div class="certicheck-left">
                <p class="cc-label">CertiCheck — jak to działa</p>
                <h2>Inspekcja zanim wystawimy auto na sprzedaż</h2>
                <p>Cztery etapy kontroli dają Ci pełny obraz stanu pojazdu. Żadnych niespodzianek przy odbiorze.</p>
                <a href="<?php echo e(route('catalog')); ?>" class="certicheck-cta">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Przeglądaj ofertę
                </a>
            </div>
            <div class="certicheck-cards">
                <div class="cc-card">
                    <span class="cc-num">01</span>
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m14.622 17.897-10.68-2.913"/><path d="M18.376 2.622a1 1 0 1 1 3.002 3.002L17.36 9.643a.5.5 0 0 0 0 .707l.944.944a2.41 2.41 0 0 1 0 3.408l-.944.944a.5.5 0 0 1-.707 0L8.354 7.348a.5.5 0 0 1 0-.707l.944-.944a2.41 2.41 0 0 1 3.408 0l.944.944a.5.5 0 0 0 .707 0z"/><path d="M9 8c-1.804 2.71-3.97 3.46-6.583 3.948a.507.507 0 0 0-.302.819l7.32 8.883a1 1 0 0 0 1.185.204C12.735 20.405 16 16.792 16 15"/></svg></div>
                    <div class="cc-title">Pomiar lakieru</div>
                    <div class="cc-desc">Każdy element karoserii mierzony osobno. Wykrywamy reperacją i przemalowania.</div>
                </div>
                <div class="cc-card">
                    <span class="cc-num">02</span>
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg></div>
                    <div class="cc-title">Mapa uszkodzeń</div>
                    <div class="cc-desc">Interaktywna mapa z dokładną lokalizacją każdego uszkodzenia i dokumentacją foto.</div>
                </div>
                <div class="cc-card">
                    <span class="cc-num">03</span>
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
                    <div class="cc-title">Stan techniczny</div>
                    <div class="cc-desc">Inspekcja punkt po punkcie. Silnik, zawieszenie, układ hamulcowy i elektryka.</div>
                </div>
                <div class="cc-card">
                    <span class="cc-num">04</span>
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg></div>
                    <div class="cc-title">Broszura PDF</div>
                    <div class="cc-desc">Pełny raport CertiCheck do pobrania. Transparentna historia dla każdego kupującego.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/home.blade.php ENDPATH**/ ?>