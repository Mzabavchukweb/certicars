@extends('layouts.public')
@section('title','Certyfikowane samochody używane')
@section('description','CertiCars — komis premium z pełną inspekcją techniczną. '.$totalCars.' certyfikowanych pojazdów w ofercie.')

@section('extra_head')
    <link rel="preload" as="image" href="/img/hero.png" fetchpriority="high">
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'CertiCars',
        'url' => url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/samochody').'?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endsection

@section('styles')
/* HERO — full-bleed illustration background, text overlay on left */
.hero-wrap{background:#0a1838;padding:0}
.hero{position:relative;color:#fff;overflow:hidden;margin-bottom:0;min-height:620px;background:#0a1838}
.hero::before{content:'';position:absolute;inset:0;background-image:url('/img/hero.png');background-size:cover;background-position:center right;background-repeat:no-repeat;z-index:1}
.hero::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#0a1838 0%,rgba(10,24,56,.92) 28%,rgba(10,24,56,.55) 48%,rgba(10,24,56,.1) 68%,rgba(10,24,56,0) 100%);z-index:2}
.hero-in{position:relative;z-index:3;padding:100px 24px 120px;max-width:1200px;margin:0 auto;min-height:620px;display:flex;flex-direction:column;justify-content:center}
.hero-text{max-width:560px;position:relative;z-index:2}
.hero-text h1{font-size:64px;font-weight:900;line-height:1;letter-spacing:-2px;margin-bottom:24px}
.hero-text h1 .line1{color:#fff;display:block}
.hero-text h1 .line2{color:var(--blue);display:block}
.hero-text .lead{font-size:16px;color:rgba(255,255,255,.78);max-width:460px;margin-bottom:36px;line-height:1.65;font-weight:400}
.hero-text .lead a{color:#4ea3ff;font-weight:600;text-decoration:none;border-bottom:1px dotted rgba(78,163,255,.5)}
.hero-text .lead a:hover{border-bottom-color:#4ea3ff}
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
.hero-search-wrap{position:relative;margin-top:-90px;z-index:4;padding-bottom:0}
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
.certicheck-section{background:#0a1838;padding:88px 0;position:relative;overflow:hidden}
.certicheck-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 15% 50%,rgba(30,58,138,.5) 0%,rgba(10,24,56,0) 60%);pointer-events:none}
.certicheck-inner{display:grid;grid-template-columns:1fr 1.15fr;gap:80px;align-items:center;position:relative;z-index:2}
.certicheck-left .cc-label{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#7eb3ff;margin-bottom:24px}
.certicheck-left .cc-label::before{content:'';width:18px;height:1.5px;background:#7eb3ff;display:inline-block}
.certicheck-left h2{font-size:44px;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1.05;margin-bottom:22px}
.certicheck-left p.cc-desc-main{font-size:15px;color:rgba(255,255,255,.7);line-height:1.7;margin-bottom:32px;max-width:480px}
.certicheck-ctas{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:28px}
.certicheck-cta{display:inline-flex;align-items:center;gap:9px;background:var(--blue);color:#fff;padding:14px 26px;border-radius:50px;font-weight:700;font-size:14px;text-decoration:none;transition:all .2s;border:1px solid var(--blue)}
.certicheck-cta:hover{background:var(--blue-h);border-color:var(--blue-h);color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.4);transform:translateY(-1px)}
.certicheck-cta svg{width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2.4}
.certicheck-cta-ghost{display:inline-flex;align-items:center;gap:9px;background:transparent;color:#fff;padding:14px 24px;border-radius:50px;font-weight:600;font-size:14px;text-decoration:none;transition:all .2s;border:1px solid rgba(255,255,255,.2)}
.certicheck-cta-ghost:hover{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.35);color:#fff;transform:translateY(-1px)}
.certicheck-cta-ghost svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4;transition:transform .2s}
.certicheck-cta-ghost:hover svg{transform:translateX(3px)}
.cc-foot{display:inline-flex;align-items:center;gap:9px;font-size:13px;color:rgba(255,255,255,.55);font-weight:500}
.cc-foot svg{width:16px;height:16px;stroke:#4ea3ff;fill:none;stroke-width:2.2;flex-shrink:0}
.certicheck-cards{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.cc-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);padding:26px 24px;display:flex;flex-direction:column;gap:12px;transition:all .2s;border-radius:16px;position:relative;text-decoration:none;color:inherit}
.cc-card:hover{background:rgba(255,255,255,.06);border-color:rgba(126,179,255,.35);transform:translateY(-2px)}
.cc-card .cc-ico{width:44px;height:44px;background:rgba(78,163,255,.12);border:1px solid rgba(78,163,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center}
.cc-card .cc-ico svg{width:20px;height:20px;stroke:#7eb3ff;fill:none;stroke-width:1.8}
.cc-card .cc-title{font-size:17px;font-weight:700;color:#fff;letter-spacing:-.3px;margin-top:4px}
.cc-card .cc-desc{font-size:13.5px;color:rgba(255,255,255,.6);line-height:1.6;margin-top:-2px}
.cc-card .cc-arrow{position:absolute;bottom:22px;right:22px;width:22px;height:22px;display:flex;align-items:center;justify-content:center;opacity:.4;transition:opacity .2s,transform .2s}
.cc-card:hover .cc-arrow{opacity:1;transform:translateX(3px)}
.cc-card .cc-arrow svg{width:18px;height:18px;stroke:rgba(255,255,255,.7);fill:none;stroke-width:2}

.featured-section{padding:80px 0 0}
.section{padding:80px 0}
.section-inner{max-width:1200px;margin:0 auto;padding:0 24px}
.section-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;gap:20px;flex-wrap:wrap}
.section-head h2{font-size:32px;font-weight:900;letter-spacing:-.6px;color:#000;margin-bottom:4px;line-height:1.1}
.section-head p{font-size:14px;color:var(--text-3)}
.section-head-link{color:var(--blue);font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:5px}
.section-head-link svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
/* Home listings — reuse lcard from catalog */
.home-listings{display:flex;flex-direction:column;gap:12px;background:transparent}
.lcard{display:flex;background:#fff;border:1px solid var(--border-l);border-radius:12px;text-decoration:none;transition:all .18s;position:relative;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--blue);transform:scaleX(0);transform-origin:left;transition:transform .2s;border-radius:2px 0 0 2px}
.lcard:hover{background:#fafafe;border-color:#c5c5cc;box-shadow:0 4px 20px rgba(0,0,0,.1);transform:translateY(-1px)}
.lcard:hover::before{transform:scaleX(1)}
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
.lcard-photo-count{position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:600;padding:3px 7px;border-radius:5px;display:flex;align-items:center;gap:4px}
.lcard-photo-count svg{width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2}
.lcard-content{flex:1;padding:16px 20px;display:flex;gap:16px;min-width:0}
.lcard-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:0}
.lcard-title{font-size:17px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:6px;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lcard-subtitle{font-size:12px;color:var(--text-3);margin-bottom:12px}
.lcard-specs{display:flex;flex-wrap:wrap;gap:4px 0;margin-bottom:14px}
.lcard-spec{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text-2);font-weight:500;padding-right:14px;margin-right:10px;border-right:1px solid var(--border-l)}
.lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
.lcard-spec svg{width:13px;height:13px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0}
.lcard-meta{margin-top:auto;padding-top:12px;border-top:1px solid var(--border-l);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.lcard-price-col{display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;min-width:160px;padding-top:2px}
.lcard-price{font-size:24px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1;white-space:nowrap}
.lcard-price-label{font-size:11px;color:var(--text-3);font-weight:500;margin-top:3px}
.cc-badge{display:inline-flex;align-items:stretch;border-radius:8px;overflow:hidden;cursor:pointer;transition:all .18s;box-shadow:0 1px 4px rgba(0,0,0,.1);text-decoration:none}
.cc-badge:hover{box-shadow:0 3px 12px rgba(0,0,0,.18);transform:translateY(-1px)}
.cc-badge-icon{display:flex;align-items:center;justify-content:center;background:rgba(0,102,255,.1);padding:6px 10px}
.cc-badge-icon svg{width:18px;height:18px}
.cc-badge-text{display:flex;align-items:center;background:#1a1a1a;color:#fff;font-size:13px;font-weight:800;letter-spacing:-.2px;padding:6px 12px 6px 10px;white-space:nowrap}
.cc-badge-text em{font-style:normal;color:var(--blue)}
.home-listings-cta{margin-top:20px;text-align:center}
.home-listings-cta-btn{display:inline-flex;align-items:center;gap:9px;border:2px solid var(--blue);color:var(--blue);font-size:14px;font-weight:700;padding:13px 32px;border-radius:50px;text-decoration:none;transition:all .2s}
.home-listings-cta-btn:hover{background:var(--blue);color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.25)}
.home-listings-cta-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}


.body-types{background:#fff;border:none;padding:0;margin-top:16px}
.body-types-card{background:#fff;border-radius:22px;box-shadow:0 24px 64px rgba(0,0,0,.16),0 4px 16px rgba(0,0,0,.06);padding:32px 40px 36px;max-width:1200px;margin:0 auto}
.body-types-inner{max-width:1200px;margin:0 auto;padding:0}
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
.body-type-card .bt-count{font-size:11.5px;font-weight:600;color:var(--blue);letter-spacing:.1px;margin-top:-4px}

@media(max-width:1024px){
    .hero-text h1{font-size:56px}
    .hero-in{padding:80px 24px 100px;min-height:520px}
    .hero{min-height:520px}
    .hero::before{background-position:right center;background-size:auto 100%}
    .hero::after{background:linear-gradient(90deg,#0a1838 0%,rgba(10,24,56,.95) 35%,rgba(10,24,56,.6) 55%,rgba(10,24,56,.15) 75%,rgba(10,24,56,0) 100%)}
}
@media(max-width:900px){
    .hero-wrap{padding:0}
    .hero{border-radius:0;min-height:560px}
    .hero::before{background-position:center top;background-size:cover;opacity:.55}
    .hero::after{background:linear-gradient(180deg,rgba(10,24,56,.4) 0%,rgba(10,24,56,.85) 55%,#0a1838 100%)}
    .hero-in{padding:64px 24px 80px;min-height:560px;text-align:center}
    .hero-text{max-width:none;margin:0 auto}
    .hero-text h1{font-size:44px}
    .hero-text .lead{margin-left:auto;margin-right:auto}
    .hero-text .hero-ctas{justify-content:center}
    .hero-trust{justify-content:center;gap:24px}
    .hero-search-wrap{margin-top:-50px}
    .hero-search-header{flex-direction:column;align-items:flex-start;gap:12px}
    .hero-search-fields{grid-template-columns:1fr 1fr}
    .body-types-grid{grid-template-columns:repeat(3,1fr)}
    .body-types-card{padding:24px 24px 20px;border-radius:16px}
    .body-types-head{flex-direction:column;align-items:flex-start;gap:8px}
    .lcard-img{width:180px;min-width:180px;height:160px}
    .lcard-price{font-size:20px}
    .lcard-price-col{min-width:130px}
    /* CertiCheck section — tighter on tablet */
    .certicheck-section{padding:56px 0}
    .certicheck-inner{grid-template-columns:1fr;gap:40px;text-align:center}
    .certicheck-left .cc-label{justify-content:center}
    .certicheck-left h2{font-size:32px}
    .certicheck-left p.cc-desc-main{max-width:520px;margin-left:auto;margin-right:auto}
    .certicheck-ctas{justify-content:center}
    .certicheck-cards{max-width:640px;margin:0 auto;text-align:left}
    .cc-card{padding:22px 22px}
    .cc-card .cc-title{font-size:15px}
    .cc-card .cc-desc{font-size:13px;line-height:1.6}
    .feature-strip-in{grid-template-columns:1fr 1fr;gap:20px}
    .section{padding:56px 0}
    .section-head h2{font-size:24px}
}
@media(max-width:600px){
    .lcard{flex-direction:column}
    .lcard-img{width:100%;min-width:0;height:200px}
    .lcard-content{flex-direction:column;padding:14px 16px;gap:10px}
    .lcard-info{gap:0}
    .lcard-title{font-size:16px;margin-bottom:4px}
    .lcard-subtitle{font-size:11px;margin-bottom:8px}
    .lcard-specs{gap:2px 0;margin-bottom:10px}
    .lcard-spec{font-size:12px;padding-right:10px;margin-right:8px}
    /* Price — BIGGER and bolder on mobile */
    .lcard-price-col{flex-direction:row;align-items:center;justify-content:space-between;min-width:0;width:100%;padding-top:10px;border-top:1px solid var(--border-l);margin-top:4px}
    .lcard-price{font-size:26px;font-weight:900;letter-spacing:-.6px}
    .lcard-price-label{font-size:10px}
    /* CertiCheck badge — compact on mobile */
    .cc-badge{transform:scale(.88);transform-origin:left center}
    .lcard-meta{padding-top:8px;gap:6px}
}
@media(max-width:560px){
    .hero-wrap{padding:0}
    .hero{border-radius:0;min-height:400px}
    .hero-in{padding:48px 18px 70px;min-height:400px}
    .hero-text h1{font-size:36px;letter-spacing:-1.2px}
    .hero-text .lead{font-size:15px}
    .hero-text .hero-ctas{flex-direction:column;align-items:stretch;gap:12px}
    .hero-text .btn{justify-content:center;padding:14px 24px;font-size:13px}
    .hero-secondary-link{justify-content:center}
    .hero-search{padding:16px 16px 18px}
    .hero-search-title{font-size:16px}
    .hero-search-badge{font-size:10px;padding:2px 8px}
    .hero-search-fields{grid-template-columns:1fr}
    .hero-search-field{border-right:none;border-bottom:1.5px solid var(--border-l);padding:12px 16px}
    .hero-search-field:last-child{border-bottom:none}
    .hero-search-field label{font-size:9px;letter-spacing:.5px;margin-bottom:3px}
    .hero-search-field select,.hero-search-field input{font-size:13px}
    .hero-search-bottom{flex-direction:column;gap:10px}
    .hero-search-submit{width:100%;justify-content:center;height:44px;font-size:13px}
    .hero-search-reset{font-size:12px}
    .body-types-grid{grid-template-columns:repeat(2,1fr);gap:6px}
    .body-types-card{padding:18px 16px 14px;border-radius:14px}
    .body-types h3{font-size:16px}
    .body-types-head h2{font-size:24px}
    .body-types-head p{font-size:13px}
    /* CertiCheck section — COMPACT on mobile */
    .certicheck-section{padding:48px 0}
    .certicheck-inner{gap:28px}
    .certicheck-left .cc-label{font-size:10.5px;letter-spacing:1.5px;margin-bottom:14px}
    .certicheck-left h2{font-size:26px;margin-bottom:14px}
    .certicheck-left p.cc-desc-main{font-size:14px;line-height:1.6;margin-bottom:24px}
    .certicheck-ctas{gap:10px}
    .certicheck-cta,.certicheck-cta-ghost{padding:12px 22px;font-size:13px}
    .cc-foot{font-size:12px}
    .certicheck-cards{grid-template-columns:1fr;gap:10px}
    .cc-card{padding:20px 20px;gap:10px;border-radius:14px}
    .cc-card .cc-ico{width:38px;height:38px}
    .cc-card .cc-ico svg{width:18px;height:18px}
    .cc-card .cc-title{font-size:15px}
    .cc-card .cc-desc{font-size:13px;line-height:1.55}
    .cc-card .cc-arrow{bottom:18px;right:18px;width:18px;height:18px}
    .cc-card .cc-arrow svg{width:16px;height:16px}
    .feature-strip-in{grid-template-columns:1fr}
    .cat-cards{grid-template-columns:1fr}
    /* Price — even MORE prominent on small mobile */
    .lcard-price{font-size:28px;letter-spacing:-.8px}
    .lcard-img{height:180px}
    /* Sections tighter */
    .section{padding:40px 0}
    .section-head h2{font-size:22px}
    .section-head p{font-size:13px}
    .home-listings{gap:10px}
    .home-listings-cta{margin-top:14px}
    .home-listings-cta-btn{padding:11px 24px;font-size:13px}
}
@endsection

@section('content')
<div class="hero-wrap">
    <section class="hero">
        <div class="hero-in">
            <div class="hero-text">
                <h1>
                    <span class="line1">Pewne auta.</span>
                    <span class="line2">Przejrzyste opisy.</span>
                </h1>
                <p class="lead">Znajdź samochód z jasnym opisem stanu, wyposażenia i pochodzenia. Wybrane auta sprawdzamy dodatkowo w ramach <a href="#certicheck">CertiCheck</a>.</p>
                <div class="hero-ctas">
                    <a href="{{ route('catalog') }}" class="btn btn-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Przeglądaj ofertę
                    </a>
                    <a href="#certicheck" class="hero-secondary-link">
                        Jak sprawdzamy auta?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="hero-search-wrap">
    <div class="container">
        <form class="hero-search" method="GET" action="{{ route('catalog') }}">

            <div class="hero-search-header">
                <div class="hero-search-title">
                    Znajdź sprawdzony samochód
                    <span class="hero-search-badge">{{ $totalCars }} ofert</span>
                </div>

            </div>

            <div class="hero-search-fields">
                <div class="hero-search-field">
                    <label>Marka</label>
                    <select name="brand">
                        <option value="">Wszystkie marki</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="hero-search-field">
                    <label>Rok od</label>
                    <select name="year_min">
                        <option value="">Dowolny</option>
                        @for($y = date('Y'); $y >= 2000; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
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
                <a href="{{ route('catalog') }}" class="hero-search-reset">Wyczyść filtry</a>
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
    <div class="container">
        <div class="body-types-card">
            <div class="body-types-head">
                <div>
                    <h2>Przeglądaj auta według nadwozia</h2>
                    <p>Wybierz typ samochodu, który najlepiej pasuje do Twoich potrzeb</p>
                </div>
                <a href="{{ route('catalog') }}" class="body-types-head-link">Pokaż wszystkie <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
            </div>
            @php
                $plCars = function ($n) {
                    $n = (int) $n;
                    if ($n === 1) return 'samochód';
                    $mod10 = $n % 10;
                    $mod100 = $n % 100;
                    if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14)) return 'samochody';
                    return 'samochodów';
                };
                $bodyTypes = [
                    ['Sedan',     'sedan.png'],
                    ['SUV',       'suv.png'],
                    ['Coupé',     'coupe.png'],
                    ['Bus',       'van.png'],
                    ['Kombi',     'kombi.png'],
                    ['Hatchback', 'hatchback.png'],
                ];
            @endphp
            <div class="body-types-grid">
                @foreach($bodyTypes as [$cat, $img])
                    @php $count = (int) ($bodyTypeCounts[$cat] ?? 0); @endphp
                    <a href="{{ route('catalog', ['category' => $cat]) }}" class="body-type-card">
                        <div class="bt-icon"><img src="/img/body-types/{{ $img }}" alt="" aria-hidden="true" loading="lazy"></div>
                        <span class="bt-label">{{ $cat }}</span>
                        <span class="bt-count">{{ $count }} {{ $plCars($count) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>




@if($featuredCars->count())
<section class="featured-section">
    <div class="section-inner">
        <div class="body-types-head">
            <div>
                <h2>Wyróżnione pojazdy</h2>
                <p>Starannie dobrane auta — sprawdzone, wyszukane, gotowe do odbioru</p>
            </div>
            <a href="{{ route('catalog') }}" class="body-types-head-link">Pełna oferta ({{ $totalCars }}) <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
        </div>

        <div class="home-listings">
            @foreach($featuredCars as $car)
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

                        @if($car->has_certicheck)
                        <div class="lcard-meta">
                            <span class="cc-badge" onclick="event.preventDefault();event.stopPropagation();var a=document.createElement('a');a.href='/samochody/{{ $car->slug }}/pdf';a.download='';document.body.appendChild(a);a.click();a.remove()">
                                <span class="cc-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#0066ff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg>
                                </span>
                                <span class="cc-badge-text"><em>Certi</em>Check</span>
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="lcard-price-col">
                        <div>
                            <div class="lcard-price">{{ $car->formatted_price }}</div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="home-listings-cta">
            <a href="{{ route('catalog') }}" class="home-listings-cta-btn">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Zobacz wszystkie {{ $totalCars }} oferty
            </a>
        </div>
    </div>
</section>
@endif

<section class="certicheck-section" id="certicheck">
    <div class="container">
        <div class="certicheck-inner">
            <div class="certicheck-left">
                <p class="cc-label">CertiCheck — dla wybranych aut</p>
                <h2>Wiesz więcej<br>przed przyjazdem</h2>
                <p class="cc-desc-main">Przy wybranych autach przygotowujemy rozszerzony opis CertiCheck z dodatkowymi informacjami o stanie pojazdu, lakierze, śladach użytkowania i dokumentach. Dzięki temu łatwiej oceniasz auto jeszcze przed wizytą.</p>
                <div class="certicheck-ctas">
                    <a href="{{ route('catalog') }}" class="certicheck-cta">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Zobacz auta z CertiCheck
                    </a>
                    <a href="{{ route('about') }}" class="certicheck-cta-ghost">
                        Jak działa CertiCheck?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="cc-foot">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    CertiCheck dotyczy wybranych pojazdów w naszej ofercie.
                </div>
            </div>
            <div class="certicheck-cards">
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 11a3 3 0 1 0 0 6v0a3 3 0 0 0 0-6"/><path d="M12 2v2"/><path d="M5 5l2 2"/><path d="M2 12h2"/><path d="M17 7l2-2"/><path d="M22 12h-2"/><path d="M19 19l-2-2"/><path d="M12 22v-2"/></svg></div>
                    <div class="cc-title">Pomiary lakieru</div>
                    <div class="cc-desc">Wykryjemy ponowne malowania i grubsze powłoki w punktach kontrolnych.</div>
                    <div class="cc-arrow"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></div>
                </a>
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
                    <div class="cc-title">Stan techniczny</div>
                    <div class="cc-desc">Sprawdzamy kluczowe elementy mechaniczne i eksploatacyjne pojazdu.</div>
                    <div class="cc-arrow"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></div>
                </a>
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12c1.5-1 2.5-1 4 0s2.5 1 4 0"/><circle cx="9" cy="9" r=".5" fill="currentColor"/><circle cx="15" cy="9" r=".5" fill="currentColor"/></svg></div>
                    <div class="cc-title">Ślady użytkowania</div>
                    <div class="cc-desc">Wskazujemy widoczne ślady eksploatacji i ich dokładną lokalizację.</div>
                    <div class="cc-arrow"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></div>
                </a>
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg></div>
                    <div class="cc-title">Raport PDF</div>
                    <div class="cc-desc">Czytelne podsumowanie ze zdjęciami i danymi do pobrania.</div>
                    <div class="cc-arrow"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></div>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

