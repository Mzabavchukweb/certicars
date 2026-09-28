@extends('layouts.public')
@section('meta_title_full','O nas — komis samochodowy pod Stargardem | CertiCars')
@section('title','O nas')
@section('description','CertiCars z Lipnika koło Stargardu — auta osobowe i dostawcze. Pokazujemy dostępne informacje o autach, pomagamy w formalnościach, ubezpieczeniu, transporcie i zakupie na odległość.')
@section('og_title','O nas — komis samochodowy pod Stargardem | CertiCars')
@section('og_description','CertiCars z Lipnika koło Stargardu — auta osobowe i dostawcze. Pokazujemy dostępne informacje o autach i pomagamy przejść przez zakup: formalności, ubezpieczenie, transport, zakup na odległość.')

@section('styles')
/* =====================================================================
   O NAS — układ wg projektu (hero z postacią, CertiCheck, usługi,
   zakup na odległość, kroki zakupu, CTA). Grafiki: /img/about/*.webp
   ===================================================================== */
.ab-in{max-width:1200px;margin:0 auto;padding:0 24px;width:100%;box-sizing:border-box}
.ab-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:800;letter-spacing:1.8px;text-transform:uppercase;color:#0066ff;margin-bottom:14px}
.ab-eyebrow::before{content:'';width:22px;height:1.5px;background:currentColor;border-radius:1px}
.ab-h2{font-size:34px;font-weight:900;color:#0a0a0a;letter-spacing:-.9px;line-height:1.15;margin:0 0 14px}
.ab-lead{font-size:15.5px;color:#4b5563;line-height:1.65;margin:0}
.ab-note{font-size:12.5px;color:#9ca3af;line-height:1.55;margin:18px 0 0}
.ab-link{display:inline-flex;align-items:center;gap:7px;font-size:14.5px;font-weight:700;color:#0066ff;text-decoration:none;margin-top:18px}
.ab-link:hover{color:#0052cc}
.ab-link svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.4}

/* ---------- HERO ---------- */
.ab-hero{position:relative;background:#0a1740 url('/img/about/tlo_hero_1920x720.webp') center bottom/cover no-repeat;padding:70px 0 0;overflow:hidden}
.ab-hero-grid{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr);gap:28px;align-items:end;min-height:470px}
.ab-hero-copy{padding-bottom:104px}
.ab-hero .ab-eyebrow{color:#7fb2ff}
.ab-hero h1{font-size:46px;font-weight:900;color:#fff;letter-spacing:-1.4px;line-height:1.12;margin:0 0 18px}
.ab-hero h1 span{color:#3b8bff;display:block}
.ab-hero p{font-size:16px;color:rgba(255,255,255,.82);line-height:1.65;margin:0 0 26px;max-width:520px}
.ab-hero-btns{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px}
.ab-btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;padding:14px 26px;border-radius:12px;font-size:14.5px;font-weight:700;text-decoration:none;transition:transform .15s,background .15s,box-shadow .15s;border:none;cursor:pointer}
.ab-btn-primary{background:#0066ff;color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.32)}
.ab-btn-primary:hover{background:#0052cc;color:#fff;transform:translateY(-1px)}
.ab-btn-dark{background:#0c1b3f;color:#fff;border:1px solid rgba(255,255,255,.22)}
.ab-btn-dark:hover{background:#12274f;color:#fff}
.ab-hero-place{display:inline-flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.7)}
.ab-hero-place svg{width:15px;height:15px;stroke:#7fb2ff;fill:none;stroke-width:2}
.ab-hero-fig{position:relative;display:flex;justify-content:center;align-items:flex-end;min-height:470px}
.ab-hero-fig img{width:100%;max-width:430px;height:auto;display:block;filter:drop-shadow(0 24px 42px rgba(4,12,38,.45))}

/* ---------- 3 kafelki „Po ludzku o samochodach” ---------- */
.ab-intro{background:#fff;padding:64px 0 10px}
.ab-intro-top{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:40px;align-items:start;margin-bottom:34px}
.ab-intro-top .ab-h2{margin:0}
.ab-cards-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.ab-card{background:#fff;border:1px solid #eaeef5;border-radius:16px;padding:24px 22px;box-shadow:0 1px 3px rgba(15,32,80,.04);transition:border-color .18s,box-shadow .18s,transform .18s}
.ab-card:hover{border-color:#cfdcf5;box-shadow:0 10px 28px rgba(15,32,80,.08);transform:translateY(-2px)}
.ab-card-ico{width:48px;height:48px;border-radius:13px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center;margin-bottom:16px}
.ab-card-ico svg{width:23px;height:23px;stroke:currentColor;fill:none;stroke-width:1.8}
.ab-card h3{font-size:16.5px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;margin:0 0 7px;line-height:1.3}
.ab-card p{font-size:14px;color:#6b7280;line-height:1.6;margin:0}

/* ---------- Panele (CertiCheck / zakup na odległość) ---------- */
.ab-panel{background:#f2f7ff;border:1px solid #e2ecff;border-radius:22px;padding:38px 40px;margin:64px 0 0}
.ab-panel-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.25fr);gap:40px;align-items:center}
.ab-panel .ab-h2{font-size:30px}
.ab-mini{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.ab-mini-card{background:#fff;border:1px solid #e6edf8;border-radius:14px;padding:20px 18px}
.ab-mini-card .ab-card-ico{width:42px;height:42px;border-radius:11px;margin-bottom:12px}
.ab-mini-card .ab-card-ico svg{width:20px;height:20px}
.ab-mini-card h3{font-size:14.5px;font-weight:800;color:#0a0a0a;margin:0 0 5px;line-height:1.3}
.ab-mini-card p{font-size:13px;color:#6b7280;line-height:1.55;margin:0}

/* ---------- Usługi ---------- */
.ab-services{padding:64px 0 0}
.ab-services-head{margin-bottom:26px}
.ab-cards-6{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.ab-srv{display:flex;gap:14px;align-items:flex-start;background:#fff;border:1px solid #eaeef5;border-radius:16px;padding:20px 20px;box-shadow:0 1px 3px rgba(15,32,80,.04);transition:border-color .18s,box-shadow .18s}
.ab-srv:hover{border-color:#cfdcf5;box-shadow:0 8px 22px rgba(15,32,80,.07)}
.ab-srv-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.ab-srv-ico svg{width:21px;height:21px;stroke:currentColor;fill:none;stroke-width:1.8}
.ab-srv h3{font-size:15px;font-weight:800;color:#0a0a0a;margin:0 0 5px;line-height:1.3}
.ab-srv p{font-size:13.5px;color:#6b7280;line-height:1.55;margin:0}

/* ---------- Zakup na odległość ---------- */
.ab-remote-grid{grid-template-columns:minmax(0,1fr) minmax(0,1.05fr) minmax(0,.62fr);gap:26px}
.ab-remote-fig{display:flex;align-items:center;justify-content:center}
.ab-remote-fig img{width:100%;max-width:440px;height:auto;display:block}
.ab-remote-list{display:flex;flex-direction:column;gap:10px}
.ab-remote-item{display:flex;align-items:center;gap:12px;background:#fff;border:1px solid #e6edf8;border-radius:12px;padding:13px 16px;font-size:14px;font-weight:600;color:#1a1a1a}
.ab-remote-item .ab-card-ico{width:38px;height:38px;border-radius:10px;margin:0}
.ab-remote-item .ab-card-ico svg{width:19px;height:19px}

/* ---------- Kroki zakupu ---------- */
.ab-steps{padding:64px 0 0}
.ab-steps-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px;margin-top:28px;position:relative}
.ab-step{position:relative;padding-top:56px}
.ab-step::before{content:'';position:absolute;top:21px;left:44px;right:-20px;height:2px;background:#e2e8f3}
.ab-step:last-child::before{display:none}
.ab-step-num{position:absolute;top:0;left:0;width:44px;height:44px;border-radius:50%;background:#0066ff;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;letter-spacing:.3px}
.ab-step h3{font-size:16px;font-weight:800;color:#0a0a0a;margin:0 0 6px;line-height:1.3}
.ab-step p{font-size:14px;color:#6b7280;line-height:1.6;margin:0}

/* ---------- CTA ---------- */
.ab-cta-wrap{padding:64px 0 72px}
.ab-cta{background:#0a1740;border-radius:22px;padding:40px 44px;display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:32px;align-items:center}
.ab-cta h2{font-size:30px;font-weight:900;color:#fff;letter-spacing:-.8px;line-height:1.18;margin:0 0 12px}
.ab-cta p{font-size:15px;color:rgba(255,255,255,.72);line-height:1.6;margin:0}
.ab-cta-actions{display:flex;flex-direction:column;align-items:flex-end;gap:14px}
.ab-cta-actions .ab-btn-primary{font-size:16px;padding:16px 30px}
.ab-cta-link{display:inline-flex;align-items:center;gap:7px;font-size:14px;font-weight:600;color:rgba(255,255,255,.8);text-decoration:none}
.ab-cta-link:hover{color:#fff}
.ab-cta-link svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}

/* ---------- Tablet ---------- */
@media(max-width:1024px){
    /* Tło 1920×720 przy wąskim ekranie przycina wbudowaną falę, więc rysujemy ją
       jako element — postać ma na niej stać, tak jak w projekcie. */
    .ab-hero{padding-top:52px;background-position:center top}
    .ab-hero::after{content:'';position:absolute;left:-12%;right:-12%;bottom:-46px;height:120px;background:#fff;border-radius:50% 50% 0 0/100% 100% 0 0;z-index:2;pointer-events:none}
    .ab-hero-grid{position:relative;z-index:1}
    .ab-hero-grid{grid-template-columns:1fr;gap:0;min-height:0}
    .ab-hero-copy{padding-bottom:28px}
    .ab-hero h1{font-size:38px}
    .ab-hero-fig{min-height:0;justify-content:center}
    .ab-hero-fig img{max-width:330px}
    .ab-intro-top{grid-template-columns:1fr;gap:16px}
    .ab-cards-3,.ab-cards-6{grid-template-columns:repeat(2,minmax(0,1fr))}
    .ab-panel{padding:30px 26px}
    .ab-panel-grid,.ab-remote-grid{grid-template-columns:1fr;gap:26px}
    .ab-steps-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:26px}
    .ab-step::before{display:none}
    .ab-cta{grid-template-columns:1fr;gap:24px;padding:32px 28px}
    .ab-cta-actions{align-items:flex-start}
}

/* ---------- Telefon ---------- */
@media(max-width:640px){
    .ab-in{padding:0 18px}
    .ab-hero{padding-top:40px;background-position:center bottom;background-size:cover}
    .ab-hero h1{font-size:32px;letter-spacing:-1px}
    .ab-hero p{font-size:15px}
    .ab-hero-btns{flex-direction:column;align-items:stretch}
    .ab-btn{width:100%}
    .ab-hero-fig img{max-width:290px}
    .ab-h2{font-size:26px}
    .ab-panel .ab-h2{font-size:24px}
    .ab-intro{padding-top:44px}
    .ab-cards-3,.ab-cards-6,.ab-mini{grid-template-columns:1fr}
    .ab-panel{margin-top:44px;padding:26px 20px;border-radius:18px}
    .ab-services,.ab-steps{padding-top:44px}
    .ab-steps-grid{grid-template-columns:1fr;gap:22px}
    .ab-cta{padding:28px 22px;border-radius:18px}
    .ab-cta h2{font-size:25px}
    .ab-cta-wrap{padding:44px 0 56px}
}
@endsection

@section('content')

{{-- ===================== HERO ===================== --}}
<section class="ab-hero">
    <div class="ab-in ab-hero-grid">
        <div class="ab-hero-copy">
            <div class="ab-eyebrow">O nas · CertiCars</div>
            <h1>Poznaj auto wcześniej.<span>Kupuj spokojniej.</span></h1>
            <p>Pokazujemy dostępne informacje o autach i pomagamy przejść przez zakup — od pierwszych pytań po formalności i odbiór.</p>
            <div class="ab-hero-btns">
                <a href="{{ route('catalog') }}" class="ab-btn ab-btn-primary">Sprawdź ofertę</a>
                <a href="{{ route('contact') }}" class="ab-btn ab-btn-dark">Porozmawiajmy</a>
            </div>
            <span class="ab-hero-place">
                <x-icon name="map-pin" size="15"/>
                Lipnik k. Stargardu · Auta osobowe i dostawcze
            </span>
        </div>
        <div class="ab-hero-fig">
            <img src="/img/about/ludzik.webp" alt="Doradca CertiCars z tabletem" width="1086" height="1448" fetchpriority="high" decoding="async">
        </div>
    </div>
</section>

{{-- ===================== PO LUDZKU O SAMOCHODACH ===================== --}}
<section class="ab-intro">
    <div class="ab-in">
        <div class="ab-intro-top">
            <h2 class="ab-h2">Po ludzku<br>o samochodach.</h2>
            <p class="ab-lead">Działamy w Lipniku koło Stargardu. Sprzedajemy samochody osobowe i dostawcze. Pomagamy poznać auto, uporządkować formalności i wybrać potrzebne usługi.</p>
        </div>

        <div class="ab-cards-3">
            <div class="ab-card">
                <div class="ab-card-ico" aria-hidden="true"><x-icon name="file-search" size="23"/></div>
                <h3>Poznaj auto wcześniej</h3>
                <p>Zdjęcia, dane i dostępna historia przed przyjazdem.</p>
            </div>
            <div class="ab-card">
                <div class="ab-card-ico" aria-hidden="true"><x-icon name="scan-search" size="23"/></div>
                <h3>Sprawdź je na miejscu</h3>
                <p>Oględziny, jazda próbna i czas na Twoje pytania.</p>
            </div>
            <div class="ab-card">
                <div class="ab-card-ico" aria-hidden="true"><x-icon name="handshake" size="23"/></div>
                <h3>Ustal szczegóły zakupu</h3>
                <p>Formalności, wybrane usługi i sposób odbioru.</p>
            </div>
        </div>

        {{-- ===================== CERTICHECK ===================== --}}
        <div class="ab-panel">
            <div class="ab-panel-grid">
                <div>
                    <div class="ab-eyebrow">CertiCheck</div>
                    <h2 class="ab-h2">Więcej informacji<br>o wybranych autach.</h2>
                    <p class="ab-lead">Dodatkowe materiały pomagają poznać konkretny samochód jeszcze przed wizytą. Sprawdź zakres dostępny w wybranym ogłoszeniu.</p>
                    <a href="{{ route('certicheck.landing') }}" class="ab-link">Poznaj CertiCheck <x-icon name="arrow-right" size="15"/></a>
                </div>
                <div class="ab-mini">
                    <div class="ab-mini-card">
                        <div class="ab-card-ico" aria-hidden="true"><x-icon name="camera" size="20"/></div>
                        <h3>Zdjęcia stanu pojazdu</h3>
                        <p>Szczegółowe ujęcia wnętrza, nadwozia i ważnych elementów.</p>
                    </div>
                    <div class="ab-mini-card">
                        <div class="ab-card-ico" aria-hidden="true"><x-icon name="scan-line" size="20"/></div>
                        <h3>Pomiary lakieru</h3>
                        <p>Pomiar grubości powłoki lakierniczej w wielu punktach pojazdu.</p>
                    </div>
                    <div class="ab-mini-card">
                        <div class="ab-card-ico" aria-hidden="true"><x-icon name="file-text" size="20"/></div>
                        <h3>Dostępne dokumenty</h3>
                        <p>Wgląd w dokumenty i historię serwisową, jeśli są dostępne.</p>
                    </div>
                </div>
            </div>
            <p class="ab-note">Zakres materiałów zależy od auta. CertiCheck to nasz wewnętrzny standard oględzin, a nie opinia rzeczoznawcy.</p>
        </div>
    </div>
</section>

{{-- ===================== NASZE USŁUGI ===================== --}}
<section class="ab-services">
    <div class="ab-in">
        <div class="ab-services-head">
            <div class="ab-eyebrow">Nasze usługi</div>
            <h2 class="ab-h2">Wsparcie przy zakupie i po odbiorze auta.</h2>
            <p class="ab-lead">Zakres pomocy dobieramy do Twoich potrzeb.</p>
        </div>

        <div class="ab-cards-6">
            <div class="ab-srv">
                <span class="ab-srv-ico" aria-hidden="true"><x-icon name="coins" size="21"/></span>
                <div><h3>Kredyt i leasing</h3><p>Pomagamy zorganizować finansowanie zakupu.</p></div>
            </div>
            <div class="ab-srv">
                <span class="ab-srv-ico" aria-hidden="true"><x-icon name="file-text" size="21"/></span>
                <div><h3>Rejestracja auta</h3><p>Możemy zająć się formalnościami rejestracyjnymi.</p></div>
            </div>
            <div class="ab-srv">
                <span class="ab-srv-ico" aria-hidden="true"><x-icon name="shield" size="21"/></span>
                <div><h3>Ubezpieczenie OC / AC</h3><p>Pomagamy dobrać polisę do samochodu.</p></div>
            </div>
            <div class="ab-srv">
                <span class="ab-srv-ico" aria-hidden="true"><x-icon name="truck" size="21"/></span>
                <div><h3>Transport pod dom</h3><p>Ustalamy adres, termin i koszt dowozu.</p></div>
            </div>
            <div class="ab-srv">
                <span class="ab-srv-ico" aria-hidden="true"><x-icon name="wrench" size="21"/></span>
                <div><h3>Pierwszy serwis</h3><p>Organizujemy uzgodniony serwis eksploatacyjny.</p></div>
            </div>
            <div class="ab-srv">
                <span class="ab-srv-ico" aria-hidden="true"><x-icon name="shield-check" size="21"/></span>
                <div><h3>Gwarancja GetHelp</h3><p>Pomagamy wybrać dostępny pakiet ochrony.</p></div>
            </div>
        </div>
        <p class="ab-note">Zapytaj także o wymianę opon i usuwanie wgnieceń. Zakres i koszt usług ustalamy indywidualnie.</p>

        {{-- ===================== ZAKUP NA ODLEGŁOŚĆ ===================== --}}
        <div class="ab-panel">
            <div class="ab-panel-grid ab-remote-grid">
                <div>
                    <div class="ab-eyebrow">Zakup na odległość</div>
                    <h2 class="ab-h2">Mieszkasz dalej?<br>Poznaj auto na odległość.</h2>
                    <p class="ab-lead">Umów prezentację wideo, poproś o dodatkowe ujęcia i omów szczegóły auta. Jeśli zdecydujesz się na zakup, ustalimy formalności oraz odbiór lub transport.</p>
                    <a href="{{ route('contact') }}" class="ab-link">Zapytaj o zakup na odległość <x-icon name="arrow-right" size="15"/></a>
                </div>
                <div class="ab-remote-fig">
                    <img src="/img/about/laptop.webp" alt="Prezentacja auta przez wideo" width="1536" height="1024" loading="lazy" decoding="async">
                </div>
                <div class="ab-remote-list">
                    <div class="ab-remote-item"><span class="ab-card-ico" aria-hidden="true"><x-icon name="video" size="19"/></span> Prezentacja wideo</div>
                    <div class="ab-remote-item"><span class="ab-card-ico" aria-hidden="true"><x-icon name="camera" size="19"/></span> Dodatkowe zdjęcia</div>
                    <div class="ab-remote-item"><span class="ab-card-ico" aria-hidden="true"><x-icon name="truck" size="19"/></span> Ustalenie odbioru</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== JAK WYGLĄDA ZAKUP ===================== --}}
<section class="ab-steps">
    <div class="ab-in">
        <h2 class="ab-h2">Jak wygląda zakup?</h2>
        <div class="ab-steps-grid">
            @foreach([
                ['01', 'Wybierz auto',      'Sprawdź ofertę i zapytaj o szczegóły.'],
                ['02', 'Poznaj je bliżej',  'Oględziny, jazda próbna lub prezentacja wideo.'],
                ['03', 'Ustal warunki',     'Cena, dokumenty i wybrane usługi.'],
                ['04', 'Odbierz samochód',  'Na miejscu lub z uzgodnionym transportem.'],
            ] as [$num, $title, $desc])
            <div class="ab-step">
                <span class="ab-step-num">{{ $num }}</span>
                <h3>{{ $title }}</h3>
                <p>{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== CTA ===================== --}}
<section class="ab-cta-wrap">
    <div class="ab-in">
        <div class="ab-cta">
            <div>
                <h2>Porozmawiajmy<br>o Twoim kolejnym aucie.</h2>
                <p>Powiedz, czego szukasz i jaki masz budżet. Pomożemy sprawdzić dostępne możliwości.</p>
            </div>
            <div class="ab-cta-actions">
                <a href="tel:+48515440623" class="ab-btn ab-btn-primary"><x-icon name="phone" size="18"/> Zadzwoń 515 440 623</a>
                <a href="{{ route('catalog') }}" class="ab-cta-link">Zobacz dostępne auta <x-icon name="arrow-right" size="15"/></a>
            </div>
        </div>
    </div>
</section>

@endsection
