<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @hasSection('meta_title_full')
    <title>@yield('meta_title_full')</title>
    @else
    <title>@yield('title','Certyfikowane samochody używane') — CertiCars</title>
    @endif
    <meta name="description" content="@yield('description','CertiCars — platforma komisowa certyfikowanych samochodów używanych z pełną inspekcją techniczną.')">
    {{-- Domyslnie indeksujemy. Strony bez wartosci dla wyszukiwarki (np. lista
         obserwowanych, ktora zyje w localStorage i dla kazdego jest pusta)
         nadpisuja to przez @section('robots','noindex,follow'). --}}
    <meta name="robots" content="@yield('robots','index,follow')">
    <meta name="theme-color" content="#0066ff">
    <meta property="og:type" content="@yield('og_type','website')">
    <meta property="og:site_name" content="CertiCars">
    <meta property="og:locale" content="pl_PL">
    <meta property="og:title" content="@yield('og_title','CertiCars — certyfikowane samochody używane')">
    <meta property="og:description" content="@yield('og_description','Platforma komisowa certyfikowanych samochodów używanych z pełną inspekcją techniczną.')">
    <meta property="og:url" content="{{ url()->current() }}">
    {{-- Nowa nazwa pliku (-v2) jest celowa: Facebook i LinkedIn cache'uja
         podglad po URL obrazka, wiec nadpisanie starego og-default.jpg nie
         odswiezyloby juz udostepnionych linkow. --}}
    <meta property="og:image" content="@yield('og_image', asset('img/og-default-v2.jpg'))">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="CertiCars — pewne auta, przejrzyste opisy. Sprawdzone samochody używane.">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title','CertiCars — certyfikowane samochody używane')">
    <meta name="twitter:description" content="@yield('og_description','Platforma komisowa certyfikowanych samochodów używanych z pełną inspekcją techniczną.')">
    <meta name="twitter:image" content="@yield('og_image', asset('img/og-default-v2.jpg'))">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    <meta name="format-detection" content="telephone=no">
    @hasSection('extra_head')@yield('extra_head')@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@700;800;900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@700;800;900&display=swap" rel="stylesheet"></noscript>
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'AutoDealer',
        'name' => 'CertiCars',
        'description' => 'Platforma komisowa certyfikowanych samochodów używanych z pełną inspekcją techniczną.',
        'url' => url('/'),
        'logo' => asset('favicon.svg'),
        'image' => asset('img/og-default-v2.jpg'),
        'telephone' => '+48515440623',
        'email' => 'kontakt@certicars.pl',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Lipnik',
            'addressCountry' => 'PL',
        ],
        'openingHoursSpecification' => [
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday'], 'opens' => '09:00', 'closes' => '18:00'],
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Saturday','Sunday'], 'opens' => '10:00', 'closes' => '15:00'],
        ],
        'areaServed' => 'PL',
        'priceRange' => '$$',
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --blue:#0066ff;--blue-h:#0052cc;--blue-bg:#e8f1ff;
            --orange:#ff6400;--orange-bg:rgba(255,100,0,.1);
            --text:#0f0f10;--text-2:#4b5563;--text-3:#6b7280;--text-4:#9ca3af;
            --bg:#f7f7f8;--bg-2:#f0f0f2;--white:#fff;
            --border:#e5e5e7;--border-l:#eeeef0;
            --radius:12px;--radius-lg:16px;
            --green:#10b981;--green-bg:#ecfdf5;--green-border:#a7f3d0;--green-dark:#047857;
            --yellow:#f59e0b;--yellow-bg:#fffbeb;--yellow-dark:#b45309;
            --red:#ef4444;--red-bg:#fef2f2;
            --shadow-sm:0 1px 2px rgba(0,0,0,.04);
            --shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.04);
            --shadow-lg:0 8px 32px rgba(0,0,0,.08);
            --font-body:'Plus Jakarta Sans',system-ui,-apple-system,sans-serif;
            --font-heading:'Plus Jakarta Sans',system-ui,-apple-system,sans-serif;
        }
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{font-family:var(--font-body);color:var(--text);background:var(--white);line-height:1.55;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;overflow-x:hidden;max-width:100vw}
        h1,h2,h3,h4{font-family:var(--font-heading);letter-spacing:-.02em}
        a{color:inherit;text-decoration:none}
        img{max-width:100%;display:block}
        button{font-family:inherit;cursor:pointer}
        input,select,textarea{font-family:inherit}
        :focus-visible{outline:2px solid var(--blue);outline-offset:2px;border-radius:4px}
        .skip-link{position:absolute;left:-9999px;top:0;background:var(--blue);color:#fff;padding:12px 18px;font-weight:600;font-size:13px;z-index:9999}
        .skip-link:focus{left:0}
        .container{max-width:1200px;margin:0 auto;padding:0 24px}

        /* ============ TOP BAR ============ */
        /* Granat #0a1838 — ten sam odcien co hero i stopka. Wczesniej var(--blue). */
        .topbar{background:#0a1838;color:rgba(255,255,255,.9);font-size:13px;font-weight:400;line-height:1;border-bottom:none}
        .topbar-in{max-width:1200px;margin:0 auto;padding:0 24px;height:40px;display:flex;align-items:center;justify-content:space-between;gap:28px}
        .topbar-left,.topbar-right{display:flex;align-items:center;gap:20px}
        .topbar a,.topbar .tb-item{display:inline-flex;align-items:center;gap:7px;color:rgba(255,255,255,.9);transition:color .15s;text-decoration:none;font-size:13px;font-weight:500}
        .topbar a:hover{color:#fff}
        .topbar .tb-ico{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;opacity:.9}
        .topbar .tb-strong{color:#fff;font-weight:700;font-size:13.5px;letter-spacing:-.2px}
        .topbar .tb-sep{width:1px;height:16px;background:rgba(255,255,255,.35)}
        .topbar .tb-cta{display:inline-flex;align-items:center;gap:6px;color:#fff;font-size:13px;font-weight:600;text-decoration:none;transition:opacity .15s}
        .topbar .tb-cta:hover{opacity:.8}
        .topbar .tb-cta .tb-ico{stroke:#fff;opacity:1}

        /* ============ HEADER ============ */
        .header{background:#fff;border-bottom:1px solid var(--border-l);position:sticky;top:0;z-index:100;transition:box-shadow .2s,border-color .2s}
        .header.scrolled{box-shadow:0 4px 24px rgba(0,0,0,.06);border-bottom-color:transparent}
        .header-in{max-width:1200px;margin:0 auto;padding:0 24px;height:72px;display:flex;align-items:center;gap:40px}
        /* LOGO */
        .header-logo{display:flex;align-items:center;gap:14px;flex-shrink:0;margin-right:auto;text-decoration:none}
        .header-logo-badge{width:42px;height:42px;flex-shrink:0}
        .header-logo-wordmark{font-size:26px;font-weight:900;letter-spacing:-.6px;color:#0a0a0a;line-height:1;font-family:'Inter',sans-serif}
        .header-logo-wordmark span{color:var(--blue)}
        .header-logo-sep{width:1px;height:36px;background:#d4d4d8;flex-shrink:0;margin:0 2px}
        .header-logo-tagline{display:flex;flex-direction:column;gap:0;line-height:1.3}
        .header-logo-tagline span{font-size:12px;font-weight:400;color:#9ca3af;letter-spacing:0}
        .header-nav{display:flex;align-items:center;gap:2px;flex-wrap:nowrap;flex-shrink:0}
        /* `white-space:nowrap` + `flex-shrink:0` na kazdym linku - "O nas"
           i inne 2-slowowe pozycje nigdy nie zawijaja na dwie linie.
           Padding zmniejszony do 12px zeby wszystkie linki miescily sie
           w top navie na tighter viewportach (1280-1440). */
        .header-nav .nav-link{font-size:14.5px;font-weight:500;color:var(--text);padding:10px 12px;border-radius:8px;transition:color .15s,background .15s;line-height:1;white-space:nowrap;flex-shrink:0}
        .header-nav .nav-link:hover{color:var(--blue)}
        .header-nav .nav-link.active{color:var(--blue);font-weight:700;background:var(--blue-bg)}
        .header-cta{background:var(--blue);color:#fff;padding:12px 22px;border-radius:50px;font-weight:600;font-size:13.5px;display:inline-flex;align-items:center;gap:7px;transition:all .2s;line-height:1;flex-shrink:0;box-shadow:0 4px 14px rgba(0,102,255,.35)}
        .header-cta i{width:15px;height:15px;stroke-width:2.4}
        .header-cta:hover{background:var(--blue-h);color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.45);transform:translateY(-1px)}

        .mmb{display:none;background:none;border:none;width:42px;height:42px;align-items:center;justify-content:center;border-radius:8px;transition:background .15s;margin-left:auto}
        .mmb:hover{background:var(--bg)}
        .mmb-bars{position:relative;width:20px;height:14px}
        .mmb-bars span{position:absolute;left:0;right:0;height:2px;background:#000;border-radius:2px;transition:transform .25s,top .25s,opacity .2s}
        .mmb-bars span:nth-child(1){top:0}
        .mmb-bars span:nth-child(2){top:6px}
        .mmb-bars span:nth-child(3){top:12px}
        .mmb[aria-expanded="true"] .mmb-bars span:nth-child(1){top:6px;transform:rotate(45deg)}
        .mmb[aria-expanded="true"] .mmb-bars span:nth-child(2){opacity:0}
        .mmb[aria-expanded="true"] .mmb-bars span:nth-child(3){top:6px;transform:rotate(-45deg)}
        .nav-mobile{display:none}


        /* ============ FOOTER ============ */
        /* ============ FOOTER ============
           Premium polish pass: ambient blue glow at the top, larger brand
           wordmark balanced with a short trust tagline on the right, a
           four-up trust badge strip under the brand row, and a refined
           hours card with subtle inner highlight. Same 4-column main grid
           and bottom bar as before. */
        .footer{background:#0d1b3e;color:rgba(255,255,255,.6);padding:0;margin-top:0;position:relative;overflow:hidden;isolation:isolate}
        /* Top gradient hairline. */
        .footer::before{content:'';display:block;height:2px;background:linear-gradient(90deg,rgba(0,102,255,.25) 0%,#0066ff 40%,rgba(0,102,255,.25) 100%)}
        /* Two ambient blue glows: top-right + bottom-left for depth.
           Pointer-events:none keeps them inert; z-index -1 pushes them
           behind all content. */
        .footer::after{content:'';position:absolute;top:-220px;right:-180px;width:760px;height:520px;background:radial-gradient(ellipse 60% 60% at 50% 50%,rgba(30,80,180,.45) 0%,rgba(30,80,180,.15) 40%,rgba(30,80,180,0) 70%);pointer-events:none;z-index:-1}
        .footer > *{position:relative;z-index:1}
        /* Bottom-left ambient glow — absolute positioned BELOW the
           normal-flow rule above so .footer > * doesn't reset it. */
        .footer .footer-glow-bl{position:absolute;bottom:-280px;left:-220px;width:680px;height:520px;background:radial-gradient(ellipse 55% 55% at 50% 50%,rgba(30,80,180,.32) 0%,rgba(30,80,180,.1) 45%,rgba(30,80,180,0) 70%);pointer-events:none;z-index:0}

        /* Brand row — bigger logo, stronger separator below. */
        .footer-top{max-width:1200px;margin:0 auto;padding:52px 24px 28px;border-bottom:1px solid rgba(255,255,255,.08)}
        .footer-logo{display:inline-flex;align-items:center;gap:13px;text-decoration:none}
        .footer-logo-badge{width:42px;height:42px;flex-shrink:0;filter:drop-shadow(0 2px 10px rgba(0,102,255,.4))}
        .footer-logo-text{font-family:'Inter',sans-serif;font-size:28px;font-weight:900;letter-spacing:-.7px;color:#fff;line-height:1}
        .footer-logo-text span{color:#4ea3ff}

        /* Main grid — 4 balanced columns. Map moved out of Kontakt into
           its own full-width row below this grid, so Kontakt no longer
           needs extra width. O CertiCars stays widest for the copy. */
        .footer-in{max-width:1200px;margin:0 auto;padding:40px 24px 28px;display:grid;grid-template-columns:1.5fr 1.1fr 1fr 1fr;gap:48px;align-items:start}
        .footer h3{position:relative;color:#fff;font-size:12px;text-transform:uppercase;letter-spacing:1px;font-weight:800;margin:0 0 20px;padding-bottom:12px}
        .footer h3::after{content:'';position:absolute;bottom:0;left:0;width:28px;height:2px;background:#4ea3ff;border-radius:2px;box-shadow:0 0 10px rgba(78,163,255,.55)}
        .footer p,.footer li{font-size:13px;line-height:1.7;color:rgba(255,255,255,.55)}
        .footer a{font-size:13px;line-height:1.7;color:rgba(255,255,255,.65);text-decoration:none;transition:color .15s}
        .footer a:hover{color:#fff}
        .footer ul{list-style:none;padding:0;margin:0}

        /* Col 1 — O CertiCars + opening hours card. */
        .footer-brand p{font-size:13px;color:rgba(255,255,255,.6);line-height:1.7;margin:0 0 20px;max-width:360px}
        .footer-hours-card{position:relative;background:linear-gradient(180deg,rgba(255,255,255,.05) 0%,rgba(255,255,255,.025) 100%);border:1px solid rgba(255,255,255,.09);border-radius:14px;padding:18px 20px;box-shadow:inset 0 1px 0 rgba(255,255,255,.04)}
        .footer-hours-head{display:flex;align-items:center;gap:10px;color:#fff;font-size:11px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;margin-bottom:14px}
        .footer-hours-head-ico{flex-shrink:0;width:30px;height:30px;border-radius:50%;border:1px solid rgba(78,163,255,.3);background:rgba(78,163,255,.08);display:flex;align-items:center;justify-content:center}
        .footer-hours-head-ico svg{width:13px;height:13px;stroke:#7eb3ff;fill:none;stroke-width:2}
        .footer-hours{font-size:12.5px;color:rgba(255,255,255,.55);line-height:1}
        .footer-hours-row{display:flex;justify-content:space-between;align-items:baseline;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06)}
        .footer-hours-row:last-child{border-bottom:none;padding-bottom:0}
        .footer-hours-row:first-child{padding-top:0}
        .footer-hours-row .val{color:#fff;font-weight:700;font-variant-numeric:tabular-nums}

        /* Col 2 — Kontakt: phone/email/address rows + map preview card. */
        .footer-contact-item{display:flex;align-items:center;gap:12px;margin-bottom:14px}
        .footer-contact-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(180deg,rgba(78,163,255,.18) 0%,rgba(78,163,255,.08) 100%);border:1px solid rgba(78,163,255,.22);display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .footer-contact-icon svg{width:14px;height:14px;stroke:#7eb3ff;fill:none;stroke-width:2}
        .footer-contact-body{display:flex;flex-direction:column;gap:2px;min-width:0}
        .footer-contact-label{font-size:10.5px;font-weight:700;color:rgba(255,255,255,.42);letter-spacing:.5px;text-transform:uppercase}
        .footer-contact-value{font-size:13.5px;font-weight:700;color:#fff;line-height:1.35;word-break:break-word;transition:color .15s}
        a.footer-contact-value:hover{color:#7eb3ff}

        /* Map row — full-width band inside the footer, between the
           .footer-in column grid and the .footer-btm legal bar. Uses
           a 2×2 OpenStreetMap tile mosaic loaded as <img> tags. No
           iframe, no API key, no CSP changes — tiles load over
           img-src 'self' data: blob: https: which is already allowed.
           A bold blue pin marker with attached "CertiCars" badge sits
           dead-centre over the pinpoint. */
        .footer-map-row{max-width:1200px;margin:0 auto;padding:0 24px 32px}
        .footer-map-card{position:relative;border-radius:18px;overflow:hidden;border:1px solid rgba(78,163,255,.28);box-shadow:0 8px 28px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.05);height:300px;background:linear-gradient(135deg,#11264f 0%,#0a1a3c 100%)}
        .footer-map-tiles{position:absolute;inset:0;overflow:hidden}
        .footer-map-tile{position:absolute;width:256px;height:256px;display:block;pointer-events:none}
        /* 6×2 z=15 mosaic — 1536 × 512 px of map content fills the
           card edge-to-edge on any container width up to ~1280 px
           (no dark side gutters). Pin pixel (221.8, 266) sits in
           col 2 / row 1 of the grid → tile c2 is at calc(50% − 222 px)
           so the marker overlay's 50%/50% centre lands on the pin. */
        .footer-map-tile.r0{top:calc(50% - 266px)}
        .footer-map-tile.r1{top:calc(50% - 10px)}
        .footer-map-tile.c0{left:calc(50% - 734px)}
        .footer-map-tile.c1{left:calc(50% - 478px)}
        .footer-map-tile.c2{left:calc(50% - 222px)}
        .footer-map-tile.c3{left:calc(50% + 34px)}
        .footer-map-tile.c4{left:calc(50% + 290px)}
        .footer-map-tile.c5{left:calc(50% + 546px)}
        /* Very subtle vignette so the marker pops without darkening
           the map content. */
        .footer-map-overlay{position:absolute;inset:0;pointer-events:none;background:
            radial-gradient(ellipse 75% 80% at 50% 50%,rgba(13,27,62,0) 0%,rgba(13,27,62,.18) 100%)}

        /* CertiCars marker — drop-pin + speech-bubble label. The pin
           sits exactly on the pinpoint; the "CertiCars" badge hangs
           below the pin like a Google-Maps label, joined by a small
           tail triangle. */
        .footer-map-marker{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);z-index:2;display:flex;flex-direction:column;align-items:center;gap:10px;pointer-events:none}
        .footer-map-marker-pin{position:relative;display:flex;align-items:center;justify-content:center;filter:drop-shadow(0 6px 14px rgba(0,102,255,.7))}
        .footer-map-marker-pin::before{content:'';position:absolute;width:76px;height:76px;border-radius:50%;background:radial-gradient(circle,rgba(0,102,255,.55) 0%,rgba(0,102,255,0) 70%);animation:footerPinPulse 2.2s ease-in-out infinite}
        .footer-map-marker-pin svg{width:46px;height:46px;color:#0066ff;position:relative;z-index:1}
        .footer-map-marker-label{position:relative;background:#0066ff;color:#fff;font-size:13px;font-weight:800;letter-spacing:.1px;padding:8px 16px;border-radius:8px;box-shadow:0 6px 18px rgba(0,102,255,.5),0 1px 3px rgba(0,0,0,.22);white-space:nowrap;line-height:1}
        .footer-map-marker-label::before{content:'';position:absolute;left:50%;top:-5px;transform:translateX(-50%) rotate(45deg);width:10px;height:10px;background:#0066ff;border-radius:1px}
        @keyframes footerPinPulse{0%,100%{transform:scale(1);opacity:.6}50%{transform:scale(1.45);opacity:.15}}

        /* Col 3 & 4 — Nav + Info lists with blue chevron bullets. */
        .footer-nav-list li{padding:0;margin:0}
        .footer-nav-list a{display:inline-flex;align-items:center;gap:8px;padding:5px 0;font-size:13px;color:rgba(255,255,255,.65);transition:color .15s,transform .15s}
        .footer-nav-list a svg{width:11px;height:11px;stroke:#4ea3ff;fill:none;stroke-width:2.4;flex-shrink:0;transition:transform .15s}
        .footer-nav-list a:hover{color:#fff}
        .footer-nav-list a:hover svg{transform:translateX(3px)}

        /* Bottom legal bar — 3-zone grid with stronger top separator.
           Center disclaimer is forced to a single line on desktop via
           white-space:nowrap; tighter font (11 px) + tighter gap keep
           it fitting at the 1200 px container even after the longer
           legal copy was added. Wraps only on tablet/mobile where the
           whole bar restacks to a single centred column. */
        .footer-btm{max-width:1200px;margin:0 auto;padding:22px 24px 26px;border-top:1px solid rgba(255,255,255,.1);font-size:11px;color:rgba(255,255,255,.48);display:grid;grid-template-columns:auto 1fr auto;gap:20px;align-items:center}
        .footer-btm-left,.footer-btm-mid{display:inline-flex;align-items:center;gap:8px;line-height:1.55}
        .footer-btm-mid{justify-self:center;text-align:center;color:rgba(255,255,255,.42);white-space:nowrap}
        .footer-btm-left svg,.footer-btm-mid svg{width:13px;height:13px;stroke:rgba(255,255,255,.55);fill:none;stroke-width:2;flex-shrink:0}
        .footer-btm-verified{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border:1.5px solid rgba(78,163,255,.4);background:rgba(78,163,255,.1);border-radius:50px;color:#7eb3ff;font-size:12px;font-weight:700;letter-spacing:.3px;white-space:nowrap;transition:background .15s,border-color .15s,color .15s,box-shadow .15s}
        .footer-btm-verified:hover{background:rgba(78,163,255,.2);border-color:rgba(78,163,255,.6);color:#fff;box-shadow:0 4px 14px rgba(78,163,255,.25)}
        .footer-btm-verified svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.2}

        /* Tablet: 2-col main grid. Map row keeps its full-width band.
           Bottom bar restacks centred, and the legal disclaimer can
           wrap (single-line is only required on desktop). */
        @media(max-width:900px){
            .footer-top{padding:36px 24px 20px}
            .footer-in{grid-template-columns:1fr 1fr;gap:36px;padding:30px 24px 24px}
            .footer-map-row{padding:0 24px 28px}
            .footer-map-card{height:260px}
            .footer-btm{grid-template-columns:1fr;gap:10px;text-align:center;padding:18px 24px 22px}
            .footer-btm-left,.footer-btm-mid{justify-self:center;justify-content:center}
            .footer-btm-mid{white-space:normal;max-width:560px}
            .footer-btm-verified{justify-self:center}
        }
        /* Mobile: full stack, 20 px gutter to match the rest of the site. */
        @media(max-width:600px){
            .footer-top{padding:28px 20px 18px}
            .footer-logo-text{font-size:22px}
            .footer-in{grid-template-columns:1fr;gap:30px;padding:24px 20px 20px}
            .footer-brand p{max-width:none}
            .footer-map-row{padding:0 20px 24px}
            .footer-map-card{height:220px;border-radius:14px}
            .footer-map-marker-pin svg{width:40px;height:40px}
            .footer-map-marker-label{font-size:12px;padding:7px 13px}
            .footer-btm{padding:16px 20px 20px}
        }

        /* ============ COMPONENTS ============ */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:12px 20px;border-radius:10px;font-size:14px;font-weight:600;font-family:inherit;border:none;cursor:pointer;transition:all .15s;text-decoration:none;line-height:1}
        .btn i{width:16px;height:16px;stroke-width:2.2}
        .btn-blue{background:var(--blue);color:#fff}
        .btn-blue:hover{background:var(--blue-h);color:#fff}
        .btn-dark{background:#0a0a0a;color:#fff}
        .btn-dark:hover{background:#1a1a1a;color:#fff}
        .btn-outline{background:#fff;border:1px solid var(--border);color:var(--text)}
        .btn-outline:hover{background:var(--bg);border-color:var(--text-4)}
        .btn-ghost{background:transparent;color:var(--text)}
        .btn-ghost:hover{background:var(--bg)}
        .btn-lg{padding:14px 26px;font-size:15px;border-radius:12px}
        .btn-pill{border-radius:50px}

        .badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:50px;font-size:11px;font-weight:600;line-height:1}
        .badge i{width:12px;height:12px;stroke-width:2.4}
        .badge-green{background:var(--green-bg);color:var(--green-dark);border:1px solid var(--green-border)}
        .badge-yellow{background:var(--yellow-bg);color:var(--yellow-dark);border:1px solid #fde68a}
        .badge-blue{background:var(--blue-bg);color:var(--blue);border:1px solid #bfdbfe}
        .badge-gray{background:var(--bg);color:var(--text-2);border:1px solid var(--border)}
        .badge-dark{background:#0a0a0a;color:#fff}

        /* ============ VCARD (shared vertical car card) ============
           Used by Podobne pojazdy on single-car page (resources/views/catalog/show.blade.php)
           and any other surface that imports this layout. Card hover effects
           intentionally removed — see catalog/index.blade.php for rationale.
           The vcard-link button keeps its own hover (it's a real CTA). */
        .vcard{background:#fff;border-radius:14px;overflow:hidden;border:1.5px solid var(--border-l);box-shadow:0 4px 16px rgba(0,0,0,.05),0 1px 4px rgba(0,0,0,.04);text-decoration:none;display:flex;flex-direction:column;color:inherit}
        .vcard-img{position:relative;width:100%;aspect-ratio:16/10;overflow:hidden;background:var(--bg)}
        .vcard-img img{width:100%;height:100%;object-fit:cover}
        .vcard-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--bg)}
        .vcard-placeholder svg{width:40px;height:40px;stroke:var(--border);stroke-width:1.5;fill:none}
        .vcard-badge{position:absolute;top:12px;left:10px;background:var(--orange);color:#fff;padding:7px 16px 7px 22px;font-size:11px;font-weight:800;display:inline-flex;align-items:center;gap:5px;letter-spacing:.8px;text-transform:uppercase;box-shadow:0 4px 12px rgba(255,100,0,.4);clip-path:polygon(10px 0,100% 0,100% 100%,10px 100%,0 50%)}
        .vcard-badge::before{content:'';position:absolute;left:13px;top:50%;transform:translateY(-50%);width:6px;height:6px;border-radius:50%;background:#fff}
        .certi-check{position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);color:#fff;padding:5px 10px;border-radius:8px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:5px;letter-spacing:.3px}
        .certi-check svg{width:12px;height:12px;stroke:#4ea3ff;stroke-width:2.5;fill:none}
        .vcard-body{padding:20px 22px;flex:1;display:flex;flex-direction:column}
        .vcard-title{font-size:16px;font-weight:800;color:#000;margin-bottom:8px;letter-spacing:-.2px;line-height:1.3}
        .vcard-specs{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:16px}
        .vcard-specs span{background:#f0f2f5;padding:4px 10px;border-radius:8px;font-size:12px;color:#374151;display:inline-flex;align-items:center;gap:4px;font-weight:500;border:1px solid #e5e7eb}
        .vcard-specs span svg{width:12px;height:12px;stroke:#6b7280;stroke-width:1.8;fill:none}
        .vcard-bottom{margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid var(--border-l);gap:10px;flex-wrap:wrap}
        .vcard-price{font-size:21px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1.1;white-space:nowrap}
        .vcard-price small{display:block;font-size:11px;font-weight:500;color:var(--text-3);letter-spacing:0;margin-top:1px}
        /* vcard-link is a real CTA button — keep its own hover state, but no
           longer driven by the parent .vcard:hover (which has been removed). */
        .vcard-link{font-size:12px;font-weight:700;color:#fff;background:var(--blue);padding:9px 16px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;transition:background .15s;white-space:nowrap;flex-shrink:0}
        .vcard-link:hover{background:var(--blue-h)}
        .vcard-link svg{width:12px;height:12px;stroke:#fff;stroke-width:2.4;fill:none}
        /* Narrow cards (carousel on tablet, listing on mobile): if price + CTA
           don't fit side-by-side, stack them so neither has to break onto
           multiple lines. flex-wrap on .vcard-bottom + width:100% on the CTA
           below the wrap point. */
        @media(max-width:780px){
            .vcard-bottom{flex-direction:column;align-items:stretch;gap:12px}
            .vcard-link{justify-content:center;width:100%}
        }

        .section{padding:80px 0}
        .section-head{margin-bottom:32px;text-align:center}
        .section-title{font-size:32px;font-weight:800;color:var(--text);margin-bottom:8px;letter-spacing:-.6px}
        .section-sub{font-size:16px;color:var(--text-2);max-width:640px;margin:0 auto}

        /* ============ RESPONSIVE ============ */
        @media(max-width:1024px){
            .topbar-left .tb-hide-lg,.topbar-right .tb-hide-lg{display:none}
            .header-nav .nav-link{padding:10px 12px;font-size:14px}
            .header-in{gap:24px}
        }
        @media(max-width:900px){
            .topbar{display:none}
            .header-cta span.hide-md{display:none}
            .section-title{font-size:26px}
        }
        @media(max-width:768px){
            .mmb{display:flex}
            .header-nav,.header-cta{display:none}
            .header-in{height:64px;padding:0 20px;gap:0}
            .header-logo{margin-right:0;min-width:0}
            .header-logo-tagline span{font-size:10px}
            /* Ponizej 360px dopisek przy logo rozpychal naglowek i wypychal
               hamburgera poza ekran (cala strona jechala w prawo o 30px na
               iPhone SE). aria-label na logo niesie te sama informacje. */
            @media(max-width:359px){
                .header-logo-tagline{display:none}
            }
            .nav-mobile{position:fixed;top:64px;left:0;right:0;bottom:0;background:#fff;padding:24px 20px;display:none;flex-direction:column;gap:4px;z-index:99;overflow-y:auto;animation:slideDown .25s ease}
            .nav-mobile.open{display:flex}
            .nav-mobile .nav-link{font-size:16px;padding:16px 18px;border-radius:12px;color:var(--text);font-weight:500;border:1px solid var(--border-l);display:flex;align-items:center;gap:10px}
            .nav-mobile .nav-link.active{background:var(--blue-bg);border-color:var(--blue-bg);color:var(--blue);font-weight:600}
            .nav-mobile-cta{margin-top:12px;background:var(--blue)!important;color:#fff!important;border:none!important;justify-content:center;font-weight:600!important}
            .nav-mobile-contact{margin-top:auto;padding-top:24px;border-top:1px solid var(--border-l);display:flex;flex-direction:column;gap:12px;font-size:14px;color:var(--text-2)}
            .nav-mobile-contact a{display:flex;align-items:center;gap:10px;color:var(--text-2)}
            .nav-mobile-contact i{width:16px;height:16px;color:var(--blue)}
            /* Footer collapses to 2-col at 900px and to 1-col at 600px —
               see the dedicated footer rules above; don't re-declare here. */
            .section{padding:48px 0}
            .section-title{font-size:24px}
            .container{padding:0 20px}
        }
        @keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
        @keyframes floatPulse{0%,100%{box-shadow:0 6px 20px rgba(0,102,255,.4),0 0 0 0 rgba(0,102,255,.45)}50%{box-shadow:0 6px 20px rgba(0,102,255,.4),0 0 0 12px rgba(0,102,255,0)}}

        .float-call{position:fixed;bottom:24px;right:24px;width:56px;height:56px;background:var(--blue);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(0,102,255,.4);z-index:60;animation:floatPulse 2.5s ease-in-out infinite;transition:transform .15s}
        .float-call:hover{transform:translateY(-2px) scale(1.05)}
        .float-call svg{width:22px;height:22px}
        @media(max-width:768px){.float-call{bottom:18px;right:18px;width:52px;height:52px}}
@yield('styles')
    </style>
</head>
<body>
    <a href="#main" class="skip-link">Przejdź do treści</a>

    <div class="topbar" role="complementary">
        <div class="topbar-in">
            <div class="topbar-left">
                <a href="tel:+48515440623">
                    <x-icon name="phone" size="14" class="tb-ico"/>
                    <span class="tb-strong">+48 515 440 623</span>
                </a>
                <span class="tb-sep"></span>
                <a href="mailto:kontakt@certicars.pl" class="tb-hide-lg">
                    <x-icon name="mail" size="14" class="tb-ico"/>
                    kontakt@certicars.pl
                </a>
            </div>
            <div class="topbar-right">
                <span class="tb-item tb-hide-lg">
                    <x-icon name="clock" size="14" class="tb-ico"/>
                    Pn–Pt <span class="tb-strong">9:00–18:00</span>
                    <span style="opacity:.55;margin:0 4px">·</span>
                    Sb–Nd <span class="tb-strong">10:00–15:00</span>
                </span>
                <span class="tb-sep tb-hide-lg"></span>
                <span class="tb-item">
                    <x-icon name="map-pin" size="14" class="tb-ico"/>
                    Lipnik
                </span>
            </div>
        </div>
    </div>

    <header class="header" id="siteHeader">
        <div class="header-in">
            <a href="{{ route('home') }}" class="header-logo" aria-label="CertiCars — strona główna">
                <svg class="header-logo-badge" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/>
                    <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="header-logo-wordmark">Certi<span>Cars</span></span>
                <span class="header-logo-sep" aria-hidden="true"></span>
                <span class="header-logo-tagline" aria-label="Sprawdzone samochody używane">
                    <span>Sprawdzone</span>
                    <span>samochody używane</span>
                </span>
            </a>

            <nav class="header-nav" aria-label="Główna nawigacja">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home')?'active':'' }}">Strona główna</a>
                <a href="{{ route('catalog') }}" class="nav-link {{ request()->routeIs('catalog*')?'active':'' }}">Oferta</a>
                <a href="{{ route('certicheck.landing') }}" class="nav-link {{ request()->routeIs('certicheck.landing')?'active':'' }}">CertiCheck</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about')?'active':'' }}">O nas</a>
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact')?'active':'' }}">Kontakt</a>
                <a href="{{ route('favorites') }}" class="nav-link {{ request()->routeIs('favorites')?'active':'' }}" id="navFavLink" style="display:flex;align-items:center;gap:6px">
                    Obserwowane
                    <span id="navFavBadge" style="display:none;background:var(--orange);color:#fff;font-size:10px;font-weight:800;min-width:18px;height:18px;border-radius:50px;padding:0 5px;align-items:center;justify-content:center">0</span>
                </a>
            </nav>

            <a href="{{ route('catalog') }}" class="header-cta"><i data-lucide="search" aria-hidden="true"></i> Znajdź auto</a>

            <button class="mmb" type="button" aria-label="Otwórz menu" aria-expanded="false" aria-controls="navMobile" onclick="cmToggle(this)">
                <span class="mmb-bars" aria-hidden="true"><span></span><span></span><span></span></span>
            </button>
        </div>

        <nav class="nav-mobile" id="navMobile" aria-label="Menu mobilne">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home')?'active':'' }}">
                <x-icon name="home" size="16"/>
                Strona główna
            </a>
            <a href="{{ route('catalog') }}" class="nav-link {{ request()->routeIs('catalog*')?'active':'' }}">
                <x-icon name="car" size="16"/>
                Oferta samochodów
            </a>
            <a href="{{ route('certicheck.landing') }}" class="nav-link {{ request()->routeIs('certicheck.landing')?'active':'' }}">
                <x-icon name="badge-check" size="16"/>
                CertiCheck
            </a>
            <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about')?'active':'' }}">
                <x-icon name="info" size="16"/>
                O nas
            </a>
            <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact')?'active':'' }}">
                <x-icon name="phone" size="16"/>
                Kontakt
            </a>
            <a href="{{ route('favorites') }}" class="nav-link" id="navMobFavLink" style="position:relative">
                <x-icon name="heart" size="16"/>
                Obserwowane
                <span id="navMobFavCount" style="background:#0066ff;color:#fff;font-size:11px;font-weight:700;min-width:20px;height:20px;border-radius:10px;display:none;align-items:center;justify-content:center;padding:0 6px;margin-left:auto">0</span>
            </a>
            <a href="{{ route('catalog') }}" class="nav-link nav-mobile-cta">
                <x-icon name="search" size="16"/>
                Znajdź auto
            </a>
            <div class="nav-mobile-contact">
                <a href="tel:+48515440623">
                    <x-icon name="phone" size="16"/>
                    +48 515 440 623
                </a>
                <a href="mailto:kontakt@certicars.pl">
                    <x-icon name="mail" size="16"/>
                    kontakt@certicars.pl
                </a>
            </div>
        </nav>
    </header>

    <main id="main">@yield('content')</main>

    <a href="tel:+48515440623" class="float-call" aria-label="Zadzwoń +48 515 440 623">
        <x-icon name="phone" size="28" :strokeWidth="2.2"/>
    </a>




    <footer class="footer" id="kontakt">
        {{-- Bottom-left ambient glow (top-right glow comes from .footer::after). --}}
        <div class="footer-glow-bl" aria-hidden="true"></div>

        {{-- Brand row. --}}
        <div class="footer-top">
            <a href="{{ route('home') }}" class="footer-logo">
                <svg class="footer-logo-badge" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="footer-logo-text">Certi<span>Cars</span></span>
            </a>
        </div>

        <div class="footer-in">
            {{-- Col 1: O CertiCars — description + opening hours card. --}}
            <div class="footer-brand">
                <h3>O CertiCars</h3>
                <p>Samochody używane z jasnym opisem stanu, pochodzenia i wyposażenia. Przy wybranych pojazdach dostępny jest rozszerzony raport CertiCheck, który pomaga lepiej ocenić auto jeszcze przed przyjazdem.</p>
                <div class="footer-hours-card">
                    <div class="footer-hours-head">
                        <span class="footer-hours-head-ico"><x-icon name="clock" size="13"/></span>
                        <span>Godziny otwarcia</span>
                    </div>
                    <div class="footer-hours">
                        <div class="footer-hours-row"><span>Poniedziałek – Piątek</span><span class="val">9:00 – 18:00</span></div>
                        <div class="footer-hours-row"><span>Sobota – Niedziela</span><span class="val">10:00 – 15:00</span></div>
                    </div>
                </div>
            </div>

            {{-- Col 2: Kontakt — phone / email / address + map preview card. --}}
            <div>
                <h3>Kontakt</h3>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><x-icon name="phone" size="14"/></div>
                    <div class="footer-contact-body">
                        <span class="footer-contact-label">Telefon</span>
                        <a href="tel:+48515440623" class="footer-contact-value">+48 515 440 623</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><x-icon name="mail" size="14"/></div>
                    <div class="footer-contact-body">
                        <span class="footer-contact-label">E-mail</span>
                        <a href="mailto:kontakt@certicars.pl" class="footer-contact-value">kontakt@certicars.pl</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><x-icon name="map-pin" size="14"/></div>
                    <div class="footer-contact-body">
                        <span class="footer-contact-label">Adres</span>
                        <span class="footer-contact-value">Lipnik</span>
                    </div>
                </div>

                {{-- Map moved OUT of the Kontakt column. It now lives in
                     its own full-width .footer-map-row below the column
                     grid so the location reads as a proper preview
                     instead of a thumbnail. --}}
            </div>

            {{-- Col 3: Nawigacja. --}}
            <div>
                <h3>Nawigacja</h3>
                <ul class="footer-nav-list">
                    <li><a href="{{ route('home') }}"><x-icon name="chevron-right" size="11"/>Strona główna</a></li>
                    <li><a href="{{ route('catalog') }}"><x-icon name="chevron-right" size="11"/>Oferta samochodów</a></li>
                    <li><a href="{{ route('certicheck.landing') }}"><x-icon name="chevron-right" size="11"/>CertiCheck</a></li>
                    <li><a href="{{ route('about') }}"><x-icon name="chevron-right" size="11"/>Jak wygląda zakup</a></li>
                    <li><a href="{{ route('about') }}"><x-icon name="chevron-right" size="11"/>O nas</a></li>
                    <li><a href="{{ route('contact') }}"><x-icon name="chevron-right" size="11"/>Kontakt</a></li>
                </ul>
            </div>

            {{-- Col 4: Informacje. --}}
            <div>
                <h3>Informacje</h3>
                <ul class="footer-nav-list">
                    <li><a href="#"><x-icon name="chevron-right" size="11"/>Polityka prywatności</a></li>
                    <li><a href="#"><x-icon name="chevron-right" size="11"/>Regulamin</a></li>
                    <li><a href="#"><x-icon name="chevron-right" size="11"/>Pliki cookies</a></li>
                </ul>
            </div>
        </div>

        {{-- Wide location row. 2×2 OpenStreetMap tile mosaic centred
             on the CertiCars pin (53.3502947 N, 14.9399374 E). Tiles
             load as plain <img> tags over img-src 'https:' — no
             iframe, no API key, no CSP changes, no env vars. A bold
             blue drop-pin sits dead-centre with a "CertiCars" badge
             hanging below it like a Google-Maps label. --}}
        <div class="footer-map-row">
            <div class="footer-map-card">
                <div class="footer-map-tiles" aria-hidden="true">
                    @foreach([[0,17741],[1,17742],[2,17743],[3,17744],[4,17745],[5,17746]] as [$ci,$tx])
                        <img class="footer-map-tile r0 c{{ $ci }}" src="https://tile.openstreetmap.org/15/{{ $tx }}/10620.png" alt="" loading="lazy" decoding="async" width="256" height="256" onerror="this.style.display='none'">
                        <img class="footer-map-tile r1 c{{ $ci }}" src="https://tile.openstreetmap.org/15/{{ $tx }}/10621.png" alt="" loading="lazy" decoding="async" width="256" height="256" onerror="this.style.display='none'">
                    @endforeach
                </div>
                <div class="footer-map-overlay" aria-hidden="true"></div>
                <div class="footer-map-marker" aria-label="Lokalizacja: CertiCars">
                    <span class="footer-map-marker-pin" aria-hidden="true">
                        <x-icon name="map-pin" size="46" :strokeWidth="2"/>
                    </span>
                    <span class="footer-map-marker-label">CertiCars</span>
                </div>
            </div>
        </div>

        {{-- Legal bar — 3 zones: copyright | civil-code disclaimer | verified dealer pill. --}}
        <div class="footer-btm">
            <span class="footer-btm-left">
                <x-icon name="shield-check" size="13"/>
                © {{ date('Y') }} CertiCars. Wszelkie prawa zastrzeżone.
            </span>
            <span class="footer-btm-mid">
                <x-icon name="info" size="13"/>
                Treści na stronie mają charakter informacyjny i nie stanowią oferty w rozumieniu art. 66 §1 Kodeksu cywilnego.
            </span>
            <span class="footer-btm-verified">
                <x-icon name="badge-check" size="13"/>
                Zweryfikowany dealer
            </span>
        </div>
    </footer>


    <script>
        var _menuScrollY=0;
        function cmToggle(btn){
            const n=document.getElementById('navMobile');
            const o=n.classList.toggle('open');
            btn.setAttribute('aria-expanded',o);
            if(o){
                _menuScrollY=window.scrollY;
                document.body.style.overflow='hidden';
                document.body.style.position='fixed';
                document.body.style.top='-'+_menuScrollY+'px';
                document.body.style.left='0';
                document.body.style.right='0';
                document.body.style.width='100%';
            }else{
                document.body.style.overflow='';
                document.body.style.position='';
                document.body.style.top='';
                document.body.style.left='';
                document.body.style.right='';
                document.body.style.width='';
                window.scrollTo(0,_menuScrollY);
            }
        }
        (function(){
            const h=document.getElementById('siteHeader');
            const onScroll=()=>{h.classList.toggle('scrolled',window.scrollY>4)};
            window.addEventListener('scroll',onScroll,{passive:true});onScroll();
        })();
        window.addEventListener('DOMContentLoaded',()=>{if(window.lucide)lucide.createIcons()});
    </script>
    <script>
    // ── Ulubione (localStorage) ──────────────────────────
    const FAV_KEY = 'certicars_favs';
    const getFavs = () => JSON.parse(localStorage.getItem(FAV_KEY) || '[]');
    const setFavs = v => localStorage.setItem(FAV_KEY, JSON.stringify(v));

    (function(){
        const favs = getFavs();
        document.querySelectorAll('[data-id]').forEach(btn => {
            if (favs.includes(+btn.dataset.id)) btn.classList.add('active');
        });
        const badge = document.getElementById('navFavBadge');
        if (badge) { badge.textContent = favs.length; badge.style.display = favs.length ? 'flex' : 'none'; }
        const mobBadge = document.getElementById('navMobFavCount');
        if (mobBadge) { mobBadge.textContent = favs.length; mobBadge.style.display = favs.length ? 'flex' : 'none'; }
    })();

    window.toggleFav = function(e, id) {
        e.preventDefault(); e.stopPropagation();
        let favs = getFavs();
        if (favs.includes(id)) {
            favs = favs.filter(f => f !== id);
            document.querySelectorAll(`[data-id="${id}"]`).forEach(b => b.classList.remove('active'));
            if (window.csTrack) window.csTrack('favorite_remove', {}, id);
        } else {
            favs.push(id);
            document.querySelectorAll(`[data-id="${id}"]`).forEach(b => b.classList.add('active'));
            if (window.csTrack) window.csTrack('favorite_add', {}, id);
        }
        setFavs(favs);
        const badge = document.getElementById('navFavBadge');
        if (badge) { badge.textContent = favs.length; badge.style.display = favs.length ? 'flex' : 'none'; }
        const mobBadge = document.getElementById('navMobFavCount');
        if (mobBadge) { mobBadge.textContent = favs.length; mobBadge.style.display = favs.length ? 'flex' : 'none'; }
    };
    </script>

    {{-- Lightbox --}}
    <style>
    .lb-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:1100;display:none;align-items:center;justify-content:center;animation:lb-fade .15s}
    .lb-backdrop.open{display:flex}
    @keyframes lb-fade{from{opacity:0}to{opacity:1}}
    .lb-stage{position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:center}
    .lb-img{max-width:92%;max-height:86%;object-fit:contain;border-radius:6px;box-shadow:0 20px 60px rgba(0,0,0,.4)}
    .lb-caption{position:absolute;bottom:18px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.7);color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;max-width:80%;text-align:center}
    .lb-count{position:absolute;top:18px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.55);color:#fff;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600}
    .lb-x,.lb-nav{position:absolute;background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.15);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s}
    .lb-x:hover,.lb-nav:hover{background:rgba(255,255,255,.2)}
    .lb-x{top:18px;right:18px}
    .lb-x svg,.lb-nav svg{width:22px;height:22px}
    .lb-nav{top:50%;transform:translateY(-50%)}
    .lb-nav.prev{left:18px}.lb-nav.next{right:18px}
    .lb-thumbs{position:absolute;bottom:70px;left:0;right:0;display:flex;gap:6px;justify-content:center;padding:0 20px;overflow-x:auto;scrollbar-width:none}
    .lb-thumbs::-webkit-scrollbar{display:none}
    .lb-thumbs img{width:56px;height:38px;object-fit:cover;border-radius:5px;opacity:.45;cursor:pointer;border:2px solid transparent;flex-shrink:0}
    .lb-thumbs img.active{opacity:1;border-color:#fff}
    .lb-trigger{cursor:zoom-in}

    /* ============ VEHICLE LIST CARD (shared component) ============
       One canonical .lcard ruleset for the horizontal vehicle card used
       on both the catalog index and the homepage Wyróżnione section.
       Lives in the global layout so both pages — and any future page
       that drops in the car-list-card component — render identically
       with no CSS duplication. Markup in
       resources/views/components/car-list-card.blade.php.

       Outer wrapper is a div (NOT an a) because the CertiCheck pill
       inside is an <a download> and nested anchors are illegal HTML.
       Whole-card click target is the absolutely-positioned .lcard-link.
       .lcard-fav and .lcard-cta sit above it via z-index so they keep
       their own click handlers. */
    .cat-cards{display:flex;flex-direction:column;gap:12px}
    .home-listings{display:flex;flex-direction:column;gap:12px}

    /* Card row — fixed-height left rail + content with vertically
       centered text. Cards are deliberately compact (~180 px tall) so
       a stacked list reads dense + premium even with just one row. */
    .lcard{position:relative;display:flex;align-items:stretch;background:#fff;border:1px solid #eeeef0;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.04);transition:box-shadow .18s ease,border-color .18s ease}
    .lcard:hover{border-color:#dbe3f0;box-shadow:0 2px 6px rgba(0,0,0,.05),0 8px 24px rgba(0,0,0,.06)}
    .lcard-link{position:absolute;inset:0;z-index:1;text-decoration:none;color:inherit;border-radius:inherit;cursor:pointer}
    .lcard-link:focus-visible{outline:2px solid #0066ff;outline-offset:-2px}

    /* Image rail — fixed 240×170 on desktop. align-self:stretch makes
       it match the card height so the right side never floats over an
       empty gutter, but the explicit aspect cap keeps cards compact. */
    .lcard-img{width:240px;min-width:240px;flex-shrink:0;align-self:stretch;height:170px;position:relative;overflow:hidden;background:linear-gradient(135deg,#eef4ff 0%,#e3ecfa 100%)}
    .lcard-img img{width:100%;height:100%;object-fit:cover;display:block}
    .lcard-img-placeholder{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef4ff 0%,#e3ecfa 100%)}
    .lcard-img-placeholder svg{width:48px;height:48px;stroke:#94a3b8;stroke-width:1.4;fill:none;opacity:.55}

    .lcard-badge-top{position:absolute;top:10px;left:10px;background:#f97316;color:#fff;font-size:10px;font-weight:800;padding:4px 9px;border-radius:5px;letter-spacing:.4px;text-transform:uppercase;z-index:2;box-shadow:0 1px 3px rgba(249,115,22,.3)}

    .lcard-fav{position:absolute;top:8px;right:8px;width:34px;height:34px;background:rgba(255,255,255,.95);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s ease;box-shadow:0 1px 4px rgba(0,0,0,.18);z-index:2}
    .lcard-fav:hover{background:#fff;transform:scale(1.08)}
    .lcard-fav svg{width:15px;height:15px;stroke:#9ca3af;fill:none;stroke-width:2;transition:stroke .18s,fill .18s}
    .lcard-fav.active svg{stroke:#f97316;fill:#f97316}

    .lcard-photo-count{position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.65);color:#fff;font-size:10.5px;font-weight:600;padding:3px 7px;border-radius:4px;display:flex;align-items:center;gap:4px;z-index:2}
    .lcard-photo-count svg{width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2}

    /* Content rail — vertically centered against the image rail so a
       compact card never leaves trailing whitespace on the right. */
    .lcard-content{flex:1;padding:16px 22px;display:flex;flex-direction:column;min-width:0;position:relative;justify-content:center}
    .lcard-main{display:flex;gap:20px;align-items:center;min-width:0}

    .lcard-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:8px}
    .lcard-title{font-size:17px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0}

    /* Inline metadata row — single line, icons + label pairs separated
       by a thin vertical rule on desktop, wrap cleanly on mobile. */
    .lcard-specs{display:flex;flex-wrap:wrap;gap:4px 0;margin:0}
    .lcard-spec{display:inline-flex;align-items:center;gap:5px;font-size:12.5px;color:#475569;font-weight:500;padding-right:10px;margin-right:10px;border-right:1px solid #e5e7eb;white-space:nowrap;line-height:1.4}
    .lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
    .lcard-spec svg{width:13px;height:13px;stroke:#64748b;fill:none;stroke-width:2;flex-shrink:0}

    .lcard-certicheck{margin-top:2px;position:relative;z-index:2}

    /* Right actions column — price + CTA stacked, right-aligned, 160
       wide so the CTA fits without wrapping but doesn't dominate. */
    .lcard-actions{flex-shrink:0;min-width:160px;display:flex;flex-direction:column;align-items:flex-end;gap:10px;align-self:center}
    .lcard-price{font-size:22px;font-weight:900;color:#0a0a0a;letter-spacing:-.5px;line-height:1;white-space:nowrap}
    .lcard-cta{display:inline-flex;align-items:center;gap:6px;background:#fff;color:#0066ff;border:1.5px solid #0066ff;padding:8px 14px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;transition:all .18s ease;position:relative;z-index:2;cursor:pointer;white-space:nowrap}
    .lcard-cta:hover{background:#0066ff;color:#fff;box-shadow:0 4px 12px rgba(0,102,255,.25)}
    .lcard-cta:focus-visible{outline:2px solid #0066ff;outline-offset:2px}
    .lcard-cta svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.4;flex-shrink:0;transition:transform .18s ease}
    .lcard-cta:hover svg{transform:translateX(2px)}

    /* Tablet — slightly narrower image, tighter padding. */
    @media(max-width:1024px){
        .lcard-img{width:220px;min-width:220px;height:160px}
        .lcard-content{padding:14px 18px}
        .lcard-actions{min-width:140px}
        .lcard-title{font-size:16px}
        .lcard-price{font-size:20px}
        .lcard-spec{font-size:12px}
    }

    /* Mobile — stack the card vertically. Image on top, content below;
       price+CTA become a horizontal footer row so they fit cleanly. */
    @media(max-width:720px){
        .lcard{flex-direction:column}
        .lcard-img{width:100%;min-width:0;height:190px}
        .lcard-content{padding:14px 16px;gap:10px}
        .lcard-main{flex-direction:column;align-items:stretch;gap:12px}
        /* flex-wrap — bez tego cena i CTA (oba white-space:nowrap) daja karcie
           min-content 340px. Grid katalogu nie moze zwezic kolumny ponizej
           min-content swojego dziecka, wiec cala strona wyjezdzala w prawo na
           waskich telefonach. Gdy brakuje miejsca, CTA schodzi pod cene. */
        .lcard-actions{min-width:0;width:100%;flex-direction:row;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding-top:10px;border-top:1px solid #f1f5f9}
        .lcard-price{font-size:20px;min-width:0}
        .lcard-cta{flex-shrink:0}
        .lcard-spec{font-size:12px;padding-right:8px;margin-right:8px}
    }

    /* Very small phones — keep the CTA legible. */
    @media(max-width:380px){
        .lcard-content{padding:12px 14px}
        .lcard-title{font-size:15.5px;white-space:normal}
        .lcard-cta{padding:7px 11px;font-size:12px}
        .lcard-price{font-size:19px}
    }

    /* Bottom CTA on the homepage's featured section. Centered pill. */
    .home-listings-cta{display:flex;justify-content:center;margin-top:20px}
    .home-listings-cta-btn{display:inline-flex;align-items:center;gap:8px;background:#0066ff;color:#fff;padding:13px 26px;border-radius:50px;font-size:13.5px;font-weight:700;text-decoration:none;transition:all .18s ease;box-shadow:0 4px 12px rgba(0,102,255,.25)}
    .home-listings-cta-btn:hover{background:#0052cc;box-shadow:0 6px 18px rgba(0,102,255,.32);transform:translateY(-1px)}
    .home-listings-cta-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2}
    @media(max-width:720px){
        .home-listings-cta{margin-top:16px}
        .home-listings-cta-btn{padding:12px 22px;font-size:13px}
    }
    </style>
    <div class="lb-backdrop" id="lbBackdrop">
        <div class="lb-stage">
            <div class="lb-count" id="lbCount"></div>
            <button type="button" class="lb-x" onclick="closeLb()" aria-label="Zamknij"><x-icon name="x" size="22"/></button>
            <button type="button" class="lb-nav prev" id="lbPrev" aria-label="Poprzednie"><x-icon name="chevron-left" size="22"/></button>
            <img class="lb-img" id="lbImg" alt="Powiększenie zdjęcia">
            <button type="button" class="lb-nav next" id="lbNext" aria-label="Następne"><x-icon name="chevron-right" size="22"/></button>
            <div class="lb-thumbs" id="lbThumbs"></div>
            <div class="lb-caption" id="lbCaption"></div>
        </div>
    </div>
    <script>
    (function(){
        const bd=document.getElementById('lbBackdrop'),img=document.getElementById('lbImg'),cap=document.getElementById('lbCaption'),count=document.getElementById('lbCount'),thumbs=document.getElementById('lbThumbs');
        let items=[],idx=0;
        function render(){
            const it=items[idx];if(!it)return;
            img.src=it.src;img.alt=it.caption||'';
            cap.textContent=it.caption||'';cap.style.display=it.caption?'block':'none';
            count.textContent=(idx+1)+' / '+items.length;count.style.display=items.length>1?'block':'none';
            document.getElementById('lbPrev').style.display=items.length>1?'flex':'none';
            document.getElementById('lbNext').style.display=items.length>1?'flex':'none';
            thumbs.style.display=items.length>1?'flex':'none';
            if(items.length>1){
                thumbs.innerHTML=items.map((x,i)=>`<img src="${x.src}" data-i="${i}" class="${i===idx?'active':''}" alt="">`).join('');
                thumbs.querySelectorAll('img').forEach(t=>t.addEventListener('click',()=>{idx=+t.dataset.i;render()}));
                thumbs.querySelector('img.active')?.scrollIntoView({inline:'center',block:'nearest',behavior:'smooth'});
            }
        }
        function next(){if(items.length<2)return;idx=(idx+1)%items.length;render()}
        function prev(){if(items.length<2)return;idx=(idx-1+items.length)%items.length;render()}
        window.closeLb=()=>bd.classList.remove('open');
        window.openLbAt=(arr,i)=>{items=arr;idx=i||0;render();bd.classList.add('open')};
        document.getElementById('lbNext').addEventListener('click',next);
        document.getElementById('lbPrev').addEventListener('click',prev);
        bd.addEventListener('click',e=>{if(e.target===bd||e.target.classList.contains('lb-stage'))closeLb()});
        document.addEventListener('keydown',e=>{
            if(!bd.classList.contains('open'))return;
            if(e.key==='Escape')closeLb();
            if(e.key==='ArrowRight')next();
            if(e.key==='ArrowLeft')prev();
        });
        window.initLightbox=()=>{
            const all=[...document.querySelectorAll('[data-lightbox]')];
            all.forEach(el=>{if(el.dataset.lbBound)return;el.dataset.lbBound='1';el.classList.add('lb-trigger');
                el.addEventListener('click',e=>{
                    e.preventDefault();
                    const g=el.dataset.gallery;
                    const group=g?all.filter(x=>x.dataset.gallery===g):[el];
                    const arr=group.map(x=>({src:x.dataset.lightbox,caption:x.dataset.caption||''}));
                    const i=group.indexOf(el);
                    openLbAt(arr,Math.max(0,i));
                });
            });
        };
        document.addEventListener('DOMContentLoaded',initLightbox);
    })();
    </script>

    {{-- ============ ANALITYKA — beacon zdarzeń ============
         window.csTrack(nazwa, meta) wysyła zdarzenie do POST /zdarzenie.
         sendBeacon przeżywa nawigację (klik w tel: natychmiast opuszcza
         stronę), fetch keepalive jest fallbackiem. Nazwy zdarzeń waliduje
         serwer — tutaj nic nie jest tajne.

         Auto-wiązanie:
           • każdy <a href="tel:..."> → phone_click
           • dowolny element [data-track="nazwa"] → ta nazwa,
             opcjonalnie data-track-meta='{"klucz":"wartosc"}' --}}
    <script>
    (function(){
        var ENDPOINT = @json(route('track.store'));
        var slug     = @json(request()->route('car') instanceof \App\Models\Car ? request()->route('car')->slug : null);

        window.csTrack = function(name, meta, carId){
            try{
                var body = JSON.stringify({name:name, slug:slug, car_id:carId||null, meta:meta||{}});
                var blob = new Blob([body], {type:'application/json'});
                if(navigator.sendBeacon && navigator.sendBeacon(ENDPOINT, blob)) return;
                fetch(ENDPOINT,{method:'POST',headers:{'Content-Type':'application/json'},body:body,keepalive:true});
            }catch(e){/* pomiar nigdy nie psuje strony */}
        };

        document.addEventListener('click', function(e){
            if(!e.target.closest) return;

            var tel = e.target.closest('a[href^="tel:"]');
            if(tel){ window.csTrack('phone_click', {href: tel.getAttribute('href')}); return; }

            var el = e.target.closest('[data-track]');
            if(!el) return;
            var meta = {};
            try{ meta = JSON.parse(el.getAttribute('data-track-meta') || '{}'); }catch(_){}
            window.csTrack(el.getAttribute('data-track'), meta);
        }, true);
    })();
    </script>
    @stack('scripts')
</body>
</html>
