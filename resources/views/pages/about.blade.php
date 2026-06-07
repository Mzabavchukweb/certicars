@extends('layouts.public')
@section('title','O nas')
@section('description','Poznaj historię CertiCars — platformy certyfikowanych samochodów używanych. Transparentność, jakość i pełna dokumentacja każdego pojazdu.')
@section('styles')
/* ===== ABOUT PAGE — premium refresh ===== */
.about-section-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;width:100%;box-sizing:border-box}

/* ===== HERO (dark) ===== */
.about-hero{background:linear-gradient(160deg,#050a17 0%,#070d20 45%,#0a1432 100%);padding:96px 0 120px;position:relative;overflow:hidden}
.about-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 18% 30%,rgba(0,102,255,.18),transparent 60%),radial-gradient(ellipse 55% 65% at 85% 70%,rgba(0,102,255,.12),transparent 65%);pointer-events:none}
.about-hero-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.05) 1px,transparent 1px);background-size:30px 30px;pointer-events:none;mask-image:radial-gradient(ellipse 70% 70% at 50% 50%,#000 30%,transparent 90%)}
.about-hero-carline{position:absolute;left:-3%;bottom:8%;width:60%;max-width:760px;opacity:.08;pointer-events:none}
.about-hero-carline svg{width:100%;height:auto;display:block}
.about-hero-bottom{position:absolute;left:0;right:0;bottom:-1px;height:88px;background:#fff;clip-path:polygon(0 100%,100% 100%,100% 35%,50% 100%,0 35%);pointer-events:none}
.about-hero-in{display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center}
.about-breadcrumb{display:flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.45);margin-bottom:24px}
.about-breadcrumb a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s}
.about-breadcrumb a:hover{color:#fff}
.about-breadcrumb svg{width:11px;height:11px;stroke:rgba(255,255,255,.3);fill:none;stroke-width:2.5}
.about-breadcrumb .current{color:#fff;font-weight:500}
.about-hero-label{font-size:11px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:#5fa1ff;margin-bottom:22px;display:inline-flex;align-items:center;gap:12px}
.about-hero-label::before{content:'';width:32px;height:2px;background:#5fa1ff;border-radius:2px;flex-shrink:0}
.about-hero h1{font-size:60px;font-weight:900;color:#fff;letter-spacing:-1.2px;line-height:1.04;margin:0 0 26px;max-width:560px}
.about-hero h1 em{font-style:normal;color:var(--blue);background:linear-gradient(120deg,#0066ff,#3b8bff);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.about-hero-desc{font-size:16px;color:rgba(255,255,255,.62);line-height:1.8;margin:0;max-width:520px}

/* Right side — Standard CertiCars panel */
.std-panel{position:relative;background:linear-gradient(180deg,rgba(15,28,60,.85),rgba(8,16,38,.95));border:1px solid rgba(95,161,255,.32);border-radius:24px;padding:36px 36px 30px;box-shadow:0 0 0 1px rgba(0,102,255,.08),0 40px 80px -20px rgba(0,40,120,.6),0 0 80px -20px rgba(0,102,255,.35)}
.std-panel::before{content:'';position:absolute;inset:0;border-radius:24px;padding:1px;background:linear-gradient(135deg,rgba(95,161,255,.45),rgba(95,161,255,0) 45%,rgba(95,161,255,.12) 100%);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}
.std-panel-head{margin-bottom:26px}
.std-panel-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:#5fa1ff;margin-bottom:8px;display:flex;align-items:center;gap:8px}
.std-panel-eyebrow svg{width:13px;height:13px;stroke:#5fa1ff;fill:none;stroke-width:2.2}
.std-panel-title{font-size:24px;font-weight:800;color:#fff;letter-spacing:-.4px;margin:0 0 6px}
.std-panel-sub{font-size:13px;color:rgba(255,255,255,.55);margin:0;line-height:1.5}
.std-list{list-style:none;margin:0;padding:0}
.std-item{display:grid;grid-template-columns:42px 1fr;gap:16px;align-items:flex-start;padding:18px 0;border-top:1px solid rgba(95,161,255,.12)}
.std-item:first-child{border-top:1px solid rgba(95,161,255,.18)}
.std-num{width:36px;height:36px;border-radius:50%;background:rgba(0,102,255,.16);color:#5fa1ff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;letter-spacing:.5px;border:1px solid rgba(95,161,255,.35);flex-shrink:0}
.std-text{font-size:13.5px;color:rgba(255,255,255,.78);line-height:1.6}
.std-text strong{display:block;color:#fff;font-weight:700;font-size:14px;margin-bottom:2px;letter-spacing:-.1px}
.std-callout{margin-top:22px;display:flex;align-items:center;gap:10px;padding:14px 16px;background:rgba(0,102,255,.12);border:1px solid rgba(95,161,255,.28);border-radius:12px;font-size:13px;font-weight:600;color:#cfe1ff;letter-spacing:-.1px}
.std-callout svg{width:16px;height:16px;stroke:#5fa1ff;fill:none;stroke-width:2.2;flex-shrink:0}

/* ===== Section helpers ===== */
.about-section-label{font-size:11px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:var(--blue);margin-bottom:14px;text-align:center;display:flex;align-items:center;justify-content:center;gap:10px}
.about-section-label::before,.about-section-label::after{content:'';width:24px;height:2px;background:var(--blue);border-radius:2px;opacity:.5}
.about-section-h{font-size:40px;font-weight:900;color:var(--text);letter-spacing:-.9px;text-align:center;margin:0 0 14px;line-height:1.1}
.about-section-sub{font-size:15.5px;color:var(--text-3);text-align:center;max-width:620px;margin:0 auto 56px;line-height:1.7}

/* ===== Values (white) ===== */
.about-values{padding:96px 0;background:#fff}
.values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.val-card{background:#fff;border-radius:20px;padding:36px 32px;border:1px solid #eeeef0;box-shadow:0 1px 3px rgba(0,0,0,.04),0 12px 32px -16px rgba(15,23,42,.12);transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;position:relative}
.val-card:hover{transform:translateY(-4px);box-shadow:0 1px 3px rgba(0,0,0,.04),0 24px 48px -16px rgba(15,23,42,.18);border-color:#dbeafe}
.val-icon{width:60px;height:60px;background:var(--blue-bg);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:22px}
.val-icon svg{width:28px;height:28px;stroke:var(--blue);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.val-card h3{font-size:19px;font-weight:800;color:var(--text);margin:0 0 12px;letter-spacing:-.3px;line-height:1.25}
.val-card p{font-size:14.5px;color:var(--text-3);line-height:1.75;margin:0}

/* ===== How it works — step cards with arrow connectors ===== */
.about-how{padding:96px 0;background:#f7f9fc;position:relative}
.steps-row{display:grid;grid-template-columns:repeat(4,1fr);gap:0;align-items:stretch;position:relative}
.step-card{background:#fff;border-radius:20px;padding:36px 26px 32px;border:1px solid #eeeef0;box-shadow:0 1px 3px rgba(0,0,0,.04),0 16px 40px -20px rgba(15,23,42,.15);display:flex;flex-direction:column;align-items:center;text-align:center;position:relative;z-index:1}
.step-num-wrap{position:relative;margin-bottom:14px}
.step-num{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#0066ff,#1a7fff);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;letter-spacing:.5px;box-shadow:0 12px 24px -6px rgba(0,102,255,.45),inset 0 -3px 6px rgba(0,0,0,.12)}
.step-num::after{content:'';position:absolute;inset:-4px;border:2px solid rgba(0,102,255,.18);border-radius:50%;pointer-events:none}
.step-ico{width:48px;height:48px;border-radius:14px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center;margin-bottom:18px}
.step-ico svg{width:24px;height:24px;stroke:var(--blue);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.step-card h4{font-size:17px;font-weight:800;color:var(--text);margin:0 0 10px;letter-spacing:-.2px;line-height:1.25}
.step-underline{width:32px;height:3px;border-radius:2px;background:var(--blue);margin:0 0 16px}
.step-card p{font-size:13.5px;color:var(--text-3);line-height:1.7;margin:0}
.step-arrow{display:flex;align-items:center;justify-content:center;position:relative;z-index:0}
.step-arrow span{width:40px;height:40px;border-radius:50%;background:#fff;border:1.5px solid #dbeafe;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 16px -8px rgba(15,23,42,.18)}
.step-arrow svg{width:18px;height:18px;stroke:var(--blue);fill:none;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}
.steps-row{grid-template-columns:1fr 56px 1fr 56px 1fr 56px 1fr}

/* ===== CTA banner (dark rounded) ===== */
.about-cta-wrap{padding:96px 0 110px;background:#fff}
.about-cta{position:relative;border-radius:28px;background:linear-gradient(135deg,#070d20 0%,#0a1532 55%,#0c1a3f 100%);overflow:hidden;padding:60px 64px;display:grid;grid-template-columns:1.2fr .9fr;gap:48px;align-items:center;box-shadow:0 30px 60px -24px rgba(7,13,32,.5)}
.about-cta::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 20% 30%,rgba(0,102,255,.22),transparent 60%),radial-gradient(ellipse 55% 70% at 85% 75%,rgba(0,102,255,.18),transparent 60%);pointer-events:none}
.about-cta-sweep{position:absolute;left:-10%;top:60%;width:140%;height:200px;background:linear-gradient(95deg,transparent 30%,rgba(95,161,255,.32) 45%,rgba(95,161,255,.06) 55%,transparent 70%);transform:rotate(-8deg);filter:blur(28px);pointer-events:none}
.about-cta-carline{position:absolute;left:8%;bottom:6%;width:55%;max-width:520px;opacity:.07;pointer-events:none}
.about-cta-carline svg{width:100%;height:auto;display:block}
.about-cta-body{position:relative;z-index:2}
.about-cta-eyebrow{font-size:11px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:#5fa1ff;margin-bottom:14px;display:inline-flex;align-items:center;gap:10px}
.about-cta-eyebrow::before{content:'';width:24px;height:2px;background:#5fa1ff;border-radius:2px}
.about-cta-body h2{font-size:42px;font-weight:900;color:#fff;letter-spacing:-1px;margin:0 0 16px;line-height:1.1}
.about-cta-body p{font-size:15.5px;color:rgba(255,255,255,.65);line-height:1.75;margin:0 0 32px;max-width:480px}
.about-cta-btn{display:inline-flex;align-items:center;gap:12px;background:linear-gradient(135deg,#0066ff,#1a7fff);color:#fff;padding:18px 30px;border-radius:50px;font-weight:800;font-size:16.5px;text-decoration:none;letter-spacing:-.2px;box-shadow:0 18px 36px -12px rgba(0,102,255,.6),inset 0 -2px 4px rgba(0,0,0,.18);transition:transform .2s ease,box-shadow .2s ease}
.about-cta-btn:hover{transform:translateY(-2px);box-shadow:0 24px 48px -12px rgba(0,102,255,.75),inset 0 -2px 4px rgba(0,0,0,.18)}
.about-cta-btn svg{width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
.about-cta-trust{display:flex;flex-wrap:wrap;gap:22px;margin-top:24px}
.about-cta-trust span{display:inline-flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);font-weight:500}
.about-cta-trust svg{width:14px;height:14px;stroke:#5fa1ff;fill:none;stroke-width:2.4;flex-shrink:0}
.about-cta-art{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;min-height:340px}
.about-cta-art svg{width:100%;max-width:380px;height:auto;display:block;filter:drop-shadow(0 18px 32px rgba(0,0,0,.4))}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .about-hero h1{font-size:48px}
    .steps-row{grid-template-columns:repeat(4,1fr);gap:18px}
    .step-arrow{display:none}
    .about-cta{padding:48px 40px;grid-template-columns:1fr;gap:32px}
    .about-cta-art{min-height:auto;order:-1}
    .about-cta-art svg{max-width:280px}
}
@media(max-width:900px){
    .about-hero{padding:72px 0 96px}
    .about-hero-in{grid-template-columns:1fr;gap:48px}
    .about-hero h1{font-size:40px}
    .about-hero-bottom{height:60px}
    .std-panel{padding:30px 26px 24px}
    .std-panel-title{font-size:21px}
    .values-grid{grid-template-columns:1fr;gap:18px}
    .about-section-h{font-size:32px}
    .steps-row{grid-template-columns:1fr 1fr;gap:18px}
    .about-cta-body h2{font-size:32px}
}
@media(max-width:600px){
    .about-hero h1{font-size:32px;letter-spacing:-.6px}
    .about-hero-desc{font-size:15px}
    .about-section-h{font-size:26px;letter-spacing:-.4px}
    .about-section-sub{font-size:14px;margin-bottom:40px}
    .steps-row{grid-template-columns:1fr;gap:16px}
    .about-cta{padding:40px 26px;border-radius:20px}
    .about-cta-body h2{font-size:26px}
    .about-cta-btn{font-size:15px;padding:16px 24px}
    .val-card{padding:28px 24px}
    .step-card{padding:30px 22px 26px}
    .about-cta-trust{gap:14px}
}
@endsection
@section('content')

{{-- ===== HERO (dark) ===== --}}
<section class="about-hero">
    <div class="about-hero-dots"></div>
    {{-- Faint line-art car silhouette behind the left narrative --}}
    <div class="about-hero-carline" aria-hidden="true">
        <svg viewBox="0 0 720 200" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#5fa1ff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M40 150 L120 150 Q140 92 210 86 L460 84 Q540 86 580 150 L680 150"/>
            <path d="M170 96 L210 96 L240 130 L160 130 Z"/>
            <path d="M255 96 L380 96 L420 130 L255 130 Z"/>
            <path d="M435 96 L545 96 L575 130 L435 130 Z"/>
            <circle cx="220" cy="160" r="22"/><circle cx="220" cy="160" r="10"/>
            <circle cx="540" cy="160" r="22"/><circle cx="540" cy="160" r="10"/>
        </svg>
    </div>
    <div class="about-section-in about-hero-in">
        <div>
            <nav class="about-breadcrumb" aria-label="Okruszki">
                <a href="{{ route('home') }}">Strona główna</a>
                <x-icon name="chevron-right" size="11"/>
                <span class="current">O nas</span>
            </nav>
            <div class="about-hero-label">Nasza historia</div>
            <h1>Zmieniamy rynek<br>aut używanych.<br><em>Na lepsze.</em></h1>
            <p class="about-hero-desc">CertiCars powstało po to, żeby przywracać zaufanie do rynku samochodów używanych. Pokazujemy auta możliwie konkretnie — z informacjami o pochodzeniu, wyposażeniu, formalnościach i stanie pojazdu, tak aby klient mógł lepiej poznać samochód jeszcze przed przyjazdem.</p>
        </div>
        <aside class="std-panel" aria-label="Standard CertiCars">
            <div class="std-panel-head">
                <div class="std-panel-eyebrow"><x-icon name="shield-check" size="13"/> Standard CertiCars</div>
                <h2 class="std-panel-title">Standard CertiCars</h2>
                <p class="std-panel-sub">Więcej konkretów przed przyjazdem.</p>
            </div>
            <ol class="std-list">
                <li class="std-item">
                    <span class="std-num">01</span>
                    <span class="std-text"><strong>Poznaj auto wcześniej</strong>Najważniejsze informacje pokazujemy jeszcze przed oględzinami.</span>
                </li>
                <li class="std-item">
                    <span class="std-num">02</span>
                    <span class="std-text"><strong>Mniej domysłów</strong>Opisujemy pochodzenie, wyposażenie, formalności, stan i ślady użytkowania.</span>
                </li>
                <li class="std-item">
                    <span class="std-num">03</span>
                    <span class="std-text"><strong>Spokojniejsza decyzja</strong>Łatwiej oceniasz, czy dane auto pasuje do Twoich potrzeb.</span>
                </li>
                <li class="std-item">
                    <span class="std-num">04</span>
                    <span class="std-text"><strong>CertiCheck dla wybranych aut</strong>Dodatkowe informacje o pojeździe w ramach CertiCheck.</span>
                </li>
            </ol>
            <div class="std-callout">
                <x-icon name="sparkles" size="16"/>
                Więcej informacji. Mniej niepewności.
            </div>
        </aside>
    </div>
    <div class="about-hero-bottom"></div>
</section>

{{-- ===== Values — Dlaczego CertiCars? ===== --}}
<section class="about-values">
    <div class="about-section-in">
        <div class="about-section-label">Nasze wartości</div>
        <h2 class="about-section-h">Dlaczego CertiCars?</h2>
        <p class="about-section-sub">Tworzymy miejsce, w którym zakup samochodu używanego ma być prosty, konkretny i w pełni zrozumiały dla klienta.</p>
        <div class="values-grid">
            <article class="val-card">
                <div class="val-icon"><x-icon name="info" size="28"/></div>
                <h3>Więcej informacji na start</h3>
                <p>Już w ogłoszeniu pokazujemy najważniejsze dane o samochodzie, jego pochodzeniu, wyposażeniu i formalnościach.</p>
            </article>
            <article class="val-card">
                <div class="val-icon"><x-icon name="layout-grid" size="28"/></div>
                <h3>Pełny obraz auta</h3>
                <p>Łączymy zdjęcia, opis, dane techniczne i informacje o stanie pojazdu, żeby klient mógł lepiej poznać samochód przed kontaktem.</p>
            </article>
            <article class="val-card">
                <div class="val-icon"><x-icon name="list-checks" size="28"/></div>
                <h3>Wszystko krok po kroku</h3>
                <p>Od pierwszego kontaktu po finalizację zakupu — tłumaczymy najważniejsze etapy prosto i konkretnie.</p>
            </article>
        </div>
    </div>
</section>

{{-- ===== How it works — 4 step cards with arrow connectors ===== --}}
<section class="about-how">
    <div class="about-section-in">
        <div class="about-section-label">Jak działamy</div>
        <h2 class="about-section-h">Jak przygotowujemy auta do sprzedaży?</h2>
        <p class="about-section-sub">Pokazujemy krok po kroku, jak wybieramy, sprawdzamy i przygotowujemy samochód, zanim trafi do sprzedaży.</p>
        <div class="steps-row">
            <article class="step-card">
                <div class="step-num-wrap"><div class="step-num">01</div></div>
                <div class="step-ico"><x-icon name="search-check" size="24"/></div>
                <h4>Selekcja<br>i zakup auta</h4>
                <div class="step-underline"></div>
                <p>Wybieramy samochód i sprawdzamy jego podstawową historię, stan, pochodzenie, dokumenty oraz wyposażenie.</p>
            </article>
            <div class="step-arrow" aria-hidden="true"><span><x-icon name="arrow-right" size="18"/></span></div>
            <article class="step-card">
                <div class="step-num-wrap"><div class="step-num">02</div></div>
                <div class="step-ico"><x-icon name="wrench" size="24"/></div>
                <h4>Oględziny auta</h4>
                <div class="step-underline"></div>
                <p>Sprawdzamy najważniejsze elementy pojazdu i przygotowujemy go do dalszej prezentacji.</p>
            </article>
            <div class="step-arrow" aria-hidden="true"><span><x-icon name="arrow-right" size="18"/></span></div>
            <article class="step-card">
                <div class="step-num-wrap"><div class="step-num">03</div></div>
                <div class="step-ico"><x-icon name="camera" size="24"/></div>
                <h4>Zdjęcia i opis</h4>
                <div class="step-underline"></div>
                <p>Robimy zdjęcia oraz przygotowujemy opis auta, tak aby oferta była konkretna i czytelna dla klienta.</p>
            </article>
            <div class="step-arrow" aria-hidden="true"><span><x-icon name="arrow-right" size="18"/></span></div>
            <article class="step-card">
                <div class="step-num-wrap"><div class="step-num">04</div></div>
                <div class="step-ico"><x-icon name="rocket" size="24"/></div>
                <h4>Trafia do oferty</h4>
                <div class="step-underline"></div>
                <p>Publikujemy samochód z najważniejszymi informacjami, które pomagają klientowi świadomie podejść do zakupu auta.</p>
            </article>
        </div>
    </div>
</section>

{{-- ===== CTA banner (dark rounded) ===== --}}
<section class="about-cta-wrap">
    <div class="about-section-in">
        <div class="about-cta">
            <div class="about-cta-sweep" aria-hidden="true"></div>
            {{-- Faint car silhouette in the banner background --}}
            <div class="about-cta-carline" aria-hidden="true">
                <svg viewBox="0 0 720 200" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#5fa1ff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M40 150 L120 150 Q140 92 210 86 L460 84 Q540 86 580 150 L680 150"/>
                    <path d="M170 96 L240 96 L260 130 L160 130 Z"/>
                    <path d="M275 96 L430 96 L450 130 L275 130 Z"/>
                    <path d="M460 96 L545 96 L575 130 L460 130 Z"/>
                    <circle cx="220" cy="160" r="20"/>
                    <circle cx="540" cy="160" r="20"/>
                </svg>
            </div>
            <div class="about-cta-body">
                <div class="about-cta-eyebrow">Masz pytania?</div>
                <h2>Porozmawiajmy o aucie</h2>
                <p>Jeśli chcesz dopytać o konkretny samochód, zadzwoń. Odpowiemy na pytania i pomożemy umówić oględziny.</p>
                <a href="tel:+48515440623" class="about-cta-btn">
                    <x-icon name="phone" size="20"/>
                    Zadzwoń: 515 440 623
                </a>
                <div class="about-cta-trust">
                    <span><x-icon name="zap" size="14"/> Szybki kontakt</span>
                    <span><x-icon name="info" size="14"/> Jasne informacje</span>
                    <span><x-icon name="calendar-check" size="14"/> Możliwość oględzin</span>
                </div>
            </div>
            <div class="about-cta-art" aria-hidden="true">
                {{-- Vector portrait of a CertiCars consultant on the phone, with laptop + mug.
                     Hand-drawn SVG so it ships inline (no external asset, no http call). --}}
                <svg viewBox="0 0 380 360" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="ccArtBg" x1="0" x2="1" y1="0" y2="1">
                            <stop offset="0" stop-color="#0f2454"/>
                            <stop offset="1" stop-color="#0a1838"/>
                        </linearGradient>
                        <linearGradient id="ccArtGlow" x1="0" x2="1" y1="0" y2="1">
                            <stop offset="0" stop-color="#5fa1ff"/>
                            <stop offset="1" stop-color="#0066ff"/>
                        </linearGradient>
                        <linearGradient id="ccArtShirt" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0" stop-color="#1a4ea8"/>
                            <stop offset="1" stop-color="#0d2f6e"/>
                        </linearGradient>
                        <linearGradient id="ccArtSkin" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0" stop-color="#f1c8a8"/>
                            <stop offset="1" stop-color="#d9a784"/>
                        </linearGradient>
                    </defs>

                    {{-- Soft glow halo behind the figure --}}
                    <circle cx="190" cy="180" r="155" fill="url(#ccArtBg)" opacity=".55"/>
                    <circle cx="190" cy="170" r="110" fill="url(#ccArtGlow)" opacity=".18"/>

                    {{-- Desk surface --}}
                    <rect x="40" y="278" width="300" height="14" rx="3" fill="#1a2a4f"/>
                    <rect x="40" y="278" width="300" height="3" fill="#5fa1ff" opacity=".4"/>

                    {{-- Laptop --}}
                    <rect x="62" y="232" width="120" height="50" rx="5" fill="#0e1a36" stroke="#5fa1ff" stroke-width="1.6"/>
                    <rect x="68" y="238" width="108" height="38" rx="2" fill="#0a1530"/>
                    <rect x="74" y="244" width="56" height="4" rx="1" fill="#5fa1ff" opacity=".7"/>
                    <rect x="74" y="252" width="80" height="3" rx="1" fill="#5fa1ff" opacity=".4"/>
                    <rect x="74" y="259" width="64" height="3" rx="1" fill="#5fa1ff" opacity=".3"/>
                    <rect x="74" y="266" width="44" height="3" rx="1" fill="#5fa1ff" opacity=".3"/>
                    <rect x="56" y="278" width="132" height="4" rx="2" fill="#1a2a4f"/>

                    {{-- Mug --}}
                    <path d="M232 248 L268 248 L264 280 L236 280 Z" fill="#fff"/>
                    <path d="M268 254 q12 0 12 12 q0 12 -12 12" fill="none" stroke="#fff" stroke-width="3"/>
                    <rect x="238" y="255" width="24" height="3" fill="#0066ff"/>
                    <path d="M244 240 q2 -8 0 -16 M250 240 q2 -8 0 -16 M256 240 q2 -8 0 -16" stroke="#cfe1ff" stroke-width="1.4" stroke-linecap="round" fill="none" opacity=".6"/>

                    {{-- Body / shirt --}}
                    <path d="M118 230 Q140 198 200 198 Q260 198 282 230 L290 290 L110 290 Z" fill="url(#ccArtShirt)"/>
                    <path d="M180 198 L200 230 L220 198" fill="none" stroke="#0a1432" stroke-width="2.4"/>
                    {{-- Lanyard / ID badge --}}
                    <rect x="194" y="232" width="14" height="20" rx="2" fill="#5fa1ff"/>
                    <rect x="195" y="235" width="12" height="3" fill="#fff" opacity=".8"/>

                    {{-- Neck --}}
                    <rect x="186" y="180" width="28" height="22" fill="url(#ccArtSkin)"/>

                    {{-- Head --}}
                    <ellipse cx="200" cy="158" rx="40" ry="44" fill="url(#ccArtSkin)"/>
                    {{-- Hair --}}
                    <path d="M158 142 Q170 110 200 108 Q236 108 244 144 Q248 132 240 122 Q224 100 200 100 Q170 100 156 124 Q150 134 158 142 Z" fill="#3a2818"/>
                    {{-- Ear --}}
                    <ellipse cx="160" cy="160" rx="6" ry="9" fill="url(#ccArtSkin)"/>
                    {{-- Eyes + brows + mouth --}}
                    <path d="M178 152 q6 -4 12 0" stroke="#2a1a0c" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <path d="M208 152 q6 -4 12 0" stroke="#2a1a0c" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <circle cx="184" cy="162" r="2" fill="#2a1a0c"/>
                    <circle cx="214" cy="162" r="2" fill="#2a1a0c"/>
                    <path d="M188 180 q12 8 22 0" stroke="#a35b3a" stroke-width="2" fill="none" stroke-linecap="round"/>

                    {{-- Phone to ear (right arm raised) --}}
                    <path d="M240 200 Q272 178 268 142 Q266 124 244 130" fill="url(#ccArtShirt)" stroke="#0a1432" stroke-width="1.2"/>
                    <rect x="232" y="118" width="20" height="40" rx="5" fill="#0a1432" stroke="#5fa1ff" stroke-width="1.4"/>
                    <rect x="234" y="122" width="16" height="28" rx="2" fill="#0066ff" opacity=".25"/>
                    <circle cx="242" cy="154" r="2" fill="#5fa1ff"/>
                    {{-- Hand on phone --}}
                    <ellipse cx="240" cy="138" rx="9" ry="11" fill="url(#ccArtSkin)"/>

                    {{-- Left arm down to laptop --}}
                    <path d="M120 230 Q90 240 92 268 Q102 274 134 262 Z" fill="url(#ccArtShirt)" stroke="#0a1432" stroke-width="1.2"/>
                    <ellipse cx="98" cy="266" rx="10" ry="7" fill="url(#ccArtSkin)"/>

                    {{-- Headset wire to the phone (suggestion of a call) --}}
                    <path d="M236 156 q-8 18 -4 30" stroke="#5fa1ff" stroke-width="1.4" fill="none" stroke-dasharray="2 3" opacity=".7"/>

                    {{-- Speech sparkle near phone --}}
                    <circle cx="266" cy="118" r="3" fill="#5fa1ff"/>
                    <circle cx="278" cy="106" r="2" fill="#5fa1ff" opacity=".7"/>
                    <circle cx="288" cy="118" r="1.6" fill="#5fa1ff" opacity=".5"/>
                </svg>
            </div>
        </div>
    </div>
</section>

@endsection
