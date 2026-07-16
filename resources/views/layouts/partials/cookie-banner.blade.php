{{-- Cookie consent banner — RODO/ePrivacy compliant:
     • non-essential categories OFF by default (no pre-ticked consent)
     • "Odrzuć" is as prominent as "Akceptuj"
     • granular per-category choice
     • decision stored in localStorage; re-openable via window.ccCookieOpen()
     Non-essential scripts (analytics/marketing) should gate on
     window.ccConsent(category) before running. --}}
<style>
.cc-cookie{position:fixed;left:0;right:0;bottom:0;z-index:2147483000;display:none;padding:16px;pointer-events:none}
.cc-cookie.show{display:flex;justify-content:center}
.cc-cookie-box{pointer-events:auto;width:100%;max-width:560px;background:#fff;border:1px solid #e5edfa;border-radius:16px;box-shadow:0 20px 60px rgba(10,20,50,.28);padding:22px 22px 18px;font-family:inherit}
.cc-cookie-title{display:flex;align-items:center;gap:10px;font-size:16px;font-weight:800;color:#0a0a0a;margin:0 0 8px;letter-spacing:-.2px}
.cc-cookie-title svg{width:20px;height:20px;stroke:#0066ff;fill:none;stroke-width:2}
.cc-cookie-text{font-size:13px;color:#475569;line-height:1.6;margin:0 0 16px}
.cc-cookie-text a{color:#0066ff;font-weight:600;text-decoration:none}
.cc-cookie-text a:hover{text-decoration:underline}
.cc-cookie-actions{display:flex;gap:10px;flex-wrap:wrap}
.cc-cookie-btn{flex:1;min-width:140px;border-radius:10px;padding:12px 14px;font:inherit;font-size:13.5px;font-weight:700;cursor:pointer;border:1.5px solid transparent;transition:all .15s;text-align:center}
.cc-cookie-btn.accept{background:#0066ff;color:#fff}
.cc-cookie-btn.accept:hover{background:#0052cc}
.cc-cookie-btn.reject{background:#fff;color:#0a0a0a;border-color:#cdddf7}
.cc-cookie-btn.reject:hover{border-color:#0066ff;color:#0066ff}
.cc-cookie-link{background:none;border:none;font:inherit;font-size:12.5px;font-weight:700;color:#64748b;cursor:pointer;padding:10px 4px;text-decoration:underline;text-underline-offset:2px}
.cc-cookie-link:hover{color:#0066ff}
.cc-cookie-cats{margin:4px 0 14px;display:none;flex-direction:column;gap:2px}
.cc-cookie-cats.show{display:flex}
.cc-cookie-cat{display:flex;align-items:flex-start;gap:12px;padding:12px 2px;border-top:1px solid #eef2f8}
.cc-cookie-cat-body{flex:1}
.cc-cookie-cat-body b{display:block;font-size:13px;color:#0a0a0a;font-weight:700}
.cc-cookie-cat-body span{font-size:11.5px;color:#94a3b8;line-height:1.5}
.cc-sw{position:relative;flex-shrink:0;width:40px;height:23px;margin-top:2px}
.cc-sw input{opacity:0;width:0;height:0;position:absolute}
.cc-sw .track{position:absolute;inset:0;background:#cbd5e1;border-radius:50px;transition:background .15s;cursor:pointer}
.cc-sw .track::before{content:'';position:absolute;top:2px;left:2px;width:19px;height:19px;background:#fff;border-radius:50%;transition:transform .15s;box-shadow:0 1px 3px rgba(0,0,0,.25)}
.cc-sw input:checked + .track{background:#0066ff}
.cc-sw input:checked + .track::before{transform:translateX(17px)}
.cc-sw input:disabled + .track{background:#16a34a;cursor:not-allowed;opacity:.85}
@media(max-width:560px){.cc-cookie{padding:10px}.cc-cookie-btn{min-width:calc(50% - 5px)}}

/* Floating re-open button (bottom-left) — always available so the visitor
   can change their cookie choice at any time. */
.cc-cookie-fab{position:fixed;left:18px;bottom:18px;z-index:2147482990;width:46px;height:46px;border-radius:50%;background:#fff;border:1px solid #e5edfa;box-shadow:0 6px 20px rgba(10,20,50,.18);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#0066ff;transition:transform .15s,box-shadow .15s;padding:0}
.cc-cookie-fab:hover{transform:translateY(-2px);box-shadow:0 10px 26px rgba(10,20,50,.24)}
.cc-cookie-fab svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2}
@media(max-width:560px){.cc-cookie-fab{width:42px;height:42px;left:12px;bottom:12px}}
</style>

<button type="button" class="cc-cookie-fab" aria-label="Ustawienia plików cookies" title="Ustawienia plików cookies" onclick="if(window.ccCookieOpen)window.ccCookieOpen()">
    <x-icon name="cookie" size="22" :strokeWidth="2"/>
</button>

<div class="cc-cookie" id="ccCookie" role="dialog" aria-modal="false" aria-label="Zgoda na pliki cookies">
    <div class="cc-cookie-box">
        <div class="cc-cookie-title"><x-icon name="cookie" size="20" :strokeWidth="2"/> Szanujemy Twoją prywatność</div>
        <p class="cc-cookie-text">Używamy plików cookies, aby serwis działał poprawnie oraz — za Twoją zgodą — do celów analitycznych i funkcjonalnych. Możesz zaakceptować wszystkie, odrzucić opcjonalne albo wybrać kategorie. Więcej w <a href="{{ route('cookies') }}">Polityce cookies</a> i <a href="{{ route('privacy') }}">Polityce prywatności</a>.</p>

        <div class="cc-cookie-cats" id="ccCookieCats">
            <div class="cc-cookie-cat">
                <label class="cc-sw"><input type="checkbox" checked disabled><span class="track"></span></label>
                <div class="cc-cookie-cat-body"><b>Niezbędne</b><span>Konieczne do działania serwisu i bezpieczeństwa. Zawsze aktywne.</span></div>
            </div>
            <div class="cc-cookie-cat">
                <label class="cc-sw"><input type="checkbox" id="ccCatAnalytics"><span class="track"></span></label>
                <div class="cc-cookie-cat-body"><b>Analityczne</b><span>Anonimowe statystyki pomagające ulepszać serwis.</span></div>
            </div>
            <div class="cc-cookie-cat">
                <label class="cc-sw"><input type="checkbox" id="ccCatFunctional"><span class="track"></span></label>
                <div class="cc-cookie-cat-body"><b>Funkcjonalne</b><span>Zapamiętywanie preferencji, np. ulubionych pojazdów.</span></div>
            </div>
            <div class="cc-cookie-cat">
                <label class="cc-sw"><input type="checkbox" id="ccCatMarketing"><span class="track"></span></label>
                <div class="cc-cookie-cat-body"><b>Marketingowe</b><span>Dopasowane treści i pomiar skuteczności działań.</span></div>
            </div>
        </div>

        <div class="cc-cookie-actions">
            <button type="button" class="cc-cookie-btn reject" id="ccRejectBtn" onclick="ccCookieReject()">Odrzuć opcjonalne</button>
            <button type="button" class="cc-cookie-btn accept" onclick="ccCookieAcceptAll()">Akceptuj wszystkie</button>
        </div>
        <div style="text-align:center">
            <button type="button" class="cc-cookie-link" id="ccToggleSettings" onclick="ccCookieToggleSettings()">Ustawienia szczegółowe</button>
            <button type="button" class="cc-cookie-link" id="ccSaveSettings" style="display:none" onclick="ccCookieSaveSettings()">Zapisz wybrane ustawienia</button>
        </div>
    </div>
</div>

<script>
(function(){
    var KEY = 'cc_cookie_consent';
    var box = document.getElementById('ccCookie');
    function read(){ try { return JSON.parse(localStorage.getItem(KEY) || 'null'); } catch(_){ return null; } }
    function save(c){
        c.ts = new Date().toISOString(); c.v = 1;
        try { localStorage.setItem(KEY, JSON.stringify(c)); } catch(_){}
        // Mirror to a cookie so server-side could read it too (1 year).
        try { document.cookie = KEY + '=' + encodeURIComponent(JSON.stringify(c)) + ';path=/;max-age=31536000;samesite=Lax'; } catch(_){}
        window.dispatchEvent(new CustomEvent('cc-consent', { detail: c }));
    }
    function show(){ box.classList.add('show'); }
    function hide(){ box.classList.remove('show'); }

    window.ccConsent = function(cat){ var c = read(); return !!(c && c[cat]); };
    window.ccCookieOpen = function(){
        var c = read();
        document.getElementById('ccCatAnalytics').checked  = !!(c && c.analytics);
        document.getElementById('ccCatFunctional').checked = !!(c && c.functional);
        document.getElementById('ccCatMarketing').checked  = !!(c && c.marketing);
        document.getElementById('ccCookieCats').classList.add('show');
        document.getElementById('ccToggleSettings').style.display = 'none';
        document.getElementById('ccSaveSettings').style.display = '';
        show();
    };
    window.ccCookieAcceptAll = function(){ save({necessary:true,analytics:true,functional:true,marketing:true}); hide(); };
    window.ccCookieReject   = function(){ save({necessary:true,analytics:false,functional:false,marketing:false}); hide(); };
    window.ccCookieToggleSettings = function(){
        document.getElementById('ccCookieCats').classList.add('show');
        document.getElementById('ccToggleSettings').style.display = 'none';
        document.getElementById('ccSaveSettings').style.display = '';
    };
    window.ccCookieSaveSettings = function(){
        save({
            necessary:true,
            analytics:  document.getElementById('ccCatAnalytics').checked,
            functional: document.getElementById('ccCatFunctional').checked,
            marketing:  document.getElementById('ccCatMarketing').checked
        });
        hide();
    };

    // Show on first visit (no stored decision).
    if(!read()) { setTimeout(show, 500); }
})();
</script>
