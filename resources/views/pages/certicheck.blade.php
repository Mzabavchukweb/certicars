@extends('layouts.public')
@section('meta_title_full','CertiCheck — rozszerzona prezentacja wybranych aut | CertiCars')
@section('title','CertiCheck — rozszerzona prezentacja wybranych aut')
@section('description','CertiCheck to rozszerzona prezentacja wybranych samochodów w CertiCars: pomiary lakieru, stan opon, mapa śladów, materiały 360° oraz dokumenty — abyś wiedział więcej przed przyjazdem.')
@section('og_title','CertiCheck — rozszerzona prezentacja wybranych aut | CertiCars')
@section('og_description','Pomiary lakieru, stan opon, mapa zauważonych śladów, materiały 360° i dokumenty. Poznaj auto jeszcze przed oględzinami.')

@section('styles')
:root{--cc-blue:#0066ff;--cc-blue-d:#0052cc;--cc-ink:#0a0a0a;--cc-muted:#475569;--cc-line:#e5edfa;--cc-bg:#f6f8fc}

/* ===== HERO ===== */
/* Seamless light→blue gradient across the whole band; the hero character's
   own light-blue background melts into it (edges masked), so the panel +
   character read as ONE composed scene. Kept intentionally short. */
/* Horizontal wash: LEFT stays the panel graphic's own light background
   (#f4f6fd) so certicheck.png melts in; it glides across to the character's
   own bluish backdrop on the RIGHT so the figure melts in too. Both graphics
   sit on a colour that matches their baked-in background — no hard block. */
.cclp-hero{position:relative;overflow:hidden;isolation:isolate;padding:40px 0;background:linear-gradient(95deg, #f4f6fd 0%, #f4f6fd 40%, #ebf1fc 56%, #dbe6fb 74%, #cddcfa 88%, #cddcfa 100%)}
.cclp-in{max-width:1200px;margin:0 auto;padding:0 24px}
.cclp-hero-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;z-index:1;display:grid;grid-template-columns:minmax(0,0.92fr) minmax(0,1.22fr);gap:20px;align-items:center}
.cclp-eyebrow{display:inline-flex;align-items:center;gap:9px;font-size:11px;font-weight:800;color:var(--cc-blue);text-transform:uppercase;letter-spacing:1.8px;margin-bottom:16px}
.cclp-eyebrow::before{content:'';width:24px;height:1.5px;background:var(--cc-blue);border-radius:1px}
.cclp-hero h1{font-size:48px;font-weight:900;color:var(--cc-ink);letter-spacing:-1.2px;line-height:1.05;margin:0 0 18px}
.cclp-hero h1 .blue{color:var(--cc-blue)}
.cclp-hero .lead{font-size:16px;color:var(--cc-muted);line-height:1.65;margin:0 0 24px;max-width:520px}
.cclp-cta-primary{display:inline-flex;align-items:center;gap:8px;background:var(--cc-blue);color:#fff;padding:16px 30px;border-radius:50px;font-size:14px;font-weight:700;letter-spacing:.1px;text-decoration:none;box-shadow:0 8px 24px rgba(0,102,255,.4);transition:all .18s}
.cclp-cta-primary:hover{background:var(--cc-blue-d);color:#fff;transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,102,255,.5)}
.cclp-cta-primary svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.4}
/* Info note under the CTA — w referencji lezy bezposrednio na tle sekcji,
   bez bialej ramki (inaczej niz cs-cc-info na home). */
.cclp-hero-note{display:flex;align-items:flex-start;gap:10px;margin-top:26px;max-width:520px;font-size:12.5px;color:#475569;line-height:1.7}
.cclp-hero-note svg{flex-shrink:0;width:17px;height:17px;stroke:var(--cc-blue);fill:none;stroke-width:2;margin-top:1px}

/* Hero visual: report panel (white card) + character bled to the right.
   Kolejnosc warstw: panel NAJNIZEJ, bohater NAD nim — tablet w jego dloni
   nachodzi na prawa krawedz panelu (wczesniej bylo odwrotnie).
   Pozycja panelu siedzi w zmiennych, bo badge 360° musi trzymac sie jego
   prawej krawedzi, a jest wypiety poza panel (inaczej bohater by go zakryl,
   bedac wyzej w stosie). */
.cclp-hero-visual{position:relative;height:430px;--rep-l:0%;--rep-w:min(84%,478px)}
.cclp-hero-report{position:absolute;left:var(--rep-l);top:50%;transform:translateY(-50%);z-index:1;width:var(--rep-w)}
/* Hand-built CertiCheck report card (1:1 with the design, no image).
   Panel jest LEKKI, nie biala karta: pol-przezroczysta biel + blur, cienka
   jasna ramka i miekki cien. Solidne #fff z mocnym cieniem odcinalo sie od
   gradientu sekcji jak naklejka — w referencji panel ledwo odstaje od tla. */
.cc-rep{position:relative;background:rgba(255,255,255,.58);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.85);border-radius:26px;box-shadow:0 20px 54px rgba(15,32,72,.09);padding:24px 34px 24px 26px;display:grid;grid-template-columns:minmax(0,1.3fr) minmax(0,1fr);gap:18px;align-items:center}
/* 360° — dziecko .cclp-hero-visual, nie panelu. Wysrodkowany na prawej
   krawedzi panelu (calc ze zmiennych) i najwyzej w stosie, wiec bohater
   go nie przykrywa. */
.cc-rep-360{position:absolute;top:30px;left:calc(var(--rep-l) + var(--rep-w) - 54px);width:84px;height:84px;border-radius:50%;background:#fff;box-shadow:0 14px 30px rgba(15,32,72,.16);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;z-index:4}
.cc-rep-360 svg{width:26px;height:16px}
.cc-rep-360 b{font-size:13.5px;font-weight:800;color:#0066ff;letter-spacing:.2px;line-height:1}
.cc-rep-list{display:flex;flex-direction:column}
.cc-rep-row{display:flex;align-items:center;gap:11px;padding:14px 2px;border-top:1px solid #eef1f7}
.cc-rep-row:first-child{border-top:0;padding-top:0}
.cc-rep-row:last-child{padding-bottom:0}
.cc-rep-ico{flex-shrink:0;width:40px;height:40px;border-radius:12px;background:#eef4ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cc-rep-ico svg,.cc-rep-ico i{width:20px !important;height:20px !important;stroke-width:1.8}
.cc-rep-tx{min-width:0}
.cc-rep-tx b{display:block;font-size:14px;white-space:nowrap;font-weight:800;color:#0a1a3a;letter-spacing:-.25px;line-height:1.25;margin-bottom:1px}
.cc-rep-tx span{display:block;font-size:12.5px;color:#5b6b86;letter-spacing:-.1px;line-height:1.35}
.cc-rep-diagram{position:relative;border-left:1px solid rgba(15,32,72,.07);padding-left:18px;display:flex;align-items:center;justify-content:flex-start;height:100%}
/* .cc-rep-car opina sie na obrazku (inline-block), zeby numerki liczyly % od
   sylwetki auta, a nie od calej kolumny — inaczej przy kazdej szerokosci
   ladowaly gdzie indziej wzgledem karoserii. */
.cc-rep-car{position:relative;display:inline-block;line-height:0}
.cc-rep-car img{width:100%;max-width:100%;height:auto;display:block}
/* Numerki wokol auta — jak w referencji: dach, prawy przod, prawy tyl,
   zderzak tylny, lewy bok. */
.cc-rep-pin{position:absolute;transform:translate(-50%,-50%);width:26px;height:26px;border-radius:50%;background:#0066ff;color:#fff;font-size:12.5px;font-weight:800;line-height:1;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 14px rgba(0,102,255,.35);z-index:2}
.cc-rep-pin.p1{top:1%;left:56%}
.cc-rep-pin.p2{top:24%;left:82%}
.cc-rep-pin.p3{top:63%;left:82%}
.cc-rep-pin.p4{top:97%;left:46%}
.cc-rep-pin.p5{top:44%;left:3%}
.cc-rep-360 i,.cc-rep-360 svg{width:21px !important;height:21px !important;stroke:#0066ff}
/* Two masks combined (intersect):
   1) an elliptical radial hugging the figure — fades his backdrop out all
      around into the hero colour;
   2) a horizontal wash whose LEFT side fades long & very soft (delicate
      tonal transition toward the lighter text side) and whose right stays
      cleaner. Together: seamless soft blend on the left, clean on the right.
   Bohater ma wypalone jasnoniebieskie tlo (941x1672), wiec maski sa konieczne
   — bez nich widac prostokat zdjecia na gradiencie sekcji. Rozmiar z wysokosci
   (width:auto), bo proporcja jest pionowa 0.56. */
/* Bohater jest kotwiczony do prawej krawedzi SIATKI (right:0), nie viewportu.
   Wczesniej siedzial na krawedzi okna (ujemny right z 100vw) i przez to jego
   odleglosc od panelu zmieniala sie z kazda szerokoscia: na 1280 tablet
   zaslanial auto, a na 1440+ odjezdzal od panelu i nie bylo zadnego nachodzenia.
   Teraz .cclp-hero-in ma sufit 1200px, wiec kompozycja panel → tablet → bohater
   jest IDENTYCZNA na kazdym ekranie powyzej 1200px.
   Kosztem jest prawa krawedz zdjecia, ktora na szerokim ekranie nie wychodzi juz
   poza viewport — dlatego doszedl krotki fade od prawej (wczesniej niepotrzebny).
   Gora i dol sa przyciete przez overflow:hidden sekcji. */
.cclp-hero-char{position:absolute;right:auto;left:calc(var(--rep-l) + var(--rep-w) - 50px);bottom:-140px;height:calc(100% + 260px);width:auto;z-index:3;pointer-events:none;
    -webkit-mask-image:linear-gradient(to right, rgba(0,0,0,0) 0, rgba(0,0,0,.55) 4%, #000 10%), linear-gradient(to left, rgba(0,0,0,0) 0, rgba(0,0,0,.55) 3%, #000 9%), linear-gradient(to bottom, rgba(0,0,0,0) 0, rgba(0,0,0,.5) 3%, #000 8%);
    -webkit-mask-composite:source-in;
            mask-image:linear-gradient(to right, rgba(0,0,0,0) 0, rgba(0,0,0,.55) 4%, #000 10%), linear-gradient(to left, rgba(0,0,0,0) 0, rgba(0,0,0,.55) 3%, #000 9%), linear-gradient(to bottom, rgba(0,0,0,0) 0, rgba(0,0,0,.5) 3%, #000 8%);
            mask-composite:intersect}

/* ===== SECTION SHELL ===== */
.cclp-sec{padding:96px 0}
.cclp-sec.alt{background:var(--cc-bg)}
.cclp-head{text-align:center;max-width:640px;margin:0 auto 56px}
/* Centered eyebrow label with flanking hairlines — mirrors the
   .about-section-label / .kt-section-label used on the O nas + Kontakt
   pages so every section on the site opens the same way. */
.cclp-eyebrow-c{font-size:11px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:var(--cc-blue);margin-bottom:14px;display:flex;align-items:center;justify-content:center;gap:10px}
.cclp-eyebrow-c::before,.cclp-eyebrow-c::after{content:'';width:24px;height:2px;background:var(--cc-blue);border-radius:2px;opacity:.5}
.cclp-head h2{font-size:40px;font-weight:900;letter-spacing:-.9px;color:var(--cc-ink);margin:0 0 14px;line-height:1.1}
.cclp-head p{font-size:15.5px;color:var(--cc-muted);line-height:1.7;margin:0}

/* ===== BENEFITS (6 cards) ===== */
.cclp-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.cclp-card{background:#fff;border:1px solid var(--cc-line);border-radius:12px;padding:36px 32px;transition:border-color .15s}
.cclp-card:hover{border-color:var(--cc-blue)}
.cclp-card-ico{width:60px;height:60px;border-radius:16px;background:#eef4ff;color:var(--cc-blue);display:flex;align-items:center;justify-content:center;margin-bottom:22px}
.cclp-card-ico svg{width:28px;height:28px;stroke:currentColor;fill:none;stroke-width:1.8}
.cclp-card h3{font-size:19px;font-weight:800;color:var(--cc-ink);margin:0 0 12px;letter-spacing:-.3px;line-height:1.25}
.cclp-card p{font-size:14.5px;color:var(--cc-muted);line-height:1.75;margin:0}

/* ===== PRACTICE (example report) ===== */
.cclp-practice-tabs{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:26px}
.cclp-ptab{border:1px solid var(--cc-line);background:#fff;color:#475569;font-size:13px;font-weight:700;padding:9px 16px;border-radius:50px;cursor:pointer;transition:all .15s}
.cclp-ptab:hover{border-color:#c7d8f5}
.cclp-ptab.active{background:var(--cc-blue);border-color:var(--cc-blue);color:#fff;box-shadow:0 6px 16px rgba(0,102,255,.28)}
.cclp-report-card{background:#fff;border:1px solid var(--cc-line);border-radius:18px;padding:22px;box-shadow:0 12px 40px rgba(15,32,72,.06);display:grid;grid-template-columns:minmax(0,1.35fr) minmax(0,1fr);gap:22px;align-items:stretch}
.cclp-rc-visual{position:relative;background:linear-gradient(180deg,#f7f9fd,#eef3fb);border-radius:16px;overflow:hidden;min-height:300px;display:flex;flex-direction:column;align-items:center;justify-content:center}
.cclp-rc-car{width:78%;max-width:360px;filter:drop-shadow(0 18px 22px rgba(15,32,72,.20))}
.cclp-rc-turn{position:absolute;bottom:34px;left:50%;transform:translateX(-50%);width:74%;height:38px;border-radius:50%;background:radial-gradient(ellipse at center,rgba(0,102,255,.14),rgba(0,102,255,0) 70%);border:2px solid rgba(0,102,255,.18)}
.cclp-rc-360{position:absolute;bottom:22px;left:50%;transform:translateX(-50%);font-size:12px;font-weight:800;color:var(--cc-blue);letter-spacing:1px;display:flex;align-items:center;gap:6px}
.cclp-rc-360 svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.2}
.cclp-rc-caption{position:absolute;top:14px;left:14px;background:rgba(10,20,50,.82);color:#fff;font-size:11.5px;font-weight:600;padding:6px 12px;border-radius:50px;backdrop-filter:blur(4px)}
.cclp-rc-info{display:flex;flex-direction:column}
.cclp-rc-title{font-size:22px;font-weight:900;color:var(--cc-ink);letter-spacing:-.4px;margin:0 0 4px}
.cclp-rc-sub{font-size:13px;color:var(--cc-muted);margin:0 0 16px}
.cclp-rc-sub b{color:#334155;font-weight:700}
.cclp-rc-specs{display:grid;grid-template-columns:1fr 1fr;gap:12px 16px;margin-bottom:16px}
.cclp-rc-spec{display:flex;align-items:center;gap:10px}
.cclp-rc-spec .ico{flex-shrink:0;width:34px;height:34px;border-radius:9px;background:#f1f5fb;color:var(--cc-blue);display:flex;align-items:center;justify-content:center}
.cclp-rc-spec .ico svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.9}
.cclp-rc-spec .txt small{display:block;font-size:10.5px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;font-weight:700}
.cclp-rc-spec .txt b{font-size:13px;color:var(--cc-ink);font-weight:700}
.cclp-rc-summary{margin-top:auto;background:#f7f9fd;border:1px solid var(--cc-line);border-radius:14px;padding:14px 16px}
.cclp-rc-sumrow{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:8px 0;border-top:1px solid #eaeff6}
.cclp-rc-sumrow:first-child{border-top:0}
.cclp-rc-sumrow .lbl b{display:block;font-size:12.5px;font-weight:700;color:var(--cc-ink)}
.cclp-rc-sumrow .lbl span{font-size:11px;color:#94a3b8}
.cclp-rc-badge{flex-shrink:0;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.cclp-rc-badge.ok{background:#dcfce7;color:#16a34a}
.cclp-rc-badge.warn{background:#fef3c7;color:#d97706}
.cclp-rc-badge svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:3}
.cclp-rc-cta{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:14px;background:var(--cc-blue);color:#fff;padding:12px;border-radius:12px;font-size:13.5px;font-weight:700;text-decoration:none;transition:background .15s}
.cclp-rc-cta:hover{background:var(--cc-blue-d);color:#fff}
.cclp-rc-cta svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
.cclp-practice-note{display:flex;align-items:center;gap:10px;justify-content:center;max-width:820px;margin:22px auto 0;font-size:12.5px;color:#64748b;line-height:1.6}
.cclp-practice-note svg{flex-shrink:0;width:16px;height:16px;stroke:var(--cc-blue);fill:none;stroke-width:2}

/* ===== PROCESS ===== */
.cclp-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;align-items:stretch}
.cclp-step{position:relative;padding:32px 26px;background:#fff;border-radius:12px;border:1px solid var(--cc-line);transition:border-color .15s}
.cclp-step:hover{border-color:var(--cc-blue)}
.cclp-step-num{width:48px;height:48px;border-radius:50%;background:var(--cc-blue);color:#fff;font-size:18px;font-weight:800;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 14px rgba(0,102,255,.28);margin-bottom:20px}
.cclp-step h3{font-size:16.5px;font-weight:800;color:var(--cc-ink);margin:0 0 14px;padding-bottom:14px;letter-spacing:-.2px;position:relative}
.cclp-step h3::after{content:'';position:absolute;left:0;bottom:0;width:28px;height:3px;border-radius:2px;background:var(--cc-blue)}
.cclp-step p{font-size:13.5px;color:var(--cc-muted);line-height:1.7;margin:0}
.cclp-step-arrow{position:absolute;top:50%;right:-11px;transform:translateY(-50%);z-index:2;color:#c7d8f5}
.cclp-step-arrow svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2.4}
.cclp-step:last-child .cclp-step-arrow{display:none}
.cclp-infobar{display:flex;align-items:center;gap:12px;justify-content:center;max-width:900px;margin:40px auto 0;background:#eef4ff;border:1px solid #d7e5fd;border-radius:14px;padding:16px 22px;font-size:13px;color:#3f5578;line-height:1.6}
.cclp-infobar svg{flex-shrink:0;width:18px;height:18px;stroke:var(--cc-blue);fill:none;stroke-width:2}

/* ===== SCOPE ===== */
.cclp-scope-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px}
.cclp-scope-col{padding:34px 30px;border-radius:16px;border:1px solid var(--cc-line);background:#fff}
.cclp-scope-col.ok{background:linear-gradient(180deg,#f2f9f4 0%,#fbfefb 100%);border-color:#c9ecd4}
.cclp-scope-col.no{background:linear-gradient(180deg,#fdf4f4 0%,#fffbfb 100%);border-color:#f4d3d3}
.cclp-scope-title{display:flex;align-items:center;gap:12px;font-size:18px;font-weight:800;color:var(--cc-ink);margin:0 0 22px;letter-spacing:-.2px}
.cclp-scope-title .ico{flex-shrink:0;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.cclp-scope-col.ok .ico{background:#dcfce7;color:#16a34a}
.cclp-scope-col.no .ico{background:#fee2e2;color:#dc2626}
.cclp-scope-title svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.6}
.cclp-scope-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:15px}
.cclp-scope-list li{display:flex;align-items:flex-start;gap:12px;font-size:14.5px;color:#334155;line-height:1.6}
.cclp-scope-list li .li-ico{flex-shrink:0;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-top:1px}
.cclp-scope-col.ok li .li-ico{background:#dcfce7;color:#16a34a}
.cclp-scope-col.no li .li-ico{background:#fee2e2;color:#dc2626}
.cclp-scope-list li .li-ico svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:3}

/* ===== FAQ (2 cols) ===== */
.cclp-faq-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:980px;margin:0 auto}
.cclp-faq-item{background:#fff;border:1px solid var(--cc-line);border-radius:12px;overflow:hidden;align-self:start}
.cclp-faq-q{width:100%;text-align:left;background:transparent;border:0;padding:20px 22px;font-size:15px;font-weight:700;color:var(--cc-ink);cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:14px;letter-spacing:-.15px;list-style:none}
.cclp-faq-q::-webkit-details-marker{display:none}
.cclp-faq-q svg{width:18px;height:18px;stroke:var(--cc-blue);fill:none;stroke-width:2.4;flex-shrink:0;transition:transform .18s}
.cclp-faq-item[open] .cclp-faq-q svg{transform:rotate(180deg)}
.cclp-faq-a{padding:0 22px 20px;font-size:13.5px;color:var(--cc-muted);line-height:1.7}

/* ===== DISCLAIMER ===== */
.cclp-disc{position:relative;overflow:hidden;padding:64px 0;background:#0a1432;color:rgba(255,255,255,.82)}
.cclp-disc::before{content:'';position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}
.cclp-disc-in{position:relative;z-index:1;max-width:960px;margin:0 auto;padding:0 24px;display:flex;gap:18px;align-items:flex-start}
.cclp-disc-ico{flex-shrink:0;width:46px;height:46px;border-radius:50%;background:rgba(95,161,255,.12);border:1.5px solid rgba(95,161,255,.4);color:#5fa1ff;display:flex;align-items:center;justify-content:center}
.cclp-disc-ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2.2}
.cclp-disc h3{font-size:18px;font-weight:800;color:#fff;margin:0 0 8px;letter-spacing:-.2px}
.cclp-disc p{font-size:14px;line-height:1.8;margin:0}

/* ===== BOTTOM CTA ===== */
.cclp-cta-strip{padding:80px 0;background:#fff}
.cclp-cta-strip-in{max-width:1000px;margin:0 auto;padding:40px 48px;background:linear-gradient(135deg,#f4f8ff 0%,#eaf1fe 100%);border:1px solid #d7e5fd;border-radius:22px;display:flex;justify-content:space-between;align-items:center;gap:24px;flex-wrap:wrap}
.cclp-cta-strip h3{font-size:26px;font-weight:800;color:var(--cc-ink);margin:0 0 6px;letter-spacing:-.4px}
.cclp-cta-strip p{font-size:14px;color:var(--cc-muted);margin:0}
.cclp-cta-strip-btns{display:flex;gap:12px;flex-wrap:wrap}
.cclp-btn-solid{display:inline-flex;align-items:center;gap:8px;background:var(--cc-blue);color:#fff;padding:13px 24px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;transition:all .15s;box-shadow:0 6px 16px rgba(0,102,255,.26)}
.cclp-btn-solid:hover{background:var(--cc-blue-d);color:#fff;transform:translateY(-1px)}
.cclp-btn-ghost{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--cc-blue);padding:13px 24px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;border:1.5px solid #cdddf7;transition:all .15s}
.cclp-btn-ghost:hover{border-color:var(--cc-blue);background:#f5f9ff}
.cclp-btn-solid svg,.cclp-btn-ghost svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.4}

/* ===== RESPONSIVE ===== */
/* Desktop posredni: .cclp-hero-in ma sufit 1200px, wiec kolumny sa tu tak samo
   szerokie jak na 1920 — ale bohater jest przyklejony do prawej krawedzi
   VIEWPORTU, wiec im wezsze okno, tym glebiej wchodzi w tresc i tablet zaczyna
   zaslaniac auto z numerkami. Tu zwezamy panel i zmniejszamy bohatera, zeby
   tablet nachodzil tylko na krawedz panelu. */
/* Ponizej 1200px na panel + auto + bohatera juz nie ma miejsca — tablet zaslanial
   pol auta razem z numerkami. Diagram znika (tak jak ponizej 1025px), zostaje
   sama lista i bohater nachodzacy na jej krawedz. */
@media(min-width:1025px) and (max-width:1199px){
    .cclp-hero-visual{--rep-w:min(66%,340px)}
    .cc-rep{grid-template-columns:1fr}
    .cc-rep-diagram{display:none}
}
@media(max-width:1024px){
    .cclp-hero{padding:52px 0 64px}
    .cclp-hero-in{grid-template-columns:1fr;gap:32px}
    .cclp-hero h1{font-size:42px}
    .cclp-hero-visual{order:-1;min-height:auto;justify-content:center}
    /* Tablet/mobile: rozmiar z wysokosci (szerokosc min(88%,420px) przy
       pionowej proporcji 0.56 dawalaby ~750px wysokosci). Desktopowy wash
       wygaszal tylko lewa krawedz — tu zdjecie nie dochodzi do zadnej krawedzi
       viewportu, wiec prostokat tla bylby widoczny z prawej i od dolu.
       Zamiast washa winieta: elipsa + fade od dolu i od prawej (intersect). */
    .cclp-hero-char{width:auto;height:calc(100% + 40px)}
    /* Cztery fade'y na krawedziach zamiast elipsy: elipsa wygaszala tez srodek
       kadru i tablet w dloni bohatera wygladal jak duch. Tu srodek zostaje
       w pelni kryjacy, znika tylko prostokat tla przy krawedziach. */
    .cclp-hero-char{
        -webkit-mask-image:
            linear-gradient(to right,transparent 0,rgba(0,0,0,.5) 5%,#000 14%),
            linear-gradient(to left,transparent 0,rgba(0,0,0,.5) 5%,#000 14%),
            linear-gradient(to top,transparent 0,rgba(0,0,0,.45) 6%,#000 18%),
            linear-gradient(to bottom,transparent 0,rgba(0,0,0,.5) 4%,#000 11%);
        mask-image:
            linear-gradient(to right,transparent 0,rgba(0,0,0,.5) 5%,#000 14%),
            linear-gradient(to left,transparent 0,rgba(0,0,0,.5) 5%,#000 14%),
            linear-gradient(to top,transparent 0,rgba(0,0,0,.45) 6%,#000 18%),
            linear-gradient(to bottom,transparent 0,rgba(0,0,0,.5) 4%,#000 11%);
        -webkit-mask-composite:source-in;mask-composite:intersect}
    /* Panel przez ZMIENNE, nie bezposrednio — badge 360° liczy z nich swoja
       pozycje, wiec inaczej rozjechalby sie z krawedzia panelu. */
    .cclp-hero-visual{--rep-l:-3%;--rep-w:min(64%,320px)}
    /* Panel kryjacy: na tablecie/mobile bohater lezy POD nim i przez
       pol-przezroczysta biel przebijal jego tablet. */
    .cc-rep{grid-template-columns:1fr;padding:18px 18px;background:rgba(255,255,255,.94)}
    .cc-rep-diagram{display:none}
    .cclp-cards{grid-template-columns:repeat(2,1fr)}
    .cclp-report-card{grid-template-columns:1fr}
    .cclp-steps{grid-template-columns:repeat(2,1fr);gap:22px}
    .cclp-step-arrow{display:none!important}
    .cclp-sec{padding:64px 0}
    .cclp-head h2{font-size:32px}
    .cclp-scope-grid,.cclp-faq-grid{grid-template-columns:1fr}
}
@media(max-width:640px){
    .cclp-hero{padding:40px 0 52px}
    .cclp-hero h1{font-size:32px;letter-spacing:-.8px}
    .cclp-hero .lead{font-size:15px}
    .cclp-hero-char{width:auto;height:calc(100% + 30px)}
    .cclp-hero-visual{--rep-l:-2%;--rep-w:min(72%,290px)}
    .cclp-cards{grid-template-columns:1fr;gap:16px}
    .cclp-card{padding:28px 24px}
    .cclp-card-ico{margin-bottom:16px}
    .cclp-rc-specs{grid-template-columns:1fr}
    .cclp-steps{grid-template-columns:1fr}
    .cclp-sec{padding:44px 0}
    .cclp-head{margin-bottom:30px}
    .cclp-head h2{font-size:25px}
    .cclp-cta-strip-in{padding:24px;flex-direction:column;text-align:center}
    .cclp-cta-strip-btns{justify-content:center}
    .cclp-disc-in{flex-direction:column}
}
@endsection

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="cclp-hero">
    <div class="cclp-hero-in">
        <div>
            <div class="cclp-eyebrow">CertiCheck</div>
            <h1>Wiesz więcej<br><span class="blue">przed przyjazdem.</span></h1>
            <p class="lead">CertiCheck to rozszerzona prezentacja wybranych samochodów w CertiCars. Pokazujemy pomiary lakieru, stan opon, zauważone ślady użytkowania, materiały 360° oraz dostępne dokumenty, aby ułatwić Ci wstępne zapoznanie się z autem jeszcze przed oględzinami.</p>
            <a class="cclp-cta-primary" href="{{ route('catalog') }}">
                Zobacz auta z CertiCheck
                <x-icon name="arrow-right" size="17" :strokeWidth="2.4"/>
            </a>
            <div class="cclp-hero-note">
                <x-icon name="info" size="17" :strokeWidth="2"/>
                <span>CertiCheck to wewnętrzny standard kontroli jakości CertiCars, a nie opinia rzeczoznawcy. Materiały pokazują stan auta na dzień oględzin, w zakresie możliwym do oceny bez specjalistycznego demontażu.</span>
            </div>
        </div>
        <div class="cclp-hero-visual">
            <img class="cclp-hero-char" src="/images/bohater-certicheck.png" alt="Konsultant CertiCars prezentujący podsumowanie CertiCheck" loading="eager" decoding="async" width="941" height="1672">
            <div class="cc-rep-360" aria-hidden="true"><x-icon name="rotate-3d" size="20" :strokeWidth="2"/><b>360°</b></div>
            <div class="cclp-hero-report cc-rep" role="img" aria-label="Panel CertiCheck — pomiary lakieru 118–164 µm, stan opon przód 6 mm tył 5 mm, 5 zauważonych śladów, 8 dokumentów">
                <div class="cc-rep-list">
                    @php
                        $ccRows = [
                            ['scan-line','Pomiary lakieru',['118 – 164 µm']],
                            ['circle-gauge','Stan opon',['Przód 6 mm, Tył 5 mm']],
                            ['triangle-alert','Ślady użytkowania',['5 elementów']],
                            ['file-text','Dokumenty',['8 plików']],
                        ];
                    @endphp
                    @foreach($ccRows as [$ic,$t,$vals])
                    <div class="cc-rep-row">
                        <span class="cc-rep-ico"><x-icon :name="$ic" size="22" :strokeWidth="1.8"/></span>
                        <span class="cc-rep-tx"><b>{{ $t }}</b>@foreach($vals as $vl)<span>{{ $vl }}</span>@endforeach</span>
                    </div>
                    @endforeach
                </div>
                {{-- Numerki 1-5 rozstawione wokol auta jak w referencji: dach, prawy
                     przod, prawy tyl, zderzak tylny, lewy bok. Pozycje w % pudelka
                     diagramu, wiec skaluja sie razem z nim. --}}
                <div class="cc-rep-diagram">
                    <span class="cc-rep-car">
                        <img src="/images/certicheck-car.png" alt="" loading="eager" decoding="async">
                        @for($n = 1; $n <= 5; $n++)
                            <span class="cc-rep-pin p{{ $n }}">{{ $n }}</span>
                        @endfor
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ BENEFITS ═══════════════ --}}
<section class="cclp-sec">
    <div class="cclp-in">
        <div class="cclp-head">
            <div class="cclp-eyebrow-c">Co zyskujesz</div>
            <h2>Co otrzymujesz w CertiCheck</h2>
            <p>Komplet materiałów i informacji, które pokazują stan auta w sposób rzetelny i przejrzysty.</p>
        </div>
        <div class="cclp-cards">
            @php
                $benefits = [
                    ['rotate-3d','Prezentacja 360° nadwozie i wnętrze','Obejrzyj samochód z każdej strony oraz dokładnie wnętrze kabiny.'],
                    ['scan-line','Pomiary grubości lakieru','Sprawdź rzeczywiste wartości lakieru na poszczególnych elementach.'],
                    ['map-pin','Mapa zauważonych śladów','Zaznaczamy rysy, odpryski, wgniecenia i inne ślady normalnej eksploatacji.'],
                    ['circle-gauge','Stan opon i bieżnika','Informacja o oponach oraz zmierzona głębokość bieżnika w mm.'],
                    ['file-text','Dokumenty i historia serwisowa','Zdjęcia dostępnych dokumentów oraz wpisów serwisowych pojazdu.'],
                    ['clipboard-check','Szczegółowy opis stanu pojazdu','Opis wybranych elementów pojazdu oraz zauważonych informacji podczas oględzin.'],
                ];
            @endphp
            @foreach($benefits as [$ico,$t,$d])
                <div class="cclp-card">
                    <div class="cclp-card-ico"><x-icon :name="$ico" size="28" :strokeWidth="1.8"/></div>
                    <h3>{{ $t }}</h3>
                    <p>{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ PRACTICE / EXAMPLE ═══════════════ --}}
<section class="cclp-sec alt">
    <div class="cclp-in">
        <div class="cclp-head">
            <div class="cclp-eyebrow-c">Przykładowy raport</div>
            <h2>Zobacz CertiCheck w praktyce</h2>
            <p>Przykładowy raport dla jednego z naszych samochodów.</p>
        </div>
        <div class="cclp-practice-tabs" id="ccPracticeTabs">
            @php
                $ptabs = [
                    ['360zew','360° zewnętrzne','Obejrzyj auto dookoła — nadwozie z każdej strony.'],
                    ['360wew','360° wewnętrzne','Zajrzyj do kabiny — kokpit i przestrzeń pasażerska.'],
                    ['lakier','Pomiary lakieru','Grubość powłoki w wyznaczonych punktach kontrolnych.'],
                    ['slady','Mapa śladów','Zaznaczone rysy, odpryski i ślady eksploatacji.'],
                    ['opony','Opony','Stan opon i zmierzona głębokość bieżnika.'],
                    ['dok','Dokumenty','Zdjęcia dokumentów i wpisów serwisowych.'],
                ];
            @endphp
            @foreach($ptabs as $i => [$key,$label,$cap])
                <button type="button" class="cclp-ptab {{ $i===0 ? 'active' : '' }}" data-cap="{{ $cap }}" onclick="ccPracticeTab(this)">{{ $label }}</button>
            @endforeach
        </div>

        <div class="cclp-report-card">
            <div class="cclp-rc-visual">
                <div class="cclp-rc-caption" id="ccPracticeCaption">Obejrzyj auto dookoła — nadwozie z każdej strony.</div>
                <svg class="cclp-rc-car" viewBox="0 0 400 150" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#33415a" stroke-width="3" stroke-linejoin="round" stroke-linecap="round">
                    <path d="M24 108c0-4 3-7 7-7h338c4 0 7 3 7 7v6c0 4-3 7-7 7H31c-4 0-7-3-7-7z" fill="#1e293b" stroke="#1e293b"/>
                    <path d="M40 101c-8 0-14-4-14-12 0-6 8-10 20-13l40-24c10-6 22-9 36-9h74c16 0 30 6 44 16l34 24c14 3 26 7 26 15 0 5-5 3-12 3"/>
                    <path d="M96 52l22-18c7-5 15-7 24-7h64c12 0 22 4 32 11l24 21z" fill="#dbe7fb" stroke="#33415a"/>
                    <line x1="176" y1="24" x2="176" y2="52"/>
                    <circle cx="112" cy="104" r="22" fill="#0f172a" stroke="#0f172a"/>
                    <circle cx="112" cy="104" r="9" fill="#64748b" stroke="none"/>
                    <circle cx="300" cy="104" r="22" fill="#0f172a" stroke="#0f172a"/>
                    <circle cx="300" cy="104" r="9" fill="#64748b" stroke="none"/>
                </svg>
                <div class="cclp-rc-turn"></div>
                <div class="cclp-rc-360"><x-icon name="rotate-3d" size="14" :strokeWidth="2.2"/> 360°</div>
            </div>
            <div class="cclp-rc-info">
                <h3 class="cclp-rc-title">BMW X5 xDrive25d</h3>
                <p class="cclp-rc-sub"><b>2017</b> · 163 000 km · Diesel · Automatyczna</p>
                <div class="cclp-rc-specs">
                    @php
                        $specs = [
                            ['cog','Silnik','2.0 Diesel 231 KM'],
                            ['git-commit-horizontal','Skrzynia biegów','Automatyczna'],
                            ['git-fork','Napęd','4x4'],
                            ['car','Nadwozie','SUV'],
                            ['palette','Kolor','Czarny metalik'],
                            ['badge-check','Zarejestrowany w PL','Tak'],
                        ];
                    @endphp
                    @foreach($specs as [$ico,$l,$v])
                    <div class="cclp-rc-spec">
                        <span class="ico"><x-icon :name="$ico" size="16" :strokeWidth="1.9"/></span>
                        <span class="txt"><small>{{ $l }}</small><b>{{ $v }}</b></span>
                    </div>
                    @endforeach
                </div>
                <div class="cclp-rc-summary">
                    @php
                        $sum = [
                            ['Pomiary lakieru','118–164 µm','ok'],
                            ['Stan opon','Przód 6 mm · Tył 5 mm','ok'],
                            ['Zauważone ślady','5 elementów','warn'],
                            ['Dokumenty','8 plików','ok'],
                        ];
                    @endphp
                    @foreach($sum as [$l,$v,$st])
                    <div class="cclp-rc-sumrow">
                        <span class="lbl"><b>{{ $l }}</b><span>{{ $v }}</span></span>
                        <span class="cclp-rc-badge {{ $st }}"><x-icon :name="$st==='ok' ? 'check' : 'alert-triangle'" size="13" :strokeWidth="3"/></span>
                    </div>
                    @endforeach
                    <a class="cclp-rc-cta" href="{{ route('catalog') }}">Zobacz pełny raport <x-icon name="arrow-right" size="14" :strokeWidth="2.4"/></a>
                </div>
            </div>
        </div>
        <p class="cclp-practice-note">
            <x-icon name="info" size="16" :strokeWidth="2"/>
            Raporty CertiCheck pokazują stan pojazdu zauważony w dniu przygotowania materiałów, w zakresie dostępnym bez demontażu elementów.
        </p>
    </div>
</section>

{{-- ═══════════════ PROCESS ═══════════════ --}}
<section class="cclp-sec">
    <div class="cclp-in">
        <div class="cclp-head">
            <div class="cclp-eyebrow-c">Jak to działa</div>
            <h2>Jak powstaje CertiCheck</h2>
            <p>Prosty proces, który zapewnia rzetelność i przejrzystość.</p>
        </div>
        <div class="cclp-steps">
            @php
                $steps = [
                    ['1','Oględziny samochodu','Oglądamy pojazd i dokumentujemy jego stan oraz wyposażenie.'],
                    ['2','Pomiary i materiały','Wykonujemy pomiary lakieru, prezentację 360° i zdjęcia oraz zbieramy dokumenty.'],
                    ['3','Opis i mapa śladów','Opisujemy wybrane elementy i zaznaczamy wszystkie zauważone ślady użytkowania.'],
                    ['4','Publikacja przy ogłoszeniu','Materiały CertiCheck udostępniamy w ogłoszeniu, abyś wiedział więcej przed przyjazdem.'],
                ];
            @endphp
            @foreach($steps as [$n,$t,$d])
                <div class="cclp-step">
                    <div class="cclp-step-num">{{ $n }}</div>
                    <h3>{{ $t }}</h3>
                    <p>{{ $d }}</p>
                    <span class="cclp-step-arrow"><x-icon name="arrow-right" size="20" :strokeWidth="2.4"/></span>
                </div>
            @endforeach
        </div>
        <div class="cclp-infobar">
            <x-icon name="info" size="18" :strokeWidth="2"/>
            Materiały pokazują stan pojazdu zauważony w dniu ich przygotowania, w zakresie dostępnym bez demontażu elementów.
        </div>
    </div>
</section>

{{-- ═══════════════ SCOPE ═══════════════ --}}
<section class="cclp-sec alt">
    <div class="cclp-in">
        <div class="cclp-head">
            <div class="cclp-eyebrow-c">Zakres</div>
            <h2>Co CertiCheck obejmuje, a czego nie</h2>
            <p>Jasno pokazujemy, na co odpowiadają materiały CertiCheck, a czego nie zastępują.</p>
        </div>
        <div class="cclp-scope-grid">
            <div class="cclp-scope-col ok">
                <div class="cclp-scope-title">
                    <span class="ico"><x-icon name="check" size="16" :strokeWidth="2.6"/></span>
                    CertiCheck obejmuje
                </div>
                <ul class="cclp-scope-list">
                    @foreach([
                        'Pomiary grubości lakieru w wybranych punktach kontrolnych',
                        'Materiały zdjęciowe oraz prezentacje 360°',
                        'Zaznaczenie wszystkich zauważonych śladów użytkowania',
                        'Informacje o oponach i głębokości bieżnika',
                        'Zdjęcia dostępnych dokumentów i historii serwisowej',
                        'Opis zauważonego stanu wybranych elementów pojazdu',
                    ] as $li)
                    <li><span class="li-ico"><x-icon name="check" size="12" :strokeWidth="3"/></span>{{ $li }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="cclp-scope-col no">
                <div class="cclp-scope-title">
                    <span class="ico"><x-icon name="x" size="16" :strokeWidth="2.6"/></span>
                    CertiCheck nie obejmuje
                </div>
                <ul class="cclp-scope-list">
                    @foreach([
                        'Pełnej diagnostyki technicznej ani komputerowej',
                        'Demontażu elementów pojazdu',
                        'Ekspertyzy rzeczoznawcy ani certyfikatu bezwypadkowości',
                        'Gwarancji wykrycia wszystkich wad, w tym ukrytych',
                        'Oceny wnętrza silnika, skrzyni biegów i innych podzespołów',
                        'Zastąpienia oględzin, jazdy próbnej ani kontroli w serwisie',
                    ] as $li)
                    <li><span class="li-ico"><x-icon name="x" size="12" :strokeWidth="3"/></span>{{ $li }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ FAQ ═══════════════ --}}
<section class="cclp-sec">
    <div class="cclp-in">
        <div class="cclp-head">
            <div class="cclp-eyebrow-c">Masz pytania?</div>
            <h2>Najczęściej zadawane pytania</h2>
            <p>Zebraliśmy najważniejsze pytania o zakres i charakter materiałów CertiCheck.</p>
        </div>
        <div class="cclp-faq-grid">
            @php
                $faqs = [
                    ['Czy CertiCheck to badanie techniczne?','Nie. CertiCheck to rozszerzona prezentacja stanu widocznego pojazdu — nie zastępuje badania technicznego, opinii rzeczoznawcy ani diagnostyki komputerowej. Pokazujemy to, co da się zobaczyć i zmierzyć bez demontażu.'],
                    ['Czy zakres CertiCheck jest taki sam przy każdym aucie?','Zakres może różnić się w zależności od pojazdu i dostępnej dokumentacji. Staramy się przygotować komplet materiałów, ale niektóre elementy mogą być niedostępne dla danego egzemplarza.'],
                    ['Czy CertiCheck zastępuje sprawdzenie auta w serwisie?','Nie. Zawsze zalecamy oględziny, jazdę próbną oraz — przy chęci głębszej weryfikacji — sprawdzenie auta u niezależnego mechanika lub w stacji diagnostycznej.'],
                    ['Czy raport CertiCheck jest aktualny?','Materiały przedstawiają stan pojazdu na dzień ich przygotowania. Jeśli od publikacji minęło więcej czasu, aktualny stan potwierdzamy podczas oględzin.'],
                    ['Czy CertiCheck wykrywa wszystkie wady samochodu?','Nie. Materiały pokazują ślady i stan zauważone w dniu przygotowania, w zakresie dostępnym bez demontażu. Nie stanowią gwarancji wykrycia wszystkich wad, w tym ukrytych.'],
                    ['Czy mogę pobrać raport CertiCheck?','Tak. Materiały CertiCheck są dostępne na stronie ogłoszenia, a podsumowanie możesz pobrać w formacie PDF — bez logowania i bezpłatnie.'],
                ];
            @endphp
            <div>
                @foreach(array_slice($faqs,0,3) as $i => [$q,$a])
                <details class="cclp-faq-item" @if($i===0) open @endif>
                    <summary class="cclp-faq-q">{{ $q }} <x-icon name="chevron-down" size="18" :strokeWidth="2.4"/></summary>
                    <div class="cclp-faq-a">{{ $a }}</div>
                </details>
                @endforeach
            </div>
            <div>
                @foreach(array_slice($faqs,3,3) as [$q,$a])
                <details class="cclp-faq-item">
                    <summary class="cclp-faq-q">{{ $q }} <x-icon name="chevron-down" size="18" :strokeWidth="2.4"/></summary>
                    <div class="cclp-faq-a">{{ $a }}</div>
                </details>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ DISCLAIMER ═══════════════ --}}
<section class="cclp-disc">
    <div class="cclp-disc-in">
        <div class="cclp-disc-ico"><x-icon name="shield-check" size="20" :strokeWidth="2.2"/></div>
        <div>
            <h3>Ważne informacje o zakresie CertiCheck</h3>
            <p>CertiCheck jest materiałem informacyjnym przedstawiającym zauważony stan pojazdu w dniu przygotowania materiałów. Nie stanowi ekspertyzy technicznej, zapewnienia o braku wad ani gwarancji wykrycia wszystkich usterek, w tym wad ukrytych. Zakres materiałów może różnić się w zależności od pojazdu i dostępnej dokumentacji. Klient ma możliwość przeprowadzenia oględzin, jazdy próbnej oraz sprawdzenia samochodu w wybranym serwisie przed zakupem.</p>
        </div>
    </div>
</section>

{{-- ═══════════════ BOTTOM CTA ═══════════════ --}}
<section class="cclp-cta-strip">
    <div class="cclp-cta-strip-in">
        <div>
            <h3>Poznaj samochód jeszcze przed przyjazdem</h3>
            <p>Zobacz pojazdy objęte rozszerzoną prezentacją CertiCheck.</p>
        </div>
        <div class="cclp-cta-strip-btns">
            <a class="cclp-btn-solid" href="{{ route('catalog') }}">Zobacz auta z CertiCheck <x-icon name="arrow-right" size="15" :strokeWidth="2.4"/></a>
            <a class="cclp-btn-ghost" href="{{ route('catalog') }}">Przejdź do całej oferty</a>
        </div>
    </div>
</section>

<script>
function ccPracticeTab(btn){
    document.querySelectorAll('#ccPracticeTabs .cclp-ptab').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    var cap = document.getElementById('ccPracticeCaption');
    if(cap && btn.dataset.cap) cap.textContent = btn.dataset.cap;
}
</script>

@endsection
