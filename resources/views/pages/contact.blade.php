@extends('layouts.public')
@section('title','Kontakt')
@section('description','Skontaktuj się z CertiCars. Zadzwoń, napisz lub umów oględziny w salonie w Lipniku k. Stargardu. Odpowiadamy szybko i konkretnie.')
@section('styles')
/* ===== CONTACT PAGE — premium refresh ===== */
.kt-section-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;width:100%;box-sizing:border-box}

/* ===== HERO (dark) ===== */
.kt-hero{background:linear-gradient(160deg,#050a17 0%,#070d20 45%,#0a1432 100%);padding:88px 0 96px;position:relative;overflow:hidden}
.kt-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 58% 50% at 18% 30%,rgba(0,102,255,.16),transparent 60%),radial-gradient(ellipse 55% 65% at 86% 70%,rgba(0,102,255,.18),transparent 65%);pointer-events:none}
.kt-hero-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.05) 1px,transparent 1px);background-size:30px 30px;pointer-events:none;mask-image:radial-gradient(ellipse 70% 70% at 50% 50%,#000 30%,transparent 90%)}
.kt-hero-roadart{position:absolute;left:-2%;top:38%;width:36%;max-width:480px;opacity:.08;pointer-events:none}
.kt-hero-roadart svg{width:100%;height:auto;display:block}
.kt-hero-in{display:grid;grid-template-columns:1.05fr .95fr;gap:64px;align-items:center}

.kt-breadcrumb{display:flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.45);margin-bottom:24px}
.kt-breadcrumb a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s}
.kt-breadcrumb a:hover{color:#fff}
.kt-breadcrumb svg{width:11px;height:11px;stroke:rgba(255,255,255,.3);fill:none;stroke-width:2.5}
.kt-breadcrumb .current{color:#fff;font-weight:500}
.kt-hero-label{font-size:11px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:var(--orange);margin-bottom:22px;display:inline-flex;align-items:center;gap:12px}
.kt-hero-label::before{content:'';width:32px;height:2px;background:var(--orange);border-radius:2px;flex-shrink:0}
.kt-hero h1{font-size:54px;font-weight:900;color:#fff;letter-spacing:-1.1px;line-height:1.05;margin:0 0 22px;max-width:540px}
.kt-hero h1 em{font-style:normal;color:var(--blue);background:linear-gradient(120deg,#0066ff,#3b8bff);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.kt-hero-desc{font-size:16px;color:rgba(255,255,255,.62);line-height:1.75;margin:0 0 28px;max-width:520px}
.kt-trust{display:flex;flex-wrap:wrap;gap:10px}
.kt-trust-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.78);padding:9px 16px;border-radius:50px;font-size:12.5px;font-weight:600;letter-spacing:-.05px}
.kt-trust-pill svg{width:14px;height:14px;stroke:#5fa1ff;fill:none;stroke-width:2.2;flex-shrink:0}

/* Hero right panel — info rows + CTA */
.kt-panel{position:relative;background:linear-gradient(180deg,rgba(15,28,60,.85),rgba(8,16,38,.95));border:1px solid rgba(95,161,255,.32);border-radius:24px;padding:30px 30px 26px;box-shadow:0 0 0 1px rgba(0,102,255,.08),0 40px 80px -20px rgba(0,40,120,.6),0 0 80px -20px rgba(0,102,255,.35)}
.kt-panel::before{content:'';position:absolute;inset:0;border-radius:24px;padding:1px;background:linear-gradient(135deg,rgba(95,161,255,.45),rgba(95,161,255,0) 45%,rgba(95,161,255,.12) 100%);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}
.kt-prow{display:grid;grid-template-columns:46px 1fr;gap:16px;align-items:center;padding:16px 0;border-top:1px solid rgba(95,161,255,.14)}
.kt-prow:first-child{border-top:none;padding-top:6px}
.kt-prow:last-of-type{padding-bottom:6px}
.kt-prow-ico{width:44px;height:44px;border-radius:12px;background:rgba(0,102,255,.14);border:1px solid rgba(95,161,255,.32);display:flex;align-items:center;justify-content:center;color:#5fa1ff;flex-shrink:0}
.kt-prow-ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2}
.kt-prow-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.45);margin:0 0 3px}
.kt-prow-val{font-size:17px;font-weight:800;color:#fff;letter-spacing:-.2px;text-decoration:none;line-height:1.25;display:block}
.kt-prow a.kt-prow-val:hover{color:#5fa1ff}
.kt-panel-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:18px;padding:14px 22px;background:linear-gradient(135deg,#0066ff,#1a7fff);color:#fff;font-weight:800;font-size:15px;border-radius:50px;text-decoration:none;letter-spacing:-.1px;box-shadow:0 16px 32px -10px rgba(0,102,255,.6),inset 0 -2px 4px rgba(0,0,0,.18);transition:transform .2s ease,box-shadow .2s ease}
.kt-panel-btn:hover{transform:translateY(-2px);box-shadow:0 24px 40px -10px rgba(0,102,255,.75),inset 0 -2px 4px rgba(0,0,0,.18)}
.kt-panel-btn svg{width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.2}

/* ===== Section helpers ===== */
.kt-section-label{font-size:11px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:var(--blue);margin-bottom:14px;text-align:center;display:flex;align-items:center;justify-content:center;gap:10px}
.kt-section-label::before,.kt-section-label::after{content:'';width:24px;height:2px;background:var(--blue);border-radius:2px;opacity:.5}
.kt-section-h{font-size:36px;font-weight:900;color:var(--text);letter-spacing:-.8px;text-align:center;margin:0 0 14px;line-height:1.1}
.kt-section-sub{font-size:15px;color:var(--text-3);text-align:center;max-width:620px;margin:0 auto 48px;line-height:1.7}

/* ===== "Skontaktuj się tak, jak Ci wygodnie" — 3 method cards ===== */
.kt-methods{padding:80px 0 24px;background:var(--bg)}
.kt-methods-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.kt-method{background:#fff;border-radius:18px;padding:28px 26px;border:1px solid #eeeef0;box-shadow:0 1px 3px rgba(0,0,0,.04),0 12px 32px -16px rgba(15,23,42,.12);transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;text-decoration:none;color:inherit;display:flex;gap:16px;align-items:flex-start;position:relative}
.kt-method:hover{transform:translateY(-3px);box-shadow:0 1px 3px rgba(0,0,0,.04),0 22px 44px -16px rgba(15,23,42,.18);border-color:#dbeafe}
.kt-method-ico{flex-shrink:0;width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#0066ff,#1a7fff);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 16px -6px rgba(0,102,255,.5)}
.kt-method-ico svg{width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2.2}
.kt-method h3{font-size:16px;font-weight:800;color:var(--text);letter-spacing:-.2px;margin:0 0 8px}
.kt-method p{font-size:13.5px;color:var(--text-3);line-height:1.7;margin:0}
.kt-method-arr{position:absolute;right:18px;bottom:18px;color:#cbd5e1;transition:color .2s ease,transform .2s ease}
.kt-method-arr svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.2}
.kt-method:hover .kt-method-arr{color:var(--blue);transform:translateX(3px)}

/* ===== Form + side info ===== */
.kt-form-wrap{padding:32px 0 72px;background:var(--bg)}
.kt-form-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:28px;align-items:start}
.kt-form-card{background:#fff;border-radius:20px;padding:36px;border:1px solid #eeeef0;box-shadow:0 1px 3px rgba(0,0,0,.04),0 14px 36px -16px rgba(15,23,42,.14)}
.kt-form-card h2{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin:0 0 6px}
.kt-form-card > p{font-size:13.5px;color:var(--text-3);line-height:1.6;margin:0 0 26px}
.kt-form-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#065f46;padding:14px 18px;border-radius:10px;font-size:14px;font-weight:500;margin-bottom:22px;display:flex;align-items:center;gap:10px}
.kt-form-success svg{width:18px;height:18px;stroke:#10b981;fill:none;stroke-width:2;flex-shrink:0}
.kt-form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.kt-field{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
.kt-field:last-of-type{margin-bottom:24px}
.kt-field label{font-size:10.5px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3)}
.kt-field input,.kt-field textarea{width:100%;padding:13px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;font-family:inherit;color:var(--text);transition:border-color .15s,box-shadow .15s;background:#fff;box-sizing:border-box}
.kt-field input:focus,.kt-field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,102,255,.08)}
.kt-field input::placeholder,.kt-field textarea::placeholder{color:var(--text-4)}
.kt-field textarea{resize:vertical;min-height:130px;line-height:1.6}
.kt-field-err{font-size:12px;color:#dc2626;margin-top:2px}
.kt-form-submit{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;border-radius:50px;background:linear-gradient(135deg,#0066ff,#1a7fff);color:#fff;border:none;font-family:inherit;font-size:15.5px;font-weight:800;cursor:pointer;letter-spacing:-.1px;box-shadow:0 14px 28px -10px rgba(0,102,255,.55),inset 0 -2px 4px rgba(0,0,0,.18);transition:transform .15s ease,box-shadow .15s ease}
.kt-form-submit:hover{transform:translateY(-2px);box-shadow:0 20px 36px -10px rgba(0,102,255,.7),inset 0 -2px 4px rgba(0,0,0,.18)}
.kt-form-submit svg{width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.2}

.kt-side{display:flex;flex-direction:column;gap:16px}
.kt-side-card{background:#fff;border-radius:18px;padding:22px;border:1px solid #eeeef0;box-shadow:0 1px 3px rgba(0,0,0,.04),0 12px 28px -16px rgba(15,23,42,.12);display:flex;gap:14px;align-items:flex-start}
.kt-side-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center;color:var(--blue)}
.kt-side-ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2.2}
.kt-side-body{flex:1;min-width:0}
.kt-side-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--text-3);margin:0 0 4px}
.kt-side-val{font-size:17px;font-weight:800;color:var(--text);letter-spacing:-.2px;margin:0 0 4px;display:block;text-decoration:none}
a.kt-side-val:hover{color:var(--blue)}
.kt-side-meta{font-size:12.5px;color:var(--text-3);line-height:1.55;margin:0}
.kt-side-btn{display:inline-flex;align-items:center;gap:8px;margin-top:12px;padding:9px 16px;background:#fff;color:var(--blue);border:1.5px solid var(--blue);border-radius:50px;font-size:12.5px;font-weight:700;text-decoration:none;transition:background .15s,color .15s}
.kt-side-btn:hover{background:var(--blue);color:#fff}
.kt-side-btn svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.4}
.kt-hours-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px;border-top:1px dashed #eef2f7}
.kt-hours-row:first-of-type{border-top:none;padding-top:6px}
.kt-hours-row .d{color:var(--text-3);font-weight:500}
.kt-hours-row .t{color:var(--text);font-weight:800}

/* ===== Map + location section ===== */
.kt-map-section{padding:24px 0 80px;background:var(--bg)}
.kt-map-grid{display:grid;grid-template-columns:1.25fr 1fr;gap:32px;align-items:start}
.kt-map-card{position:relative;border-radius:20px;overflow:hidden;border:1px solid rgba(95,161,255,.18);box-shadow:0 4px 16px rgba(15,23,42,.08);height:420px;background:linear-gradient(135deg,#dbeafe,#f1f5f9)}
.kt-map-tiles{position:absolute;inset:0;overflow:hidden}
.kt-map-tile{position:absolute;width:256px;height:256px;display:block;pointer-events:none}
.kt-map-tile.r0{top:calc(50% - 266px)}
.kt-map-tile.r1{top:calc(50% - 10px)}
.kt-map-tile.c0{left:calc(50% - 734px)}
.kt-map-tile.c1{left:calc(50% - 478px)}
.kt-map-tile.c2{left:calc(50% - 222px)}
.kt-map-tile.c3{left:calc(50% + 34px)}
.kt-map-tile.c4{left:calc(50% + 290px)}
.kt-map-tile.c5{left:calc(50% + 546px)}
.kt-map-overlay{position:absolute;inset:0;pointer-events:none;background:linear-gradient(180deg,rgba(255,255,255,0) 60%,rgba(255,255,255,.12) 100%)}
.kt-map-marker{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);z-index:2;display:flex;flex-direction:column;align-items:center;gap:10px;pointer-events:none}
.kt-map-marker-pin{position:relative;display:flex;align-items:center;justify-content:center;filter:drop-shadow(0 6px 14px rgba(0,102,255,.55))}
.kt-map-marker-pin::before{content:'';position:absolute;width:76px;height:76px;border-radius:50%;background:radial-gradient(circle,rgba(0,102,255,.45) 0%,rgba(0,102,255,0) 70%);animation:ktMapPulse 2.2s ease-in-out infinite}
.kt-map-marker-pin svg{width:46px;height:46px;color:#0066ff;position:relative;z-index:1;stroke:currentColor;fill:none;stroke-width:2}
.kt-map-marker-label{position:relative;background:#0066ff;color:#fff;font-size:13px;font-weight:800;letter-spacing:.1px;padding:8px 16px;border-radius:8px;box-shadow:0 6px 18px rgba(0,102,255,.5),0 1px 3px rgba(0,0,0,.22);white-space:nowrap;line-height:1}
.kt-map-marker-label::before{content:'';position:absolute;left:50%;top:-5px;transform:translateX(-50%) rotate(45deg);width:10px;height:10px;background:#0066ff;border-radius:1px}
@keyframes ktMapPulse{0%,100%{transform:scale(.85);opacity:.85}50%{transform:scale(1.08);opacity:.4}}

.kt-loc-body{padding-top:8px}
.kt-loc-eyebrow{font-size:11px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:var(--blue);margin-bottom:12px;display:inline-flex;align-items:center;gap:10px}
.kt-loc-eyebrow::before{content:'';width:24px;height:2px;background:var(--blue);border-radius:2px;opacity:.6}
.kt-loc-h{font-size:32px;font-weight:900;color:var(--text);letter-spacing:-.7px;margin:0 0 22px;line-height:1.15}
.kt-loc-line{display:grid;grid-template-columns:40px 1fr;gap:14px;align-items:flex-start;padding:14px 0;border-top:1px solid #eef2f7}
.kt-loc-line:first-of-type{border-top:none}
.kt-loc-line-ico{width:36px;height:36px;border-radius:10px;background:var(--blue-bg);color:var(--blue);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kt-loc-line-ico svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.2}
.kt-loc-line-eyebrow{font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:1.3px;margin:0 0 2px}
.kt-loc-line-val{font-size:15px;font-weight:700;color:var(--text);line-height:1.5;margin:0}
.kt-loc-line-sub{font-size:13px;color:var(--text-3);margin:2px 0 0;line-height:1.5}
.kt-loc-hours-grid{display:grid;grid-template-columns:1fr auto;row-gap:6px;column-gap:18px;margin-top:6px;font-size:13px}
.kt-loc-hours-grid .d{color:var(--text-3);font-weight:500}
.kt-loc-hours-grid .t{color:var(--text);font-weight:800}
.kt-loc-callout{display:flex;gap:12px;align-items:flex-start;background:#eff6ff;border:1px solid #dbeafe;border-radius:12px;padding:14px 16px;margin-top:20px}
.kt-loc-callout-ico{flex-shrink:0;width:28px;height:28px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;margin-top:1px}
.kt-loc-callout-ico svg{width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2.6}
.kt-loc-callout p{margin:0;font-size:13px;color:var(--text);line-height:1.5}
.kt-loc-callout p b{display:block;font-weight:800;color:var(--text);margin-bottom:2px;font-size:13.5px}
.kt-loc-callout p span{color:var(--text-3);font-weight:500}

/* ===== Bottom phone CTA strip ===== */
.kt-call-strip{position:relative;background:linear-gradient(120deg,#0052cc 0%,#0066ff 45%,#1a7fff 100%);overflow:hidden;padding:48px 0}
.kt-call-strip::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 40% 80% at 10% 50%,rgba(255,255,255,.18),transparent 60%),radial-gradient(ellipse 50% 80% at 90% 50%,rgba(0,255,255,.06),transparent 70%);pointer-events:none}
.kt-call-strip-in{display:grid;grid-template-columns:auto 1fr auto;gap:32px;align-items:center}
.kt-call-strip-ico{width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.16);border:1.5px solid rgba(255,255,255,.32);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:inset 0 -2px 6px rgba(0,0,0,.14)}
.kt-call-strip-ico svg{width:34px;height:34px;stroke:#fff;fill:none;stroke-width:2}
.kt-call-strip-body h2{font-size:30px;font-weight:900;color:#fff;letter-spacing:-.5px;margin:0 0 6px;line-height:1.1}
.kt-call-strip-body p{font-size:14.5px;color:rgba(255,255,255,.75);line-height:1.6;margin:0;max-width:580px}
.kt-call-strip-btn{display:inline-flex;align-items:center;gap:12px;background:#fff;color:var(--blue);padding:18px 32px;border-radius:50px;font-weight:800;font-size:18px;text-decoration:none;letter-spacing:-.2px;box-shadow:0 18px 36px -12px rgba(0,0,0,.32);transition:transform .2s ease,box-shadow .2s ease;flex-shrink:0}
.kt-call-strip-btn:hover{transform:translateY(-2px);box-shadow:0 24px 44px -12px rgba(0,0,0,.4)}
.kt-call-strip-btn svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2.2}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .kt-hero h1{font-size:44px}
    .kt-hero-in{grid-template-columns:1fr;gap:48px}
    .kt-form-grid{grid-template-columns:1fr;gap:22px}
    .kt-map-grid{grid-template-columns:1fr;gap:28px}
    .kt-map-card{height:340px}
    .kt-call-strip-in{grid-template-columns:auto 1fr;gap:24px;text-align:left}
    .kt-call-strip-btn{grid-column:1/-1;justify-self:start}
}
@media(max-width:900px){
    .kt-hero{padding:64px 0 76px}
    .kt-hero h1{font-size:38px}
    .kt-methods-grid{grid-template-columns:1fr;gap:14px}
    .kt-section-h{font-size:28px}
    .kt-loc-h{font-size:26px}
    .kt-panel{padding:24px 22px 20px}
    .kt-prow-val{font-size:16px}
}
@media(max-width:600px){
    .kt-hero h1{font-size:30px;letter-spacing:-.5px}
    .kt-hero-desc{font-size:14.5px}
    .kt-section-h{font-size:24px}
    .kt-form-card{padding:24px 20px}
    .kt-form-row{grid-template-columns:1fr;gap:0}
    .kt-trust{gap:8px}
    .kt-trust-pill{padding:8px 12px;font-size:11.5px}
    .kt-call-strip{padding:36px 0}
    .kt-call-strip-in{grid-template-columns:1fr;gap:18px;text-align:center;justify-items:center}
    .kt-call-strip-ico{margin:0 auto}
    .kt-call-strip-body h2{font-size:24px}
    .kt-call-strip-btn{font-size:16px;padding:14px 24px;justify-self:center}
}
@endsection
@section('content')

{{-- ===== HERO (dark) ===== --}}
<section class="kt-hero">
    <div class="kt-hero-dots"></div>
    <div class="kt-hero-roadart" aria-hidden="true">
        <svg viewBox="0 0 480 360" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#5fa1ff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M40 350 Q80 240 130 220 Q180 200 200 150 Q230 85 280 70 Q330 55 360 100"/>
            <path d="M280 70 q-6 -3 -10 -10 q-2 -6 5 -10 q9 -2 13 6 q3 8 -8 14 z" fill="#5fa1ff" stroke="none" opacity=".6"/>
            <circle cx="86" cy="290" r="3"/><circle cx="120" cy="235" r="3"/><circle cx="155" cy="210" r="3"/><circle cx="190" cy="180" r="3"/>
        </svg>
    </div>
    <div class="kt-section-in kt-hero-in">
        <div>
            <nav class="kt-breadcrumb" aria-label="Okruszki">
                <a href="{{ route('home') }}">Strona główna</a>
                <x-icon name="chevron-right" size="11"/>
                <span class="current">Kontakt</span>
            </nav>
            <div class="kt-hero-label">Skontaktuj się</div>
            <h1>Masz pytanie?<br><em>Jesteśmy do dyspozycji.</em></h1>
            <p class="kt-hero-desc">Zadzwoń, napisz lub umów oględziny auta. Odpowiadamy konkretnie i pomagamy szybko ustalić najważniejsze informacje.</p>
            <div class="kt-trust">
                <span class="kt-trust-pill"><x-icon name="zap" size="14"/> Szybka odpowiedź</span>
                <span class="kt-trust-pill"><x-icon name="map-pin" size="14"/> Salon w Lipniku k. Stargardu</span>
                <span class="kt-trust-pill"><x-icon name="clock" size="14"/> Pn-Pt 9:00-18:00 · Sb-Nd 10:00-15:00</span>
            </div>
        </div>
        <aside class="kt-panel" aria-label="Dane kontaktowe">
            <div class="kt-prow">
                <div class="kt-prow-ico"><x-icon name="phone" size="20"/></div>
                <div>
                    <p class="kt-prow-eyebrow">Zadzwoń do nas</p>
                    <a href="tel:+48515440623" class="kt-prow-val">+48 515 440 623</a>
                </div>
            </div>
            <div class="kt-prow">
                <div class="kt-prow-ico"><x-icon name="mail" size="20"/></div>
                <div>
                    <p class="kt-prow-eyebrow">Napisz e-mail</p>
                    <a href="mailto:kontakt@certicars.pl" class="kt-prow-val">kontakt@certicars.pl</a>
                </div>
            </div>
            <div class="kt-prow">
                <div class="kt-prow-ico"><x-icon name="map-pin" size="20"/></div>
                <div>
                    <p class="kt-prow-eyebrow">Nasz salon</p>
                    <span class="kt-prow-val">Lipnik k. Stargardu</span>
                </div>
            </div>
            <div class="kt-prow">
                <div class="kt-prow-ico"><x-icon name="clock" size="20"/></div>
                <div>
                    <p class="kt-prow-eyebrow">Godziny otwarcia</p>
                    <span class="kt-prow-val" style="font-size:14.5px;font-weight:700">Pn-Pt 9:00-18:00 · Sb-Nd 10:00-15:00</span>
                </div>
            </div>
            <a href="#kontakt-formularz" class="kt-panel-btn">
                <x-icon name="calendar-check" size="18"/>
                Umów oględziny
            </a>
        </aside>
    </div>
</section>

{{-- ===== Skontaktuj się tak, jak Ci wygodnie — 3 method cards ===== --}}
<section class="kt-methods">
    <div class="kt-section-in">
        <div class="kt-section-label">Wygodny kontakt</div>
        <h2 class="kt-section-h">Skontaktuj się tak, jak Ci wygodnie</h2>
        <div class="kt-methods-grid">
            <a href="tel:+48515440623" class="kt-method">
                <div class="kt-method-ico"><x-icon name="phone" size="22"/></div>
                <div>
                    <h3>Zadzwoń</h3>
                    <p>Najszybszy kontakt. Odpowiadamy od razu i pomagamy ustalić najważniejsze informacje.</p>
                </div>
                <span class="kt-method-arr"><x-icon name="arrow-right" size="18"/></span>
            </a>
            <a href="#kontakt-formularz" class="kt-method">
                <div class="kt-method-ico"><x-icon name="mail" size="22"/></div>
                <div>
                    <h3>Napisz wiadomość</h3>
                    <p>Wyślij wiadomość, a odpowiemy z konkretną informacją o aucie i dostępności.</p>
                </div>
                <span class="kt-method-arr"><x-icon name="arrow-right" size="18"/></span>
            </a>
            <a href="#kontakt-formularz" class="kt-method">
                <div class="kt-method-ico"><x-icon name="calendar-check" size="22"/></div>
                <div>
                    <h3>Umów oględziny</h3>
                    <p>Potwierdź auto i umów dogodny termin. Oględziny tylko po wcześniejszym kontakcie.</p>
                </div>
                <span class="kt-method-arr"><x-icon name="arrow-right" size="18"/></span>
            </a>
        </div>
    </div>
</section>

{{-- ===== Form + side info ===== --}}
<section class="kt-form-wrap" id="kontakt-formularz">
    <div class="kt-section-in">
        <div class="kt-form-grid">
            <div class="kt-form-card">
                <h2>Napisz do nas</h2>
                <p>Masz pytanie o konkretne auto, dostępność lub oględziny? Wyślij wiadomość — wrócimy z konkretną odpowiedzią.</p>

                @if(session('success'))
                <div class="kt-form-success">
                    <x-icon name="check-circle" size="18"/>
                    {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" novalidate>
                    @csrf
                    {{-- honeypot --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0">
                    <div class="kt-form-row">
                        <div class="kt-field">
                            <label for="kt-name">Imię i nazwisko *</label>
                            <input type="text" id="kt-name" name="name" placeholder="Jan Kowalski" value="{{ old('name') }}" required>
                            @error('name')<span class="kt-field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="kt-field">
                            <label for="kt-phone">Telefon</label>
                            <input type="tel" id="kt-phone" name="phone" placeholder="+48 123 456 789" value="{{ old('phone') }}">
                            @error('phone')<span class="kt-field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="kt-field">
                        <label for="kt-email">Adres e-mail *</label>
                        <input type="email" id="kt-email" name="email" placeholder="jan.kowalski@example.pl" value="{{ old('email') }}" required>
                        @error('email')<span class="kt-field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="kt-field">
                        <label for="kt-msg">Wiadomość *</label>
                        <textarea id="kt-msg" name="message" placeholder="W czym możemy pomóc? Opisz szczegółowo swoje pytanie…" required>{{ old('message') }}</textarea>
                        @error('message')<span class="kt-field-err">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="kt-form-submit">
                        <x-icon name="send" size="18"/>
                        Wyślij wiadomość
                    </button>
                </form>
            </div>

            <aside class="kt-side" aria-label="Inne sposoby kontaktu">
                <div class="kt-side-card">
                    <div class="kt-side-ico"><x-icon name="phone" size="20"/></div>
                    <div class="kt-side-body">
                        <p class="kt-side-eyebrow">Zadzwoń do nas</p>
                        <a href="tel:+48515440623" class="kt-side-val">+48 515 440 623</a>
                        <p class="kt-side-meta">Pn-Pt 9:00-18:00 · Sb-Nd 10:00-15:00</p>
                    </div>
                </div>
                <div class="kt-side-card">
                    <div class="kt-side-ico"><x-icon name="map-pin" size="20"/></div>
                    <div class="kt-side-body">
                        <p class="kt-side-eyebrow">Nasz salon</p>
                        <span class="kt-side-val">Lipnik k. Stargardu</span>
                        <p class="kt-side-meta">Oględziny po wcześniejszym kontakcie.</p>
                        <a href="#kontakt-mapa" class="kt-side-btn"><x-icon name="map" size="13"/> Pokaż na mapie</a>
                    </div>
                </div>
                <div class="kt-side-card">
                    <div class="kt-side-ico"><x-icon name="clock" size="20"/></div>
                    <div class="kt-side-body" style="flex:1">
                        <p class="kt-side-eyebrow">Godziny otwarcia</p>
                        <div style="margin-top:8px">
                            <div class="kt-hours-row"><span class="d">Poniedziałek – Piątek</span><span class="t">9:00 – 18:00</span></div>
                            <div class="kt-hours-row"><span class="d">Sobota – Niedziela</span><span class="t">10:00 – 15:00</span></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- ===== Map + Gdzie nas znajdziesz? ===== --}}
<section class="kt-map-section" id="kontakt-mapa">
    <div class="kt-section-in">
        <div class="kt-map-grid">
            {{-- OSM tile mosaic centred on the CertiCars pin in Lipnik k. Stargardu.
                 Same coordinates + tile pattern as the footer map. --}}
            <div class="kt-map-card">
                <div class="kt-map-tiles" aria-hidden="true">
                    @foreach([[0,17741],[1,17742],[2,17743],[3,17744],[4,17745],[5,17746]] as [$ci,$tx])
                        <img class="kt-map-tile r0 c{{ $ci }}" src="https://tile.openstreetmap.org/15/{{ $tx }}/10620.png" alt="" loading="lazy" decoding="async" width="256" height="256" onerror="this.style.display='none'">
                        <img class="kt-map-tile r1 c{{ $ci }}" src="https://tile.openstreetmap.org/15/{{ $tx }}/10621.png" alt="" loading="lazy" decoding="async" width="256" height="256" onerror="this.style.display='none'">
                    @endforeach
                </div>
                <div class="kt-map-overlay" aria-hidden="true"></div>
                <div class="kt-map-marker" aria-label="Lokalizacja: CertiCars">
                    <span class="kt-map-marker-pin" aria-hidden="true"><x-icon name="map-pin" size="46" :strokeWidth="2"/></span>
                    <span class="kt-map-marker-label">CertiCars</span>
                </div>
            </div>
            <div class="kt-loc-body">
                <div class="kt-loc-eyebrow">Nasza lokalizacja</div>
                <h2 class="kt-loc-h">Gdzie nas znajdziesz?</h2>
                <div class="kt-loc-line">
                    <div class="kt-loc-line-ico"><x-icon name="map-pin" size="18"/></div>
                    <div>
                        <p class="kt-loc-line-eyebrow">Adres</p>
                        <p class="kt-loc-line-val">Lipnik k. Stargardu</p>
                        <p class="kt-loc-line-sub">73-110 Stargard, Polska</p>
                    </div>
                </div>
                <div class="kt-loc-line">
                    <div class="kt-loc-line-ico"><x-icon name="clock" size="18"/></div>
                    <div>
                        <p class="kt-loc-line-eyebrow">Godziny otwarcia</p>
                        <div class="kt-loc-hours-grid">
                            <span class="d">Poniedziałek – Piątek</span><span class="t">9:00 – 18:00</span>
                            <span class="d">Sobota – Niedziela</span><span class="t">10:00 – 15:00</span>
                        </div>
                    </div>
                </div>
                <div class="kt-loc-callout">
                    <div class="kt-loc-callout-ico"><x-icon name="check" size="14"/></div>
                    <p>
                        <b>Przed przyjazdem zadzwoń i potwierdź dostępność auta.</b>
                        <span>Oględziny tylko po wcześniejszym kontakcie.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Bottom phone CTA strip ===== --}}
<section class="kt-call-strip">
    <div class="kt-section-in kt-call-strip-in">
        <div class="kt-call-strip-ico" aria-hidden="true"><x-icon name="phone" size="34"/></div>
        <div class="kt-call-strip-body">
            <h2>Wolisz porozmawiać?</h2>
            <p>Zadzwoń bezpośrednio — odpowiemy na pytania o auto, potwierdzimy dostępność i pomożemy umówić oględziny.</p>
        </div>
        <a href="tel:+48515440623" class="kt-call-strip-btn">
            <x-icon name="phone" size="22"/>
            +48 515 440 623
        </a>
    </div>
</section>

@endsection
