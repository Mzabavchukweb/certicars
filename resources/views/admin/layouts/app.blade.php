<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Panel admina') — CertiCars</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--blue:#0066ff;--blue-h:#0052cc;--blue-bg:#e8f1ff;--text:#0a0a0a;--text-2:#555;--text-3:#868686;--text-4:#b0b0b0;--bg:#f7f7f8;--border:#e5e5e7;--border-l:#eeeef0;--green:#10b981;--yellow:#f59e0b;--red:#ef4444}
        body{font-family:'Inter',system-ui,sans-serif;color:var(--text);background:var(--bg);font-size:14px;line-height:1.5;-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}
        button{font-family:inherit;cursor:pointer}
        input,select,textarea{font-family:inherit}
        :focus-visible{outline:2px solid var(--blue);outline-offset:2px;border-radius:4px}

        .ad-wrap{display:grid;grid-template-columns:240px 1fr;min-height:100vh}
        .ad-side{background:#0a0a0a;color:#fff;padding:24px 0;position:sticky;top:0;height:100vh;overflow-y:auto;display:flex;flex-direction:column}
        .ad-side-logo{display:flex;align-items:center;gap:10px;padding:0 22px 22px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:16px}
        .ad-side-logo .ic{width:34px;height:34px;background:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff}
        .ad-side-logo .ic i{width:18px;height:18px;stroke-width:2.5}
        .ad-side-logo .tx{font-size:17px;font-weight:800;letter-spacing:-.4px;color:#fff}
        .ad-side-logo .tx span{color:var(--blue)}
        .ad-nav{flex:1}
        .ad-nav a{display:flex;align-items:center;gap:11px;padding:11px 22px;font-size:13.5px;color:rgba(255,255,255,.65);font-weight:500;transition:all .15s;position:relative}
        .ad-nav a:hover{color:#fff;background:rgba(255,255,255,.04)}
        .ad-nav a.active{color:#fff;background:rgba(0,102,255,.15);border-right:2px solid var(--blue)}
        .ad-nav a i{width:17px;height:17px;stroke-width:2}
        .ad-nav a .badge{margin-left:auto;background:var(--blue);color:#fff;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:9px;min-width:18px;text-align:center}
        .ad-nav .section-lbl{padding:16px 22px 6px;font-size:10.5px;color:rgba(255,255,255,.35);text-transform:uppercase;font-weight:600;letter-spacing:.5px}
        .ad-nav .kbd{margin-left:auto;background:rgba(255,255,255,.06);font-size:10px;padding:2px 6px;border-radius:4px;font-family:ui-monospace,monospace;color:rgba(255,255,255,.5)}
        .ad-side-bottom{padding:16px 22px;border-top:1px solid rgba(255,255,255,.08)}
        .ad-user{display:flex;align-items:center;gap:10px;font-size:13px;color:rgba(255,255,255,.7);margin-bottom:12px}
        .ad-user .av{width:34px;height:34px;background:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px}
        .ad-user-info{flex:1;min-width:0}
        .ad-user-info .n{color:#fff;font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .ad-user-info .e{font-size:11px;color:rgba(255,255,255,.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .ad-logout{display:flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.55);background:none;border:none;padding:6px 0;width:100%}
        .ad-logout:hover{color:#fff}
        .ad-logout i{width:14px;height:14px}

        .ad-main{min-width:0;overflow-x:auto}
        .ad-topbar{background:#fff;border-bottom:1px solid var(--border-l);padding:14px 28px;display:flex;align-items:center;gap:18px;position:sticky;top:0;z-index:10}
        .ad-topbar-left{display:flex;align-items:center;gap:12px;flex:1;min-width:0}
        .ad-topbar h1{font-size:19px;font-weight:800;letter-spacing:-.4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .ad-topbar .crumbs{font-size:12px;color:var(--text-3);margin-top:2px}
        .ad-topbar .crumbs a{color:var(--text-2)}
        .ad-topbar .crumbs a:hover{color:var(--blue)}
        .ad-topbar-actions{display:flex;gap:10px;align-items:center;flex-shrink:0}
        .ad-hamburger{display:none;background:none;border:1px solid var(--border);border-radius:9px;padding:8px;color:var(--text)}
        .ad-hamburger i{width:18px;height:18px}
        .ad-content{padding:26px 28px;padding-bottom:96px}

        /* Command palette trigger */
        .ad-cmd-btn{display:flex;align-items:center;gap:8px;padding:9px 12px 9px 14px;background:var(--bg);border:1px solid var(--border);border-radius:9px;font-size:13px;color:var(--text-3);min-width:280px;transition:all .15s}
        .ad-cmd-btn:hover{background:#fff;border-color:var(--blue);color:var(--text)}
        .ad-cmd-btn .ic{width:15px;height:15px}
        .ad-cmd-btn .kbd{margin-left:auto;display:flex;gap:3px}
        .ad-cmd-btn .kbd span{background:#fff;border:1px solid var(--border);border-radius:4px;padding:2px 6px;font-size:10.5px;font-family:ui-monospace,monospace;color:var(--text-2);font-weight:600}

        .card{background:#fff;border:1px solid var(--border-l);border-radius:14px;padding:22px;margin-bottom:20px}
        .card h2{font-size:16px;font-weight:700;margin-bottom:14px;letter-spacing:-.2px}

        .btn{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:9px;font-size:13px;font-weight:600;font-family:inherit;border:1px solid transparent;cursor:pointer;transition:all .15s;text-decoration:none;line-height:1;white-space:nowrap;position:relative}
        .btn i{width:15px;height:15px}
        .btn[disabled],.btn.is-loading{opacity:.7;cursor:not-allowed;pointer-events:none}
        .btn.is-loading i{visibility:hidden}
        .btn.is-loading::after{content:'';position:absolute;left:50%;top:50%;width:14px;height:14px;border:2px solid currentColor;border-right-color:transparent;border-radius:50%;animation:spin .6s linear infinite;margin:-7px 0 0 -7px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .btn-blue{background:var(--blue);color:#fff}
        .btn-blue:hover{background:var(--blue-h)}
        .btn-dark{background:#0a0a0a;color:#fff}
        .btn-dark:hover{background:#1a1a1a}
        .btn-outline{background:#fff;border-color:var(--border);color:var(--text)}
        .btn-outline:hover{background:var(--bg)}
        .btn-red{background:var(--red);color:#fff}
        .btn-red:hover{background:#dc2626}
        .btn-ghost-red{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
        .btn-ghost-red:hover{background:#fee2e2}
        .btn-sm{padding:7px 11px;font-size:12px}

        .field{margin-bottom:14px}
        .field label{display:block;font-size:11.5px;font-weight:600;color:var(--text-2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.3px}
        .field input,.field select,.field textarea{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:#fff;transition:border-color .15s, box-shadow .15s}
        .field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,102,255,.08)}
        .field textarea{min-height:90px;resize:vertical;font-family:inherit}
        .field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .field-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}

        table.data-table{width:100%;border-collapse:collapse;font-size:13px}
        .data-table th{text-align:left;padding:12px 14px;background:var(--bg);font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.3px;color:var(--text-2);border-bottom:1px solid var(--border-l)}
        .data-table th a{display:inline-flex;align-items:center;gap:4px;color:inherit}
        .data-table th a:hover{color:var(--text)}
        .data-table th a i{width:11px;height:11px;opacity:.5}
        .data-table td{padding:14px;border-bottom:1px solid var(--border-l);vertical-align:middle}
        .data-table tr{transition:background .12s}
        .data-table tr:hover td{background:#fafafb}
        .data-table tr.selected td{background:#eff6ff}
        .data-table img.thumb{width:60px;height:40px;object-fit:cover;border-radius:6px}
        .data-table input[type=checkbox]{width:16px;height:16px;accent-color:var(--blue);cursor:pointer}

        .badge-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;transition:all .15s}
        .pill-green{background:#ecfdf5;color:#047857}
        .pill-red{background:#fef2f2;color:#b91c1c}
        .pill-gray{background:#f3f4f6;color:#555}
        .pill-blue{background:#eff6ff;color:#1e40af}
        .pill-yellow{background:#fffbeb;color:#b45309}

        .stat{background:#fff;border:1px solid var(--border-l);border-radius:12px;padding:20px;position:relative;overflow:hidden;transition:transform .15s, box-shadow .15s}
        .stat:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.04)}
        .stat-label{font-size:12px;color:var(--text-3);text-transform:uppercase;font-weight:600;letter-spacing:.3px;margin-bottom:6px}
        .stat-value{font-size:26px;font-weight:800;color:var(--text);letter-spacing:-.6px}
        .stat-sub{font-size:11.5px;color:var(--text-3);margin-top:4px}
        .stat-ico{position:absolute;top:16px;right:16px;width:38px;height:38px;border-radius:10px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center;color:var(--blue)}
        .stat-ico i{width:18px;height:18px}

        /* Filter chips */
        .filter-chips{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px}
        .filter-chip{display:inline-flex;align-items:center;gap:6px;background:var(--blue-bg);color:var(--blue);font-size:12px;font-weight:600;padding:5px 6px 5px 10px;border-radius:8px}
        .filter-chip .k{color:var(--text-3);font-weight:500;font-size:11px;text-transform:uppercase;letter-spacing:.3px}
        .filter-chip a{display:flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:4px;color:var(--blue);opacity:.7}
        .filter-chip a:hover{background:rgba(0,102,255,.15);opacity:1}
        .filter-chip i{width:11px;height:11px}
        .filter-chip-clear{color:var(--text-3);font-size:12px;font-weight:600;padding:5px 10px;border-radius:8px;text-decoration:none}
        .filter-chip-clear:hover{color:var(--red);background:#fef2f2}

        .bulk-bar{background:#111;color:#fff;padding:10px 16px;border-radius:10px;display:none;align-items:center;gap:10px;margin-bottom:14px;font-size:13px;box-shadow:0 4px 14px rgba(0,0,0,.08)}
        .bulk-bar.active{display:flex;animation:slide-in .2s}
        @keyframes slide-in{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
        .bulk-bar .count{font-weight:700}
        .bulk-bar .sep{color:rgba(255,255,255,.3)}
        .bulk-bar button{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;padding:6px 11px;border-radius:7px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:all .15s}
        .bulk-bar button:hover{background:rgba(255,255,255,.18)}
        .bulk-bar button i{width:13px;height:13px}
        .bulk-bar .close{margin-left:auto;background:none;border:none;opacity:.6}
        .bulk-bar .close:hover{opacity:1}

        /* Empty state */
        .empty-state{text-align:center;padding:60px 20px;color:var(--text-3)}
        .empty-state .ic{width:60px;height:60px;margin:0 auto 14px;background:var(--bg);border-radius:50%;display:flex;align-items:center;justify-content:center}
        .empty-state .ic i{width:26px;height:26px;color:var(--text-4)}
        .empty-state h3{font-size:15.5px;font-weight:700;color:var(--text);margin-bottom:4px}
        .empty-state p{font-size:13px;margin-bottom:14px}

        /* Sticky save bar */
        .sticky-save{position:fixed;bottom:0;left:240px;right:0;background:#fff;border-top:1px solid var(--border);padding:14px 28px;display:none;align-items:center;justify-content:flex-end;gap:12px;z-index:20;box-shadow:0 -4px 16px rgba(0,0,0,.04)}
        .sticky-save.show{display:flex;animation:slide-up .2s}
        @keyframes slide-up{from{transform:translateY(100%)}to{transform:translateY(0)}}
        .sticky-save .hint{margin-right:auto;font-size:12.5px;color:var(--text-3);display:flex;align-items:center;gap:6px}
        .sticky-save .hint i{width:14px;height:14px;color:var(--yellow)}

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

        /* Modal */
        .modal-backdrop{position:fixed;inset:0;background:rgba(10,10,10,.55);z-index:900;display:none;align-items:center;justify-content:center;padding:20px}
        .modal-backdrop.open{display:flex;animation:fade-in .15s}
        @keyframes fade-in{from{opacity:0}to{opacity:1}}
        .modal{background:#fff;border-radius:14px;padding:24px;max-width:420px;width:100%;box-shadow:0 20px 40px rgba(0,0,0,.15);animation:modal-in .2s}
        @keyframes modal-in{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
        .modal .ic{width:44px;height:44px;background:#fef2f2;color:#b91c1c;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px}
        .modal .ic i{width:22px;height:22px}
        .modal h3{font-size:17px;font-weight:700;margin-bottom:6px;letter-spacing:-.3px}
        .modal p{font-size:13.5px;color:var(--text-2);margin-bottom:18px;line-height:1.5}
        .modal-actions{display:flex;gap:8px;justify-content:flex-end}

        /* Command palette */
        .cmdk-backdrop{position:fixed;inset:0;background:rgba(10,10,10,.4);z-index:950;display:none;align-items:flex-start;justify-content:center;padding:90px 20px 20px}
        .cmdk-backdrop.open{display:flex;animation:fade-in .12s}
        .cmdk{background:#fff;border-radius:14px;width:100%;max-width:560px;box-shadow:0 24px 48px rgba(0,0,0,.2);overflow:hidden;animation:modal-in .15s;display:flex;flex-direction:column;max-height:70vh}
        .cmdk-search{display:flex;align-items:center;gap:10px;padding:14px 18px;border-bottom:1px solid var(--border-l)}
        .cmdk-search i{width:17px;height:17px;color:var(--text-3)}
        .cmdk-search input{flex:1;border:none;outline:none;font-size:15px;font-weight:500}
        .cmdk-search .esc{font-size:10.5px;font-family:ui-monospace,monospace;padding:3px 8px;border:1px solid var(--border);border-radius:5px;color:var(--text-3);font-weight:600}
        .cmdk-list{overflow-y:auto;padding:8px 0;flex:1}
        .cmdk-group{padding:6px 0}
        .cmdk-group-lbl{padding:6px 18px 4px;font-size:10.5px;color:var(--text-3);font-weight:700;text-transform:uppercase;letter-spacing:.4px}
        .cmdk-item{display:flex;align-items:center;gap:12px;padding:9px 18px;font-size:13.5px;cursor:pointer;border-left:2px solid transparent}
        .cmdk-item.active{background:var(--bg);border-left-color:var(--blue)}
        .cmdk-item i.ic{width:16px;height:16px;color:var(--text-3)}
        .cmdk-item img{width:36px;height:26px;object-fit:cover;border-radius:4px}
        .cmdk-item .tx{flex:1;min-width:0}
        .cmdk-item .tx .t{font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .cmdk-item .tx .s{font-size:11.5px;color:var(--text-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .cmdk-item .kbd{font-size:10.5px;font-family:ui-monospace,monospace;padding:3px 7px;border:1px solid var(--border);border-radius:5px;color:var(--text-3);font-weight:600}
        .cmdk-footer{border-top:1px solid var(--border-l);padding:8px 18px;display:flex;gap:16px;font-size:11.5px;color:var(--text-3)}
        .cmdk-footer .k{background:var(--bg);padding:2px 6px;border-radius:4px;font-family:ui-monospace,monospace;font-weight:600;color:var(--text-2);margin-right:4px}

        /* Lightbox */
        .lb-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:1100;display:none;align-items:center;justify-content:center}
        .lb-backdrop.open{display:flex;animation:fade-in .15s}
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

        /* Shortcuts modal */
        .shortcut-row{display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border-l);font-size:13px}
        .shortcut-row:last-child{border:none}
        .shortcut-row .keys{display:flex;gap:4px}
        .shortcut-row .keys span{background:var(--bg);border:1px solid var(--border);border-radius:5px;padding:2px 7px;font-size:11px;font-family:ui-monospace,monospace;font-weight:600}

        .ad-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:40}
        .ad-backdrop.active{display:block}

        /* Mobile card view (used by lists) */
        .m-card{display:none}

        @media(max-width:900px){
            .ad-wrap{grid-template-columns:1fr}
            .ad-side{position:fixed;top:0;left:-260px;width:240px;transition:left .25s;z-index:50}
            .ad-side.open{left:0}
            .ad-topbar{padding:12px 16px;gap:10px}
            .ad-topbar h1{font-size:16px}
            .ad-content{padding:18px 16px;padding-bottom:96px}
            .ad-hamburger{display:flex;align-items:center}
            .ad-cmd-btn{min-width:auto;padding:8px 10px}
            .ad-cmd-btn span:not(.k){display:none}
            .ad-cmd-btn .kbd{display:none}
            .sticky-save{left:0}
            table.data-table.responsive{display:none}
            .m-card{display:block}
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="ad-backdrop" id="adBackdrop"></div>
<div class="ad-wrap">
    <aside class="ad-side" id="adSide">
        <div class="ad-side-logo">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" fill="#0066ff"/>
                <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="tx">Certi<span>Cars</span></span>
        </div>
        <nav class="ad-nav">
            <div class="section-lbl">Przegląd</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard')?'active':'' }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
            <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*')?'active':'' }}">
                <i data-lucide="inbox"></i> Wiadomości
                @if(($unreadMessagesCount ?? 0) > 0)<span class="badge">{{ $unreadMessagesCount }}</span>@endif
            </a>
            @php $unreadInquiries = App\Models\Inquiry::unread()->count(); @endphp
            <a href="{{ route('admin.inquiries.index') }}" class="{{ request()->routeIs('admin.inquiries.*')?'active':'' }}">
                <i data-lucide="mail-question"></i> Zapytania
                @if($unreadInquiries > 0)<span class="badge">{{ $unreadInquiries }}</span>@endif
            </a>
            <div class="section-lbl">Zarządzanie</div>
            <a href="{{ route('admin.cars.index') }}" class="{{ request()->routeIs('admin.cars.*')?'active':'' }}"><i data-lucide="car"></i> Samochody</a>
            <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*')?'active':'' }}"><i data-lucide="tag"></i> Marki</a>
            <div class="section-lbl">Konto</div>
            <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*')?'active':'' }}"><i data-lucide="user"></i> Profil</a>
            <a href="#" onclick="event.preventDefault();openShortcuts()"><i data-lucide="keyboard"></i> Skróty <span class="kbd">?</span></a>
            <div class="section-lbl">Publiczne</div>
            <a href="{{ route('home') }}" target="_blank"><i data-lucide="external-link"></i> Strona główna</a>
            <a href="{{ route('catalog') }}" target="_blank"><i data-lucide="external-link"></i> Oferta</a>
        </nav>
        <div class="ad-side-bottom">
            <div class="ad-user">
                <span class="av">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</span>
                <div class="ad-user-info">
                    <div class="n">{{ auth()->user()?->name ?? 'Admin' }}</div>
                    <div class="e">{{ auth()->user()?->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="ad-logout"><i data-lucide="log-out"></i> Wyloguj się</button>
            </form>
        </div>
    </aside>

    <main class="ad-main">
        <div class="ad-topbar">
            <button class="ad-hamburger" id="adHamburger" aria-label="Menu"><i data-lucide="menu"></i></button>
            <div class="ad-topbar-left">
                <div>
                    <h1>@yield('title','Dashboard')</h1>
                    @hasSection('crumbs')<div class="crumbs">@yield('crumbs')</div>@endif
                </div>
            </div>
            <button class="ad-cmd-btn" onclick="openCmdk()" title="Otwórz menu szybkich akcji">
                <i data-lucide="search" class="ic"></i>
                <span>Szukaj auta, stronę...</span>
                <span class="kbd"><span>⌘</span><span>K</span></span>
            </button>
            <div class="ad-topbar-actions">@yield('actions')</div>
        </div>
        <div class="ad-content">
            @yield('content')
        </div>
    </main>
</div>

<!-- Toast container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Lightbox -->
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

<!-- Confirm modal -->
<div class="modal-backdrop" id="confirmModal">
    <div class="modal" role="dialog">
        <div class="ic"><i data-lucide="alert-triangle"></i></div>
        <h3 id="confirmTitle">Na pewno?</h3>
        <p id="confirmText">Ta akcja jest nieodwracalna.</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" onclick="closeConfirm()">Anuluj</button>
            <button type="button" class="btn btn-red" id="confirmOk">Potwierdź</button>
        </div>
    </div>
</div>

<!-- Shortcuts modal -->
<div class="modal-backdrop" id="shortcutsModal">
    <div class="modal" role="dialog" style="max-width:440px">
        <div class="ic" style="background:var(--blue-bg);color:var(--blue)"><i data-lucide="keyboard"></i></div>
        <h3>Skróty klawiszowe</h3>
        <p>Poruszaj się po panelu bez dotykania myszki.</p>
        <div>
            <div class="shortcut-row"><span>Otwórz menu szybkich akcji</span><span class="keys"><span>⌘</span><span>K</span></span></div>
            <div class="shortcut-row"><span>Skup się na wyszukiwaniu</span><span class="keys"><span>/</span></span></div>
            <div class="shortcut-row"><span>Dashboard</span><span class="keys"><span>G</span><span>D</span></span></div>
            <div class="shortcut-row"><span>Samochody</span><span class="keys"><span>G</span><span>C</span></span></div>
            <div class="shortcut-row"><span>Wiadomości</span><span class="keys"><span>G</span><span>M</span></span></div>
            <div class="shortcut-row"><span>Marki</span><span class="keys"><span>G</span><span>B</span></span></div>
            <div class="shortcut-row"><span>Nowy samochód</span><span class="keys"><span>N</span></span></div>
            <div class="shortcut-row"><span>Pokaż ten panel</span><span class="keys"><span>?</span></span></div>
            <div class="shortcut-row"><span>Zamknij okno dialogowe</span><span class="keys"><span>Esc</span></span></div>
        </div>
        <div class="modal-actions" style="margin-top:16px">
            <button type="button" class="btn btn-blue" onclick="closeShortcuts()">Rozumiem</button>
        </div>
    </div>
</div>

<!-- Command palette -->
<div class="cmdk-backdrop" id="cmdkBackdrop">
    <div class="cmdk" role="dialog">
        <div class="cmdk-search">
            <i data-lucide="search"></i>
            <input type="text" id="cmdkInput" placeholder="Wpisz polecenie lub szukaj auta..." autocomplete="off">
            <span class="esc">esc</span>
        </div>
        <div class="cmdk-list" id="cmdkList"></div>
        <div class="cmdk-footer">
            <span><span class="k">↑↓</span>nawigacja</span>
            <span><span class="k">↵</span>wybór</span>
            <span><span class="k">esc</span>zamknij</span>
        </div>
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

// ==== Custom confirm ====
let confirmResolver=null;
window.confirmAction=(title,text,okLabel='Potwierdź')=>{
    return new Promise(resolve=>{
        confirmResolver=resolve;
        document.getElementById('confirmTitle').textContent=title;
        document.getElementById('confirmText').textContent=text;
        const ok=document.getElementById('confirmOk');
        ok.textContent=okLabel;
        document.getElementById('confirmModal').classList.add('open');
        if(window.lucide)lucide.createIcons();
        ok.focus();
    });
};
window.closeConfirm=()=>{document.getElementById('confirmModal').classList.remove('open');confirmResolver?.(false);confirmResolver=null};
document.getElementById('confirmOk').addEventListener('click',()=>{confirmResolver?.(true);confirmResolver=null;document.getElementById('confirmModal').classList.remove('open')});

// ==== Shortcuts help ====
window.openShortcuts=()=>document.getElementById('shortcutsModal').classList.add('open');
window.closeShortcuts=()=>document.getElementById('shortcutsModal').classList.remove('open');

// ==== Command palette ====
const CMDK_PAGES=[
    {t:'Dashboard',s:'Przegląd',icon:'layout-dashboard',url:'{{ route('admin.dashboard') }}',kbd:'g d'},
    {t:'Samochody',s:'Lista ofert',icon:'car',url:'{{ route('admin.cars.index') }}',kbd:'g c'},
    {t:'Dodaj samochód',s:'Nowa oferta',icon:'plus',url:'{{ route('admin.cars.create') }}',kbd:'n'},
    {t:'Wiadomości',s:'Skrzynka odbiorcza',icon:'inbox',url:'{{ route('admin.messages.index') }}',kbd:'g m'},
    {t:'Nieprzeczytane wiadomości',s:'Filtr',icon:'mail',url:'{{ route('admin.messages.index',['filter'=>'unread']) }}'},
    {t:'Marki',s:'Zarządzanie markami',icon:'tag',url:'{{ route('admin.brands.index') }}',kbd:'g b'},
    {t:'Profil',s:'Dane konta, hasło',icon:'user',url:'{{ route('admin.profile.edit') }}'},
    {t:'Strona główna',s:'Widok publiczny',icon:'external-link',url:'{{ route('home') }}',external:true},
    {t:'Oferta publiczna',s:'Katalog',icon:'external-link',url:'{{ route('catalog') }}',external:true},
    {t:'Wyloguj się',s:'Zakończ sesję',icon:'log-out',logout:true},
];
let cmdkItems=[],cmdkActive=0,cmdkFetchTimer=null;
function renderCmdk(items){
    cmdkItems=items;cmdkActive=0;
    const list=document.getElementById('cmdkList');
    if(!items.length){list.innerHTML='<div class="empty-state" style="padding:30px"><p style="font-size:13px">Brak wyników</p></div>';return}
    const groups={};
    items.forEach((it,i)=>{
        const g=it.group||'Nawigacja';
        groups[g]=groups[g]||[];
        groups[g].push({...it,_i:i});
    });
    list.innerHTML=Object.entries(groups).map(([g,arr])=>`<div class="cmdk-group"><div class="cmdk-group-lbl">${g}</div>`+arr.map(it=>`
        <div class="cmdk-item ${it._i===0?'active':''}" data-i="${it._i}">
            ${it.image?`<img src="${it.image}" alt="">`:`<i data-lucide="${it.icon||'arrow-right'}" class="ic"></i>`}
            <div class="tx"><div class="t">${it.t}</div>${it.s?`<div class="s">${it.s}</div>`:''}</div>
            ${it.kbd?`<span class="kbd">${it.kbd}</span>`:''}
        </div>`).join('')+'</div>').join('');
    if(window.lucide)lucide.createIcons();
    list.querySelectorAll('.cmdk-item').forEach(el=>{
        el.addEventListener('mouseenter',()=>{cmdkActive=+el.dataset.i;updateCmdkActive()});
        el.addEventListener('click',()=>cmdkRun(cmdkItems[+el.dataset.i]));
    });
}
function updateCmdkActive(){
    document.querySelectorAll('.cmdk-item').forEach(el=>el.classList.toggle('active',+el.dataset.i===cmdkActive));
    const el=document.querySelector('.cmdk-item.active');
    el?.scrollIntoView({block:'nearest'});
}
function cmdkRun(it){
    if(!it)return;
    closeCmdk();
    if(it.logout){document.querySelector('form[action="{{ route('logout') }}"]')?.submit();return}
    if(it.external){window.open(it.url,'_blank');return}
    if(it.url)window.location=it.url;
}
function filterCmdkPages(q){
    q=q.toLowerCase();
    return CMDK_PAGES.filter(p=>p.t.toLowerCase().includes(q)||p.s?.toLowerCase().includes(q)).map(p=>({...p,group:'Nawigacja'}));
}
async function cmdkSearchCars(q){
    try{
        const r=await fetch(`{{ route('admin.cars.index') }}?search=${encodeURIComponent(q)}&format=json`,{headers:{Accept:'application/json'}});
        if(!r.ok)return[];
        const d=await r.json();
        return d.cars.map(c=>({t:c.title,s:(c.identifier||'')+' · '+c.price,image:c.image,url:c.edit_url,group:'Samochody'}));
    }catch{return[]}
}
window.openCmdk=()=>{
    document.getElementById('cmdkBackdrop').classList.add('open');
    setTimeout(()=>document.getElementById('cmdkInput').focus(),50);
    renderCmdk(CMDK_PAGES.map(p=>({...p,group:'Nawigacja'})));
};
window.closeCmdk=()=>{document.getElementById('cmdkBackdrop').classList.remove('open');document.getElementById('cmdkInput').value=''};
document.getElementById('cmdkInput').addEventListener('input',e=>{
    clearTimeout(cmdkFetchTimer);
    const q=e.target.value.trim();
    if(!q){renderCmdk(CMDK_PAGES.map(p=>({...p,group:'Nawigacja'})));return}
    const pages=filterCmdkPages(q);
    renderCmdk(pages);
    if(q.length>=2){
        cmdkFetchTimer=setTimeout(async()=>{
            const cars=await cmdkSearchCars(q);
            renderCmdk([...pages,...cars]);
        },200);
    }
});

// ==== Keyboard shortcuts ====
let gSeq=null;
document.addEventListener('keydown',e=>{
    const openModal=document.querySelector('.modal-backdrop.open, .cmdk-backdrop.open');
    const inInput=/input|textarea|select/i.test(e.target.tagName)||e.target.isContentEditable;

    if(e.key==='Escape'){
        closeCmdk();closeConfirm();closeShortcuts();return;
    }
    if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==='k'){e.preventDefault();openCmdk();return}

    if(openModal||inInput){
        if(openModal?.classList.contains('cmdk-backdrop')){
            if(e.key==='ArrowDown'){e.preventDefault();cmdkActive=Math.min(cmdkActive+1,cmdkItems.length-1);updateCmdkActive()}
            if(e.key==='ArrowUp'){e.preventDefault();cmdkActive=Math.max(cmdkActive-1,0);updateCmdkActive()}
            if(e.key==='Enter'){e.preventDefault();cmdkRun(cmdkItems[cmdkActive])}
        }
        return;
    }

    if(e.key==='/'){e.preventDefault();openCmdk();return}
    if(e.key==='?'){e.preventDefault();openShortcuts();return}
    if(e.key.toLowerCase()==='n'){window.location='{{ route('admin.cars.create') }}';return}

    if(gSeq){
        clearTimeout(gSeq.t);
        const k=e.key.toLowerCase();
        const map={d:'{{ route('admin.dashboard') }}',c:'{{ route('admin.cars.index') }}',m:'{{ route('admin.messages.index') }}',b:'{{ route('admin.brands.index') }}'};
        if(map[k])window.location=map[k];
        gSeq=null;return;
    }
    if(e.key.toLowerCase()==='g'){gSeq={t:setTimeout(()=>gSeq=null,1000)}}
});

// ==== Button loading state ====
document.addEventListener('submit',e=>{
    const btn=e.target.querySelector('button[type=submit]:not([disabled])');
    if(btn&&!btn.dataset.noLoading)btn.classList.add('is-loading');
});

// ==== Delete confirm hook (any form with data-confirm) ====
document.addEventListener('submit',async e=>{
    const f=e.target;
    if(f.dataset.confirmed==='1')return;
    const msg=f.dataset.confirm;
    if(!msg)return;
    e.preventDefault();
    const ok=await confirmAction(f.dataset.confirmTitle||'Na pewno?',msg,f.dataset.confirmOk||'Potwierdź');
    if(ok){f.dataset.confirmed='1';f.submit()}
},true);

// ==== Flash messages to toasts ====
@if(session('success'))toast(@json(session('success')),'success');@endif
@if(session('error'))toast(@json(session('error')),'error');@endif
@if($errors->any())toast(@json($errors->first()),'error','Błąd walidacji');@endif

// ==== Lightbox ====
const lb={bd:document.getElementById('lbBackdrop'),img:document.getElementById('lbImg'),cap:document.getElementById('lbCaption'),count:document.getElementById('lbCount'),thumbs:document.getElementById('lbThumbs'),items:[],idx:0};
function openLbAt(items, idx){
    lb.items=items;lb.idx=idx;
    renderLb();
    lb.bd.classList.add('open');
}
function renderLb(){
    const it=lb.items[lb.idx];
    if(!it)return;
    lb.img.src=it.src;
    lb.img.alt=it.caption||'';
    lb.cap.textContent=it.caption||'';
    lb.cap.style.display=it.caption?'block':'none';
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
// Auto-wire any [data-lightbox] element. Grouping via data-gallery="name".
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

window.addEventListener('DOMContentLoaded',()=>{
    if(window.lucide)lucide.createIcons();
    const side=document.getElementById('adSide'),bd=document.getElementById('adBackdrop'),ham=document.getElementById('adHamburger');
    function toggleSide(open){side.classList.toggle('open',open);bd.classList.toggle('active',open)}
    ham?.addEventListener('click',()=>toggleSide(!side.classList.contains('open')));
    bd?.addEventListener('click',()=>toggleSide(false));
    initLightbox();
});
</script>
@stack('scripts')
</body>
</html>
