@extends('layouts.public')
@section('title','O nas')
@section('description','Poznaj historię CertiCars — platformy certyfikowanych samochodów używanych. Transparentność, jakość i pełna dokumentacja każdego pojazdu.')
@section('styles')
/* ===== ABOUT PAGE ===== */
.about-hero{background:linear-gradient(135deg,#060b18 0%,#080e1e 40%,#0b1530 100%);padding:88px 0 80px;position:relative;overflow:hidden}
.about-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 70% at 65% 30%,rgba(0,102,255,.2),transparent),radial-gradient(ellipse 40% 60% at 10% 80%,rgba(255,100,0,.08),transparent)}
.about-hero::after{content:'';position:absolute;bottom:-2px;left:0;right:0;height:64px;background:var(--bg);clip-path:polygon(0 100%,100% 100%,100% 20%,50% 100%,0 20%)}
/* Dot pattern */
.about-hero-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.about-hero-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center}
.about-breadcrumb{display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,.35);margin-bottom:20px}
.about-breadcrumb a{color:rgba(255,255,255,.35);text-decoration:none;transition:color .15s}
.about-breadcrumb a:hover{color:#fff}
.about-breadcrumb svg{width:10px;height:10px;stroke:rgba(255,255,255,.2);fill:none;stroke-width:2}
.about-hero-label{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--orange);margin-bottom:18px;display:flex;align-items:center;gap:10px}
.about-hero-label::before{content:'';width:24px;height:2px;background:var(--orange);border-radius:2px;flex-shrink:0}
.about-hero h1{font-size:54px;font-weight:900;color:#fff;letter-spacing:-.9px;line-height:1.04;margin-bottom:22px}
.about-hero h1 em{font-style:normal;color:var(--blue)}
.about-hero-desc{font-size:16px;color:rgba(255,255,255,.55);line-height:1.75;margin-bottom:40px;max-width:520px}
/* Right side — achievement cards */
.about-hero-right{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.ach-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);border-radius:16px;padding:22px;transition:all .25s;position:relative;overflow:hidden}
.ach-card:hover{background:rgba(0,102,255,.08);border-color:rgba(0,102,255,.3);transform:translateY(-2px)}
.ach-card::before{content:'';position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:radial-gradient(var(--blue),transparent 70%);opacity:0;transition:opacity .3s}
.ach-card:hover::before{opacity:.3}
.ach-icon{width:44px;height:44px;border-radius:12px;background:rgba(0,102,255,.15);display:flex;align-items:center;justify-content:center;margin-bottom:14px}
.ach-icon svg{width:22px;height:22px;stroke:var(--blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.ach-card h4{font-size:14px;font-weight:700;color:#fff;margin-bottom:6px;letter-spacing:-.1px}
.ach-card p{font-size:12px;color:rgba(255,255,255,.45);line-height:1.6}
.ach-card-lg{grid-column:1/-1;background:linear-gradient(135deg,rgba(0,102,255,.12),rgba(0,102,255,.06));border-color:rgba(0,102,255,.2);display:flex;align-items:center;gap:20px}
.ach-card-lg .ach-icon{width:56px;height:56px;flex-shrink:0;margin-bottom:0}
.ach-card-lg .ach-icon svg{width:28px;height:28px}
.ach-card-lg-body h4{font-size:15px;font-weight:800}
.ach-card-lg-body p{font-size:13px}
@keyframes spin{to{transform:rotate(360deg)}}

/* Values */
.about-values{padding:80px 0;background:var(--bg)}
.about-section-in{max-width:1200px;margin:0 auto;padding:0 24px}
.about-section-label{font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--blue);margin-bottom:12px;text-align:center}
.about-section-h{font-size:38px;font-weight:900;color:var(--text);letter-spacing:-.7px;text-align:center;margin-bottom:14px}
.about-section-sub{font-size:15px;color:var(--text-3);text-align:center;max-width:560px;margin:0 auto 56px}
.values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.val-card{background:#fff;border-radius:16px;padding:32px;border:1px solid var(--border-l);box-shadow:0 4px 16px rgba(0,0,0,.04);transition:all .2s;position:relative;overflow:hidden}
.val-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--blue);transform:scaleX(0);transform-origin:left;transition:transform .3s}
.val-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.08)}
.val-card:hover::before{transform:scaleX(1)}
.val-icon{width:52px;height:52px;background:var(--blue-bg);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:20px}
.val-icon svg{width:24px;height:24px;stroke:var(--blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.val-card h3{font-size:18px;font-weight:800;color:var(--text);margin-bottom:10px;letter-spacing:-.2px}
.val-card p{font-size:14px;color:var(--text-3);line-height:1.75}

/* How it works — timeline */
.about-how{padding:80px 0;background:#fff}
.timeline{display:grid;grid-template-columns:repeat(4,1fr);gap:0;position:relative;margin-top:56px}
.timeline::before{content:'';position:absolute;top:32px;left:calc(12.5%);right:calc(12.5%);height:2px;background:var(--border-l);z-index:0}
.tl-item{display:flex;flex-direction:column;align-items:center;text-align:center;padding:0 16px;position:relative;z-index:1}
.tl-num{width:64px;height:64px;border-radius:50%;background:#fff;border:2px solid var(--border-l);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:var(--text-3);margin-bottom:20px;position:relative;transition:all .3s}
.tl-item:first-child .tl-num,.tl-item:nth-child(2) .tl-num{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 6px 20px rgba(0,102,255,.35)}
.tl-icon{position:absolute;bottom:-2px;right:-2px;width:22px;height:22px;background:#fff;border:2px solid var(--border-l);border-radius:50%;display:flex;align-items:center;justify-content:center}
.tl-icon svg{width:11px;height:11px;stroke:var(--blue);fill:none;stroke-width:2.5}
.tl-item h4{font-size:15px;font-weight:700;color:var(--text);margin-bottom:8px;letter-spacing:-.1px}
.tl-item p{font-size:13px;color:var(--text-3);line-height:1.65}

/* CTA strip */
.about-cta{background:linear-gradient(135deg,#0052cc,#0066ff 50%,#1a7fff);padding:72px 0;text-align:center}
.about-cta h2{font-size:36px;font-weight:900;color:#fff;letter-spacing:-.6px;margin-bottom:14px}
.about-cta p{font-size:16px;color:rgba(255,255,255,.7);margin-bottom:36px}
.about-cta-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap}
.cta-btn-white{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--blue);padding:14px 28px;border-radius:50px;font-weight:700;font-size:14px;text-decoration:none;transition:all .2s;box-shadow:0 8px 24px rgba(0,0,0,.15)}
.cta-btn-white:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,0,0,.2)}
.cta-btn-outline{display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;padding:14px 28px;border-radius:50px;font-weight:700;font-size:14px;text-decoration:none;border:2px solid rgba(255,255,255,.4);transition:all .2s}
.cta-btn-outline:hover{border-color:#fff;background:rgba(255,255,255,.1)}

@media(max-width:900px){
    .about-hero-in{grid-template-columns:1fr}
    .about-hero h1{font-size:38px}
    .about-hero-right{display:none}
    .values-grid{grid-template-columns:1fr}
    .timeline{grid-template-columns:1fr 1fr;gap:32px}
    .timeline::before{display:none}
}
@media(max-width:600px){
    .timeline{grid-template-columns:1fr}
    .about-section-h{font-size:28px}
    .about-hero h1{font-size:30px}
    .about-hero-metrics{flex-direction:column}
    .about-metric{border-right:none;border-bottom:1px solid rgba(255,255,255,.08)}
    .about-metric:last-child{border-bottom:none}
}
@endsection
@section('content')

{{-- Hero --}}
<section class="about-hero">
    <div class="about-hero-dots"></div>
    <div class="about-hero-in">
        <div>
            <nav class="about-breadcrumb">
                <a href="{{ route('home') }}">Strona główna</a>
                <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                <span>O nas</span>
            </nav>
            <div class="about-hero-label">Nasza historia</div>
            <h1>Zmieniamy rynek<br>aut używanych.<br><em>Na lepsze.</em></h1>
            <p class="about-hero-desc">CertiCars to platforma komisowa, która przywraca zaufanie do rynku samochodów używanych. Każdy pojazd przechodzi naszą wielopunktową inspekcję — pełna dokumentacja, bez niespodzianek.</p>
        </div>
        <div class="about-hero-right">
            <div class="ach-card ach-card-lg">
                <div class="ach-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <div class="ach-card-lg-body">
                    <h4>Certyfikat CertiCheck</h4>
                    <p>Każde auto trafia do oferty z pełnym raportem inspekcji w formacie PDF — do pobrania przed zakupem.</p>
                </div>
            </div>
            <div class="ach-card">
                <div class="ach-icon">
                    <svg viewBox="0 0 24 24"><path d="M2 7l10-5 10 5"/><path d="M12 22V12"/><path d="M17 9.5 12 12 7 9.5"/><path d="m2 7 10 5 10-5"/></svg>
                </div>
                <h4>Pomiar lakieru</h4>
                <p>Każdy punkt nadwozia sprawdzony grubościomierzem.</p>
            </div>
            <div class="ach-card">
                <div class="ach-icon">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h4>Historia serwisowa</h4>
                <p>Kompletna dokumentacja z ASO i serwisów partnerskich.</p>
            </div>
            <div class="ach-card">
                <div class="ach-icon">
                    <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h4>Jazda próbna</h4>
                <p>Testuj każde auto przed decyzją — bez presji.</p>
            </div>
        </div>
    </div>
</section>


{{-- Values --}}
<section class="about-values">
    <div class="about-section-in">
        <div class="about-section-label">Nasze wartości</div>
        <h2 class="about-section-h">Dlaczego CertiCars?</h2>
        <p class="about-section-sub">Założyliśmy platformę, bo wiedzieliśmy, że rynek aut używanych potrzebuje transparentności i standardów, których wcześniej brakowało.</p>
        <div class="values-grid">
            <div class="val-card">
                <div class="val-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Transparentność</h3>
                <p>Każde auto ma pełną dokumentację — raport lakierniczy, historię serwisową i szczegółowy protokół inspekcji. Żadnych ukrytych wad.</p>
            </div>
            <div class="val-card">
                <div class="val-icon">
                    <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-9.953-9.953c-.549-.055-.997.398-.997.951v8a1 1 0 0 0 1 1h8z"/><path d="M3 20.659A10 10 0 0 0 15.041 3"/></svg>
                </div>
                <h3>Jakość inspekcji</h3>
                <p>Nasza kontrola obejmuje ponad 150 punktów technicznych, pomiar grubości lakieru i mapę uszkodzeń nadwozia — standard premium.</p>
            </div>
            <div class="val-card">
                <div class="val-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <h3>Uczciwa cena</h3>
                <p>Ceny ustalamy na podstawie aktualnej analizy rynku i stanu pojazdu. Bez zawyżania — tylko realna wartość każdego samochodu.</p>
            </div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="about-how">
    <div class="about-section-in">
        <div class="about-section-label">Jak działamy</div>
        <h2 class="about-section-h">Od przyjęcia do sprzedaży</h2>
        <p class="about-section-sub">Nasz proces jest powtarzalny, transparentny i nastawiony na jedno: byś kupił auto, które nie sprawi ci rozczarowań.</p>
        <div class="timeline">
            <div class="tl-item">
                <div class="tl-num">01
                    <div class="tl-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                </div>
                <h4>Przyjęcie pojazdu</h4>
                <p>Auto trafia do naszego salonu i przechodzi pierwszą ocenę wizualną oraz sprawdzenie historii.</p>
            </div>
            <div class="tl-item">
                <div class="tl-num">02
                    <div class="tl-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></div>
                </div>
                <h4>Inspekcja CertiCheck</h4>
                <p>150+ punkty kontrolne, pomiar lakieru, mapa uszkodzeń, odczyt diagnostyczny — protokół dla każdego auta.</p>
            </div>
            <div class="tl-item">
                <div class="tl-num">03
                    <div class="tl-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                </div>
                <h4>Dokumentacja i wycena</h4>
                <p>Przygotowujemy kompletny raport PDF i ustalamy cenę na podstawie stanu i analizy rynku.</p>
            </div>
            <div class="tl-item">
                <div class="tl-num">04
                    <div class="tl-icon"><svg viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg></div>
                </div>
                <h4>Trafia do oferty</h4>
                <p>Auto dostępne online z pełnym raportem. Kupujesz ze spokojem — wiesz dokładnie co kupujesz.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="about-cta">
    <div class="about-section-in">
        <h2>Gotowy na sprawdzone auto?</h2>
        <p>Przeglądaj ofertę certyfikowanych pojazdów lub skontaktuj się z nami bezpośrednio.</p>
        <div class="about-cta-btns">
            <a href="{{ route('catalog') }}" class="cta-btn-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Przeglądaj ofertę
            </a>
            <a href="{{ route('contact') }}" class="cta-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.21 12 19.79 19.79 0 0 1 1.14 3.38 2 2 0 0 1 3.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Skontaktuj się
            </a>
        </div>
    </div>
</section>

@endsection
