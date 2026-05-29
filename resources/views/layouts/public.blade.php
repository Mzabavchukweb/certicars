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
    <meta name="theme-color" content="#0066ff">
    <meta property="og:type" content="@yield('og_type','website')">
    <meta property="og:site_name" content="CertiCars">
    <meta property="og:locale" content="pl_PL">
    <meta property="og:title" content="@yield('og_title','CertiCars — certyfikowane samochody używane')">
    <meta property="og:description" content="@yield('og_description','Platforma komisowa certyfikowanych samochodów używanych z pełną inspekcją techniczną.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('img/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title','CertiCars — certyfikowane samochody używane')">
    <meta name="twitter:description" content="@yield('og_description','Platforma komisowa certyfikowanych samochodów używanych z pełną inspekcją techniczną.')">
    <meta name="twitter:image" content="@yield('og_image', asset('img/og-default.jpg'))">
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
        'image' => asset('img/og-default.jpg'),
        'telephone' => '+48585586090',
        'email' => 'kontakt@certicars.pl',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'ul. Przykładowa 15',
            'postalCode' => '00-001',
            'addressLocality' => 'Warszawa',
            'addressCountry' => 'PL',
        ],
        'openingHoursSpecification' => [
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday'], 'opens' => '09:00', 'closes' => '18:00'],
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => 'Saturday', 'opens' => '10:00', 'closes' => '14:00'],
        ],
        'areaServed' => 'PL',
        'priceRange' => '$$',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
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
        .topbar{background:var(--blue);color:rgba(255,255,255,.9);font-size:13px;font-weight:400;line-height:1;border-bottom:none}
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
        .header-nav{display:flex;align-items:center;gap:4px}
        .header-nav .nav-link{font-size:14.5px;font-weight:500;color:var(--text);padding:10px 16px;border-radius:8px;transition:color .15s,background .15s;line-height:1}
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
        .footer{background:#0d1b3e;color:rgba(255,255,255,.6);padding:0;margin-top:0;position:relative}
        .footer::before{content:'';display:block;height:2px;background:linear-gradient(90deg,rgba(0,102,255,.25) 0%,#0066ff 40%,rgba(0,102,255,.25) 100%)}
        .footer-top{max-width:1200px;margin:0 auto;padding:48px 24px 40px;display:flex;align-items:center;justify-content:space-between;gap:24px;border-bottom:1px solid rgba(255,255,255,.08)}
        .footer-top-brand{display:flex;align-items:center;gap:10px}
        .footer-logo{display:inline-flex;align-items:center;gap:9px;text-decoration:none}
        .footer-logo-badge{width:30px;height:30px;flex-shrink:0}
        .footer-logo span{font-family:'Inter',sans-serif;font-size:24px;font-weight:900;letter-spacing:-.6px;color:#fff;line-height:1}
        .footer-logo span span{color:#4ea3ff}
        .footer-top-socials{display:flex;gap:8px}
        .footer-social{width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;transition:all .2s;text-decoration:none;border:1px solid rgba(255,255,255,.12)}
        .footer-social:hover{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.25);transform:translateY(-2px);box-shadow:0 4px 14px rgba(0,0,0,.3)}
        .footer-in{max-width:1200px;margin:0 auto;padding:40px 24px 0;display:grid;grid-template-columns:1.4fr 1.2fr 1fr;gap:48px}
        .footer h3{color:#fff;font-size:13px;margin-bottom:16px;text-transform:none;letter-spacing:-.2px;font-weight:800}
        .footer p,.footer li{font-size:13.5px;line-height:1.9;color:rgba(255,255,255,.55)}
        .footer a{font-size:13.5px;line-height:1.9;color:rgba(255,255,255,.6);text-decoration:none;transition:all .15s}
        .footer ul{list-style:none}
        .footer li{padding:3px 0}
        .footer a:hover{color:#fff}
        .footer-brand p{font-size:13.5px;color:rgba(255,255,255,.55);line-height:1.7;margin-top:0;max-width:340px}
        .footer-contact-item{display:flex;align-items:center;gap:10px;margin-bottom:10px}
        .footer-contact-icon{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .footer-contact-icon svg{width:14px;height:14px;stroke:#7eb3ff}
        .footer-hours{font-size:13px;color:rgba(255,255,255,.5);line-height:1}
        .footer-hours div{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.07)}
        .footer-hours div:last-child{border-bottom:none}
        .footer-hours .val{color:#fff;font-weight:700}
        .footer-hours .closed{color:rgba(255,255,255,.25);font-weight:500}
        .footer-btm{max-width:1200px;margin:0 auto;padding:20px 24px;border-top:1px solid rgba(255,255,255,.08);font-size:12px;color:rgba(255,255,255,.3);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
        @media(max-width:900px){.footer-in{grid-template-columns:1fr 1fr;gap:32px}}
        @media(max-width:600px){.footer-top{flex-direction:column;align-items:flex-start;padding:32px 20px 28px}.footer-in{grid-template-columns:1fr;gap:28px;padding:32px 20px 0}.footer-btm{flex-direction:column;text-align:center;padding:20px}}

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
        .vcard-bottom{margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid var(--border-l);gap:10px}
        .vcard-price{font-size:21px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1.1}
        .vcard-price small{display:block;font-size:11px;font-weight:500;color:var(--text-3);letter-spacing:0;margin-top:1px}
        /* vcard-link is a real CTA button — keep its own hover state, but no
           longer driven by the parent .vcard:hover (which has been removed). */
        .vcard-link{font-size:12px;font-weight:700;color:#fff;background:var(--blue);padding:9px 16px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;transition:background .15s;white-space:nowrap;flex-shrink:0}
        .vcard-link:hover{background:var(--blue-h)}
        .vcard-link svg{width:12px;height:12px;stroke:#fff;stroke-width:2.4;fill:none}

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
            .header-logo{margin-right:0}
            .header-logo-tagline span{font-size:10px}
            .nav-mobile{position:fixed;top:64px;left:0;right:0;bottom:0;background:#fff;padding:24px 20px;display:none;flex-direction:column;gap:4px;z-index:99;overflow-y:auto;animation:slideDown .25s ease}
            .nav-mobile.open{display:flex}
            .nav-mobile .nav-link{font-size:16px;padding:16px 18px;border-radius:12px;color:var(--text);font-weight:500;border:1px solid var(--border-l);display:flex;align-items:center;gap:10px}
            .nav-mobile .nav-link.active{background:var(--blue-bg);border-color:var(--blue-bg);color:var(--blue);font-weight:600}
            .nav-mobile-cta{margin-top:12px;background:var(--blue)!important;color:#fff!important;border:none!important;justify-content:center;font-weight:600!important}
            .nav-mobile-contact{margin-top:auto;padding-top:24px;border-top:1px solid var(--border-l);display:flex;flex-direction:column;gap:12px;font-size:14px;color:var(--text-2)}
            .nav-mobile-contact a{display:flex;align-items:center;gap:10px;color:var(--text-2)}
            .nav-mobile-contact i{width:16px;height:16px;color:var(--blue)}
            /* Footer mobile */
            .footer-in{grid-template-columns:1fr;gap:28px;padding:40px 20px 0}
            .footer-btm{flex-direction:column;text-align:center;padding:20px}
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
                <a href="tel:+48585586090">
                    <x-icon name="phone" size="14" class="tb-ico"/>
                    <span class="tb-strong">+48 58 558 60 90</span>
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
                    Pon–Pt <span class="tb-strong">9:00–18:00</span>
                </span>
                <span class="tb-sep tb-hide-lg"></span>
                <span class="tb-item">
                    <x-icon name="map-pin" size="14" class="tb-ico"/>
                    Warszawa
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
                <a href="tel:+48585586090">
                    <x-icon name="phone" size="16"/>
                    +48 58 558 60 90
                </a>
                <a href="mailto:kontakt@certicars.pl">
                    <x-icon name="mail" size="16"/>
                    kontakt@certicars.pl
                </a>
            </div>
        </nav>
    </header>

    <main id="main">@yield('content')</main>

    <a href="tel:+48585586090" class="float-call" aria-label="Zadzwoń +48 58 558 60 90">
        <x-icon name="phone" size="28" :strokeWidth="2.2"/>
    </a>




    <footer class="footer" id="kontakt">
        {{-- Top bar: Logo + social --}}
        <div class="footer-top">
            <a href="{{ route('home') }}" class="footer-logo">
                <svg class="footer-logo-badge" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Certi<span>Cars</span></span>
            </a>
            <div class="footer-top-socials">
                <a href="#" class="footer-social" aria-label="Facebook" style="color:#1877f2"><x-icon name="facebook" size="17"/></a>
                <a href="#" class="footer-social" aria-label="Instagram" style="color:#e4405f"><x-icon name="instagram" size="17"/></a>
            </div>
        </div>

        <div class="footer-in">
            {{-- Col 1: O nas --}}
            <div class="footer-brand">
                <h3>O CertiCars</h3>
                <p>Każdy samochód w naszej ofercie przechodzi pełną inspekcję techniczną i weryfikację historii pojazdu. Kupujesz z pewnością.</p>
                <div style="margin-top:20px;padding:16px 18px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px">
                    <div style="font-size:12px;font-weight:700;color:#fff;margin-bottom:8px;display:flex;align-items:center;gap:6px;color:#7eb3ff"><x-icon name="clock" size="14"/> <span style="color:#fff">Godziny otwarcia</span></div>
                    <div class="footer-hours">
                        <div><span>Poniedziałek – Piątek</span><span class="val">9:00 – 18:00</span></div>
                        <div><span>Sobota</span><span class="val">10:00 – 14:00</span></div>
                        <div><span>Niedziela</span><span class="closed">Zamknięte</span></div>
                    </div>
                </div>
            </div>

            {{-- Col 2: Kontakt --}}
            <div>
                <h3>Kontakt</h3>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><x-icon name="phone" size="16"/></div>
                    <div><div style="font-size:11px;color:rgba(255,255,255,.4);font-weight:500">Telefon</div><a href="tel:+48585586090" style="font-weight:700;color:#fff">+48 58 558 60 90</a></div>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><x-icon name="mail" size="16"/></div>
                    <div><div style="font-size:11px;color:rgba(255,255,255,.4);font-weight:500">E-mail</div><a href="mailto:kontakt@certicars.pl" style="font-weight:700;color:#fff">kontakt@certicars.pl</a></div>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><x-icon name="map-pin" size="16"/></div>
                    <div><div style="font-size:11px;color:rgba(255,255,255,.4);font-weight:500">Adres</div><span style="font-weight:700;color:#fff;font-size:13.5px">Warszawa, Polska</span></div>
                </div>
                {{-- Mini map --}}
                <div style="margin-top:16px;border-radius:12px;overflow:hidden;border:1px solid rgba(255,255,255,.1);height:120px;background:#162040">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2443.8!2d18.638!3d54.371!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1spl!2spl!4v1" width="100%" height="100%" style="border:0" loading="lazy"></iframe>
                </div>
            </div>

            {{-- Col 3: Nawigacja + Informacje --}}
            <div>
                <h3>Nawigacja</h3>
                <ul style="margin-bottom:24px">
                    <li><a href="{{ route('home') }}">Strona główna</a></li>
                    <li><a href="{{ route('catalog') }}">Oferta samochodów</a></li>
                    <li><a href="{{ route('favorites') }}">Obserwowane</a></li>
                    <li><a href="{{ route('about') }}">O nas</a></li>
                    <li><a href="{{ route('contact') }}">Kontakt</a></li>
                </ul>
                <h3>Informacje</h3>
                <ul>
                    <li><a href="#">Polityka prywatności</a></li>
                    <li><a href="#">Regulamin</a></li>
                </ul>
            </div>

        </div>
        <div class="footer-btm">
            <span>© {{ date('Y') }} CertiCars. Wszelkie prawa zastrzeżone.</span>
            <span style="display:flex;align-items:center;gap:5px;color:#4ea3ff"><x-icon name="shield-check" size="12"/> <span style="color:inherit">Zweryfikowany dealer</span></span>
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
        } else {
            favs.push(id);
            document.querySelectorAll(`[data-id="${id}"]`).forEach(b => b.classList.add('active'));
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
    @stack('scripts')
</body>
</html>
