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
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
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

/* ============ JAK WYGLĄDA ZAKUP — purchase process section ============
   Dark premium block. Five step-cards in one row on desktop, a benefits
   strip below, and a primary CTA at the bottom. Real component. */
.cs-jwz{position:relative;background:linear-gradient(180deg,#0a1a3c 0%,#11264f 100%);color:#fff;padding:60px 0;overflow:hidden;isolation:isolate}
.cs-jwz::before{content:'';position:absolute;top:-30%;right:-8%;width:55%;height:120%;background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(0,102,255,.28) 0%,rgba(0,102,255,.08) 40%,rgba(0,102,255,0) 70%);pointer-events:none;z-index:0}
.cs-jwz::after{content:'';position:absolute;bottom:-25%;left:-6%;width:50%;height:90%;background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(78,163,255,.18) 0%,rgba(78,163,255,.06) 45%,rgba(78,163,255,0) 70%);pointer-events:none;z-index:0}
.cs-jwz > .container{position:relative;z-index:1}
.cs-jwz-inner{max-width:1200px;margin:0 auto}
.cs-jwz-head{text-align:center;margin-bottom:36px}
.cs-jwz-kicker{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;color:#7eb3ff;text-transform:uppercase;letter-spacing:1.6px;margin-bottom:14px}
.cs-jwz-kicker::before,.cs-jwz-kicker::after{content:'';width:24px;height:1.5px;background:#7eb3ff;border-radius:1px}
.cs-jwz-head h2{font-size:34px;font-weight:900;color:#fff;letter-spacing:-.7px;line-height:1.12;margin:0 0 14px;max-width:720px;margin-left:auto;margin-right:auto}
.cs-jwz-head p{font-size:15px;color:rgba(255,255,255,.7);line-height:1.6;margin:0;max-width:620px;margin-left:auto;margin-right:auto}

.cs-jwz-steps{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:32px}
.cs-jwz-step{background:#fff;border-radius:14px;padding:22px 18px 20px;display:flex;flex-direction:column;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.18);transition:transform .18s ease,box-shadow .18s ease;position:relative}
.cs-jwz-step:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(0,0,0,.25)}
.cs-jwz-step-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
.cs-jwz-step-ico{flex-shrink:0;width:42px;height:42px;border-radius:11px;background:#eff6ff;border:1px solid #dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-jwz-step-ico svg,.cs-jwz-step-ico i[data-lucide]{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8}
.cs-jwz-step-num{font-size:13px;font-weight:800;color:#0066ff;background:#eff6ff;border-radius:6px;padding:3px 9px;letter-spacing:.4px}
.cs-jwz-step h3{font-size:15px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:6px 0 2px;line-height:1.25}
.cs-jwz-step p{font-size:12.5px;color:#4b5563;line-height:1.55;margin:0}

.cs-jwz-benefits{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:28px}
.cs-jwz-benefit{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(126,179,255,.18);border-radius:12px;padding:12px 16px;color:#fff;font-size:13px;font-weight:600}
.cs-jwz-benefit-ico{flex-shrink:0;width:30px;height:30px;border-radius:8px;background:rgba(78,163,255,.15);border:1px solid rgba(78,163,255,.3);color:#7eb3ff;display:flex;align-items:center;justify-content:center}
.cs-jwz-benefit-ico svg,.cs-jwz-benefit-ico i[data-lucide]{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2}

.cs-jwz-cta-wrap{text-align:center}
.cs-jwz-cta{display:inline-flex;align-items:center;gap:9px;background:#0066ff;color:#fff;padding:14px 32px;border-radius:50px;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s ease;box-shadow:0 6px 20px rgba(0,102,255,.4)}
.cs-jwz-cta:hover{background:#0052cc;box-shadow:0 8px 26px rgba(0,102,255,.5);transform:translateY(-1px);color:#fff}
.cs-jwz-cta svg,.cs-jwz-cta i[data-lucide]{width:17px;height:17px;stroke:currentColor;fill:none;stroke-width:2.2}

/* ============ CERTICHECK SECTION (homepage, redesigned) ============ */
/* Tło sekcji — jednolity, delikatny niebieski light. Bez agresywnego
   diagonalnego gradientu. Radial „spotlight" za bohaterem tworzy
   naturalną poświatę która płynnie łączy PNG z tłem sekcji (patrz
   .cs-cc::after). */
.cs-cc{position:relative;background:#eef2fa;padding:56px 0;overflow:hidden;isolation:isolate}
.cs-cc::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,#f4f6fc 0%,#eef2fa 40%,#e8edf7 100%);pointer-events:none;z-index:0}
/* Spotlight za bohaterem — duża soft radial która wypełnia się dokładnie
   za PNG bohatera po prawej. Kolory dopasowane do PNG (samples: outer
   #c1d1f7, inner #e0e4f9). Efekt „aureola" która wciąga PNG w sekcję. */
.cs-cc::after{content:'';position:absolute;top:50%;right:-4%;transform:translateY(-50%);width:52%;height:110%;background:radial-gradient(ellipse 55% 60% at 50% 50%,rgba(202,215,246,.6) 0%,rgba(202,215,246,.35) 35%,rgba(202,215,246,.12) 60%,rgba(202,215,246,0) 85%);pointer-events:none;z-index:0}
.cs-cc > .container{position:relative;z-index:1}
/* Grid 3-kolumnowy z template-areas. Info-box jest osobnym elementem
   (nie zagnieżdżony w .cs-cc-left) żeby na mobile mógł trafić między
   karty a hero. Desktop: info sittuje pod content w lewej kolumnie
   za pomocą grid-area assignment. */
.cs-cc-grid{max-width:1240px;margin:0 auto;display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr) minmax(0,1.15fr);grid-template-areas:"content cards hero" "info    cards hero";gap:16px 32px;align-items:center}
.cs-cc-left{grid-area:content}
.cs-cc-cards{grid-area:cards;align-self:center}
.cs-cc-hero{grid-area:hero;align-self:center}
.cs-cc-info{grid-area:info;align-self:start}
.cs-cc-hero{position:relative;display:flex;align-items:center;justify-content:center;overflow:visible}
/* MASK — tighter ellipse (45% × 62%) + agresywne fade steps sprawiają
   że TOP i BOTTOM PNG (najdłuższe strony bo portrait) wygasają dużo
   wcześniej niż sam prostokąt zdjęcia. Wcześniej 72% vertical zostawiało
   ostre górne/dolne krawędzie widoczne — teraz 62% + wcześniejszy start
   fade (od 30%) daje płynne wtopienie w tło. */
.cs-cc-hero img{height:600px;width:auto;max-width:100%;object-fit:contain;display:block;
    -webkit-mask-image:radial-gradient(ellipse 55% 65% at center,#000 35%,rgba(0,0,0,.92) 50%,rgba(0,0,0,.6) 65%,rgba(0,0,0,.25) 80%,transparent 96%);
    mask-image:radial-gradient(ellipse 55% 65% at center,#000 35%,rgba(0,0,0,.92) 50%,rgba(0,0,0,.6) 65%,rgba(0,0,0,.25) 80%,transparent 96%)}
.cs-cc-hero .cs-cc-hero-desktop{display:none}
.cs-cc-hero .cs-cc-hero-mobile{display:block}
.cs-cc-kicker{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;color:#0066ff;text-transform:uppercase;letter-spacing:1.6px;margin-bottom:12px}
.cs-cc-kicker::before{content:'';width:22px;height:1.5px;background:#0066ff;border-radius:1px}
.cs-cc-left h2{font-size:44px;font-weight:900;color:#0a0a0a;letter-spacing:-1px;line-height:1.05;margin:0 0 18px}
.cs-cc-left p{font-size:15px;color:#475569;line-height:1.65;margin:0 0 20px;max-width:420px}
.cs-cc-ctas{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px}
.cs-cc-cta-primary{display:inline-flex;align-items:center;gap:8px;background:#0066ff;color:#fff;padding:12px 22px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;transition:all .18s ease;box-shadow:0 4px 16px rgba(0,102,255,.3)}
.cs-cc-cta-primary:hover{background:#0052cc;box-shadow:0 6px 20px rgba(0,102,255,.42);transform:translateY(-1px);color:#fff}
.cs-cc-cta-secondary{display:inline-flex;align-items:center;gap:6px;color:#0066ff;padding:10px 4px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;border:1.5px solid transparent;transition:color .15s,border-color .15s}
.cs-cc-cta-secondary:hover{color:#0052cc;border-color:#dbeafe}
.cs-cc-ctas svg,.cs-cc-ctas i[data-lucide]{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}
.cs-cc-info{grid-area:info;align-self:start;display:flex;align-items:flex-start;gap:10px;background:rgba(255,255,255,.7);border:1px solid rgba(219,234,254,.7);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);border-radius:10px;padding:12px 14px;font-size:12.5px;color:#475569;line-height:1.55;max-width:520px}
.cs-cc-info-ico{flex-shrink:0;width:24px;height:24px;border-radius:6px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cs-cc-info-ico svg,.cs-cc-info-ico i[data-lucide]{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2}

/* 4 karty w środkowej kolumnie 2×2. Bez aspect-ratio bo tekst 3-4
   linii z tytułem NIE mieści się w małym kwadracie — min-height 200px
   daje kartom stałą wysokość, szerokość wynika z kolumny 1.2fr. */
.cs-cc-cards{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.cs-cc-card{position:relative;background:#fff;border:1px solid #eaf0fc;border-radius:18px;padding:22px 22px 24px;min-height:230px;display:flex;flex-direction:column;box-shadow:0 1px 3px rgba(0,0,0,.04),0 8px 24px rgba(15,32,80,.06);transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
.cs-cc-card:hover{transform:translateY(-2px);box-shadow:0 4px 8px rgba(0,0,0,.04),0 14px 36px rgba(15,32,80,.1);border-color:#cfdcf5}
.cs-cc-card-ico{width:48px;height:48px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center;margin-bottom:auto}
.cs-cc-card-ico svg,.cs-cc-card-ico i[data-lucide]{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.9}
.cs-cc-card h3{font-size:16px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:14px 0 6px;line-height:1.25}
.cs-cc-card p{font-size:13px;color:#4b5563;line-height:1.55;margin:0}
.cs-cc-card-arrow{position:absolute;top:22px;right:22px;width:32px;height:32px;border-radius:50%;background:#fff;border:1px solid #dbeafe;color:#0066ff;display:flex;align-items:center;justify-content:center;opacity:.65;transition:opacity .18s ease,transform .18s ease}
.cs-cc-card-arrow svg,.cs-cc-card-arrow i[data-lucide]{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.4}
.cs-cc-card:hover .cs-cc-card-arrow{opacity:1;transform:translateX(2px) translateY(-2px)}

@media(max-width:1024px){
    .cs-jwz{padding:48px 0}
    .cs-jwz-steps{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .cs-jwz-benefits{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
    .cs-jwz-head h2{font-size:28px}
    .cs-cc{padding:48px 0}
    /* Tablet 1024px: content full width top, potem karty | hero, info full
       width przed hero. Grid-areas explicit żeby info nie utknęło w
       lewej kolumnie. */
    .cs-cc-grid{grid-template-columns:1fr 1fr;grid-template-areas:"content content" "cards hero" "info info";gap:24px 32px}
    .cs-cc-hero img{height:auto;max-height:440px;width:auto;max-width:100%}
    .cs-cc-left h2{font-size:32px}
    .cs-cc-card{min-height:170px}
}
@media(max-width:768px){
    /* Mobile: pełny stack z grid-areas w kolejności:
       content → cards → info → hero (bohater ostatni).
       User feedback: „info idzie jako ostatni ten tekst, a potem bohater". */
    .cs-cc{padding:40px 0}
    .cs-cc-grid{grid-template-columns:1fr;grid-template-areas:"content" "cards" "info" "hero";gap:20px}
    .cs-cc-left h2{font-size:28px;margin-bottom:14px}
    .cs-cc-left p{font-size:14px;margin-bottom:20px}
    .cs-cc-ctas{margin-bottom:16px}
    .cs-cc-info{margin-bottom:0}
    .cs-cc-cards{grid-template-columns:1fr 1fr;gap:10px}
    .cs-cc-card{min-height:auto;padding:14px 13px 16px}
    .cs-cc-card-ico{width:34px;height:34px;margin-bottom:10px}
    .cs-cc-card-ico svg,.cs-cc-card-ico i[data-lucide]{width:16px;height:16px}
    .cs-cc-card h3{font-size:12.5px;margin:6px 0 4px}
    .cs-cc-card p{font-size:11px;line-height:1.45}
    .cs-cc-card-arrow{top:14px;right:14px;width:22px;height:22px}
    .cs-cc-card-arrow svg,.cs-cc-card-arrow i[data-lucide]{width:11px;height:11px}
    /* Bohater na dole — smaller, wycentrowany. Spotlight w tle sekcji
       przesunięty niżej żeby aureola była za PNG (nie w prawym górnym). */
    .cs-cc-hero{justify-content:center;padding:0 20px}
    .cs-cc-hero img{height:auto;width:100%;max-width:280px;max-height:none}
}
@media(max-width:768px){
    /* Spotlight tła — reposition dla mobile żeby aureola była pod bohaterem */
    .cs-cc::after{top:auto;bottom:-5%;right:50%;transform:translateX(50%);width:80%;height:40%}
}
@media(max-width:480px){
    /* Small mobile 480px: dalej 2×2 karty ale jeszcze tighter. Bohater
       najmniejszy — 220px żeby nie zajmował 60% viewportu. */
    .cs-cc-left h2{font-size:24px}
    .cs-cc-cards{gap:8px}
    .cs-cc-card{padding:12px 11px 14px}
    .cs-cc-hero img{max-width:220px}
}
@media(max-width:390px){
    /* Very small — karty stack 1-col żeby tekst był czytelny. */
    .cs-cc-cards{grid-template-columns:1fr}
    .cs-cc-hero img{max-width:200px}
}
@media(max-width:600px){
    .cs-jwz{padding:36px 0}
    .cs-jwz-steps{grid-template-columns:1fr;gap:12px}
    .cs-jwz-benefits{grid-template-columns:1fr;gap:8px}
    .cs-jwz-head{margin-bottom:26px}
    .cs-jwz-head h2{font-size:24px}
    .cs-jwz-head p{font-size:13.5px}
    .cs-jwz-cta{padding:12px 26px;font-size:14px}
    .cs-cc{padding:36px 0}
    .cs-cc-left h2{font-size:26px}
    .cs-cc-left p{font-size:14px}
    .cs-cc-cards{grid-template-columns:1fr}
    .cs-cc-ctas{flex-direction:column;align-items:stretch}
    .cs-cc-cta-primary,.cs-cc-cta-secondary{justify-content:center}
}

.featured-section{padding:60px 0 20px}
.featured-section .section-inner{max-width:1200px;margin:0 auto;padding:0 24px}
.featured-section .body-types-head{margin-bottom:18px;align-items:flex-end}
.featured-section .body-types-head h2{font-size:30px;font-weight:900;letter-spacing:-.6px;color:#0a0a0a;line-height:1.1;margin:0 0 4px}
.featured-section .body-types-head p{font-size:13.5px;color:#6b7280;line-height:1.5;margin:0;max-width:520px}
.featured-section .body-types-head-link{font-size:13px;font-weight:700;color:#0066ff;display:inline-flex;align-items:center;gap:4px;text-decoration:none;flex-shrink:0;white-space:nowrap}
.featured-section .body-types-head-link:hover{color:#0052cc}
.featured-section .body-types-head-link svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.4}
@media(max-width:720px){
    .featured-section{padding:40px 0 16px}
    .featured-section .body-types-head h2{font-size:24px}
    .featured-section .body-types-head{margin-bottom:14px}
}
.section{padding:80px 0}
.section-inner{max-width:1200px;margin:0 auto;padding:0 24px}
.section-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;gap:20px;flex-wrap:wrap}
.section-head h2{font-size:32px;font-weight:900;letter-spacing:-.6px;color:#000;margin-bottom:4px;line-height:1.1}
.section-head p{font-size:14px;color:var(--text-3)}
.section-head-link{color:var(--blue);font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:5px}
.section-head-link svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
/* .lcard / .home-listings / .home-listings-cta rules live in
   resources/views/layouts/public.blade.php as the single source of truth
   for the shared car-list-card component. */

.body-types{background:#fff;border:none;padding:32px 0 48px;margin-top:0}
.body-types-card{background:#fff;border-radius:22px;box-shadow:0 24px 64px rgba(0,0,0,.16),0 4px 16px rgba(0,0,0,.06);padding:32px 40px 36px;max-width:1200px;margin:0 auto}
.body-types-inner{max-width:1200px;margin:0 auto;padding:0}
.body-types-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;gap:20px}
.body-types-eyebrow{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:var(--blue);text-transform:uppercase;letter-spacing:1.4px;margin-bottom:8px}
.body-types-eyebrow::before{content:'';width:18px;height:1.5px;background:var(--blue);display:inline-block;border-radius:1px}
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
    /* CertiCheck + Jak wygląda zakup responsive rules live in the
       main rule block above (cs-cc / cs-jwz @media queries). */
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
    /* CertiCheck + Jak wygląda zakup mobile rules already covered by
       the cs-cc / cs-jwz @media(max-width:600px) block above. */
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
                <p class="lead">Znajdź samochód z jasnym opisem stanu, pochodzenia i wyposażenia. Wybrane auta opisujemy dodatkowo w ramach <a href="#certicheck">CertiCheck</a>.</p>
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
                    <div class="body-types-eyebrow">Szukaj samochodów</div>
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
                <p>Sprawdź aktualnie dostępne auta z naszej oferty.</p>
            </div>
            <a href="{{ route('catalog') }}" class="body-types-head-link">Pełna oferta ({{ $totalCars }}) <x-icon name="arrow-right" size="14"/></a>
        </div>

        <div class="home-listings">
            @foreach($featuredCars as $car)
                <x-car-list-card :car="$car"/>
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

<!-- ============ JAK WYGLĄDA ZAKUP — purchase process section ============ -->
<section class="cs-jwz" id="jak-wyglada-zakup">
    <div class="container">
    <div class="cs-jwz-inner">
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
                    ['wallet',       'Jasne koszty zakupu'],
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
    </div>
    </div>
</section>

<!-- ============ CERTICHECK SECTION (redesigned) ============ -->
<section class="cs-cc" id="certicheck">
    <div class="container">
    <div class="cs-cc-grid">
        <div class="cs-cc-left">
            <div class="cs-cc-kicker">CertiCheck — dla wybranych aut</div>
            <h2>Wiesz więcej<br>przed przyjazdem</h2>
            <p>Przy wybranych autach przygotowujemy rozszerzony opis CertiCheck z dodatkowymi informacjami o stanie pojazdu, lakierze, śladach użytkowania i dokumentach. Dzięki temu łatwiej ocenisz auto jeszcze przed wizytą.</p>
            <div class="cs-cc-ctas">
                <a class="cs-cc-cta-primary" href="{{ route('catalog') }}">
                    <x-icon name="search" size="15" :strokeWidth="2.2"/>
                    Zobacz auta z CertiCheck
                </a>
                <a class="cs-cc-cta-secondary" href="{{ route('certicheck.landing') }}">
                    Jak działa CertiCheck?
                    <x-icon name="arrow-right" size="14" :strokeWidth="2.2"/>
                </a>
            </div>
        </div>

        <div class="cs-cc-cards">
            @php
                $ccCards = [
                    ['scan-line',     'Pomiary lakieru',   'Wskazujemy pomiary i ewentualne różnice grubości powłoki w punktach kontrolnych.'],
                    ['wrench',        'Stan techniczny',   'Opisujemy widoczne elementy techniczne i podstawowe obserwacje z oględzin pojazdu.'],
                    ['search',        'Ślady użytkowania', 'Pokazujemy widoczne ślady eksploatacji i ich lokalizację.'],
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

        <div class="cs-cc-info">
            <span class="cs-cc-info-ico" aria-hidden="true"><x-icon name="shield-check" size="13" :strokeWidth="2"/></span>
            CertiCheck to wewnętrzny standard kontroli jakości CertiCars, a nie opinia rzeczoznawcy. Opis dotyczy stanu pojazdu na dzień oględzin i obejmuje elementy możliwe do oceny bez specjalistycznego demontażu podzespołów.
        </div>

        <div class="cs-cc-hero" aria-hidden="true">
            <img class="cs-cc-hero-desktop" src="/images/bohater-desktop.png" alt="" width="1672" height="941" loading="lazy" decoding="async">
            <img class="cs-cc-hero-mobile" src="/images/bohater-mobile.png" alt="" width="941" height="1672" loading="lazy" decoding="async">
        </div>
    </div>
    </div>
</section>

@endsection

