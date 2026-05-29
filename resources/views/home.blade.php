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
/* Home listings — same visual contract as catalog/index.blade.php.
   Outer .lcard is intentionally a <div>, NOT an <a>. The CertiCheck pill
   in the footer is an <a download>, and HTML5 forbids nested anchors —
   browsers auto-close the outer one and the card visually shatters
   (image floats above a detached title / price / pill block). The
   .lcard-link overlay below is the whole-card click target; .lcard-fav
   and .lcard-footer are bumped to z-index 2 so they keep their own
   click handlers. Keep this in lockstep with catalog/index.blade.php. */
/* Card hovers intentionally removed — see catalog/index.blade.php for the
   rationale. Keep this view in lockstep with the catalog card visuals. */
.home-listings{display:flex;flex-direction:column;gap:12px;background:transparent}
.lcard{position:relative;display:flex;align-items:stretch;background:#fff;border:1px solid var(--border-l);border-radius:12px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.lcard-link{position:absolute;inset:0;z-index:1;text-decoration:none;color:inherit;border-radius:inherit;cursor:pointer}
.lcard-link:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

.lcard-img{width:260px;min-width:260px;flex-shrink:0;align-self:stretch;min-height:190px;position:relative;overflow:hidden;background:linear-gradient(135deg,#eef4ff 0%,#e3ecfa 100%)}
.lcard-img img{width:100%;height:100%;object-fit:cover;display:block}
.lcard-img-placeholder{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef4ff 0%,#e3ecfa 100%)}
.lcard-img-placeholder svg{width:54px;height:54px;stroke:#94a3b8;stroke-width:1.4;fill:none;opacity:.55}
.lcard-badge-top{position:absolute;top:10px;left:10px;background:var(--orange);color:#fff;font-size:10px;font-weight:800;padding:4px 8px;border-radius:6px;letter-spacing:.5px;z-index:2}
.lcard-fav{position:absolute;top:8px;right:8px;width:32px;height:32px;background:rgba(255,255,255,.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.15);z-index:2}
.lcard-fav:hover{background:#fff;transform:scale(1.1)}
.lcard-fav svg{width:15px;height:15px;stroke:#bbb;fill:none;stroke-width:2;transition:stroke .2s}
.lcard-fav.active svg{stroke:var(--orange);fill:var(--orange)}
.lcard-photo-count{position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:600;padding:3px 7px;border-radius:5px;display:flex;align-items:center;gap:4px;z-index:2}
.lcard-photo-count svg{width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2}

.lcard-content{position:relative;flex:1;padding:18px 22px;display:flex;flex-direction:column;gap:12px;min-width:0}
.lcard-main{display:flex;gap:20px;align-items:flex-start;min-width:0}
.lcard-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:6px}
.lcard-title{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.3px;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0}
.lcard-subtitle{font-size:13px;color:var(--text-3);margin:0;line-height:1.3}
.lcard-specs{display:flex;flex-wrap:wrap;gap:6px 0;margin-top:6px}
.lcard-spec{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text-2);font-weight:500;padding-right:14px;margin-right:10px;border-right:1px solid var(--border-l);white-space:nowrap}
.lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
.lcard-spec svg{width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0}

.lcard-price-col{flex-shrink:0;min-width:140px;text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:4px}
.lcard-price{font-size:24px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1;white-space:nowrap}
.lcard-price-label{font-size:11px;color:var(--text-3);font-weight:500}

.lcard-footer{margin-top:auto;display:flex;align-items:center;gap:10px;flex-wrap:wrap;min-height:32px;position:relative;z-index:2}
.home-listings-cta{margin-top:20px;padding-bottom:40px;text-align:center}
.home-listings-cta-btn{display:inline-flex;align-items:center;gap:9px;border:2px solid var(--blue);color:var(--blue);font-size:14px;font-weight:700;padding:13px 32px;border-radius:50px;text-decoration:none;transition:all .2s}
.home-listings-cta-btn:hover{background:var(--blue);color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.25)}
.home-listings-cta-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}


.body-types{background:#fff;border:none;padding:32px 0 48px;margin-top:0}
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
.body-type-card .bt-icon{width:100%;height:96px;display:flex;align-items:flex-end;justify-content:center;position:relative;padding-bottom:8px}
.body-type-card .bt-icon::before{content:'';position:absolute;bottom:0;left:50%;transform:translateX(-50%) scaleX(1);width:85%;height:10px;background:rgba(0,0,0,.06);border-radius:50%;filter:blur(4px);transition:all .2s;opacity:.7}
.body-type-card:hover .bt-icon::before{width:95%;opacity:1;filter:blur(5px)}
.body-type-card .bt-icon img{max-width:100%;max-height:84px;width:auto;height:auto;object-fit:contain;mix-blend-mode:multiply;transition:transform .2s;display:block}
.body-type-card:hover .bt-icon img{transform:translateY(-3px)}
.body-type-card .bt-icon img.flip{transform:scaleX(-1)}
.body-type-card:hover .bt-icon img.flip{transform:scaleX(-1) translateY(-3px)}
.body-type-card .bt-label{font-size:14.5px;font-weight:700;color:#1a1a1a;letter-spacing:-.15px}
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
    .body-type-card .bt-icon{height:76px}
    .body-type-card .bt-icon img{max-height:66px}
}
@media(max-width:600px){
    .lcard{flex-direction:column;align-items:stretch}
    .lcard-img{width:100%;min-width:0;height:200px;min-height:200px}
    .lcard-content{padding:16px 18px;gap:10px}
    .lcard-main{flex-direction:column;align-items:stretch;gap:10px}
    .lcard-info{gap:4px}
    .lcard-title{font-size:17px}
    .lcard-subtitle{font-size:12px}
    .lcard-specs{gap:2px 0;margin-top:4px}
    .lcard-spec{font-size:12px;padding-right:10px;margin-right:8px}
    .lcard-price-col{min-width:0;text-align:left;flex-direction:row;align-items:baseline;gap:8px}
    .lcard-price{font-size:24px}
    .lcard-footer{margin-top:4px}
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
    .body-type-card .bt-icon{height:62px}
    .body-type-card .bt-icon img{max-height:54px}
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
                        <x-icon name="search" size="16" :strokeWidth="2.2"/>
                        Przeglądaj ofertę
                    </a>
                    <a href="#certicheck" class="hero-secondary-link">
                        Jak sprawdzamy auta?
                        <x-icon name="arrow-right" size="16"/>
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
                    <x-icon name="search" size="18"/>
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
                <a href="{{ route('catalog') }}" class="body-types-head-link">Pokaż wszystkie <x-icon name="arrow-right" size="14"/></a>
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
            <a href="{{ route('catalog') }}" class="body-types-head-link">Pełna oferta ({{ $totalCars }}) <x-icon name="arrow-right" size="14"/></a>
        </div>

        <div class="home-listings">
            @foreach($featuredCars as $car)
            {{-- Outer wrapper is a <div>, NOT an <a>: the CertiCheck footer pill is
                 itself an <a> and nested anchors are illegal HTML. The .lcard-link
                 overlay below is the whole-card click target. --}}
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
                            <x-icon name="car" size="40"/>
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
                     footer at the bottom. Same structure as catalog/index.blade.php. --}}
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
            @endforeach
        </div>

        <div class="home-listings-cta">
            <a href="{{ route('catalog') }}" class="home-listings-cta-btn">
                <x-icon name="search" size="16"/>
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
                        <x-icon name="search" size="16"/>
                        Zobacz auta z CertiCheck
                    </a>
                    <a href="{{ route('about') }}" class="certicheck-cta-ghost">
                        Jak działa CertiCheck?
                        <x-icon name="arrow-right" size="14"/>
                    </a>
                </div>
                <div class="cc-foot">
                    <x-icon name="shield-check" size="14"/>
                    CertiCheck dotyczy wybranych pojazdów w naszej ofercie.
                </div>
            </div>
            <div class="certicheck-cards">
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><x-icon name="paintbrush" size="22"/></div>
                    <div class="cc-title">Pomiary lakieru</div>
                    <div class="cc-desc">Wykryjemy ponowne malowania i grubsze powłoki w punktach kontrolnych.</div>
                    <div class="cc-arrow"><x-icon name="chevron-right" size="16"/></div>
                </a>
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><x-icon name="wrench" size="22"/></div>
                    <div class="cc-title">Stan techniczny</div>
                    <div class="cc-desc">Sprawdzamy kluczowe elementy mechaniczne i eksploatacyjne pojazdu.</div>
                    <div class="cc-arrow"><x-icon name="chevron-right" size="16"/></div>
                </a>
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><x-icon name="search" size="22"/></div>
                    <div class="cc-title">Ślady użytkowania</div>
                    <div class="cc-desc">Wskazujemy widoczne ślady eksploatacji i ich dokładną lokalizację.</div>
                    <div class="cc-arrow"><x-icon name="chevron-right" size="16"/></div>
                </a>
                <a href="{{ route('catalog') }}" class="cc-card">
                    <div class="cc-ico"><x-icon name="file-text" size="22"/></div>
                    <div class="cc-title">Raport PDF</div>
                    <div class="cc-desc">Czytelne podsumowanie ze zdjęciami i danymi do pobrania.</div>
                    <div class="cc-arrow"><x-icon name="chevron-right" size="16"/></div>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

