@extends('layouts.public')
@section('meta_title_full','O nas — komis samochodowy pod Stargardem | CertiCars')
@section('title','O nas')
@section('description','CertiCars z Lipnika koło Stargardu — auta osobowe i dostawcze. Pokazujemy dostępne informacje o autach, pomagamy w formalnościach, ubezpieczeniu, transporcie i zakupie na odległość.')
@section('og_title','O nas — komis samochodowy pod Stargardem | CertiCars')
@section('og_description','CertiCars z Lipnika koło Stargardu — auta osobowe i dostawcze. Pokazujemy dostępne informacje o autach i pomagamy przejść przez zakup: formalności, ubezpieczenie, transport, zakup na odległość.')


@section('styles')
/* =====================================================================
   O NAS: projekt zaakceptowany przez klienta (październik 2026).
   Wszystkie reguły są zamknięte w .onas, żeby nie ruszać stylów serwisu
   (.container, .button, .hero, .icon itd. mają tu własne znaczenie).
   ===================================================================== */
.onas{--blue:#0866ff;--navy:#091b3c;--ink:#101b2d;--muted:#637087;--line:#e2eaf5;--ice:#f2f7ff;--container:1000px;--wide:1080px}
.onas,.onas *{box-sizing:border-box}
.onas{background:white;color:var(--ink);font-family:inherit;font-size:16px;font-weight:400;-webkit-font-smoothing:antialiased}
.onas button,.onas a{font:inherit}
.onas button{cursor:pointer}
.onas a{text-decoration:none;color:inherit}
.onas button{border:0;background:none}
.onas svg{width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0}
.onas .defs{position:absolute;width:0;height:0;overflow:hidden}
.onas .container{width:var(--container);margin:0 auto}
.onas .wide{width:var(--wide);margin:0 auto}
.onas h1,.onas h2,.onas h3,.onas h4,.onas p{margin:0}
.onas h1,.onas h2{font-weight:800;letter-spacing:-1.3px}
.onas h2{font-size:34px;line-height:1.2}
.onas h3{font-size:17px;line-height:1.4;font-weight:800;letter-spacing:-.35px}
.onas p{color:var(--muted);font-size:15px;line-height:1.8}
.onas .eyebrow{display:flex;align-items:center;gap:12px;font-size:10px;letter-spacing:1.5px;font-weight:800;color:var(--blue);margin-bottom:18px;line-height:16px}
.onas .eyebrow:before{content:'';width:24px;height:2px;background:currentColor}
.onas .eyebrow.light{color:#8fc3ff}
.onas .button{display:inline-flex;align-items:center;justify-content:center;gap:12px;height:50px;padding:0 23px;border-radius:9px;font-size:13px;font-weight:800;white-space:nowrap}
.onas .button svg{width:18px;height:18px}
.onas .primary{background:var(--blue);color:white;box-shadow:0 7px 16px #0066ff1f}
.onas .secondary{border:1px solid #ffffff38;background:#ffffff04;color:white}
.onas .text-link{display:inline-flex;align-items:center;gap:12px;color:var(--blue);font-size:14px;font-weight:800;line-height:24px}
.onas .text-link svg{width:19px;height:19px}
.onas .icon{display:flex;align-items:center;justify-content:center;width:46px;height:46px;background:#eef5ff;color:var(--blue);border-radius:12px}
.onas .icon svg{width:24px;height:24px}
.onas .hero{height:576px;position:relative;overflow:hidden;background:radial-gradient(ellipse at 74% 50%,#0b51aa 0,#073370 30%,#062555 47%,#071c41 80%);color:white}
.onas .hero-inner{height:100%;position:relative}
.onas .hero-copy{position:relative;z-index:2;padding-top:92px;width:610px}
.onas .hero h1{font-size:49px;line-height:1.16;letter-spacing:-1.9px}
.onas .hero h1 span{color:#4b95ff}
.onas .hero-copy>p{margin-top:22px;font-size:15px;line-height:1.8;color:#c7d6ed}
.onas .actions{display:flex;gap:12px;margin-top:28px}
.onas .hero-location{display:flex;align-items:center;gap:9px;margin-top:26px;font-size:11px;color:#b4cae8}
.onas .hero-location svg{width:14px;height:14px;color:#529aff}
.onas .dot{width:3px;height:3px;background:#7e9bc4;border-radius:50%;margin:0 2px}
.onas .hero-art{position:absolute;inset:0;pointer-events:none;overflow:visible}
.onas .hero-art img{position:absolute;bottom:-139px;right:-18px;width:400px;display:block;z-index:1;filter:drop-shadow(0 10px 24px #00112d25)}
.onas .hero-glow{width:470px;height:420px;position:absolute;right:-50px;top:45px;background:radial-gradient(ellipse,#1179ff4d,transparent 68%)}
.onas .hero-curve{position:absolute;bottom:-1px;left:0;width:100%;height:58px;stroke:none;z-index:3}
.onas .approach{padding:70px 0 66px}
.onas .section-intro{display:flex;justify-content:space-between;gap:55px;align-items:center}
.onas .section-intro>div{flex:1}
.onas .section-intro>p{width:470px;font-size:15px;line-height:1.85}
.onas .section-intro .eyebrow{margin-bottom:13px}
.onas .principles{display:grid;grid-template-columns:repeat(3,1fr);margin-top:42px}
.onas .principles article{padding:0 30px;border-left:1px solid var(--line)}
.onas .principles article:first-child{padding-left:0;border:0}
.onas .principles article:last-child{padding-right:0}
.onas .principles .icon{margin-bottom:17px}
.onas .principles h3{margin-bottom:9px}
.onas .principles p{font-size:14px;line-height:1.7}
.onas .certi-section{padding:12px 0 20px}
.onas .certi-panel{border:1px solid #e1ebfa;border-radius:24px;background:linear-gradient(115deg,#f0f6ff,#f7faff);padding:44px 40px 24px}
.onas .certi-main{display:flex;gap:40px;align-items:center}
.onas .certi-copy{width:430px;flex-shrink:0}
.onas .certi-copy h2{font-size:31px;line-height:1.23;letter-spacing:-1.1px}
.onas .certi-copy>p{margin:19px 0 22px;font-size:14px;line-height:1.85;width:380px}
.onas .report{flex:1;border:1px solid #e5edf8;border-radius:15px;background:white;box-shadow:0 14px 30px #24446f08;overflow:hidden}
.onas .report-top{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #edf2f9;font-size:9px;letter-spacing:1px;font-weight:800;color:#687a96}
.onas .report-dot{width:6px;height:6px;border-radius:50%;background:#a9caff}
.onas .report-content{display:flex;padding:18px;gap:19px}
.onas .car-map{width:127px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(#f7fbff,#eaf3ff);border:1px solid #edf4fd;border-radius:9px}
.onas .car-map img{width:105px;height:218px;object-fit:contain}
.onas .report-rows{flex:1}
.onas .report-row{display:flex;gap:10px;padding:12px 0;border-bottom:1px solid #eef2f8;align-items:flex-start}
.onas .report-row:last-child{border:0}
.onas .report-row>svg{width:18px;height:18px;color:var(--blue);margin-top:1px}
.onas .report-row h4{font-size:13px;line-height:1.4;font-weight:800;letter-spacing:-.1px}
.onas .report-row p{font-size:11px;line-height:1.7;margin-top:5px}
.onas .report-caption{font-size:10px;color:#7b8ba2;background:#fbfdff;text-align:center;border-top:1px solid #f0f4fa;padding:9px}
.onas .scope-note{border-top:1px solid #dfe9f7;display:flex;gap:10px;align-items:flex-start;margin-top:28px;padding-top:17px}
.onas .scope-note svg{width:15px;height:15px;margin-top:3px;color:#6087b9}
.onas .scope-note p{font-size:11px;color:#71809a;line-height:1.7}
.onas .services{padding:66px 0 62px}
.onas .service-intro{margin-bottom:32px}
.onas .service-intro>p{width:285px}
.onas .service-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.onas .service-grid article{display:flex;align-items:flex-start;gap:13px;border:1px solid var(--line);border-radius:13px;padding:21px 17px;min-height:124px;background:linear-gradient(145deg,#fff,#fdfefe)}
.onas .service-grid .icon{width:40px;height:40px;border-radius:10px;flex-shrink:0}
.onas .service-grid .icon svg{width:21px;height:21px}
.onas .service-grid h3{font-size:14px;margin-top:2px;letter-spacing:-.15px}
.onas .service-grid p{font-size:12px;line-height:1.75;margin-top:7px}
.onas .services-note{font-size:11px;line-height:1.8;color:#7b899f;margin-top:21px;padding-top:18px;border-top:1px solid var(--line)}
.onas .remote-section{padding:10px 0 20px}
.onas .remote-panel{border:1px solid #e1ebfa;border-radius:24px;padding:43px 40px;background:linear-gradient(120deg,#f2f7ff,#eef5ff);display:flex;align-items:center;gap:30px}
.onas .remote-copy{width:440px;flex-shrink:0}
.onas .remote-copy h2{font-size:31px;letter-spacing:-1px}
.onas .remote-copy>p{font-size:14px;line-height:1.8;margin:18px 0 20px;max-width:395px}
.onas .remote-details{display:flex;gap:24px;border-top:1px solid #dfe9f8;padding-top:22px;margin-top:25px}
.onas .remote-details span{display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;color:#566d8e}
.onas .remote-details svg{width:16px;height:16px;color:var(--blue)}
.onas .remote-visual{width:100%;padding-top:9px}
.onas .laptop{position:relative;width:100%;margin:0 auto}
.onas .laptop-screen{border:8px solid #16233c;border-bottom-width:12px;border-radius:13px 13px 7px 7px;background:white;margin:0 21px;overflow:hidden;box-shadow:0 7px 25px #0d3a7624}
.onas .call-top{display:flex;align-items:center;justify-content:space-between;padding:11px 12px;border-bottom:1px solid #e4edfa}
.onas .call-top>div{display:flex;gap:7px;align-items:center;font-size:8px;font-weight:700;color:#516a90}
.onas .call-top svg{width:12px;height:12px;color:var(--blue)}
.onas .call-indicator{width:5px;height:5px;background:#3987ee;border-radius:50%}
.onas .call-main{height:166px;background:linear-gradient(160deg,#f1f8ff,#d8eafe);position:relative;display:flex;align-items:center}
.onas .car-illustration{width:100%;height:158px;stroke:none}
.onas .call-thumb{position:absolute;right:10px;top:10px;width:47px;height:35px;background:#bad4f4;border:2px solid white;border-radius:4px;display:flex;align-items:center;justify-content:center}
.onas .call-thumb svg{width:16px;height:16px;color:#6a9cd6}
.onas .call-controls{display:flex;gap:10px;justify-content:center;padding:10px;background:white}
.onas .call-controls>span{width:23px;height:23px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#edf2fa;color:#6a7c98}
.onas .call-controls svg{width:11px;height:11px}
.onas .call-controls .hangup{background:#e56c76;color:white}
.onas .hangup svg{transform:rotate(135deg)}
.onas .laptop-base{width:100%;height:13px;border-radius:2px 2px 50% 50%;background:linear-gradient(#bfcbd9,#e6edf5 35%,#a3b3c6 90%);position:relative;box-shadow:0 15px 22px -12px #24446f38}
.onas .laptop-base i{position:absolute;left:40%;width:20%;height:5px;background:#91a3bb;border-radius:0 0 4px 4px}
.onas .visual-caption{font-size:10px;text-align:center;margin-top:21px;color:#7b90ad}
.onas .purchase{padding:68px 0 60px}
.onas .purchase .eyebrow{margin-bottom:13px}
.onas .steps{display:grid;grid-template-columns:repeat(4,1fr);gap:29px;margin-top:35px;position:relative}
.onas .steps:before{content:'';position:absolute;height:1px;top:18px;left:17px;right:20px;background:#dbe7f8}
.onas .steps article{position:relative}
.onas .step-number{position:relative;background:var(--blue);color:white;width:36px;height:36px;border:5px solid white;outline:1px solid #dce9fc;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;box-sizing:content-box;margin-top:-5px;margin-left:-5px}
.onas .steps h3{font-size:14px;margin:17px 0 8px}
.onas .steps p{font-size:12px;line-height:1.75}
.onas .contact-section{padding:9px 0 80px}
.onas .contact-panel{background:radial-gradient(ellipse at 110% 100%,#153366 0,#101f44 45%,#0c1b3c 80%);border-radius:23px;padding:39px 40px;display:flex;justify-content:space-between;align-items:center;gap:40px;color:white}
.onas .contact-panel h2{font-size:29px;line-height:1.25;letter-spacing:-.9px}
.onas .contact-panel p{font-size:13px;line-height:1.8;margin-top:15px;color:#becce3}
.onas .contact-actions{display:flex;flex-direction:column;align-items:flex-end;gap:18px}
.onas .contact-actions .button{height:50px;font-size:13px}
.onas .light-link{color:#c5d4ec;font-size:11px;font-weight:600}
.onas .light-link svg{width:15px;height:15px}
@media(max-width:1100px){.onas .container{width:calc(100% - 64px)}
.onas .wide{width:calc(100% - 40px)}
.onas .hero-copy{width:60%}
.onas .hero h1{font-size:42px}
.onas .hero-art img{right:0;width:370px}
.onas .certi-copy{width:43%}
.onas .certi-copy>p{width:100%}
.onas .certi-main{gap:25px}
.onas .certi-panel,.onas .remote-panel{padding-left:30px;padding-right:30px}
.onas .car-map{width:100px}
.onas .car-map img{width:85px}
.onas .report-content{padding:13px;gap:14px}
.onas .remote-copy{width:45%}
.onas .service-grid article{padding:17px 12px;gap:9px}
.onas .service-grid h3{font-size:12px}
.onas .service-grid p{font-size:10px}}
@media(max-width:760px){.onas h2{font-size:29px}
.onas .container,.onas .wide{width:calc(100% - 40px)}
.onas .hero{height:830px}
.onas .hero-copy{width:100%;padding-top:52px}
.onas .hero h1{font-size:37px;letter-spacing:-1.4px}
.onas .hero-copy>p{font-size:13px}
.onas .hero-location{font-size:9px;gap:7px}
.onas .hero-art img{width:275px;right:calc(50% - 137px);bottom:-121px}
.onas .hero-glow{right:0;top:370px;width:100%}
.onas .section-intro{display:block}
.onas .section-intro>p{width:100%;margin-top:23px;font-size:14px}
.onas .approach{padding:48px 0}
.onas .principles{grid-template-columns:1fr;gap:21px;margin-top:30px}
.onas .principles article,.onas .principles article:first-child,.onas .principles article:last-child{position:relative;border:0;border-bottom:1px solid var(--line);padding:0 0 22px 65px;min-height:90px}
.onas .principles article:last-child{border:0;padding-bottom:0}
.onas .principles .icon{position:absolute;left:0;top:0}
.onas .principles p{font-size:13px}
.onas .certi-panel,.onas .remote-panel{padding:28px 22px;border-radius:19px}
.onas .certi-main{display:block}
.onas .certi-copy{width:100%}
.onas .certi-copy h2,.onas .remote-copy h2{font-size:27px}
.onas .certi-copy>p{font-size:13px}
.onas .report{margin-top:28px}
.onas .report-content{padding:12px;gap:15px}
.onas .report-row h4{font-size:11px}
.onas .report-row p{font-size:9px}
.onas .scope-note p{font-size:10px}
.onas .service-intro>p br{display:none}
.onas .services{padding:44px 0}
.onas .service-grid{grid-template-columns:1fr;gap:10px}
.onas .service-grid article{padding:18px;min-height:100px;gap:16px}
.onas .service-grid h3{font-size:15px}
.onas .service-grid p{font-size:12px}
.onas .service-grid p br{display:none}
.onas .services-note{font-size:10px}
.onas .remote-panel{display:block}
.onas .remote-copy{width:100%}
.onas .remote-copy>p{font-size:13px}
.onas .remote-visual{margin-top:33px}
.onas .call-main{height:135px}
.onas .car-illustration{height:130px}
.onas .laptop-screen{margin:0 13px;border-width:6px}
.onas .call-top{padding:8px}
.onas .visual-caption{font-size:9px}
.onas .purchase{padding:49px 0}
.onas .steps{grid-template-columns:1fr 1fr;gap:28px 18px}
.onas .steps:before{display:none}
.onas .steps h3{font-size:13px}
.onas .steps p{font-size:11px}
.onas .contact-section{padding:0 0 45px}
.onas .contact-panel{padding:29px 24px;display:block}
.onas .contact-panel h2{font-size:25px}
.onas .contact-panel p{font-size:12px}
.onas .contact-actions{align-items:flex-start;margin-top:26px}
.onas .contact-actions .button{font-size:12px}}
.onas .car-render{width:100%;height:100%;object-fit:contain;padding:8px 8px 2px}
.onas .call-main{overflow:hidden}
.onas .human-section{padding:72px 0 46px}
.onas .human-layout{display:grid;grid-template-columns:370px 1fr;gap:66px;align-items:start}
.onas .human-title h2{font-size:35px;line-height:1.22;letter-spacing:-1.3px}
.onas .human-title .eyebrow{font-size:9px;letter-spacing:1.2px;margin-bottom:18px}
.onas .human-accent{width:43px;height:3px;background:var(--blue);margin:23px 0 19px}
.onas .human-title>p{font-size:13px;color:#7b8aa1;line-height:1.8}
.onas .human-story{padding-top:29px}
.onas .human-story p{font-size:15px;line-height:1.9;color:#617087}
.onas .human-story p+p{margin-top:18px}
.onas .human-story strong{font-weight:700;color:#172940}
.onas .purchase-early{padding:0 0 44px}
.onas .purchase-early>.container{border-top:1px solid var(--line);padding-top:43px}
.onas .purchase-early h2{font-size:30px}
.onas .purchase-early .steps{margin-top:30px}
.onas .explore-section{padding:23px 0 52px}
.onas .explore-layout{display:grid;grid-template-columns:460px 1fr;gap:64px;align-items:center}
.onas .explore-visual{height:300px;position:relative;isolation:isolate}
.onas .explore-disc{position:absolute;left:30px;right:30px;top:27px;height:224px;border-radius:50%;background:radial-gradient(ellipse,#e9f3ff 0,#f8fbff 65%,#fff 73%);z-index:-1}
.onas .explore-visual>img{position:absolute;width:350px;height:222px;object-fit:contain;left:55px;top:36px;z-index:2}
.onas .angle-label{position:absolute;top:0;right:17px;color:#0765f5;font-size:34px;font-weight:800;letter-spacing:-1.5px;z-index:3}
.onas .angle-label>span{font-size:26px;vertical-align:top;line-height:1.2}
.onas .rotation-orbit{position:absolute;top:71px;left:0;width:460px;height:206px;stroke-width:2;z-index:1}
.onas .orbit-back{stroke:#d6e6ff;stroke-dasharray:4 6}
.onas .orbit-front{stroke:#3985ed}
.onas .explore-labels{position:absolute;bottom:3px;left:0;width:100%;display:flex;justify-content:center;gap:28px;color:#4271b3}
.onas .explore-labels>span{display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700}
.onas .explore-labels svg{width:17px;height:17px;color:var(--blue)}
.onas .explore-copy h2{font-size:31px;line-height:1.25;letter-spacing:-1.05px}
.onas .explore-copy>p{font-size:14px;line-height:1.85;margin:18px 0 21px}
.onas .explore-copy button{padding:0}
.onas .certi-v2 .certi-panel{padding-top:39px}
.onas .certi-v2 .certi-main{gap:50px;align-items:center}
.onas .certi-v2 .certi-copy{width:440px}
.onas .certi-v2 .certi-copy>p{width:400px;margin:17px 0 23px;font-size:14px}
.onas .certi-v2 .eyebrow{font-size:9px;letter-spacing:1.05px}
.onas .certi-features{display:grid;grid-template-columns:1fr;gap:16px;margin:0 0 25px}
.onas .certi-features>div{display:flex;gap:13px;align-items:flex-start}
.onas .certi-features>div>svg{width:21px;height:21px;margin-top:2px;color:var(--blue)}
.onas .certi-features h3{font-size:13px;letter-spacing:-.15px}
.onas .certi-features p{font-size:11px;line-height:1.75;margin-top:3px}
.onas .condition-view{width:508px;flex:1;min-width:0;border:1px solid #e0e9f5;border-radius:16px;background:white;overflow:hidden;box-shadow:0 16px 35px #24446f08}
.onas .condition-head{padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #e9eff8}
.onas .condition-head>span:first-child{font-size:12px;font-weight:800;color:#263a57}
.onas .condition-head>span:last-child{font-size:8px;font-weight:700;letter-spacing:.7px;color:#7e94b0}
.onas .provided-map{display:block;width:100%;height:354px;object-fit:contain;background:white}
.onas .condition-caption{padding:13px 15px;display:flex;justify-content:center;gap:7px;align-items:center;background:#fbfdff;border-top:1px solid #e9eff8;color:#71829a;font-size:10px}
.onas .condition-caption svg{width:14px;height:14px;color:#5c8cd1}
.onas .certi-v2 .scope-note{margin-top:29px}
.onas .remote-v2{padding-bottom:61px}
.onas .remote-v2 .remote-panel{display:grid;grid-template-columns:395px 322px 1fr;gap:26px;padding:40px}
.onas .remote-v2 .remote-copy{width:auto}
.onas .remote-v2 .remote-copy h2{font-size:27px;line-height:1.25;letter-spacing:-.9px}
.onas .remote-v2 .remote-copy>p{font-size:12px;line-height:1.8;max-width:360px;margin:16px 0 20px}
.onas .remote-v2 .remote-copy .eyebrow{font-size:9px;letter-spacing:1.15px;margin-bottom:15px}
.onas .remote-v2 .text-link{font-size:12px;gap:8px}
.onas .remote-v2 .remote-visual{padding:0;width:322px}
.onas .remote-v2 .laptop-screen{margin:0 17px;border-width:6px;border-bottom-width:8px;border-radius:9px 9px 5px 5px}
.onas .remote-v2 .call-main{height:128px}
.onas .remote-v2 .call-top{padding:7px 9px}
.onas .remote-v2 .call-top>div{font-size:6px;gap:5px}
.onas .remote-v2 .call-top svg{width:10px;height:10px}
.onas .remote-v2 .call-thumb{width:34px;height:27px;top:7px;right:7px}
.onas .remote-v2 .call-controls{padding:6px;gap:8px}
.onas .remote-v2 .call-controls>span{width:18px;height:18px}
.onas .remote-v2 .call-controls svg{width:9px;height:9px}
.onas .remote-v2 .laptop-base{height:10px}
.onas .remote-list{display:flex;flex-direction:column;gap:27px;align-self:center}
.onas .remote-list>div{display:flex;align-items:center;gap:10px;font-size:11px;font-weight:600;white-space:nowrap}
.onas .remote-list svg{width:17px;height:17px;color:var(--blue)}
@media(max-width:1100px){.onas .human-layout{grid-template-columns:34% 1fr;gap:40px}
.onas .explore-layout{grid-template-columns:43% 1fr;gap:35px}
.onas .explore-visual{transform:scale(.88);transform-origin:center center}
.onas .explore-visual,.onas .rotation-orbit{width:410px}
.onas .explore-visual>img{left:30px}
.onas .angle-label{right:20px}
.onas .certi-v2 .certi-copy{width:45%}
.onas .certi-v2 .certi-copy>p{width:100%}
.onas .certi-v2 .certi-main{gap:30px}
.onas .remote-v2 .remote-panel{grid-template-columns:42% 35% 1fr;gap:17px;padding:30px}
.onas .remote-v2 .remote-visual{width:100%}
.onas .remote-v2 .remote-copy h2{font-size:25px}
.onas .remote-list>div{font-size:9px;gap:7px}}
@media(max-width:760px){.onas .human-section{padding:47px 0 38px}
.onas .human-layout{display:block}
.onas .human-title h2{font-size:30px}
.onas .human-title>p{font-size:12px}
.onas .human-accent{margin:20px 0 14px}
.onas .human-story{padding-top:25px}
.onas .human-story p{font-size:14px;line-height:1.9}
.onas .human-story p+p{margin-top:15px}
.onas .purchase-early{padding:0 0 35px}
.onas .purchase-early>.container{padding-top:32px}
.onas .purchase-early h2{font-size:27px}
.onas .explore-section{padding:0 0 37px}
.onas .explore-layout{display:flex;flex-direction:column-reverse;gap:20px}
.onas .explore-copy h2{font-size:27px}
.onas .explore-copy>p{font-size:13px}
.onas .explore-visual{width:100%;height:260px;transform:none}
.onas .explore-visual>img{width:290px;height:195px;left:calc(50% - 145px);top:25px}
.onas .rotation-orbit{width:100%;height:190px;top:48px}
.onas .angle-label{font-size:29px;right:9px;top:2px}
.onas .explore-disc{left:10px;right:10px;top:10px;height:220px}
.onas .explore-labels{gap:23px;font-size:10px;bottom:0}
.onas .certi-v2 .certi-panel{padding:28px 22px 20px}
.onas .certi-v2 .certi-main{display:block}
.onas .certi-v2 .certi-copy{width:100%}
.onas .certi-v2 .certi-copy>p{font-size:13px}
.onas .certi-features h3{font-size:13px}
.onas .certi-features p{font-size:11px}
.onas .condition-view{width:100%;margin-top:28px}
.onas .provided-map{height:290px}
.onas .condition-head{padding:14px}
.onas .condition-head>span:first-child{font-size:10px}
.onas .condition-head>span:last-child{font-size:7px}
.onas .condition-caption{font-size:9px}
.onas .remote-v2{padding-bottom:42px}
.onas .remote-v2 .remote-panel{display:block;padding:28px 22px}
.onas .remote-v2 .remote-copy h2{font-size:26px}
.onas .remote-v2 .remote-copy>p{font-size:13px}
.onas .remote-v2 .remote-visual{width:100%;margin-top:27px}
.onas .remote-v2 .call-main{height:128px}
.onas .remote-list{flex-direction:row;flex-wrap:wrap;gap:16px;margin-top:25px}
.onas .remote-list>div{font-size:9px;gap:6px}
.onas .remote-list svg{width:14px;height:14px}}
.onas [hidden]{display:none!important}
.onas button:focus-visible,.onas a:focus-visible{outline:3px solid #6eabff;outline-offset:4px}
.onas .human-section{padding-bottom:49px}
.onas .human-title .brand-motto{font-size:40px;line-height:1.23;letter-spacing:-1.5px}
.onas .brand-motto span{color:var(--blue);font-size:35px;font-weight:600;letter-spacing:-1.4px}
.onas .human-title>p{margin-top:21px;color:#77869d}
.onas .human-story{padding-top:33px}
.onas .purchase-early{padding-bottom:0}
.onas .purchase-early>.container{border-bottom:1px solid var(--line);padding-bottom:47px}
.onas .explore-section{padding:54px 0 53px}
.onas .explore-layout{grid-template-columns:520px 1fr;gap:44px}
.onas .real-viewer{overflow:hidden;border:1px solid var(--line);border-radius:17px;background:white;box-shadow:0 10px 28px #243d5908;min-width:0}
.onas .viewer-tabs{display:flex;gap:5px;padding:9px;background:#f5f8fd}
.onas .viewer-tabs button{display:flex;align-items:center;justify-content:center;gap:8px;flex:1;padding:10px 7px;font-size:12px;font-weight:700;color:#687b98;border-radius:9px;white-space:nowrap}
.onas .viewer-tabs svg{width:17px;height:17px}
.onas .viewer-tabs button[aria-selected=true]{color:var(--blue);background:white;box-shadow:0 2px 6px #24446f09}
.onas .viewer-stage{position:relative;aspect-ratio:16/9;overflow:hidden;cursor:grab;touch-action:pan-y;background:#e8ebee;user-select:none}
.onas .viewer-stage.dragging{cursor:grabbing}
.onas .viewer-stage>[role=tabpanel]{position:absolute;inset:0}
.onas .viewer-stage img,.onas .viewer-stage canvas{display:block;width:100%;height:100%;object-fit:cover}
.onas .viewer-badge{position:absolute;top:14px;left:14px;padding:5px 11px;border-radius:7px;background:#fffef7ed;color:#1c2d44;font-size:13px;font-weight:800;box-shadow:0 2px 6px #12253f12;pointer-events:none}
.onas .viewer-controls{position:absolute;bottom:12px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:9px;border-radius:24px;padding:4px;background:#11243adc;white-space:nowrap;color:white;box-shadow:0 3px 10px #0002}
.onas .viewer-controls>span{font-size:10px;font-weight:600;min-width:155px;text-align:center}
.onas .viewer-controls button{width:30px;height:30px;border-radius:50%;background:#ffffff18;color:white;font-size:19px;line-height:1}
.onas .viewer-caption{display:flex;align-items:center;justify-content:space-between;padding:13px 16px;font-size:9px;color:#8190a6}
.onas .viewer-caption>span:last-child{font-weight:800;color:#516783}
.onas .pano-loading{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:13px;color:#5b6c84;background:#edf2f8}
.onas .explore-copy h2{font-size:31px}
.onas .certi-v2 .certi-main{gap:35px}
.onas .certi-v2 .certi-copy{width:365px}
.onas .certi-v2 .certi-copy>p{width:335px}
.onas .check-viewer{background:#fff;border:1px solid #dee8f5;border-radius:16px;flex:1;min-width:0;overflow:hidden;box-shadow:0 14px 32px #24446f08}
.onas .check-header{padding:19px 20px 14px;display:flex;align-items:center;justify-content:space-between;gap:8px}
.onas .check-header>strong{font-size:12px;font-weight:800;color:#273c59}
.onas .check-header>span{font-size:8px;font-weight:700;letter-spacing:.2px;color:#8493a9}
.onas .check-tabs{display:flex;margin:0 20px;padding:4px;gap:4px;background:#f2f6fc;border-radius:10px}
.onas .check-tabs button{display:flex;align-items:center;justify-content:center;flex:1;padding:10px 5px;gap:7px;border-radius:7px;font-size:11px;font-weight:700;color:#637895;white-space:nowrap}
.onas .check-tabs svg{width:16px;height:16px}
.onas .check-tabs button[aria-selected=true]{color:var(--blue);background:#fff;box-shadow:0 2px 7px #213f6d0b}
.onas .check-panels{padding:23px 20px 18px;height:345px}
.onas .check-panels h3{font-size:15px;letter-spacing:-.4px;line-height:1.4;color:#172c47}
.onas .check-panels [role=tabpanel]>p{font-size:10px;line-height:1.65;margin-top:5px;color:#7a899e}
.onas .paint-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:17px}
.onas .paint-reading{background:#f3f8ff;border:1px solid #e6eefb;border-radius:9px;padding:11px 10px}
.onas .paint-reading>span{display:block;font-size:9px;font-weight:600;color:#6c7e96;line-height:1.35;min-height:11px;white-space:nowrap}
.onas .paint-reading>strong{display:block;font-size:21px;line-height:1.2;letter-spacing:-.6px;margin-top:7px;font-weight:800;color:#254f84}
.onas .paint-reading small{font-size:11px;font-weight:500;color:#6687af;letter-spacing:0}
.onas .check-panels [role=tabpanel]>.reading-note{font-size:9px;margin-top:12px}
.onas .tires-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:18px}
.onas .tire-reading{display:flex;align-items:center;justify-content:space-between;padding:16px 13px;border:1px solid #e6edf7;border-radius:10px;background:#fcfdff}
.onas .tire-reading strong{font-size:11px;font-weight:700;color:#30445e;display:block}
.onas .tire-reading span{font-size:10px;color:#8997a9;display:block;margin-top:3px}
.onas .tire-reading>b{font-size:17px;color:#248748;font-weight:800;background:#eaf9ef;border-radius:20px;padding:5px 9px;white-space:nowrap;letter-spacing:-.5px}
.onas .tire-reading small{font-size:10px;font-weight:600}
.onas .wheel-info{display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e8eef6;margin-top:19px;padding-top:16px;font-size:10px;color:#516682;gap:10px}
.onas .wheel-info>span,.onas .wheel-info>strong{display:flex;align-items:center;gap:7px}
.onas .wheel-info>strong{color:#278348;font-size:10px;font-weight:700}
.onas .wheel-info svg{width:16px;height:16px}
.onas .wear-layout{display:grid;grid-template-columns:185px 1fr;gap:14px;margin-top:15px;align-items:start}
.onas .wear-map-wrap{background:#fafbfd;border:1px solid #edf1f7;border-radius:10px;overflow:hidden;padding:5px 0 8px}
.onas .wear-map{position:relative;width:100%;aspect-ratio:1;overflow:hidden}
.onas .wear-map>img{display:block;width:100%;height:auto}
.onas .wear-map-wrap>span{display:block;font-size:7px;color:#8c99ab;text-align:center;margin-top:6px}
.onas .wear-marker{position:absolute;transform:translate(-50%,-50%);width:24px;height:24px;padding:0;border-radius:50%;background:white;border:2px solid #f19a1e;font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;color:#bc7618;box-shadow:0 3px 8px #c17a2722}
.onas .wear-marker[aria-pressed=true]{background:var(--blue);border:2px solid white;color:white;box-shadow:0 0 0 4px #0866ff26,0 3px 8px #0866ff26}
.onas .wear-photo{min-width:0;overflow:hidden;border:1px solid #e7edf6;border-radius:10px;background:#fff}
.onas .wear-photo>img{display:block;width:100%;height:195px;object-fit:cover}
.onas .wear-caption{padding:12px 10px;display:flex;align-items:center;justify-content:space-between;gap:7px;color:#a97023;font-size:9px}
.onas .wear-caption>span:before{content:'';display:inline-block;width:5px;height:5px;border:1.5px solid #e99a29;border-radius:50%;margin-right:5px}
.onas .wear-caption>strong{color:#72859e;font-size:9px;white-space:nowrap}
.onas .check-footer{display:flex;align-items:center;justify-content:space-between;padding:13px 20px;background:#fbfdff;border-top:1px solid #e8eff9;color:var(--blue);font-size:11px;font-weight:700}
.onas .check-footer svg{width:17px;height:17px}
@media(max-width:1100px){.onas .explore-layout{grid-template-columns:50% 1fr;gap:30px}
.onas .certi-v2 .certi-copy{width:35%}
.onas .certi-v2 .certi-copy>p{width:100%}
.onas .certi-v2 .certi-main{gap:25px}
.onas .check-header{display:block}
.onas .check-header>span{display:block;margin-top:6px}
.onas .paint-reading>span{white-space:normal;min-height:22px}
.onas .check-panels{height:378px}
.onas .wear-layout{grid-template-columns:35% 1fr;gap:10px}
.onas .human-title .brand-motto{font-size:36px}
.onas .brand-motto span{font-size:30px}}
@media(max-width:760px){.onas .human-title .brand-motto{font-size:38px}
.onas .brand-motto span{font-size:32px}
.onas .human-title>p{margin-top:17px}
.onas .human-story{padding-top:25px}
.onas .human-section{padding-bottom:36px}
.onas .purchase-early{padding-bottom:0}
.onas .purchase-early>.container{padding-bottom:34px}
.onas .explore-section{padding:36px 0}
.onas .explore-layout{display:flex;gap:24px}
.onas .real-viewer{width:100%;border-radius:13px}
.onas .viewer-tabs button{font-size:10px;padding:10px 4px;gap:6px}
.onas .viewer-tabs svg{width:16px;height:16px}
.onas .viewer-tabs{padding:6px}
.onas .viewer-badge{top:10px;left:10px;font-size:11px;padding:4px 8px}
.onas .viewer-controls{bottom:9px;gap:4px}
.onas .viewer-controls>span{font-size:9px;min-width:145px}
.onas .viewer-controls button{width:30px;height:30px}
.onas .viewer-caption{font-size:8px;padding:11px}
.onas .certi-v2 .certi-copy{width:100%}
.onas .certi-v2 .certi-copy>p{width:100%}
.onas .check-viewer{margin-top:28px}
.onas .check-header{padding:16px 12px 12px}
.onas .check-tabs{margin:0 10px;gap:2px;padding:3px}
.onas .check-tabs button{font-size:10px;gap:4px;padding:10px 3px}
.onas .check-tabs svg{width:14px;height:14px}
.onas .check-panels{height:386px;padding:20px 12px 15px}
.onas .check-panels h3{font-size:14px}
.onas .check-panels [role=tabpanel]>p{font-size:10px}
.onas .paint-grid{gap:6px}
.onas .paint-reading{padding:10px 7px}
.onas .paint-reading>span{font-size:8px;min-height:33px}
.onas .paint-reading>strong{font-size:19px}
.onas .paint-reading small{font-size:10px}
.onas .tires-grid{gap:7px}
.onas .tire-reading{display:block;padding:12px}
.onas .tire-reading>b{display:inline-block;margin-top:7px;font-size:16px}
.onas .wheel-info{font-size:9px;gap:5px}
.onas .wheel-info>strong{font-size:8px;gap:4px}
.onas .wheel-info svg{width:14px}
.onas .wear-layout{grid-template-columns:38% 1fr;gap:9px;margin-top:18px}
.onas .wear-map{height:auto}
.onas .wear-photo>img{height:190px}
.onas .wear-caption{display:block;padding:9px 6px;min-height:45px;font-size:8px}
.onas .wear-caption>strong{display:block;margin-top:4px;font-size:8px}
.onas .wear-map-wrap>span{font-size:6px}
.onas .check-footer{font-size:10px;padding:12px}
.onas .wear-marker{width:24px;height:24px}
.onas .wear-marker:after{content:'';position:absolute;inset:-8px}
.onas .viewer-stage{touch-action:pan-y}}
.onas .wear-selectors{display:none}
@media(max-width:760px){.onas .wear-marker{width:18px;height:18px;font-size:8px}
.onas .wear-marker:after{display:none}
.onas .wear-selectors{display:flex;gap:6px;justify-content:center;padding:0 6px 8px}
.onas .wear-selectors button{width:36px;height:36px;border-radius:7px;background:#f0f5fd;font-size:11px;color:#6d819d}
.onas .wear-selectors button[aria-pressed=true]{color:white;background:var(--blue)}}
@media(max-width:760px){.onas{--muted:#5a6c83}
.onas{overflow:clip}
.onas [id]{scroll-margin-top:85px}
.onas .container,.onas .wide{width:calc(100% - 40px)}
.onas .eyebrow{font-size:10px;letter-spacing:1.2px;gap:10px;margin-bottom:16px}
.onas .eyebrow:before{width:22px}
.onas .text-link{font-size:13px;min-height:44px;line-height:1.45;gap:9px}
.onas .text-link svg{width:18px;height:18px}
.onas .hero{height:auto;min-height:0}
.onas .hero-inner{height:auto;display:flex;flex-direction:column}
.onas .hero-copy{width:100%;padding-top:32px}
.onas .hero h1{font-size:clamp(26px,8.1vw,34px);line-height:1.17;letter-spacing:-1.1px}
.onas .hero-copy>.eyebrow{font-size:10px;margin-bottom:15px;letter-spacing:1.35px}
.onas .hero-copy>p{margin-top:16px;font-size:13.5px;line-height:1.7;color:#cbd9ee}
.onas .hero-copy>p br{display:none}
.onas .hero .actions{display:flex;gap:10px;margin-top:20px}
.onas .hero .button{height:46px;padding:0 10px;font-size:12px;gap:8px;border-radius:10px;flex:1;min-width:0}
.onas .hero .button svg{width:16px;height:16px}
.onas .hero-location{margin-top:17px;gap:7px;font-size:10px;line-height:1.6;flex-wrap:wrap;color:#b9cee9}
.onas .hero-location svg{width:13px;height:13px}
.onas .hero-art{position:relative;inset:auto;width:100%;height:305px;overflow:hidden}
.onas .hero-art img{width:230px;right:calc(50% - 115px);bottom:-104px;filter:drop-shadow(0 9px 22px #00112d22)}
.onas .hero-glow{right:calc(50% - 210px);top:-30px;width:420px;height:330px}
.onas .hero-curve{height:35px}
.onas .human-section{padding:43px 0 36px}
.onas .human-title .eyebrow{font-size:10px;letter-spacing:1.1px;margin-bottom:17px}
.onas .human-title .brand-motto{font-size:36px;line-height:1.2;letter-spacing:-1.4px}
.onas .brand-motto span{font-size:31px;letter-spacing:-1.15px}
.onas .human-title>p{margin-top:17px;font-size:13.5px;line-height:1.7;color:#66788f}
.onas .human-story{padding-top:24px}
.onas .human-story p{font-size:14px;line-height:1.85;color:#566981}
.onas .human-story p+p{margin-top:15px}
.onas .purchase-early>.container{padding-top:34px;padding-bottom:35px}
.onas .purchase-early h2{font-size:29px;letter-spacing:-1px}
.onas .purchase-early .steps{display:grid;grid-template-columns:1fr;gap:22px;margin-top:28px}
.onas .steps:before{display:block;left:18px;right:auto;top:18px;bottom:34px;width:1px;height:auto;background:#dce7f7}
.onas .steps article{display:grid;grid-template-columns:36px 1fr;column-gap:17px;row-gap:4px;align-content:start}
.onas .step-number{grid-column:1;grid-row:1/span 2;box-sizing:border-box;width:36px;height:36px;margin:0;border:4px solid white;font-size:10px;align-self:start}
.onas .steps h3{grid-column:2;margin:1px 0 0;font-size:14px;line-height:1.35;letter-spacing:-.2px}
.onas .steps p{grid-column:2;font-size:12.5px;line-height:1.65;color:#65768b}
.onas .steps p br{display:none}
.onas .explore-section{padding:38px 0 42px}
.onas .explore-layout{gap:21px}
.onas .explore-copy h2{font-size:29px;line-height:1.22;letter-spacing:-1.05px}
.onas .explore-copy>p{font-size:14px;line-height:1.8;margin:17px 0 11px}
.onas .real-viewer{border-radius:15px;box-shadow:0 9px 22px #1d3d6510}
.onas .viewer-tabs{padding:6px;gap:4px}
.onas .viewer-tabs button{font-size:12px;min-height:44px;padding:8px 4px;gap:6px}
.onas .viewer-tabs svg{width:16px;height:16px}
.onas .viewer-badge{font-size:12px;padding:5px 9px;top:11px;left:11px}
.onas .viewer-controls{padding:3px;gap:3px;bottom:10px;background:#10243ee8}
.onas .viewer-controls button{width:40px;height:40px;font-size:20px}
.onas .viewer-controls>span{font-size:10px;min-width:151px;font-weight:600}
.onas .viewer-caption{font-size:10px;line-height:1.45;padding:12px 13px;color:#64758d;gap:12px}
.onas .viewer-caption>span:first-child{max-width:210px}
.onas .certi-section{padding:0}
.onas .certi-v2 .certi-panel{width:100%;padding:35px 20px 26px;border:0;border-radius:0;background:linear-gradient(135deg,#f0f6ff,#f7faff)}
.onas .certi-v2 .certi-main{display:block}
.onas .certi-v2 .eyebrow{font-size:9px;letter-spacing:1px}
.onas .certi-v2 .certi-copy{width:100%}
.onas .certi-copy h2{font-size:30px;line-height:1.2;letter-spacing:-1.05px}
.onas .certi-v2 .certi-copy>p{width:100%;font-size:14px;line-height:1.8;margin:15px 0 20px}
.onas .certi-features{grid-template-columns:1fr 1fr;gap:14px 12px;margin:0 0 10px}
.onas .certi-features>div{gap:8px;align-items:center}
.onas .certi-features>div>svg{width:18px;height:18px;margin:0}
.onas .certi-features h3{font-size:12px;line-height:1.45;font-weight:700;letter-spacing:-.12px}
.onas .certi-features p{display:none}
.onas .certi-copy>.text-link{font-size:13px}
.onas .check-viewer{width:100%;margin-top:20px;border-radius:17px;border-color:#dfe8f5;box-shadow:0 12px 30px #153b6a08}
.onas .check-header{padding:19px 16px 13px;display:block}
.onas .check-header>strong{font-size:16px;letter-spacing:-.4px;color:#18304f}
.onas .check-header>span{display:block;font-size:10px;letter-spacing:.15px;color:#708198;margin-top:6px}
.onas .check-tabs{margin:0 12px;gap:3px;padding:4px;border-radius:10px}
.onas .check-tabs button{min-height:44px;padding:9px 5px;gap:6px;font-size:12px;font-weight:700}
.onas .check-tabs svg{width:16px;height:16px}
.onas .check-panels{display:grid;height:auto;padding:20px 16px 18px}
.onas .check-panels>[role=tabpanel]{grid-area:1/1;min-width:0;align-self:start}
.onas .check-panels>[role=tabpanel][hidden]{display:block!important;visibility:hidden;pointer-events:none}
.onas .check-panels h3{font-size:16px;line-height:1.4;letter-spacing:-.4px}
.onas .check-panels [role=tabpanel]>p{font-size:12px;line-height:1.6;color:#64758d;margin-top:6px}
.onas .paint-grid{display:block;margin-top:16px;border:1px solid #e4ecf7;border-radius:11px;overflow:hidden;background:#f7faff}
.onas .paint-reading{display:flex;align-items:center;justify-content:space-between;gap:12px;border:0;border-bottom:1px solid #e4ecf7;border-radius:0;padding:10px 12px;background:transparent;min-height:43px}
.onas .paint-reading:last-child{border-bottom:0}
.onas .paint-reading>span{font-size:12px;line-height:1.4;font-weight:500;min-height:0;white-space:normal;color:#53657c}
.onas .paint-reading>strong{font-size:18px;line-height:1.25;margin:0;white-space:nowrap;flex-shrink:0;letter-spacing:-.4px;color:#1c528e}
.onas .paint-reading small{font-size:11px;font-weight:500;color:#4f6c8d;margin-left:1px}
.onas .check-panels [role=tabpanel]>.reading-note{font-size:10.5px;line-height:1.6;color:#708198;margin-top:12px}
.onas .tires-grid{display:block;border:1px solid #e5edf7;border-radius:11px;overflow:hidden;margin-top:16px}
.onas .tire-reading{display:flex;align-items:center;justify-content:space-between;gap:12px;min-height:86px;padding:15px 13px;border:0;border-bottom:1px solid #e5edf7;border-radius:0;background:#fcfdff}
.onas .tire-reading:last-child{border-bottom:0}
.onas .tire-reading strong{font-size:13px;line-height:1.45;color:#30475f}
.onas .tire-reading span{font-size:12px;line-height:1.5;color:#6b7c92;margin-top:3px}
.onas .tire-reading>b{font-size:19px;line-height:1.4;margin:0;padding:4px 12px;color:#207e43}
.onas .tire-reading small{font-size:12px}
.onas .wheel-info{margin-top:18px;padding-top:17px;gap:12px;flex-wrap:wrap;font-size:12px}
.onas .wheel-info>strong{font-size:12px}
.onas .wheel-info svg{width:18px;height:18px}
.onas .wear-layout{display:grid;grid-template-columns:112px 1fr;gap:12px;align-items:center;margin-top:16px}
.onas .wear-photo{display:contents}
.onas .wear-photo>img{grid-column:1/-1;grid-row:1;width:100%;height:auto;aspect-ratio:4/3;object-fit:cover;object-position:50% 50%;border-radius:11px;border:1px solid #e5edf7}
.onas .wear-map-wrap{grid-column:1;grid-row:2/span 2;padding:5px 3px 7px;border-radius:10px}
.onas .wear-map-wrap>span{font-size:9px;line-height:1.3;color:#687a92;padding:0 3px;margin-top:5px}
.onas .wear-caption{grid-column:2;grid-row:2;display:block;padding:0;min-height:0;font-size:11px;line-height:1.5;color:#956320;align-self:end}
.onas .wear-caption>strong{display:block;font-size:11px;line-height:1.4;color:#6a7c94;margin-top:6px}
.onas .wear-marker{width:17px;height:17px;font-size:8px;border-width:1.5px}
.onas .wear-marker[aria-pressed=true]{border-width:1.5px;box-shadow:0 0 0 3px #0866ff26}
.onas .wear-selectors{grid-column:2;grid-row:3;display:flex;justify-content:flex-start;padding:0;gap:8px;align-self:start}
.onas .wear-selectors button{width:44px;height:44px;border-radius:9px;font-size:14px;font-weight:700;color:#57708f;background:#edf3fc}
.onas .wear-selectors button[aria-pressed=true]{color:white;background:var(--blue)}
.onas .check-footer{min-height:50px;padding:13px 16px;font-size:12px;gap:10px}
.onas .certi-v2 .scope-note{margin-top:23px;padding-top:17px;gap:9px}
.onas .scope-note p{font-size:11px;line-height:1.75;color:#63768e}
.onas .scope-note svg{width:15px;height:15px;margin-top:2px}
.onas .services{padding:42px 0}
.onas .service-intro{margin-bottom:24px}
.onas .service-intro h2{font-size:29px;line-height:1.23;letter-spacing:-1px}
.onas .service-intro>p{font-size:14px;line-height:1.7;margin-top:14px}
.onas .service-grid{grid-template-columns:1fr;gap:0;border:1px solid #e0e8f3;border-radius:16px;overflow:hidden;background:white}
.onas .service-grid article{border:0;border-bottom:1px solid #e6edf6;border-radius:0;padding:15px 14px;gap:12px;min-height:0;align-items:center;background:white}
.onas .service-grid article:last-child{border-bottom:0}
.onas .service-grid .icon{width:36px;height:36px;border-radius:10px}
.onas .service-grid .icon svg{width:19px;height:19px}
.onas .service-grid h3{font-size:14px;line-height:1.4;letter-spacing:-.15px;margin:0}
.onas .service-grid p{font-size:12.5px;line-height:1.55;margin-top:4px;color:#64758b}
.onas .services-note{font-size:11px;line-height:1.75;color:#6a7b91;margin-top:18px;padding-top:17px}
.onas .remote-v2{padding:0 0 38px}
.onas .remote-v2 .remote-panel{padding:27px 20px 24px;border-radius:19px}
.onas .remote-v2 .remote-copy .eyebrow{font-size:10px;letter-spacing:1px}
.onas .remote-v2 .remote-copy h2{font-size:27px;line-height:1.22;letter-spacing:-1px}
.onas .remote-v2 .remote-copy>p{font-size:14px;line-height:1.8;margin:16px 0 12px}
.onas .remote-v2 .text-link{font-size:12.5px;min-height:44px}
.onas .remote-v2 .remote-visual{margin-top:22px}
.onas .remote-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:24px}
.onas .remote-list>div{display:flex;flex-direction:column;gap:8px;white-space:normal;font-size:11px;line-height:1.5;text-align:center;color:#506984}
.onas .remote-list svg{width:20px;height:20px}
.onas .contact-section{padding:0 0 40px}
.onas .contact-panel{padding:27px 23px 23px;border-radius:19px}
.onas .contact-panel h2{font-size:27px;line-height:1.24;letter-spacing:-.9px}
.onas .contact-panel p{font-size:13px;line-height:1.8;margin-top:15px;color:#c4d2e7}
.onas .contact-actions{margin-top:22px;gap:8px;align-items:stretch}
.onas .contact-actions .button{width:100%;height:49px;font-size:13px;border-radius:10px;gap:10px}
.onas .contact-actions .light-link{font-size:12px;min-height:44px;justify-content:center}}
@media(max-width:359px){.onas .container,.onas .wide{width:calc(100% - 32px)}
.onas .hero-copy>p{font-size:13px}
.onas .hero-location{font-size:9px;gap:6px}
.onas .human-title .brand-motto{font-size:33px}
.onas .brand-motto span{font-size:28px}
.onas .certi-v2 .certi-panel{padding-left:16px;padding-right:16px}
.onas .certi-features{gap:12px 8px}
.onas .certi-features h3{font-size:11px}
.onas .check-header,.onas .check-panels{padding-left:12px;padding-right:12px}
.onas .check-tabs button{font-size:12px}
.onas .check-tabs svg{display:none}
.onas .paint-reading{padding-left:10px;padding-right:10px;gap:8px}
.onas .paint-reading>span{font-size:11.5px}
.onas .wear-layout{grid-template-columns:96px 1fr;gap:10px}
.onas .wear-selectors{gap:4px}
.onas .viewer-tabs button{font-size:10.5px;gap:4px}
.onas .viewer-controls>span{min-width:138px;font-size:9px}
.onas .viewer-controls button{width:37px;height:37px}
.onas .viewer-caption{font-size:9px;gap:8px}
.onas .service-grid article{padding-left:12px;padding-right:12px;gap:10px}
.onas .remote-v2 .remote-copy h2,.onas .contact-panel h2{font-size:25px}
.onas .remote-list>div{font-size:10px}}

/* ---------- Wdrożenie w serwisie ---------- */
.onas{font-family:var(--font-body);line-height:normal;text-rendering:auto}
.onas h1,.onas h2,.onas h3,.onas h4{font-family:inherit}
/* globalny .container serwisu ma max-width i padding, tutaj szerokość daje projekt */
.onas .container{max-width:none;padding:0}
.onas img{max-width:none}
.onas .hero-art img{height:auto}
/* odtwarzacz 360 */
.onas .viewer-stage .viewer-poster{position:absolute;inset:0}
.onas .viewer-stage canvas{position:absolute;inset:0}
.onas .viewer-stage .pano-host{position:absolute;inset:0;background:#e8ebee}
.onas .viewer-stage .pano-host .pnlm-load-box,.onas .viewer-stage .pano-host .pnlm-about-msg{display:none!important}
.onas .viewer-stage.is-static{cursor:default}
.onas .viewer-stage.is-static .viewer-badge,.onas .viewer-stage.is-static .viewer-controls{display:none}
/* opony: ten sam podział stanu co w karcie „Koła i opony” na stronie auta */
.onas .tire-reading>b.warn{color:#b45309;background:#fef3c7}
.onas .tire-reading>b.bad{color:#b91c1c;background:#fee2e2}
.onas .wheel-info>strong.warn{color:#b45309}

/* ===== System serwisu (31.10.2026, po uwagach Maksa): te same wartości co CertiCheck/strona główna.
   Kontener 1200, sekcje 96 px z naprzemiennym tłem, nagłówki 40/900, tekst #475569, karty 12 px z obramowaniem. ===== */
.onas{--blue:#0066ff;--ink:#0a0a0a;--muted:#475569;--line:#e5edfa;--ice:#f6f8fc;font-family:var(--font-body);color:var(--ink)}
.onas .container,.onas .wide{width:auto;max-width:1200px;margin-left:auto;margin-right:auto;padding-left:24px;padding-right:24px}
.onas p{color:var(--muted);font-size:15px;line-height:1.7}
.onas h1,.onas h2{font-weight:900;color:var(--ink)}
.onas h2,.onas .human-title .brand-motto,.onas .explore-copy h2,.onas .certi-copy h2,.onas .certi-v2 .certi-copy h2,.onas .remote-copy h2,.onas .remote-v2 .remote-copy h2,.onas .purchase-early h2{font-size:40px;letter-spacing:-.9px;line-height:1.1}
.onas .brand-motto span,.onas h1 span{font-size:inherit;font-weight:900;letter-spacing:inherit;color:var(--blue)}
.onas h3{font-weight:800;color:var(--ink);letter-spacing:-.3px}
.onas .eyebrow,.onas .human-title .eyebrow,.onas .certi-v2 .eyebrow,.onas .remote-v2 .remote-copy .eyebrow{font-size:11px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:var(--blue);margin-bottom:14px;gap:10px}
.onas .eyebrow:before{width:18px}
.onas .eyebrow.light{color:#8fc3ff}
/* hero jak na stronie głównej (ciemny) */
.onas .hero h1{font-size:52px;letter-spacing:-1.4px;line-height:1.05;color:#fff}
.onas .hero h1 span{color:#4b95ff}
.onas .hero-copy>p{font-size:16px;line-height:1.65;color:rgba(255,255,255,.75)}
/* sekcje i tła */
.onas .human-section,.onas .purchase-early,.onas .explore-section,.onas .certi-section,.onas .services,.onas .remote-section{padding:96px 0}
.onas .purchase-early,.onas .certi-section,.onas .remote-section{background:var(--ice)}
.onas .purchase-early>.container{border:0;padding-top:0;padding-bottom:0}
.onas .human-title>p,.onas .human-story p{font-size:15.5px;line-height:1.75;color:var(--muted)}
.onas .human-story strong{color:var(--ink);font-weight:700}
/* nagłówki wyśrodkowane jak „Co otrzymujesz w CertiCheck” */
.onas .purchase-early .container>.eyebrow,.onas .service-intro .eyebrow{justify-content:center}
.onas .purchase-early .container>.eyebrow:after,.onas .service-intro .eyebrow:after{content:'';width:18px;height:2px;background:currentColor}
.onas .purchase-early h2{text-align:center;margin-bottom:56px}
.onas .service-intro{display:block!important;text-align:center;max-width:640px;margin:0 auto 56px}
.onas .service-intro>p{width:auto!important;margin:14px auto 0;font-size:15.5px}
.onas .service-intro>p br{display:none}
/* kroki jak „Jak powstaje CertiCheck” */
.onas .steps{grid-template-columns:repeat(4,1fr);gap:20px;margin-top:0}
.onas .steps:before{display:none}
.onas .steps article{background:#fff;border:1px solid var(--line);border-radius:12px;padding:32px 26px}
.onas .step-number{width:48px;height:48px;margin:0 0 20px;border:0;outline:0;box-sizing:border-box;font-size:18px;font-weight:800;box-shadow:0 6px 14px rgba(0,102,255,.28)}
.onas .steps h3{font-size:16.5px;margin:0 0 14px;padding-bottom:14px;position:relative}
.onas .steps h3:after{content:'';position:absolute;left:0;bottom:0;width:28px;height:3px;border-radius:2px;background:var(--blue)}
.onas .steps p{font-size:13.5px;line-height:1.7}
.onas .steps p br{display:none}
/* usługi jak karty CertiCheck */
.onas .service-grid{gap:24px}
.onas .service-grid article{border:1px solid var(--line);border-radius:12px;padding:32px 28px;background:#fff;min-height:0;gap:18px}
.onas .service-grid .icon{width:52px;height:52px;border-radius:14px;background:#eef4ff;color:var(--blue)}
.onas .service-grid .icon svg{width:24px;height:24px}
.onas .service-grid h3{font-size:17px;margin-top:4px}
.onas .service-grid p{font-size:14px;line-height:1.7;margin-top:8px}
.onas .services-note{font-size:13px;color:var(--muted);text-align:center;border:0;margin-top:28px;padding-top:0}
/* panele CertiCheck i zakupu na odległość: tło sekcji zamiast osobnej „wyspy” */
.onas .certi-panel,.onas .remote-panel,.onas .remote-v2 .remote-panel{background:transparent;border:0;border-radius:0;padding:0;box-shadow:none}
.onas .certi-copy>p,.onas .certi-v2 .certi-copy>p,.onas .remote-copy>p,.onas .remote-v2 .remote-copy>p,.onas .explore-copy>p{font-size:15.5px;line-height:1.75}
.onas .certi-features h3{font-size:15px}
.onas .certi-features p{font-size:13.5px;line-height:1.6}
.onas .scope-note p{font-size:12.5px;color:var(--muted)}
.onas .check-viewer,.onas .real-viewer{border:1px solid var(--line);border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.04)}
.onas .remote-list>div{font-size:13.5px;color:var(--ink)}
.onas .remote-v2 .remote-panel{grid-template-columns:minmax(0,1fr) 360px 200px;gap:48px}
.onas .remote-v2 .remote-visual{width:100%}
.onas .service-grid p br,.onas .certi-features p br{display:none}
@media(max-width:1100px){.onas .remote-v2 .remote-panel{grid-template-columns:minmax(0,1fr) 300px;gap:32px}.onas .remote-list{grid-column:1/-1;flex-direction:row;flex-wrap:wrap;gap:24px}}
/* ikony: Lucide jak w całym serwisie (grubość linii 2) */
.onas svg.lucide,.onas i[data-lucide]{stroke-width:2;fill:none;flex-shrink:0}
/* przyciski i linki jak w serwisie */
.onas .button{height:auto;padding:15px 26px;border-radius:12px;font-size:15px;font-weight:700;gap:8px;line-height:1;letter-spacing:.1px}
.onas .button svg{width:16px;height:16px;stroke-width:2.2}
.onas .primary{background:var(--blue);color:#fff;box-shadow:0 8px 24px rgba(0,102,255,.4)}
.onas .secondary{background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.35)}
.onas .contact-actions .button{height:auto;font-size:15px}
.onas .text-link{font-size:14px;font-weight:700;gap:8px}
.onas .text-link svg{width:16px;height:16px}
/* końcowy kontakt */
.onas .contact-section{padding:96px 0}
.onas .contact-panel{border-radius:18px;padding:48px 56px}
.onas .contact-panel h2{font-size:32px;color:#fff;letter-spacing:-.6px}
.onas .contact-panel p{font-size:15px;color:rgba(255,255,255,.7)}
@media(max-width:1100px){.onas .steps{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.onas h2,.onas .human-title .brand-motto,.onas .explore-copy h2,.onas .certi-copy h2,.onas .certi-v2 .certi-copy h2,.onas .remote-copy h2,.onas .remote-v2 .remote-copy h2,.onas .purchase-early h2{font-size:32px}.onas .human-section,.onas .purchase-early,.onas .explore-section,.onas .certi-section,.onas .services,.onas .remote-section,.onas .contact-section{padding:64px 0}.onas .service-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:760px){.onas .container,.onas .wide{padding-left:20px;padding-right:20px}.onas h2,.onas .human-title .brand-motto,.onas .explore-copy h2,.onas .certi-copy h2,.onas .certi-v2 .certi-copy h2,.onas .remote-copy h2,.onas .remote-v2 .remote-copy h2,.onas .purchase-early h2{font-size:28px;letter-spacing:-.6px}.onas .hero h1{font-size:34px;letter-spacing:-.8px}.onas .human-section,.onas .purchase-early,.onas .explore-section,.onas .certi-section,.onas .services,.onas .remote-section,.onas .contact-section{padding:48px 0}.onas .purchase-early h2,.onas .service-intro{margin-bottom:30px}.onas .steps,.onas .service-grid{grid-template-columns:1fr;gap:16px}.onas .service-grid article{padding:24px 22px}.onas .contact-panel{padding:32px 24px}.onas .contact-panel h2{font-size:26px}.onas .button,.onas .hero .button,.onas .contact-actions .button{height:auto;padding:13px 16px;font-size:14px;border-radius:12px}}
@endsection

@section('content')
@php
    $sc       = $showcase;
    $hasOut   = $sc && ($sc['exterior'] || $sc['poster']);
    $hasIn    = $sc && $sc['interior'];
    $outSpins = $sc && $sc['exterior'];
    $check    = $sc['check'] ?? null;
    $checkTabs = [];
    if ($check) {
        if ($check['paint']['items']) $checkTabs[] = 'paint';
        if ($check['tires'])          $checkTabs[] = 'tires';
        if ($check['damages']['items']) $checkTabs[] = 'wear';
    }
@endphp
<div class="onas">

<section id="01-hero" class="hero">
 <div class="container hero-inner">
  <div class="hero-copy">
   <div class="eyebrow light">O NAS · CERTICARS</div>
   <h1>Poznaj auto wcześniej.<br><span>Kupuj spokojniej.</span></h1>
   <p>Pokazujemy dostępne informacje o autach<br>
i pomagamy przejść przez zakup — od pierwszych<br>
pytań po formalności i odbiór.</p>
   <div class="actions"><a class="button primary" href="{{ route('catalog') }}">Sprawdź ofertę <x-icon name="arrow-right" size="16"/></a><a href="tel:+48515440623" class="button secondary">Porozmawiajmy</a></div>
   <div class="hero-location"><x-icon name="map-pin" size="14"/><span>Lipnik k. Stargardu</span><span class="dot"></span><span>Auta osobowe i dostawcze</span></div>
  </div>
  <div class="hero-art"><div class="hero-glow"></div><img src="/img/about/doradca-tablet.webp" width="800" height="1422" alt="Doradca CertiCars z tabletem" fetchpriority="high" decoding="async"></div>
 </div>
 <svg class="hero-curve" viewBox="0 0 1440 58" preserveAspectRatio="none" aria-hidden="true"><path d="M0 8C390 58 1050 58 1440 8V58H0Z" fill="white"/></svg>
</section>

<section id="02-podejscie" class="approach human-section">
 <div class="container human-layout">
  <div class="human-title"><div class="eyebrow">PO LUDZKU O SAMOCHODACH</div><h2 class="brand-motto">Najpierw Ty.<br><span>Potem samochód.</span></h2><p>Chcemy pomóc Ci wybrać<br>
auto, które pasuje do Ciebie.</p></div>
  <div class="human-story"><p>Chcemy poznać Ciebie, Twój budżet i potrzeby. Szukasz auta do pracy, dla rodziny czy na codzienne dojazdy? <strong>Od tego zaczynamy rozmowę.</strong></p><p>Nie namawiamy do auta na siłę. Przyjedź, obejrzyj je z bliska, usiądź za kierownicą i przejedź się. Masz czas na pytania i własną ocenę. <strong>To Ty decydujesz, czy ten samochód jest dla Ciebie.</strong></p></div>
 </div>
</section>

<section id="03-zakup" class="purchase purchase-early">
 <div class="container"><div class="eyebrow">KROK PO KROKU</div><h2>Jak wygląda zakup?</h2>
  <div class="steps">
   <article><div class="step-number">01</div><h3>Wybierz auto</h3><p>Sprawdź ofertę i zapytaj<br>
o szczegóły.</p></article>
   <article><div class="step-number">02</div><h3>Poznaj je bliżej</h3><p>Oględziny, jazda próbna<br>
lub prezentacja wideo.</p></article>
   <article><div class="step-number">03</div><h3>Ustal warunki</h3><p>Cena, dokumenty<br>
i wybrane usługi.</p></article>
   <article><div class="step-number">04</div><h3>Odbierz samochód</h3><p>Na miejscu lub<br>
z uzgodnionym transportem.</p></article>
  </div>
 </div>
</section>

@if($hasOut || $hasIn)
<section id="04-prezentacje" class="explore-section">
 <div class="container explore-layout">
  <div class="real-viewer">
   @if($hasOut && $hasIn)
   <div class="viewer-tabs" role="tablist" aria-label="Widok samochodu">
    <button id="view-outside" type="button" role="tab" aria-selected="true" aria-controls="outside-panel"><x-icon name="car" size="16"/>{{ $outSpins ? '360° z zewnątrz' : 'Z zewnątrz' }}</button>
    <button id="view-inside" type="button" role="tab" aria-selected="false" aria-controls="inside-panel"><x-icon name="armchair" size="16"/>360° wnętrza</button>
   </div>
   @endif
   <div class="viewer-stage" aria-label="Interaktywna prezentacja {{ $sc['name'] }}" data-onas-stage>
    @if($hasOut)
    <div id="outside-panel" role="tabpanel" @if($hasIn) aria-labelledby="view-outside" @endif
         data-kind="{{ $outSpins ? 'frames' : 'static' }}"
         @if($outSpins) data-frames='@json($sc['exterior']['urls'])' data-sprites='@json($sc['exterior']['sprites'])' @endif>
     @if($sc['poster'])<img class="viewer-poster" src="{{ $sc['poster'] }}" alt="{{ $sc['name'] }}, zdjęcie z ogłoszenia" draggable="false" loading="lazy" decoding="async">@endif
     @if($outSpins)<canvas role="img" aria-label="{{ $sc['name'] }}: widok 360° nadwozia z ogłoszenia"></canvas>@endif
    </div>
    @endif
    @if($hasIn)
    <div id="inside-panel" role="tabpanel" @if($hasOut) aria-labelledby="view-inside" hidden @endif
         data-kind="{{ $sc['interior']['type'] }}"
         @if($sc['interior']['type'] === 'frames') data-frames='@json($sc['interior']['urls'])' data-sprites='@json($sc['interior']['sprites'])' @endif>
     @if($sc['interior']['type'] === 'frames')
      <canvas role="img" aria-label="{{ $sc['name'] }}: widok 360° wnętrza z ogłoszenia"></canvas>
     @else
      <div class="pano-host" data-pano-src="{{ $sc['interior']['src'] }}" role="img" aria-label="Panorama 360 stopni wnętrza {{ $sc['name'] }}"></div><span class="pano-loading" role="status">Wczytuję wnętrze…</span>
     @endif
    </div>
    @endif
    <span class="viewer-badge">360°</span>
    <div class="viewer-controls"><button type="button" data-rotate="-1" aria-label="Obróć widok w lewo">←</button><span data-viewer-hint>Przeciągnij, aby obrócić auto</span><button type="button" data-rotate="1" aria-label="Obróć widok w prawo">→</button></div>
   </div>
   <div class="viewer-caption"><span>Rzeczywiste zdjęcia z ogłoszenia</span><span>{{ $sc['name'] }}</span></div>
  </div>
  <div class="explore-copy"><div class="eyebrow">PREZENTACJE 360°</div><h2>Obejrzyj auto dookoła.<br>Zajrzyj do środka.</h2><p>Na CertiCars możesz oglądać samochody w 360° — z zewnątrz i we wnętrzu. Przyjrzyj się nadwoziu, rozejrzyj po kabinie i poznaj więcej szczegółów jeszcze przed wizytą.</p><a class="text-link" href="{{ route('catalog') }}">Zobacz auta w ofercie <x-icon name="arrow-right" size="16"/></a></div>
 </div>
</section>
@endif

<section id="05-certicheck" class="certi-section certi-v2">
 <div class="wide certi-panel">
  <div class="certi-main">
   <div class="certi-copy"><div class="eyebrow">CERTICHECK · DODATKOWE MATERIAŁY</div><h2>Więcej niż opis<br>i zdjęcia.</h2><p>Przy wybranych samochodach do ogłoszenia dokładamy pomiary i materiały pokazujące stan auta.</p>
    <div class="certi-features">
     <div><x-icon name="scan-line" size="21"/><div><h3>Pomiary lakieru</h3><p>Grubość powłoki w wybranych punktach.</p></div></div>
     <div><x-icon name="tabler:wheel" size="21"/><div><h3>Pomiary opon</h3><p>Zmierzona głębokość bieżnika.</p></div></div>
     <div><x-icon name="camera" size="21"/><div><h3>Stan wizualny</h3><p>Ujęcia nadwozia, wnętrza i detali.</p></div></div>
     <div><x-icon name="map-pin" size="21"/><div><h3>Ślady eksploatacji</h3><p>Zauważone ślady zaznaczone na mapie auta.</p></div></div>
    </div>
    <a class="text-link" href="{{ route('certicheck.landing') }}">Poznaj CertiCheck <x-icon name="arrow-right" size="16"/></a>
   </div>
   @if($checkTabs)
   <div class="check-viewer">
    <div class="check-header"><strong>Zajrzyj do CertiCheck</strong><span>PRZYKŁADOWE AUTO · {{ $sc['name'] }}</span></div>
    <div class="check-tabs" role="tablist" aria-label="Materiały CertiCheck">
     @if(in_array('paint', $checkTabs))<button id="check-paint" type="button" role="tab" aria-selected="{{ $checkTabs[0] === 'paint' ? 'true' : 'false' }}" aria-controls="paint-panel"><x-icon name="scan-line" size="16"/>Lakier</button>@endif
     @if(in_array('tires', $checkTabs))<button id="check-tires" type="button" role="tab" aria-selected="{{ $checkTabs[0] === 'tires' ? 'true' : 'false' }}" aria-controls="tires-panel"><x-icon name="tabler:wheel" size="16"/>Opony</button>@endif
     @if(in_array('wear', $checkTabs))<button id="check-wear" type="button" role="tab" aria-selected="{{ $checkTabs[0] === 'wear' ? 'true' : 'false' }}" aria-controls="wear-panel"><x-icon name="map-pin" size="16"/>Stan i ślady</button>@endif
    </div>
    <div class="check-panels">
     @if(in_array('paint', $checkTabs))
     <div id="paint-panel" role="tabpanel" aria-labelledby="check-paint" @if($checkTabs[0] !== 'paint') hidden @endif>
      <h3>Pomiary grubości lakieru</h3><p>Wyniki pomiarów w wybranych punktach nadwozia.</p>
      <div class="paint-grid">@foreach($check['paint']['items'] as $r)<div class="paint-reading"><span>{{ $r['label'] }}</span><strong>{{ $r['value'] }} <small>μm</small></strong></div>@endforeach</div><p class="reading-note">Odczyty dotyczą prezentowanego samochodu.@if($check['paint']['total'] > count($check['paint']['items'])) Wszystkie pomiary ({{ $check['paint']['total'] }}) są w raporcie CertiCheck.@endif</p>
     </div>
     @endif
     @if(in_array('tires', $checkTabs))
     <div id="tires-panel" role="tabpanel" aria-labelledby="check-tires" @if($checkTabs[0] !== 'tires') hidden @endif>
      <h3>Koła i opony</h3><p>{{ $check['tires']['mounted'] ? 'Zamontowany komplet' : 'Komplet opon' }} · głębokość bieżnika.</p>
      <div class="tires-grid">@foreach($check['tires']['rows'] as $t)<div class="tire-reading"><div><strong>{{ $t['label'] }}</strong>@if($t['type'])<span>{{ $t['type'] }}</span>@endif</div><b @class([$t['status'] => $t['status'] !== 'ok'])>{{ $t['depth'] }} <small>mm</small></b></div>@endforeach</div>
      <div class="wheel-info">@if($check['tires']['rim'])<span><x-icon name="armchair" size="16"/>{{ $check['tires']['rim'] }}</span>@else<span></span>@endif
       @if($check['tires']['anyIssue'])<strong class="warn"><x-icon name="info" size="15"/>Wymaga uwagi</strong>@else<strong><x-icon name="check" size="15"/>Stan bardzo dobry</strong>@endif</div>
     </div>
     @endif
     @if(in_array('wear', $checkTabs))
     @php $dmg = $check['damages']['items']; $dmgN = count($dmg); @endphp
     <div id="wear-panel" role="tabpanel" aria-labelledby="check-wear" @if($checkTabs[0] !== 'wear') hidden @endif>
      <h3>Stan wizualny i ślady eksploatacji</h3><p>Wybierz punkt, aby zobaczyć zdjęcie z tego miejsca.</p>
      <div class="wear-layout"><div class="wear-map-wrap"><div class="wear-map"><img src="{{ $check['damages']['map'] }}" alt="Poglądowa mapa nadwozia z oznaczonymi śladami ({{ $dmgN }})" loading="lazy" decoding="async">@foreach($dmg as $i => $d)<button type="button" class="wear-marker" data-damage="{{ $i + 1 }}" data-photo="{{ $d['photo'] }}" data-label="{{ $d['label'] }}" data-area="{{ $d['area'] }}" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Ślad {{ $i + 1 }} — zobacz zdjęcie" style="left:{{ $d['x'] }}%;top:{{ $d['y'] }}%">{{ $i + 1 }}</button>@endforeach</div><span>Poglądowa mapa nadwozia</span></div><div class="wear-photo"><img data-damage-photo src="{{ $dmg[0]['photo'] }}" alt="Ślad 1: {{ mb_strtolower($dmg[0]['label']) }}@if($dmg[0]['area']), {{ $dmg[0]['area'] }}@endif" loading="lazy" decoding="async"><div class="wear-caption"><span data-damage-label>{{ $dmg[0]['label'] }}</span><strong data-damage-counter aria-live="polite">1 / {{ $dmgN }}</strong></div><div class="wear-selectors" aria-label="Wybierz zdjęcie śladu">@foreach($dmg as $i => $d)<button type="button" data-damage="{{ $i + 1 }}" data-photo="{{ $d['photo'] }}" data-label="{{ $d['label'] }}" data-area="{{ $d['area'] }}" aria-label="Zobacz ślad {{ $i + 1 }}" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}">{{ $i + 1 }}</button>@endforeach</div></div></div>
     </div>
     @endif
    </div>
    <a class="check-footer" href="{{ $sc['checkUrl'] }}">Zobacz materiały w ogłoszeniu <x-icon name="arrow-right" size="16"/></a>
   </div>
   @endif
  </div>
  <div class="scope-note"><x-icon name="info" size="15"/><p>CertiCheck jest dostępny przy wybranych autach. Zakres materiałów zależy od samochodu i nie zastępuje jego oględzin.</p></div>
 </div>
</section>

<section id="06-uslugi" class="services">
 <div class="container">
  <div class="section-intro service-intro"><div><div class="eyebrow">NASZE USŁUGI</div><h2>Wsparcie przy zakupie<br>i po odbiorze auta.</h2></div><p>Zakres pomocy dobieramy<br>
do Twoich potrzeb.</p></div>
  <div class="service-grid">
   <article><div class="icon"><x-icon name="wallet" size="24"/></div><div><h3>Kredyt i leasing</h3><p>Pomagamy zorganizować<br>
finansowanie zakupu.</p></div></article>
   <article><div class="icon"><x-icon name="file-text" size="24"/></div><div><h3>Rejestracja auta</h3><p>Możemy zająć się formalnościami<br>
związanymi z rejestracją.</p></div></article>
   <article><div class="icon"><x-icon name="shield-check" size="24"/></div><div><h3>Ubezpieczenie OC / AC</h3><p>Pomagamy dobrać polisę<br>
do samochodu.</p></div></article>
   <article><div class="icon"><x-icon name="truck" size="24"/></div><div><h3>Transport pod dom</h3><p>Ustalamy adres, termin<br>
i koszt dowozu samochodu.</p></div></article>
   <article><div class="icon"><x-icon name="wrench" size="24"/></div><div><h3>Pierwszy serwis</h3><p>Organizujemy uzgodniony<br>
serwis eksploatacyjny.</p></div></article>
   <article><div class="icon"><x-icon name="shield-check" size="24"/></div><div><h3>Gwarancja GetHelp</h3><p>Zapytaj o dostępne pakiety<br>
i warunki ochrony.</p></div></article>
  </div>
  <p class="services-note">Zapytaj również o wymianę opon i usuwanie wgnieceń. Zakres, dostępność i koszt usług ustalamy indywidualnie.</p>
 </div>
</section>

<section id="07-odleglosc" class="remote-section remote-v2">
 <div class="wide remote-panel">
  <div class="remote-copy"><div class="eyebrow">ZAKUP NA ODLEGŁOŚĆ</div><h2>Mieszkasz dalej?<br>Poznaj auto na odległość.</h2><p>Umów prezentację wideo, poproś o dodatkowe ujęcia i omów szczegóły auta. Jeśli zdecydujesz się na zakup, ustalimy formalności oraz odbiór lub transport.</p><a class="text-link" href="tel:+48515440623">Zapytaj o zakup na odległość <x-icon name="arrow-right" size="16"/></a></div>
  <div class="remote-visual">
   <div class="laptop">
    <div class="laptop-screen">
     <div class="call-top"><div><x-icon name="video" size="11"/><span>Prezentacja samochodu</span></div><span class="call-indicator"></span></div>
     <div class="call-main">
      <img class="car-render" src="/img/about/prezentacja-auto.webp" width="640" height="427" alt="Poglądowa ilustracja samochodu na ekranie prezentacji" loading="lazy" decoding="async">
      <div class="call-thumb"><x-icon name="video" size="11"/></div>
     </div>
     <div class="call-controls"><span><x-icon name="mic" size="11"/></span><span class="hangup"><x-icon name="phone" size="11"/></span><span><x-icon name="video" size="11"/></span></div>
    </div>
    <div class="laptop-base"><i></i></div>
   </div>
  </div>
  <div class="remote-list"><div><x-icon name="video" size="17"/><span>Prezentacja wideo</span></div><div><x-icon name="camera" size="17"/><span>Dodatkowe zdjęcia</span></div><div><x-icon name="truck" size="17"/><span>Ustalenie odbioru</span></div></div>
 </div>
</section>

<section id="08-kontakt" class="contact-section">
 <div class="wide contact-panel"><div><h2>Porozmawiajmy<br>o Twoim kolejnym aucie.</h2><p>Powiedz, czego szukasz i jaki masz budżet.<br>
Pomożemy sprawdzić dostępne możliwości.</p></div><div class="contact-actions"><a href="tel:+48515440623" class="button primary"><x-icon name="phone" size="16"/>Zadzwoń 515 440 623</a><a class="text-link light-link" href="{{ route('catalog') }}">Zobacz dostępne auta <x-icon name="arrow-right" size="16"/></a></div></div>
</section>
</div>
@endsection

@push('scripts')
<script>
/* O nas: zakładki, ślady CertiCheck i prezentacja 360°.
   360°: auto obraca się samo w spokojnym tempie, gdy sekcja jest widoczna.
   Przeciągnięcie albo strzałki zatrzymują obrót, po ok. 2,5 s wraca.
   Przy prefers-reduced-motion nie ma samoczynnego obrotu. */
(function(){
 'use strict';
 var root = document.querySelector('.onas');
 if(!root) return;
 var $ = function(sel){ return root.querySelector(sel); };

 function tabs(group, callback){
  if(!group) return;
  var buttons = [].slice.call(group.querySelectorAll('[role=tab]'));
  function activate(button){
   buttons.forEach(function(b){
    var on = b === button;
    var panel = document.getElementById(b.getAttribute('aria-controls'));
    b.setAttribute('aria-selected', String(on));
    b.tabIndex = on ? 0 : -1;
    panel.hidden = !on;
    panel.inert = !on;
   });
   if(callback) callback(button);
  }
  buttons.forEach(function(button, index){
   button.tabIndex = button.getAttribute('aria-selected') === 'true' ? 0 : -1;
   document.getElementById(button.getAttribute('aria-controls')).inert = button.tabIndex !== 0;
   button.addEventListener('click', function(){ activate(button); });
   button.addEventListener('keydown', function(e){
    var next;
    if(e.key === 'ArrowRight') next = (index + 1) % buttons.length;
    else if(e.key === 'ArrowLeft') next = (index + buttons.length - 1) % buttons.length;
    else if(e.key === 'Home') next = 0;
    else if(e.key === 'End') next = buttons.length - 1;
    else return;
    e.preventDefault(); buttons[next].focus(); activate(buttons[next]);
   });
  });
 }

 /* ---------- CertiCheck: zakładki i ślady ---------- */
 tabs($('.check-tabs'));
 var markers = [].slice.call(root.querySelectorAll('[data-damage]'));
 var photo = $('[data-damage-photo]');
 markers.forEach(function(marker){
  marker.addEventListener('click', function(){
   var n = marker.getAttribute('data-damage');
   markers.forEach(function(m){ m.setAttribute('aria-pressed', String(m.getAttribute('data-damage') === n)); });
   var area = marker.getAttribute('data-area');
   photo.src = marker.getAttribute('data-photo');
   photo.alt = 'Ślad ' + n + ': ' + marker.getAttribute('data-label').toLowerCase() + (area ? ', ' + area : '');
   $('[data-damage-label]').textContent = marker.getAttribute('data-label');
   $('[data-damage-counter]').textContent = n + ' / ' + root.querySelectorAll('.wear-marker').length;
  });
 });

 /* ---------- Prezentacja 360° ---------- */
 var stage = $('[data-onas-stage]');
 if(!stage) return;
 var REV_MS = 9000;          // pełny obrót auta z zewnątrz: ok. 9 s
 var PANO_SPEED = -6;        // wnętrze: stopnie na sekundę (pełny obrót ok. 60 s)
 var RESUME_MS = 2500;       // po puszczeniu obrót wraca po 2,5 s
 var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
 var hint = $('[data-viewer-hint]');

 function FramePlayer(panel){
  var sp = null, urls = [];
  try { sp = JSON.parse(panel.getAttribute('data-sprites') || 'null'); } catch(_){}
  try { urls = JSON.parse(panel.getAttribute('data-frames') || '[]'); } catch(_){}
  var n = sp ? sp.n : urls.length;
  var canvas = panel.querySelector('canvas'), ctx = canvas.getContext('2d');
  var poster = panel.querySelector('.viewer-poster');
  var cache = {}, current = 0, shownHi = false, started = false;
  function img(url){
   var im = cache[url];
   if(!im){
    im = new Image();
    im.decoding = 'async';
    im.addEventListener('load', function(){ if(!shownHi) draw(); });
    im.src = url;
    cache[url] = im;
   }
   return im;
  }
  function ready(im){ return !!(im && im.complete && im.naturalWidth > 0); }
  function load(){
   if(started) return;
   started = true;
   if(sp){ img(sp.preview); sp.sheets.forEach(img); }
   else urls.forEach(img);
  }
  // [obraz, sx, sy, sw, sh, pełna jakość]
  function frameSource(i){
   if(!started) return null;
   if(sp){
    var sheet = cache[sp.sheets[Math.floor(i / sp.per)]];
    if(ready(sheet)) return [sheet, 0, (i % sp.per) * sp.h, sp.w, sp.h, true];
    var pv = cache[sp.preview];
    if(ready(pv)) return [pv, (i % sp.pcols) * sp.pw, Math.floor(i / sp.pcols) * sp.ph, sp.pw, sp.ph, false];
    return null;
   }
   var im = cache[urls[i]];
   return ready(im) ? [im, 0, 0, im.naturalWidth, im.naturalHeight, true] : null;
  }
  function draw(){
   var r = panel.getBoundingClientRect();
   if(!r.width) return false;
   var dpr = Math.min(2, window.devicePixelRatio || 1);
   var cw = Math.round(r.width * dpr), ch = Math.round(r.height * dpr);
   if(canvas.width !== cw || canvas.height !== ch){ canvas.width = cw; canvas.height = ch; }
   var f = frameSource(current);
   if(!f) return false;
   var sc = Math.max(cw / f[3], ch / f[4]), dw = f[3] * sc, dh = f[4] * sc;
   ctx.imageSmoothingEnabled = true;
   ctx.imageSmoothingQuality = 'high';
   ctx.drawImage(f[0], f[1], f[2], f[3], f[4], (cw - dw) / 2, (ch - dh) / 2, dw, dh);
   shownHi = f[5];
   canvas.dataset.frame = current + 1;
   if(poster) poster.hidden = true;
   return true;
  }
  function set(i){ current = ((Math.round(i) % n) + n) % n; draw(); }
  return {
   type: 'frames', n: n, load: load, draw: draw, set: set,
   get frame(){ return current; },
   canShow: function(i){ return !!frameSource(((i % n) + n) % n); }
  };
 }

 function PanoPlayer(panel){
  var host = panel.querySelector('.pano-host'), status = panel.querySelector('.pano-loading');
  var viewer = null, loaded = false, started = false, want = false, running = false;
  function fail(){ if(status){ status.hidden = false; status.textContent = 'Nie udało się wczytać panoramy wnętrza.'; } }
  function init(){
   viewer = pannellum.viewer(host, {
    type: 'equirectangular', panorama: host.getAttribute('data-pano-src'), autoLoad: true,
    showControls: false, mouseZoom: false, keyboardZoom: false, doubleClickZoom: false,
    compass: false, hfov: 100, minHfov: 100, maxHfov: 100, pitch: -15, yaw: 0
   });
   viewer.on('load', function(){ loaded = true; if(status) status.hidden = true; apply(); });
   viewer.on('error', fail);
  }
  function load(){
   if(started) return;
   started = true;
   if(!document.querySelector('link[data-pannellum]')){
    var css = document.createElement('link');
    css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
    css.dataset.pannellum = '1'; document.head.appendChild(css);
   }
   if(window.pannellum){ init(); return; }
   var s = document.createElement('script');
   s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
   s.onload = init; s.onerror = fail;
   document.body.appendChild(s);
  }
  function apply(){
   if(!viewer || !loaded) return;
   if(want && !running){ viewer.startAutoRotate(PANO_SPEED); running = true; }
   else if(!want && running){ viewer.stopAutoRotate(); running = false; }
  }
  return {
   type: 'pano', load: load, draw: function(){},
   run: function(on){ want = on; apply(); },
   turn: function(dir){ if(viewer && loaded) viewer.setYaw(viewer.getYaw() + dir * 20, 500); },
   stopNow: function(){ if(viewer && loaded){ viewer.stopAutoRotate(); running = false; } }
  };
 }

 var players = {}, panels = {};
 ['outside', 'inside'].forEach(function(key){
  var panel = document.getElementById(key + '-panel');
  if(!panel) return;
  panels[key] = panel;
  var kind = panel.getAttribute('data-kind');
  if(kind === 'frames') players[key] = FramePlayer(panel);
  else if(kind === 'pano') players[key] = PanoPlayer(panel);
 });
 var view = panels.outside ? 'outside' : 'inside';
 var visible = false, near = false, held = false, resumeTimer = null;

 function active(){ return players[view] || null; }
 function updateChrome(){
  var p = active();
  stage.classList.toggle('is-static', !p);
  if(hint) hint.textContent = view === 'inside' ? 'Przeciągnij, aby się rozejrzeć' : 'Przeciągnij, aby obrócić auto';
 }

 // Pętla obrotu dla klatek (zewnątrz albo wnętrze z klatek).
 var raf = null, last = 0, acc = 0;
 function tick(ts){
  if(!raf) return;
  var p = active();
  if(!last) last = ts;
  acc += ts - last; last = ts;
  if(p && p.type === 'frames'){
   var step = REV_MS / p.n;
   if(acc > 1000) acc = step; // po uśpieniu karty nie nadrabiaj
   while(acc >= step){
    acc -= step;
    if(p.canShow(p.frame + 1)) p.set(p.frame + 1); else { acc = 0; break; }
   }
  }
  raf = requestAnimationFrame(tick);
 }
 function sync(){
  var run = visible && !held && !reduce && !document.hidden;
  var p = active();
  if(run && p && p.type === 'frames'){ if(!raf){ last = 0; acc = 0; raf = requestAnimationFrame(tick); } }
  else if(raf){ cancelAnimationFrame(raf); raf = null; }
  Object.keys(players).forEach(function(k){
   if(players[k].type === 'pano') players[k].run(run && k === view);
  });
 }
 function hold(){
  held = true;
  clearTimeout(resumeTimer);
  var p = active();
  if(p && p.type === 'pano') p.stopNow();
  sync();
 }
 function release(){
  clearTimeout(resumeTimer);
  resumeTimer = setTimeout(function(){ held = false; sync(); }, RESUME_MS);
 }

 tabs($('.viewer-tabs'), function(button){
  view = button.id === 'view-inside' ? 'inside' : 'outside';
  var p = active();
  if(p){ p.load(); p.draw(); }
  updateChrome();
  sync();
 });
 updateChrome();

 function rotate(dir){
  var p = active();
  if(!p) return;
  hold();
  if(p.type === 'frames') p.set(p.frame + dir * 2);
  else p.turn(dir);
  release();
 }
 [].forEach.call(root.querySelectorAll('[data-rotate]'), function(b){
  b.addEventListener('click', function(){ rotate(Number(b.getAttribute('data-rotate'))); });
 });
 stage.tabIndex = 0;
 stage.addEventListener('keydown', function(e){
  if(e.target !== stage) return;
  if(e.key === 'ArrowLeft' || e.key === 'ArrowRight'){ e.preventDefault(); rotate(e.key === 'ArrowLeft' ? -1 : 1); }
 });

 var drag = null;
 stage.addEventListener('pointerdown', function(e){
  if(e.target.closest('button') || e.button !== 0) return;
  var p = active();
  if(!p) return;
  hold();
  if(p.type === 'frames'){
   drag = { x: e.clientX, frame: p.frame };
   stage.setPointerCapture(e.pointerId);
   stage.classList.add('dragging');
  } else {
   drag = { pano: true };
  }
 });
 stage.addEventListener('pointermove', function(e){
  if(!drag || drag.pano) return;
  var p = active();
  if(p && p.type === 'frames') p.set(drag.frame - (e.clientX - drag.x) / 7);
 });
 function stopDrag(){
  if(!drag) return;
  drag = null;
  stage.classList.remove('dragging');
  release();
 }
 stage.addEventListener('pointerup', stopDrag);
 stage.addEventListener('pointercancel', stopDrag);
 stage.addEventListener('lostpointercapture', stopDrag);
 window.addEventListener('pointerup', stopDrag);

 // Klatki pobieramy dopiero, gdy sekcja zbliża się do ekranu; obrót tylko, gdy jest widoczna.
 if('IntersectionObserver' in window){
  new IntersectionObserver(function(entries){
   if(entries[entries.length - 1].isIntersecting && !near){ near = true; var p = active(); if(p) p.load(); }
  }, { rootMargin: '400px 0px' }).observe(stage);
  new IntersectionObserver(function(entries){
   visible = entries[entries.length - 1].isIntersecting; sync(); // ostatni wpis = aktualny stan
  }, { threshold: 0.2 }).observe(stage);
 } else {
  near = visible = true; var p0 = active(); if(p0) p0.load(); sync();
 }
 document.addEventListener('visibilitychange', sync);
 if(window.ResizeObserver) new ResizeObserver(function(){ var p = active(); if(p) p.draw(); }).observe(stage);
})();
</script>
@endpush
