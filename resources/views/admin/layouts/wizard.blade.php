<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- P1 data-loss safety: surface session expiry to the autosave + warning JS. --}}
    <meta name="session-expires-at" content="{{ now()->addMinutes((int) config('session.lifetime'))->timestamp }}">
    <meta name="wizard-autosave-key" content="wiz:car:{{ isset($car) && $car ? 'edit:' . $car->id : 'create' }}">
    <meta name="wizard-car-updated-at" content="{{ isset($car) && $car ? $car->updated_at?->timestamp : '' }}">
    <meta name="wizard-session-expired" content="{{ session('session_expired') ? '1' : '0' }}">
    <title>@yield('title','Kreator ogłoszenia') — CertiCars</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--blue:#0066ff;--blue-h:#0052cc;--blue-bg:#e8f1ff;--text:#0a0a0a;--text-2:#555;--text-3:#868686;--text-4:#b0b0b0;--bg:#f7f7f8;--border:#e5e5e7;--border-l:#eeeef0;--green:#10b981;--yellow:#f59e0b;--red:#ef4444}
        body{font-family:'Inter',system-ui,sans-serif;color:var(--text);background:var(--bg);font-size:14px;line-height:1.5;-webkit-font-smoothing:antialiased;overflow:hidden;height:100vh}
        a{color:inherit;text-decoration:none}
        button{font-family:inherit;cursor:pointer}
        input,select,textarea{font-family:inherit}
        :focus-visible{outline:2px solid var(--blue);outline-offset:2px;border-radius:4px}

        /* ===================== LAYOUT GRID ===================== */
        .wiz-shell{display:grid;grid-template-rows:60px 1fr 64px;grid-template-columns:240px 1fr 340px;height:100vh;width:100vw}
        .wiz-topbar{grid-column:1/-1;grid-row:1}
        .wiz-sidebar{grid-column:1;grid-row:2}
        .wiz-center{grid-column:2;grid-row:2}
        .wiz-preview{grid-column:3;grid-row:2}
        .wiz-bottombar{grid-column:1/-1;grid-row:3}

        /* ===================== TOP BAR ===================== */
        .wiz-topbar{background:#fff;border-bottom:1px solid var(--border-l);display:flex;align-items:center;padding:0 20px;gap:16px;z-index:20}
        .wiz-topbar-logo{display:flex;align-items:center;gap:10px;flex-shrink:0;padding-right:20px;border-right:1px solid var(--border-l);margin-right:4px}
        .wiz-topbar-logo svg{flex-shrink:0}
        .wiz-topbar-logo .tx{font-size:16px;font-weight:800;letter-spacing:-.4px;color:var(--text)}
        .wiz-topbar-logo .tx span{color:var(--blue)}

        .wiz-progress-wrap{flex:1;display:flex;align-items:center;gap:14px;min-width:0}
        .wiz-progress-label{font-size:12px;font-weight:600;color:var(--text-3);white-space:nowrap}
        .wiz-progress-track{flex:1;height:8px;background:var(--border-l);border-radius:99px;overflow:hidden;max-width:480px}
        .wiz-progress-fill{height:100%;background:var(--blue);border-radius:99px;transition:width .45s cubic-bezier(.4,0,.2,1);width:9%}
        .wiz-progress-text{font-size:12px;font-weight:700;color:var(--blue);white-space:nowrap}

        .wiz-topbar-actions{display:flex;align-items:center;gap:10px;flex-shrink:0;margin-left:auto}
        .wiz-topbar-actions .btn-save-exit{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;font-size:13px;font-weight:600;border:1px solid var(--border);background:#fff;color:var(--text);transition:all .15s;white-space:nowrap}
        .wiz-topbar-actions .btn-save-exit:hover{background:var(--bg);border-color:var(--text-4)}
        .wiz-topbar-actions .btn-save-exit i{width:15px;height:15px}
        .wiz-topbar-actions .btn-close{display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid var(--border);background:#fff;color:var(--text-3);transition:all .15s}
        .wiz-topbar-actions .btn-close:hover{background:#fef2f2;border-color:#fecaca;color:var(--red)}
        .wiz-topbar-actions .btn-close i{width:18px;height:18px}

        /* ===================== LEFT SIDEBAR ===================== */
        .wiz-sidebar{background:#fff;border-right:1px solid var(--border-l);overflow-y:auto;display:flex;flex-direction:column;padding:20px 0}
        .wiz-steps{flex:1;padding:0 0 16px}
        .wiz-step{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:13px;font-weight:500;color:var(--text-3);cursor:pointer;position:relative;transition:all .15s;border-left:3px solid transparent}
        .wiz-step:hover{background:var(--bg);color:var(--text)}
        .wiz-step .num{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11.5px;font-weight:700;background:var(--bg);color:var(--text-3);border:1.5px solid var(--border);flex-shrink:0;transition:all .2s}
        .wiz-step .name{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;transition:color .15s}
        .wiz-step .check{width:20px;height:20px;flex-shrink:0;display:none;color:var(--green)}

        /* Active step */
        .wiz-step.active{border-left-color:var(--blue);background:var(--blue-bg)}
        .wiz-step.active .num{background:var(--blue);color:#fff;border-color:var(--blue)}
        .wiz-step.active .name{color:var(--text);font-weight:700}

        /* Completed step */
        .wiz-step.completed .num{display:none}
        .wiz-step.completed .check{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:#ecfdf5;border:1.5px solid #a7f3d0;color:#047857}
        .wiz-step.completed .check i{width:14px;height:14px}
        .wiz-step.completed .name{color:var(--text-2)}

        /* Step with validation errors */
        .wiz-step.has-error{border-left-color:var(--red)}
        .wiz-step.has-error .num{background:var(--red);color:#fff;border-color:var(--red)}

        /* Help card */
        .wiz-help{margin:0 16px;padding:16px;background:var(--bg);border-radius:12px;border:1px solid var(--border-l)}
        .wiz-help .title{font-size:12px;font-weight:700;color:var(--text);margin-bottom:4px}
        .wiz-help .desc{font-size:11.5px;color:var(--text-3);margin-bottom:10px;line-height:1.45}
        .wiz-help a{font-size:12px;font-weight:600;color:var(--blue);display:inline-flex;align-items:center;gap:4px;transition:opacity .15s}
        .wiz-help a:hover{opacity:.8}
        .wiz-help a i{width:13px;height:13px}

        /* ===================== CENTER CONTENT ===================== */
        .wiz-center{overflow-y:auto;background:#fff;padding:32px 40px;scrollbar-width:thin;scrollbar-color:var(--border) transparent}
        .wiz-center::-webkit-scrollbar{width:6px}
        .wiz-center::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px}

        /* ===================== RIGHT SIDEBAR (PREVIEW) ===================== */
        .wiz-preview{background:var(--bg);border-left:1px solid var(--border-l);overflow-y:auto;padding:24px 20px;scrollbar-width:thin;scrollbar-color:var(--border) transparent}
        .wiz-preview::-webkit-scrollbar{width:6px}
        .wiz-preview::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px}
        .wiz-preview-header{font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px}
        .wiz-preview-header i{width:16px;height:16px;color:var(--text-3)}

        /* ===================== BOTTOM BAR ===================== */
        .wiz-bottombar{background:#fff;border-top:1px solid var(--border-l);display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:20;box-shadow:0 -4px 16px rgba(0,0,0,.04)}
        .wiz-bottombar .btn{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:9px;font-size:13.5px;font-weight:600;border:1px solid transparent;cursor:pointer;transition:all .15s;line-height:1;white-space:nowrap}
        .wiz-bottombar .btn i{width:15px;height:15px}
        .wiz-bottombar .btn-prev{background:#fff;border-color:var(--border);color:var(--text)}
        .wiz-bottombar .btn-prev:hover{background:var(--bg)}
        .wiz-bottombar .btn-prev.hidden{visibility:hidden;pointer-events:none}
        .wiz-bottombar .btn-save{background:#fff;border-color:var(--border);color:var(--text)}
        .wiz-bottombar .btn-save:hover{background:var(--bg)}
        .wiz-bottombar .btn-next{background:var(--blue);color:#fff;border-color:var(--blue)}
        .wiz-bottombar .btn-next:hover{background:var(--blue-h);border-color:var(--blue-h)}

        /* ===================== SHARED COMPONENTS ===================== */
        .card{background:#fff;border:1px solid var(--border-l);border-radius:14px;padding:22px;margin-bottom:20px}
        .card h2{font-size:16px;font-weight:700;margin-bottom:14px;letter-spacing:-.2px}

        .field{margin-bottom:14px}
        .field label{display:block;font-size:11.5px;font-weight:600;color:var(--text-2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.3px}
        .field input,.field select,.field textarea{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:#fff;transition:border-color .15s, box-shadow .15s}
        .field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,102,255,.08)}
        .field textarea{min-height:90px;resize:vertical;font-family:inherit}
        .field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .field-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}

        .btn{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:9px;font-size:13px;font-weight:600;font-family:inherit;border:1px solid transparent;cursor:pointer;transition:all .15s;text-decoration:none;line-height:1;white-space:nowrap}
        .btn i{width:15px;height:15px}
        .btn-blue{background:var(--blue);color:#fff}
        .btn-blue:hover{background:var(--blue-h)}
        .btn-outline{background:#fff;border-color:var(--border);color:var(--text)}
        .btn-outline:hover{background:var(--bg)}
        .btn-sm{padding:7px 11px;font-size:12px}

        .badge-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
        .pill-green{background:#ecfdf5;color:#047857}
        .pill-red{background:#fef2f2;color:#b91c1c}
        .pill-gray{background:#f3f4f6;color:#555}
        .pill-blue{background:#eff6ff;color:#1e40af}
        .pill-yellow{background:#fffbeb;color:#b45309}

        /* Toasts */
        .toast-container{position:fixed;top:20px;right:20px;z-index:1000;display:flex;flex-direction:column;gap:10px;max-width:360px;pointer-events:none}
        .toast{background:#fff;border:1px solid var(--border);border-radius:10px;padding:12px 14px 12px 12px;display:flex;align-items:flex-start;gap:10px;font-size:13.5px;box-shadow:0 8px 24px rgba(0,0,0,.1);pointer-events:auto;animation:toast-in .25s}
        @keyframes toast-in{from{opacity:0;transform:translateX(100%)}to{opacity:1;transform:translateX(0)}}
        .toast.out{animation:toast-out .2s forwards}
        @keyframes toast-out{to{opacity:0;transform:translateX(100%)}}
        .toast .ic{flex-shrink:0;width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center}
        .toast .ic i{width:14px;height:14px}
        .toast.success .ic{background:#ecfdf5;color:#047857}
        .toast.error .ic{background:#fef2f2;color:#b91c1c}
        .toast.info .ic{background:var(--blue-bg);color:var(--blue)}
        .toast .tx{flex:1;min-width:0}
        .toast .tx .t{font-weight:600;margin-bottom:1px}
        .toast .tx .s{color:var(--text-3);font-size:12.5px}
        .toast .x{background:none;border:none;color:var(--text-3);padding:0;width:18px;height:18px;display:flex;align-items:center;justify-content:center;border-radius:4px}
        .toast .x:hover{background:var(--bg);color:var(--text)}
        .toast .x i{width:13px;height:13px}

        /* Lightbox */
        .lb-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:1100;display:none;align-items:center;justify-content:center}
        .lb-backdrop.open{display:flex;animation:fade-in .15s}
        @keyframes fade-in{from{opacity:0}to{opacity:1}}
        .lb-stage{position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:center}
        .lb-img{max-width:92%;max-height:86%;object-fit:contain;border-radius:6px;box-shadow:0 20px 60px rgba(0,0,0,.4);animation:lb-in .15s}
        @keyframes lb-in{from{opacity:0;transform:scale(.98)}to{opacity:1;transform:scale(1)}}
        .lb-caption{position:absolute;bottom:18px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.7);color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;max-width:80%;text-align:center;backdrop-filter:blur(6px)}
        .lb-count{position:absolute;top:18px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.55);color:#fff;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;backdrop-filter:blur(6px)}
        .lb-x,.lb-nav{position:absolute;background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.15);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s}
        .lb-x:hover,.lb-nav:hover{background:rgba(255,255,255,.2);transform:scale(1.05)}
        .lb-x{top:18px;right:18px}
        .lb-nav{top:50%;transform:translateY(-50%)}
        .lb-nav.prev{left:18px}
        .lb-nav.next{right:18px}
        .lb-nav i,.lb-x i{width:22px;height:22px}
        .lb-thumbs{position:absolute;bottom:70px;left:0;right:0;display:flex;gap:6px;justify-content:center;padding:0 20px;overflow-x:auto;scrollbar-width:none}
        .lb-thumbs::-webkit-scrollbar{display:none}
        .lb-thumbs img{width:56px;height:38px;object-fit:cover;border-radius:5px;opacity:.45;cursor:pointer;border:2px solid transparent;flex-shrink:0;transition:all .15s}
        .lb-thumbs img:hover{opacity:.85}
        .lb-thumbs img.active{opacity:1;border-color:#fff}
        .lb-trigger{cursor:zoom-in}

        /* ===================== RESPONSIVE ===================== */
        @media(max-width:1100px){
            .wiz-shell{grid-template-columns:240px 1fr}
            .wiz-preview{display:none}
        }
        @media(max-width:768px){
            .wiz-shell{grid-template-columns:56px 1fr}
            .wiz-sidebar{padding:12px 0}
            .wiz-step .name{display:none}
            .wiz-step .check i{width:12px;height:12px}
            .wiz-step{padding:10px 14px;justify-content:center;gap:0}
            .wiz-step .num{width:24px;height:24px;font-size:10px}
            .wiz-step .check{width:24px;height:24px}
            .wiz-help{display:none}
            .wiz-topbar-logo .tx{display:none}
            .wiz-topbar-logo{padding-right:12px;margin-right:0}
            .wiz-progress-label{display:none}
            .wiz-topbar-actions .btn-save-exit span{display:none}
            .wiz-center{padding:20px 16px}
            .wiz-bottombar{padding:0 12px}
            .wiz-bottombar .btn{padding:9px 12px;font-size:12px}
        }
        @media(max-width:480px){
            .wiz-shell{grid-template-columns:1fr;grid-template-rows:56px 1fr 56px}
            .wiz-sidebar{display:none}
        }

        @keyframes progress-pulse{
            0%,100%{opacity:1}
            50%{opacity:.85}
        }

        @yield('wizard-styles')
    </style>
</head>
<body>

{{-- P1 DATA-LOSS SAFETY — restore banner + session expiry warning.
     Hidden by default; the autosave JS module shows them when applicable. --}}
<div id="wizRestoreBanner" role="status" aria-live="polite"
     style="display:none;position:fixed;top:0;left:0;right:0;z-index:99999;background:#fef3c7;border-bottom:2px solid #f59e0b;padding:12px 16px;box-shadow:0 2px 12px rgba(0,0,0,.08);font-family:'Inter',system-ui,sans-serif">
    <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:14px;flex-wrap:wrap">
        <strong style="color:#92400e;font-size:14px;font-weight:700">Znaleziono niezapisane dane formularza.</strong>
        <span id="wizRestoreFilesHint" style="color:#92400e;font-size:13px;display:none;flex:1;min-width:240px"></span>
        <span style="flex:1"></span>
        <button type="button" id="wizRestoreAccept"
                style="background:#92400e;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer">Przywróć</button>
        <button type="button" id="wizRestoreDiscard"
                style="background:transparent;color:#92400e;border:1.5px solid #92400e;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">Odrzuć</button>
    </div>
</div>

<div id="wizSessionWarning" role="status" aria-live="polite"
     style="display:none;position:fixed;bottom:16px;right:16px;z-index:99999;background:#1f2937;color:#fff;padding:14px 18px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.25);font-size:13px;max-width:340px;font-family:'Inter',system-ui,sans-serif">
    <div style="font-weight:700;margin-bottom:4px" id="wizSessionWarningTitle">Sesja wkrótce wygaśnie</div>
    <div style="opacity:.85;line-height:1.5" id="wizSessionWarningText">Twoje dane są zapisywane lokalnie. Zaloguj się ponownie w nowej karcie, aby kontynuować bezpiecznie.</div>
</div>

<div class="wiz-shell">
    {{-- ==================== TOP BAR ==================== --}}
    <header class="wiz-topbar">
        <div class="wiz-topbar-logo">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/>
                <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="tx">Certi<span>Cars</span></span>
        </div>

        <div class="wiz-progress-wrap">
            <span class="wiz-progress-label">Postęp ogłoszenia</span>
            <div class="wiz-progress-track">
                <div class="wiz-progress-fill" id="wizProgressFill"></div>
            </div>
            <span class="wiz-progress-text" id="wizProgressText">9% (1 z 11)</span>
        </div>

        <div class="wiz-topbar-actions">
            <button type="button" class="btn-save-exit" id="wizSaveExit">
                <i data-lucide="save"></i>
                <span>Zapisz i wyjdź</span>
            </button>
            <a href="{{ route('admin.cars.index') }}" class="btn-close" title="Zamknij kreator">
                <i data-lucide="x"></i>
            </a>
        </div>
    </header>

    {{-- ==================== LEFT SIDEBAR — STEPS ==================== --}}
    <aside class="wiz-sidebar">
        <div class="wiz-steps" id="wizSteps">
            <div class="wiz-step active" data-step="1" onclick="goToStep(1)">
                <span class="num">1</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Dane podstawowe</span>
            </div>
            <div class="wiz-step" data-step="2" onclick="goToStep(2)">
                <span class="num">2</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Zdjęcia i media</span>
            </div>
            <div class="wiz-step" data-step="3" onclick="goToStep(3)">
                <span class="num">3</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Historia pojazdu</span>
            </div>
            <div class="wiz-step" data-step="4" onclick="goToStep(4)">
                <span class="num">4</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Serwisowanie</span>
            </div>
            <div class="wiz-step" data-step="5" onclick="goToStep(5)">
                <span class="num">5</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Dokumenty</span>
            </div>
            <div class="wiz-step" data-step="6" onclick="goToStep(6)">
                <span class="num">6</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Wyposażenie</span>
            </div>
            <div class="wiz-step" data-step="7" data-certicheck-only="1" onclick="goToStep(7)">
                <span class="num">7</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Stan wizualny</span>
            </div>
            <div class="wiz-step" data-step="8" onclick="goToStep(8)">
                <span class="num">8</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Stan techniczny</span>
            </div>
            <div class="wiz-step" data-step="9" data-certicheck-only="1" onclick="goToStep(9)">
                <span class="num">9</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Pomiary lakieru</span>
            </div>
            <div class="wiz-step" data-step="10" data-certicheck-only="1" onclick="goToStep(10)">
                <span class="num">10</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Opony i bieżnik</span>
            </div>
            <div class="wiz-step" data-step="11" onclick="goToStep(11)">
                <span class="num">11</span>
                <span class="check"><i data-lucide="check"></i></span>
                <span class="name">Podgląd i publikacja</span>
            </div>
        </div>


    </aside>

    {{-- ==================== CENTER CONTENT ==================== --}}
    <main class="wiz-center" id="wizCenter">
        @yield('wizard-form-open')
        @yield('wizard-content')
        @yield('wizard-form-close')
    </main>

    {{-- ==================== RIGHT SIDEBAR — PREVIEW ==================== --}}
    <aside class="wiz-preview">
        <div class="wiz-preview-header">
            <i data-lucide="eye"></i>
            Podgląd ogłoszenia
        </div>
        @yield('wizard-preview')
    </aside>

    {{-- ==================== BOTTOM BAR ==================== --}}
    <footer class="wiz-bottombar">
        <button type="button" class="btn btn-prev hidden" id="wizPrev" onclick="prevStep()">
            <i data-lucide="arrow-left"></i>
            Wstecz
        </button>

        <button type="button" class="btn btn-save" id="wizSave">
            <i data-lucide="save"></i>
            Zapisz
        </button>

        <button type="button" class="btn btn-next" id="wizNext" onclick="nextStep()">
            <span id="wizNextLabel">Dalej</span>
            <i data-lucide="arrow-right" id="wizNextIcon"></i>
        </button>
    </footer>
</div>

{{-- Toast container --}}
<div class="toast-container" id="toastContainer"></div>

{{-- Lightbox --}}
<div class="lb-backdrop" id="lbBackdrop">
    <div class="lb-stage">
        <div class="lb-count" id="lbCount"></div>
        <button type="button" class="lb-x" onclick="closeLb()" aria-label="Zamknij"><i data-lucide="x"></i></button>
        <button type="button" class="lb-nav prev" id="lbPrev" aria-label="Poprzednie"><i data-lucide="chevron-left"></i></button>
        <img class="lb-img" id="lbImg" alt="Powiększenie zdjęcia">
        <button type="button" class="lb-nav next" id="lbNext" aria-label="Następne"><i data-lucide="chevron-right"></i></button>
        <div class="lb-thumbs" id="lbThumbs"></div>
        <div class="lb-caption" id="lbCaption"></div>
    </div>
</div>

<script>
// ==== Toast system ====
window.toast=(msg,type='success',title=null)=>{
    const c=document.getElementById('toastContainer');
    const el=document.createElement('div');
    el.className='toast '+type;
    const icon=type==='success'?'check':type==='error'?'x':'info';
    el.innerHTML=`<div class="ic"><i data-lucide="${icon}"></i></div><div class="tx">${title?`<div class="t">${title}</div><div class="s">${msg}</div>`:`<div class="t">${msg}</div>`}</div><button class="x" aria-label="Zamknij"><i data-lucide="x"></i></button>`;
    c.appendChild(el);
    if(window.lucide)lucide.createIcons();
    const dismiss=()=>{el.classList.add('out');setTimeout(()=>el.remove(),200)};
    el.querySelector('.x').addEventListener('click',dismiss);
    setTimeout(dismiss,4500);
};

// ==== Flash messages to toasts ====
@if(session('success'))toast(@json(session('success')),'success');@endif
@if(session('warning'))toast(@json(session('warning')),'error','Częściowe zapisanie');@endif
@if(session('error'))toast(@json(session('error')),'error');@endif
@if($errors->any())toast(@json($errors->first()),'error','Błąd walidacji');@endif

// ==== Lightbox ====
const lb={bd:document.getElementById('lbBackdrop'),img:document.getElementById('lbImg'),cap:document.getElementById('lbCaption'),count:document.getElementById('lbCount'),thumbs:document.getElementById('lbThumbs'),items:[],idx:0};
function openLbAt(items,idx){lb.items=items;lb.idx=idx;renderLb();lb.bd.classList.add('open')}
function renderLb(){
    const it=lb.items[lb.idx];if(!it)return;
    lb.img.src=it.src;lb.img.alt=it.caption||'';
    lb.cap.textContent=it.caption||'';lb.cap.style.display=it.caption?'block':'none';
    lb.count.textContent=(lb.idx+1)+' / '+lb.items.length;
    lb.count.style.display=lb.items.length>1?'block':'none';
    document.getElementById('lbPrev').style.display=lb.items.length>1?'flex':'none';
    document.getElementById('lbNext').style.display=lb.items.length>1?'flex':'none';
    lb.thumbs.style.display=lb.items.length>1?'flex':'none';
    if(lb.items.length>1){
        lb.thumbs.innerHTML=lb.items.map((x,i)=>`<img src="${x.src}" data-i="${i}" class="${i===lb.idx?'active':''}" alt="">`).join('');
        lb.thumbs.querySelectorAll('img').forEach(t=>t.addEventListener('click',()=>{lb.idx=+t.dataset.i;renderLb()}));
        lb.thumbs.querySelector('img.active')?.scrollIntoView({inline:'center',block:'nearest',behavior:'smooth'});
    }
}
function lbNext(){if(lb.items.length<2)return;lb.idx=(lb.idx+1)%lb.items.length;renderLb()}
function lbPrev(){if(lb.items.length<2)return;lb.idx=(lb.idx-1+lb.items.length)%lb.items.length;renderLb()}
window.closeLb=()=>lb.bd.classList.remove('open');
document.getElementById('lbNext').addEventListener('click',lbNext);
document.getElementById('lbPrev').addEventListener('click',lbPrev);
lb.bd.addEventListener('click',e=>{if(e.target===lb.bd||e.target.classList.contains('lb-stage'))closeLb()});
document.addEventListener('keydown',e=>{
    if(!lb.bd.classList.contains('open'))return;
    if(e.key==='Escape'){e.preventDefault();closeLb()}
    if(e.key==='ArrowRight')lbNext();
    if(e.key==='ArrowLeft')lbPrev();
});
window.initLightbox=()=>{
    const all=[...document.querySelectorAll('[data-lightbox]')];
    all.forEach(el=>{if(el.dataset.lbBound)return;el.dataset.lbBound='1';el.classList.add('lb-trigger');
        el.addEventListener('click',e=>{
            if(e.target.tagName==='INPUT'||e.target.tagName==='BUTTON'||e.target.closest('button,input,label'))return;
            e.preventDefault();
            const g=el.dataset.gallery;
            const group=g?all.filter(x=>x.dataset.gallery===g):[el];
            const items=group.map(x=>({src:x.dataset.lightbox,caption:x.dataset.caption||''}));
            const idx=group.indexOf(el);
            openLbAt(items,Math.max(0,idx));
        });
    });
};

// ==== Wizard step navigation ====
let currentStep = 1;
const totalSteps = 11;
const completedSteps = new Set();

const stepNames = [
    '', 'Dane podstawowe', 'Zdjęcia i media', 'Historia pojazdu',
    'Serwisowanie', 'Dokumenty', 'Wyposażenie', 'Stan wizualny',
    'Stan techniczny', 'Pomiary lakieru', 'Opony i bieżnik', 'Podgląd i publikacja'
];

function goToStep(n) {
    if (n < 1 || n > totalSteps) return;
    currentStep = n;

    // Update sidebar steps
    document.querySelectorAll('.wiz-step').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.remove('active');
        if (s === currentStep) el.classList.add('active');
    });

    // Update Wstecz button visibility
    const prevBtn = document.getElementById('wizPrev');
    if (currentStep === 1) {
        prevBtn.classList.add('hidden');
    } else {
        prevBtn.classList.remove('hidden');
    }

    // Update Dalej / Opublikuj button
    const nextLabel = document.getElementById('wizNextLabel');
    const nextIcon = document.getElementById('wizNextIcon');
    if (currentStep === totalSteps) {
        nextLabel.textContent = 'Opublikuj';
        nextIcon.setAttribute('data-lucide', 'send');
    } else {
        nextLabel.textContent = 'Dalej';
        nextIcon.setAttribute('data-lucide', 'arrow-right');
    }

    updateProgress();

    // Scroll center content to top
    document.getElementById('wizCenter').scrollTop = 0;

    // Re-render Lucide icons
    if (window.lucide) lucide.createIcons();

    // Dispatch custom event for child views
    window.dispatchEvent(new CustomEvent('wizard:step-changed', { detail: { step: currentStep } }));
}

function nextStep() {
    if (currentStep < totalSteps) {
        goToStep(currentStep + 1);
    } else {
        // On last step, dispatch publish event
        window.dispatchEvent(new CustomEvent('wizard:publish'));
    }
}

function prevStep() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
}

function updateProgress() {
    const pct = Math.round((currentStep / totalSteps) * 100);
    const fill = document.getElementById('wizProgressFill');
    const text = document.getElementById('wizProgressText');
    fill.style.width = pct + '%';
    text.textContent = pct + '% (' + currentStep + ' z ' + totalSteps + ')';
}

function markStepComplete(n) {
    if (n < 1 || n > totalSteps) return;
    completedSteps.add(n);
    const stepEl = document.querySelector('.wiz-step[data-step="' + n + '"]');
    if (stepEl) {
        stepEl.classList.add('completed');
        if (window.lucide) lucide.createIcons();
    }
}

function markStepIncomplete(n) {
    if (n < 1 || n > totalSteps) return;
    completedSteps.delete(n);
    const stepEl = document.querySelector('.wiz-step[data-step="' + n + '"]');
    if (stepEl) {
        stepEl.classList.remove('completed');
    }
}

function isStepComplete(n) {
    return completedSteps.has(n);
}

// ==== Init ====
// Single shared "submitting" guard — prevents double-submit from any save trigger
// (Save button, Save+Exit button, wizard:publish event, keyboard Enter, etc.)
var wizSubmitting = false;
function wizSafeSubmit() {
    var form = document.getElementById('wizardForm');
    if (!form) return;
    if (wizSubmitting) return; // already in-flight → ignore further clicks/events
    wizSubmitting = true;
    var saveBtn = document.getElementById('wizSave');
    var saveExitBtn = document.getElementById('wizSaveExit');
    [saveBtn, saveExitBtn].forEach(function(b) {
        if (!b) return;
        b.dataset.origText = b.textContent;
        b.disabled = true;
        b.setAttribute('aria-busy', 'true');
        b.style.opacity = '0.7';
        b.style.cursor = 'wait';
        b.textContent = 'Zapisywanie…';
    });
    form.submit();
}
function wizResetSubmitState() {
    wizSubmitting = false;
    [document.getElementById('wizSave'), document.getElementById('wizSaveExit')].forEach(function(b) {
        if (!b) return;
        b.disabled = false;
        b.removeAttribute('aria-busy');
        b.style.opacity = '';
        b.style.cursor = '';
        if (b.dataset.origText) b.textContent = b.dataset.origText;
    });
}
// If the page is restored from bfcache (user hits Back after save), re-enable buttons
window.addEventListener('pageshow', function(e) { if (e.persisted) wizResetSubmitState(); });

window.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
    updateProgress();
    initLightbox();

    document.getElementById('wizSave')?.addEventListener('click', wizSafeSubmit);
    document.getElementById('wizSaveExit')?.addEventListener('click', wizSafeSubmit);

    wizInitAutosave();
    wizInitSessionWarning();
});

// On the last step "Dalej" dispatches wizard:publish — submit the form (guarded)
window.addEventListener('wizard:publish', wizSafeSubmit);

// ===================================================================
// P1 DATA-LOSS SAFETY NET — wizard autosave + restore + session warning
// Layer 1: persist non-file form values to localStorage on every input/change
// Layer 2: show session-expiry warning before cookie dies
// Layer 3: surface restore banner on form load if a draft exists
// Layer 4: hidden _diag input on submit for safe observability
// ===================================================================
(function(){
    // expose for tests + manual debugging
    window.WizardAutosave = { storageKey:null, opened_at:Date.now(), filenames:[] };
})();

function wizStorageKey() {
    return document.querySelector('meta[name="wizard-autosave-key"]')?.content || null;
}
function wizCarUpdatedAt() {
    const v = document.querySelector('meta[name="wizard-car-updated-at"]')?.content || '';
    const n = parseInt(v, 10);
    return isNaN(n) ? 0 : n * 1000;
}
function wizSessionExpiresAtMs() {
    const v = document.querySelector('meta[name="session-expires-at"]')?.content || '';
    const n = parseInt(v, 10);
    return isNaN(n) ? 0 : n * 1000;
}
function wizForm() { return document.getElementById('wizardForm'); }

function wizSerializeForm() {
    const form = wizForm();
    if (!form) return null;
    const SKIP_NAMES = new Set(['_token', '_ts', 'website', 'password', 'password_confirmation', '_diag']);
    const out = {};
    const filenames = [];
    form.querySelectorAll('input,select,textarea').forEach(function(el){
        if (!el.name) return;
        if (SKIP_NAMES.has(el.name)) return;
        if (el.type === 'password') return;
        if (el.type === 'file') {
            // can't restore <input type=file>, but record filenames for the hint.
            // FileList does NOT implement .forEach — must convert via Array.from.
            // Pre-fix bug crashed the autosave whenever any file input had files.
            Array.from(el.files || []).forEach(function(f){
                filenames.push({ name: f.name, size: f.size, field: el.name });
            });
            return;
        }
        if (el.type === 'checkbox' || el.type === 'radio') {
            if (el.checked) {
                out[el.name] = (out[el.name] === undefined) ? el.value : (Array.isArray(out[el.name]) ? out[el.name].concat([el.value]) : [out[el.name], el.value]);
            }
            return;
        }
        let v = el.value;
        if (typeof v === 'string' && v.length > 4096) v = v.slice(0, 4096); // per-field cap
        if (el.name.endsWith('[]')) {
            (out[el.name] = out[el.name] || []).push(v);
        } else {
            out[el.name] = v;
        }
    });
    return { values: out, filenames: filenames, saved_at: Date.now() };
}

function wizPersistDraft() {
    const key = wizStorageKey();
    if (!key) return;
    try {
        const payload = wizSerializeForm();
        if (!payload) return;
        const json = JSON.stringify(payload);
        if (json.length > 1024 * 1024) {
            // exceeds 1 MB cap — drop large textareas defensively and retry once
            Object.keys(payload.values).forEach(function(k){
                if (typeof payload.values[k] === 'string' && payload.values[k].length > 2000) payload.values[k] = payload.values[k].slice(0, 2000);
            });
            localStorage.setItem(key, JSON.stringify(payload));
        } else {
            localStorage.setItem(key, json);
        }
        window.WizardAutosave.filenames = payload.filenames;
    } catch (e) {
        // quota or serialization failure — silently drop; restore banner is best-effort
        try { localStorage.removeItem(key); } catch(_) {}
    }
}

function wizLoadDraft() {
    const key = wizStorageKey();
    if (!key) return null;
    try {
        const raw = localStorage.getItem(key);
        if (!raw) return null;
        const payload = JSON.parse(raw);
        if (!payload || typeof payload !== 'object') return null;
        return payload;
    } catch (e) { return null; }
}

function wizClearDraft() {
    const key = wizStorageKey();
    if (!key) return;
    try { localStorage.removeItem(key); } catch (e) {}
}

function wizApplyDraft(payload) {
    const form = wizForm();
    if (!form || !payload || !payload.values) return;
    Object.keys(payload.values).forEach(function(name){
        const value = payload.values[name];
        const els = form.querySelectorAll('[name="' + CSS.escape(name) + '"]');
        if (!els.length) return;
        if (els[0].type === 'checkbox' || els[0].type === 'radio') {
            const wanted = Array.isArray(value) ? value : [value];
            els.forEach(function(el){ el.checked = wanted.indexOf(el.value) !== -1; });
        } else if (els.length === 1) {
            els[0].value = value;
            els[0].dispatchEvent(new Event('change', { bubbles: true }));
        } else {
            // array-named fields like equipment[Komfort] — fill in order
            const arr = Array.isArray(value) ? value : [value];
            els.forEach(function(el, i){ if (arr[i] !== undefined) el.value = arr[i]; });
        }
    });
}

function wizFormatFileHint(filenames) {
    if (!filenames || !filenames.length) return '';
    const first = filenames.slice(0, 3).map(function(f){ return f.name; });
    const more = filenames.length - first.length;
    return 'Do ponownego dodania: ' + first.join(', ') + (more > 0 ? ' (i ' + more + ' więcej)' : '') + '.';
}

function wizInitAutosave() {
    const form = wizForm();
    if (!form) return;
    const key = wizStorageKey();
    if (!key) return;

    // restore banner
    const draft = wizLoadDraft();
    const carUpdated = wizCarUpdatedAt();
    const sessionExpired = document.querySelector('meta[name="wizard-session-expired"]')?.content === '1';
    if (draft && (sessionExpired || draft.saved_at > carUpdated)) {
        const banner = document.getElementById('wizRestoreBanner');
        const filesHint = document.getElementById('wizRestoreFilesHint');
        if (banner) {
            const hint = wizFormatFileHint(draft.filenames || []);
            if (filesHint && hint) { filesHint.textContent = hint; filesHint.style.display = ''; }
            banner.style.display = '';
        }
        document.getElementById('wizRestoreAccept')?.addEventListener('click', function(){
            wizApplyDraft(draft);
            const b = document.getElementById('wizRestoreBanner'); if (b) b.style.display = 'none';
            window.WizardAutosave.had_draft = true;
        }, { once: true });
        document.getElementById('wizRestoreDiscard')?.addEventListener('click', function(){
            wizClearDraft();
            const b = document.getElementById('wizRestoreBanner'); if (b) b.style.display = 'none';
        }, { once: true });
    }

    // persist on input/change (debounced) + heartbeat every 5s
    let t = null;
    const schedule = function(){ clearTimeout(t); t = setTimeout(wizPersistDraft, 300); };
    form.addEventListener('input', schedule);
    form.addEventListener('change', schedule);
    setInterval(wizPersistDraft, 5000);

    // clear draft on confirmed success — read flash via meta added by edit response
    // (we infer success: when the page rendered the edit view AND no session_expired marker AND no validation errors).
    const hasErrors = !!form.querySelector('.is-invalid, [data-validation-error]') || (document.querySelector('[data-flash="error"]'));
    const isCreate = key.endsWith(':create');
    if (!isCreate && !sessionExpired && !hasErrors) {
        // we're on the edit page after a save → clear
        wizClearDraft();
    }

    // add hidden _diag input right before submit
    const beforeSubmit = function(){
        const draftExists = !!wizLoadDraft();
        let diagInput = form.querySelector('input[name="_diag"]');
        if (!diagInput) {
            diagInput = document.createElement('input');
            diagInput.type = 'hidden'; diagInput.name = '_diag';
            form.appendChild(diagInput);
        }
        // figure out which button triggered
        const lastBtn = window._wizLastSubmitButton || null;
        let fileCount = 0, fileTotal = 0;
        form.querySelectorAll('input[type="file"]').forEach(function(input){
            // FileList does NOT implement .forEach — convert via Array.from first.
            // Pre-fix bug threw TypeError here when any file input had files,
            // breaking the _diag hidden input + crashing autosave persistence.
            Array.from(input.files || []).forEach(function(f){ fileCount++; fileTotal += f.size; });
        });
        diagInput.value = JSON.stringify({
            opened_at: Math.floor(window.WizardAutosave.opened_at / 1000),
            elapsed_sec: Math.floor((Date.now() - window.WizardAutosave.opened_at) / 1000),
            field_count: form.querySelectorAll('input,select,textarea').length,
            file_count: fileCount,
            file_total_bytes: fileTotal,
            button: lastBtn,
            step: parseInt(form.querySelector('input[name="active_tab"]')?.value || '0', 10) || null,
            had_draft: draftExists,
        });
    };
    form.addEventListener('submit', beforeSubmit, true);
    document.getElementById('wizSave')?.addEventListener('click', function(){ window._wizLastSubmitButton = 'save'; });
    document.getElementById('wizSaveExit')?.addEventListener('click', function(){ window._wizLastSubmitButton = 'save_exit'; });
}

function wizInitSessionWarning() {
    const expiresAt = wizSessionExpiresAtMs();
    if (!expiresAt) return;
    const warn = document.getElementById('wizSessionWarning');
    const title = document.getElementById('wizSessionWarningTitle');
    const text = document.getElementById('wizSessionWarningText');
    if (!warn) return;

    function check() {
        const msLeft = expiresAt - Date.now();
        if (msLeft <= 0) {
            title.textContent = 'Sesja wygasła';
            text.textContent = 'Twoje dane są zapisywane lokalnie. Otwórz panel w nowej karcie i zaloguj się ponownie przed zapisem.';
            warn.style.background = '#7f1d1d';
            warn.style.display = '';
        } else if (msLeft < 60 * 1000) {
            title.textContent = 'Sesja wygaśnie za <1 min';
            text.textContent = 'Zapisz teraz lub otwórz panel w nowej karcie i zaloguj się ponownie.';
            warn.style.background = '#7f1d1d';
            warn.style.display = '';
        } else if (msLeft < 10 * 60 * 1000) {
            const min = Math.ceil(msLeft / 60000);
            title.textContent = 'Sesja wygaśnie za ~' + min + ' min';
            text.textContent = 'Twoje dane są zapisywane lokalnie. Możesz odświeżyć sesję w nowej karcie.';
            warn.style.background = '#1f2937';
            warn.style.display = '';
        } else {
            warn.style.display = 'none';
        }
    }
    check();
    setInterval(check, 30 * 1000);

    // before submit, if we're past T-30s, soft-warn unless user has already overridden
    document.addEventListener('click', function(e){
        if (!e.target.closest('#wizSave, #wizSaveExit')) return;
        const msLeft = expiresAt - Date.now();
        if (msLeft > 30 * 1000) return; // plenty of time, no warning
        if (window._wizSessionWarningAck) return;
        e.preventDefault(); e.stopPropagation();
        if (confirm('Sesja mogła wygasnąć. Twoje dane są zapisane lokalnie. Otwórz panel w nowej karcie i zaloguj się ponownie, a następnie wróć tutaj.\n\nNacisnij OK, aby spróbować zapisać mimo to.')) {
            window._wizSessionWarningAck = true;
            // re-fire the click without intercept
            e.target.click();
        }
    }, true);
}
</script>
@yield('wizard-scripts')
@stack('scripts')
</body>
</html>
