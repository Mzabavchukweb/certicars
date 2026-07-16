@extends('layouts.public')
@section('meta_title_full','Kontakt — Lipnik koło Stargardu | CertiCars')
@section('title','Kontakt')
@section('description','Skontaktuj się z CertiCars. Zadzwoń, napisz lub umów oględziny auta w Lipniku koło Stargardu. Odpowiadamy szybko i konkretnie.')
@section('og_title','Kontakt — Lipnik koło Stargardu | CertiCars')
@section('og_description','Skontaktuj się z CertiCars. Zadzwoń, napisz lub umów oględziny auta w Lipniku koło Stargardu. Odpowiadamy szybko i konkretnie.')
@section('styles')
/* ===== CONTACT PAGE — premium refresh ===== */
.kt-section-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;width:100%;box-sizing:border-box}

/* ===== HERO (dark) — flat navy, no radial overlays ===== */
.kt-hero{background:#0a1432;padding:88px 0 96px;position:relative;overflow:hidden;border-bottom:1px solid #131b3a}
.kt-hero-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}
.kt-hero-in{display:grid;grid-template-columns:1.05fr .95fr;gap:64px;align-items:center;position:relative}

.kt-breadcrumb{display:flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.45);margin-bottom:24px}
.kt-breadcrumb a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s}
.kt-breadcrumb a:hover{color:#fff}
.kt-breadcrumb svg{width:11px;height:11px;stroke:rgba(255,255,255,.3);fill:none;stroke-width:2.5}
.kt-breadcrumb .current{color:#fff;font-weight:500}
.kt-hero-label{font-size:11px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:var(--orange);margin-bottom:22px;display:inline-flex;align-items:center;gap:12px}
.kt-hero-label::before{content:'';width:32px;height:2px;background:var(--orange);border-radius:2px;flex-shrink:0}
.kt-hero h1{font-size:54px;font-weight:900;color:#fff;letter-spacing:-1.1px;line-height:1.05;margin:0 0 22px;max-width:540px}
.kt-hero h1 em{font-style:normal;color:#3b8bff}
.kt-hero-desc{font-size:16px;color:rgba(255,255,255,.62);line-height:1.75;margin:0 0 28px;max-width:520px}
.kt-trust{display:flex;flex-wrap:wrap;gap:10px}
.kt-trust-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.78);padding:9px 16px;border-radius:50px;font-size:12.5px;font-weight:600;letter-spacing:-.05px}
.kt-trust-pill svg{width:14px;height:14px;stroke:#5fa1ff;fill:none;stroke-width:2.2;flex-shrink:0}

/* Hero right panel — flat dark navy card, single thin border, no glow */
.kt-panel{position:relative;background:#0e1a3a;border:1px solid #1f2e58;border-radius:18px;padding:28px 30px 24px}
.kt-prow{display:grid;grid-template-columns:46px 1fr;gap:16px;align-items:center;padding:16px 0;border-top:1px solid #1f2e58}
.kt-prow:first-child{border-top:none;padding-top:6px}
.kt-prow:last-of-type{padding-bottom:6px}
.kt-prow-ico{width:44px;height:44px;border-radius:10px;background:#142347;color:#5fa1ff;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kt-prow-ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2}
.kt-prow-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.45);margin:0 0 3px}
.kt-prow-val{font-size:17px;font-weight:800;color:#fff;letter-spacing:-.2px;text-decoration:none;line-height:1.25;display:block}
.kt-prow a.kt-prow-val:hover{color:#5fa1ff}
.kt-panel-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:18px;padding:14px 22px;background:var(--blue);color:#fff;font-weight:700;font-size:15px;border-radius:8px;text-decoration:none;letter-spacing:-.1px;transition:background .15s ease}
.kt-panel-btn:hover{background:var(--blue-h)}
.kt-panel-btn svg{width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.2}

/* ===== Section helpers ===== */
.kt-section-label{font-size:11px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:var(--blue);margin-bottom:14px;text-align:center;display:flex;align-items:center;justify-content:center;gap:10px}
.kt-section-label::before,.kt-section-label::after{content:'';width:24px;height:2px;background:var(--blue);border-radius:2px;opacity:.5}
.kt-section-h{font-size:36px;font-weight:900;color:var(--text);letter-spacing:-.8px;text-align:center;margin:0 0 14px;line-height:1.1}
.kt-section-sub{font-size:15px;color:var(--text-3);text-align:center;max-width:620px;margin:0 auto 48px;line-height:1.7}

/* ===== "Skontaktuj się tak, jak Ci wygodnie" — 3 method cards ===== */
.kt-methods{padding:80px 0 24px;background:var(--bg)}
.kt-methods-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.kt-method{background:#fff;border-radius:12px;padding:28px 26px;border:1px solid var(--border-l);transition:border-color .15s ease;text-decoration:none;color:inherit;display:flex;gap:16px;align-items:flex-start;position:relative}
.kt-method:hover{border-color:var(--blue)}
.kt-method-ico{flex-shrink:0;width:44px;height:44px;border-radius:10px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center}
.kt-method-ico svg{width:22px;height:22px;stroke:var(--blue);fill:none;stroke-width:2}
.kt-method h3{font-size:16px;font-weight:700;color:var(--text);letter-spacing:-.2px;margin:0 0 8px}
.kt-method p{font-size:13.5px;color:var(--text-3);line-height:1.7;margin:0}
.kt-method-arr{position:absolute;right:18px;bottom:18px;color:var(--text-4);transition:color .15s ease}
.kt-method-arr svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2}
.kt-method:hover .kt-method-arr{color:var(--blue)}

/* ===== Form + side info ===== */
.kt-form-wrap{padding:32px 0 72px;background:var(--bg)}
.kt-form-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:28px;align-items:start}
.kt-form-card{background:#fff;border-radius:12px;padding:36px;border:1px solid var(--border-l)}
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
.kt-form-submit{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;border-radius:8px;background:var(--blue);color:#fff;border:none;font-family:inherit;font-size:15.5px;font-weight:700;cursor:pointer;letter-spacing:-.1px;transition:background .15s ease}
.kt-form-submit:hover{background:var(--blue-h)}
.kt-form-submit svg{width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.2}

.kt-side{display:flex;flex-direction:column;gap:16px}
.kt-side-card{background:#fff;border-radius:12px;padding:22px;border:1px solid var(--border-l);display:flex;gap:14px;align-items:flex-start}
.kt-side-ico{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center;color:var(--blue)}
.kt-side-ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2.2}
.kt-side-body{flex:1;min-width:0}
.kt-side-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--text-3);margin:0 0 4px}
.kt-side-val{font-size:17px;font-weight:800;color:var(--text);letter-spacing:-.2px;margin:0 0 4px;display:block;text-decoration:none}
a.kt-side-val:hover{color:var(--blue)}
.kt-side-meta{font-size:12.5px;color:var(--text-3);line-height:1.55;margin:0}
.kt-hours-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px;border-top:1px dashed #eef2f7}
.kt-hours-row:first-of-type{border-top:none;padding-top:6px}
.kt-hours-row .d{color:var(--text-3);font-weight:500}
.kt-hours-row .t{color:var(--text);font-weight:800}

/* ===== Bottom phone CTA strip — flat blue, no gradients/glow ===== */
.kt-call-strip{background:var(--blue);padding:48px 0}
.kt-call-strip-in{display:grid;grid-template-columns:auto 1fr auto;gap:32px;align-items:center}
.kt-call-strip-ico{width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.14);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kt-call-strip-ico svg{width:30px;height:30px;stroke:#fff;fill:none;stroke-width:2}
.kt-call-strip-body h2{font-size:28px;font-weight:800;color:#fff;letter-spacing:-.4px;margin:0 0 6px;line-height:1.15}
.kt-call-strip-body p{font-size:14.5px;color:rgba(255,255,255,.78);line-height:1.6;margin:0;max-width:580px}
.kt-call-strip-btn{display:inline-flex;align-items:center;gap:12px;background:#fff;color:var(--blue);padding:16px 30px;border-radius:8px;font-weight:700;font-size:17px;text-decoration:none;letter-spacing:-.2px;transition:background .15s ease;flex-shrink:0}
.kt-call-strip-btn:hover{background:#f4f7fb}
.kt-call-strip-btn svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2.2}

/* ===== Responsive ===== */
@media(max-width:1024px){
    .kt-hero h1{font-size:44px}
    .kt-hero-in{grid-template-columns:1fr;gap:48px}
    .kt-form-grid{grid-template-columns:1fr;gap:22px}
    .kt-call-strip-in{grid-template-columns:auto 1fr;gap:24px;text-align:left}
    .kt-call-strip-btn{grid-column:1/-1;justify-self:start}
}
@media(max-width:900px){
    .kt-hero{padding:64px 0 76px}
    .kt-hero h1{font-size:38px}
    .kt-methods-grid{grid-template-columns:1fr;gap:14px}
    .kt-section-h{font-size:28px}
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

                <form method="POST" action="{{ route('contact.submit') }}" novalidate id="kt-contact-form">
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

<script>
(function(){
    var form = document.getElementById('kt-contact-form');
    if(!form) return;
    var card = form.closest('.kt-form-card');
    var btn  = form.querySelector('.kt-form-submit');
    var btnHTML = btn ? btn.innerHTML : '';

    function clearErrors(){
        form.querySelectorAll('.kt-field-err[data-ajax-err]').forEach(function(el){ el.remove(); });
        var old = card && card.querySelector('.kt-form-alert');
        if(old) old.remove();
    }
    function showError(field, msg){
        var input = form.querySelector('[name="'+field+'"]');
        var wrap = input ? input.closest('.kt-field') : null;
        if(!wrap) return;
        var span = document.createElement('span');
        span.className = 'kt-field-err';
        span.setAttribute('data-ajax-err','1');
        span.textContent = msg;
        wrap.appendChild(span);
    }
    function showAlert(type, msg){
        // Inserted at the top of the card WITHOUT scrolling the page.
        var box = document.createElement('div');
        box.className = 'kt-form-alert ' + (type === 'ok' ? 'kt-form-success' : '');
        if(type !== 'ok'){
            box.style.cssText = 'background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.25);color:#991b1b;padding:14px 18px;border-radius:10px;font-size:14px;font-weight:500;margin-bottom:22px';
        }
        box.textContent = msg;
        card.insertBefore(box, form);       // just above the form — no page scroll
    }

    form.addEventListener('submit', function(e){
        e.preventDefault();                 // never reload → the visitor stays put
        clearErrors();
        if(btn){ btn.disabled = true; btn.style.opacity = '.7'; btn.innerHTML = 'Wysyłanie…'; }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': (document.querySelector('input[name=_token]') || {}).value || ''
            },
            body: new FormData(form)
        }).then(function(res){
            return res.json().then(function(data){ return { status: res.status, data: data }; });
        }).then(function(r){
            if(r.status === 200){
                form.reset();
                showAlert('ok', (r.data && r.data.message) || 'Dziękujemy! Odezwiemy się wkrótce.');
            } else if(r.status === 422 && r.data && r.data.errors){
                Object.keys(r.data.errors).forEach(function(f){ showError(f, r.data.errors[f][0]); });
            } else {
                showAlert('err', 'Wystąpił błąd. Spróbuj ponownie za chwilę lub zadzwoń do nas.');
            }
        }).catch(function(){
            showAlert('err', 'Brak połączenia. Sprawdź internet i spróbuj ponownie.');
        }).finally(function(){
            if(btn){ btn.disabled = false; btn.style.opacity = ''; btn.innerHTML = btnHTML; }
        });
    });
})();
</script>

@endsection
