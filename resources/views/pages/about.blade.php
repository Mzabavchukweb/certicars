@extends('layouts.public')
@section('title','O nas')
@section('description','Poznaj historię CertiCars — platformy certyfikowanych samochodów używanych. Transparentność, jakość i pełna dokumentacja każdego pojazdu.')
@section('styles')
/* ===== ABOUT PAGE — premium refresh ===== */
.about-section-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;width:100%;box-sizing:border-box}

/* ===== HERO (dark, flat) ===== */
.about-hero{background:#0a1432;padding:96px 0 120px;position:relative;overflow:hidden}
.about-hero-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}
/* Hero info pill (#98) sitting under the narrative paragraph — flat
   bg + border, no glow, per the AI-tone-down direction. */
.about-hero-pill{display:inline-flex;align-items:flex-start;gap:14px;margin-top:22px;padding:14px 18px;background:#0e1a3a;border:1px solid #1f2e58;border-radius:12px;max-width:520px;text-decoration:none;color:rgba(255,255,255,.78);transition:border-color .15s ease}
.about-hero-pill:hover{border-color:#2a3d6e}
.about-hero-pill-ico{flex-shrink:0;width:32px;height:32px;border-radius:50%;border:1.5px solid rgba(95,161,255,.55);display:flex;align-items:center;justify-content:center;color:#5fa1ff}
.about-hero-pill-ico svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.2}
.about-hero-pill-text{font-size:14.5px;line-height:1.55;letter-spacing:-.05px}
.about-hero-pill-text b{color:#5fa1ff;font-weight:700}
.about-hero-pill-text svg{width:14px;height:14px;stroke:#5fa1ff;fill:none;stroke-width:2.2;vertical-align:-2px;margin-left:4px}
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
.about-hero h1 em{font-style:normal;color:#3b8bff}
.about-hero-desc{font-size:16px;color:rgba(255,255,255,.62);line-height:1.8;margin:0;max-width:520px}

/* Right side — Standard CertiCars panel — flat dark navy, single border */
.std-panel{position:relative;background:#0e1a3a;border:1px solid #1f2e58;border-radius:18px;padding:32px 32px 26px}
.std-panel-head{margin-bottom:26px}
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
.val-card{background:#fff;border-radius:12px;padding:36px 32px;border:1px solid var(--border-l);transition:border-color .15s ease}
.val-card:hover{border-color:var(--blue)}
.val-icon{width:60px;height:60px;background:var(--blue-bg);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:22px}
.val-icon svg{width:28px;height:28px;stroke:var(--blue);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.val-card h3{font-size:19px;font-weight:800;color:var(--text);margin:0 0 12px;letter-spacing:-.3px;line-height:1.25}
.val-card p{font-size:14.5px;color:var(--text-3);line-height:1.75;margin:0}

/* ===== How it works — step cards with arrow connectors ===== */
.about-how{padding:96px 0 32px;background:#f7f9fc;position:relative}
.steps-row{display:grid;grid-template-columns:repeat(4,1fr);gap:0;align-items:stretch;position:relative}
.step-card{background:#fff;border-radius:12px;padding:36px 26px 32px;border:1px solid var(--border-l);display:flex;flex-direction:column;align-items:center;text-align:center;position:relative;z-index:1}
.step-num-wrap{position:relative;margin-bottom:14px}
.step-num{width:64px;height:64px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;letter-spacing:.5px}
.step-ico{width:48px;height:48px;border-radius:14px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center;margin-bottom:18px}
.step-ico svg{width:24px;height:24px;stroke:var(--blue);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.step-card h4{font-size:17px;font-weight:800;color:var(--text);margin:0 0 10px;letter-spacing:-.2px;line-height:1.25}
.step-underline{width:32px;height:3px;border-radius:2px;background:var(--blue);margin:0 0 16px}
.step-card p{font-size:13.5px;color:var(--text-3);line-height:1.7;margin:0}
.step-arrow{display:flex;align-items:center;justify-content:center;position:relative;z-index:0}
.step-arrow span{width:36px;height:36px;border-radius:50%;background:#fff;border:1px solid var(--border-l);display:flex;align-items:center;justify-content:center}
.step-arrow svg{width:18px;height:18px;stroke:var(--blue);fill:none;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}
.steps-row{grid-template-columns:1fr 56px 1fr 56px 1fr 56px 1fr}

/* ===== CTA banner (dark rounded) ===== */
/* The bohater illustration is taller than the dark card and overflows
   above its top edge. Top padding here is dimensioned so that overflow
   FILLS the space above the card instead of poking up into the steps
   section. Result: no visible empty white band, dark card sits tight
   below the steps row. */
.about-cta-wrap{padding:240px 0 96px;background:#fff}
/* Container matches the other page sections (1200 max-width) so the dark
   banner aligns vertically with the values cards and step cards above. */
.about-cta-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;box-sizing:border-box}
/* Shell sits outside the dark card and is overflow:visible so the character
   illustration can break above the card's top edge. */
.about-cta-shell{position:relative;overflow:visible}
/* Shorter, more panoramic card: 36 top / 40 bottom padding (was 48/48). */
.about-cta{position:relative;border-radius:18px;background:#0a1432;overflow:hidden;padding:36px 64px 40px;min-height:240px;border:1px solid #131b3a}
.about-cta-sweep{display:none}
.about-cta-carline{position:absolute;left:52%;bottom:8%;width:48%;max-width:560px;opacity:.09;pointer-events:none}
.about-cta-carline svg{width:100%;height:auto;display:block}
.about-cta-body{position:relative;z-index:2;max-width:520px}
.about-cta-eyebrow{font-size:11px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:#5fa1ff;margin-bottom:14px;display:inline-flex;align-items:center;gap:10px}
.about-cta-eyebrow::before{content:'';width:24px;height:2px;background:#5fa1ff;border-radius:2px}
.about-cta-body h2{font-size:40px;font-weight:900;color:#fff;letter-spacing:-1px;margin:0 0 14px;line-height:1.08}
.about-cta-body p{font-size:15.5px;color:rgba(255,255,255,.65);line-height:1.7;margin:0 0 28px;max-width:480px}
.about-cta-btn{display:inline-flex;align-items:center;gap:12px;background:var(--blue);color:#fff;padding:16px 28px;border-radius:8px;font-weight:700;font-size:16px;text-decoration:none;letter-spacing:-.1px;transition:background .15s ease}
.about-cta-btn:hover{background:var(--blue-h)}
.about-cta-btn svg{width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
.about-cta-trust{display:flex;flex-wrap:nowrap;gap:24px;margin-top:22px;align-items:center}
.about-cta-trust span{display:inline-flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.62);font-weight:500;white-space:nowrap}
.about-cta-trust svg{width:14px;height:14px;stroke:#5fa1ff;fill:none;stroke-width:2.4;flex-shrink:0}

/* Bohater illustration — lives in the shell, NOT inside the dark card, so
   it can break above the card's top edge (overflow:hidden on .about-cta
   would otherwise clip the head). Anchored to the bottom-right with a
   small negative bottom so the laptop visually rests on the card's
   rounded base. */
/* Bohater anchored bottom-right. Width is 42% of the shell (matches the
   38–45% target band) and height auto preserves the 0.8 aspect ratio, so
   at a typical 1152 px shell width the image is ~484×605 px. With the
   card at ~280 px tall and bottom:-16, that means the head + shoulders
   extend ~310 px above the card's top edge — the dramatic "break-out"
   look from the reference. */
.about-cta-bohater{position:absolute;right:36px;bottom:-16px;width:42%;max-width:520px;min-width:360px;height:auto;object-fit:contain;z-index:5;pointer-events:none;user-select:none;-webkit-user-drag:none}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .about-hero h1{font-size:48px}
    .steps-row{grid-template-columns:repeat(4,1fr);gap:18px}
    .step-arrow{display:none}
    /* Tablet: keep bohater anchored bottom-right but shrink so it still
       breaks above the card edge without overlapping the text. The body
       max-width tightens to keep the text out of the illustration. */
    /* Tablet: shrink the top padding because the bohater is smaller too. */
    .about-cta-wrap{padding-top:180px}
    .about-cta{padding:32px 48px 36px}
    .about-cta-body{max-width:52%}
    .about-cta-bohater{right:24px;bottom:-12px;width:44%;max-width:420px;min-width:280px;height:auto}
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
    .about-cta-wrap{padding-top:140px}
    .about-cta{padding:32px 36px 32px}
    .about-cta-body h2{font-size:32px}
    .about-cta-body{max-width:56%}
    .about-cta-bohater{width:46%;max-width:340px;min-width:240px;right:14px;bottom:-8px}
}
@media(max-width:760px){
    /* Below tablet, stack: bohater drops below text inside the card.
       The wrap padding-top resets to normal because there's no overflow to absorb. */
    .about-cta-wrap{padding:48px 0 80px}
    .about-cta{padding:40px 32px 0;min-height:auto}
    .about-cta-body{max-width:100%}
    .about-cta-bohater{position:relative;right:auto;bottom:auto;height:auto;width:100%;max-width:300px;display:block;margin:24px auto 0}
}
@media(max-width:600px){
    .about-hero h1{font-size:32px;letter-spacing:-.6px}
    .about-hero-desc{font-size:15px}
    .about-section-h{font-size:26px;letter-spacing:-.4px}
    .about-section-sub{font-size:14px;margin-bottom:40px}
    .steps-row{grid-template-columns:1fr;gap:16px}
    .about-cta{padding:32px 24px 0;border-radius:20px}
    .about-cta-body h2{font-size:26px}
    .about-cta-btn{font-size:15px;padding:16px 24px}
    .about-cta-bohater{max-width:240px;margin-top:20px}
    .about-cta-trust{flex-wrap:wrap;gap:14px}
    .val-card{padding:28px 24px}
    .step-card{padding:30px 22px 26px}
}
@endsection
@section('content')

{{-- ===== HERO (dark) ===== --}}
<section class="about-hero">
    <div class="about-hero-dots"></div>
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
            <a href="{{ route('home') }}#certicheck" class="about-hero-pill">
                <span class="about-hero-pill-ico"><x-icon name="info" size="16"/></span>
                <span class="about-hero-pill-text">Przy wybranych autach dodatkowe informacje przygotowujemy w&nbsp;ramach <b>CertiCheck.<x-icon name="arrow-up-right" size="14"/></b></span>
            </a>
        </div>
        <aside class="std-panel" aria-label="Standard CertiCars">
            <div class="std-panel-head">
                <h2 class="std-panel-title">Standard CertiCars</h2>
                <p class="std-panel-sub">Więcej konkretów przed przyjazdem.</p>
            </div>
            <ol class="std-list">
                <li class="std-item">
                    <span class="std-num">01</span>
                    <span class="std-text"><strong>Poznaj auto wcześniej —</strong>Najważniejsze informacje pokazujemy jeszcze przed oględzinami.</span>
                </li>
                <li class="std-item">
                    <span class="std-num">02</span>
                    <span class="std-text"><strong>Mniej domysłów —</strong>Opisujemy pochodzenie, wyposażenie, formalności, stan i ślady użytkowania.</span>
                </li>
                <li class="std-item">
                    <span class="std-num">03</span>
                    <span class="std-text"><strong>Spokojniejsza decyzja —</strong>Łatwiej ocenisz, czy dane auto pasuje do Twoich potrzeb.</span>
                </li>
                <li class="std-item">
                    <span class="std-num">04</span>
                    <span class="std-text"><strong>CertiCheck dla wybranych aut —</strong>Dodatkowe informacje o pojeździe w ramach CertiCheck.</span>
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
    <div class="about-cta-in">
        {{-- Shell wrapper sits outside the dark card with overflow:visible so the
             bohater illustration can break above the card's top edge. The dark
             card itself keeps overflow:hidden for its internal sweep + gradient. --}}
        <div class="about-cta-shell">
            <div class="about-cta">
                <div class="about-cta-sweep" aria-hidden="true"></div>
                {{-- Faint car silhouette behind the character on the right --}}
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
            </div>
            <img class="about-cta-bohater" src="/images/bohater.png" alt="Doradca CertiCars rozmawiający z klientem o aucie" loading="lazy" decoding="async" width="1122" height="1402">
        </div>
    </div>
</section>

@endsection
