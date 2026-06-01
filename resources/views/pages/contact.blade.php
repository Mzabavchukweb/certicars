@extends('layouts.public')
@section('title','Kontakt')
@section('description','Skontaktuj się z CertiCars. Zadzwoń, napisz lub odwiedź nasz salon w Warszawie. Odpowiadamy szybko.')
@section('styles')
/* ===== CONTACT PAGE ===== */
.contact-hero{background:linear-gradient(135deg,#060b18 0%,#080e1e 50%,#0b1530 100%);padding:80px 0 72px;position:relative;overflow:hidden;text-align:center}
.contact-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 30% 20%,rgba(0,102,255,.18),transparent),radial-gradient(ellipse 50% 70% at 80% 80%,rgba(255,100,0,.07),transparent)}
.contact-hero-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.contact-hero::after{content:'';position:absolute;bottom:-2px;left:0;right:0;height:56px;background:var(--bg);clip-path:polygon(0 100%,100% 100%,100% 30%,0 100%)}
.contact-hero-in{max-width:760px;margin:0 auto;padding:0 24px;position:relative}
.contact-breadcrumb{display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:rgba(255,255,255,.35);margin-bottom:20px}
.contact-breadcrumb a{color:rgba(255,255,255,.35);text-decoration:none;transition:color .15s}
.contact-breadcrumb a:hover{color:#fff}
.contact-breadcrumb svg{width:10px;height:10px;stroke:rgba(255,255,255,.2);fill:none;stroke-width:2}
.contact-hero-label{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--orange);margin-bottom:18px;display:inline-flex;align-items:center;gap:10px}
.contact-hero-label::before,.contact-hero-label::after{content:'';width:20px;height:2px;background:var(--orange);border-radius:2px}
.contact-hero h1{font-size:50px;font-weight:900;color:#fff;letter-spacing:-.9px;line-height:1.06;margin-bottom:18px}
.contact-hero h1 em{font-style:normal;color:var(--blue)}
.contact-hero-sub{font-size:16px;color:rgba(255,255,255,.5);line-height:1.75;margin-bottom:32px}
.contact-trust{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.contact-trust-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.75);padding:8px 16px;border-radius:50px;font-size:12px;font-weight:600}
.contact-trust-pill svg{width:13px;height:13px;stroke:var(--blue);fill:none;stroke-width:2;flex-shrink:0}
.contact-trust-pill b{color:#fff}

/* Main grid */
.contact-wrap{max-width:1200px;margin:0 auto;padding:56px 24px 80px;display:grid;grid-template-columns:1fr 420px;gap:48px;align-items:start}

/* Form card */
.contact-form-card{background:#fff;border-radius:20px;padding:40px;border:1px solid var(--border-l);box-shadow:0 8px 32px rgba(0,0,0,.07)}
.contact-form-card h2{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:6px}
.contact-form-card > p{font-size:14px;color:var(--text-3);margin-bottom:28px}
.cf-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.cf-group{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
.cf-group:last-of-type{margin-bottom:0}
.cf-label{font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.7px}
.cf-input,.cf-textarea{width:100%;padding:12px 14px;border:1.5px solid var(--border-l);border-radius:10px;font-size:14px;font-family:inherit;color:var(--text);transition:all .2s;background:#fff}
.cf-input:focus,.cf-textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,102,255,.08)}
.cf-input::placeholder,.cf-textarea::placeholder{color:var(--text-4)}
.cf-textarea{resize:vertical;min-height:130px;line-height:1.6}
.cf-submit{width:100%;padding:14px;border-radius:50px;background:var(--blue);color:#fff;border:none;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 6px 20px rgba(0,102,255,.35);margin-top:24px}
.cf-submit:hover{background:var(--blue-h);box-shadow:0 10px 28px rgba(0,102,255,.45);transform:translateY(-1px)}
.cf-submit svg{width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2.5}
.cf-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#065f46;padding:14px 18px;border-radius:10px;font-size:14px;font-weight:500;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.cf-success svg{width:18px;height:18px;stroke:#10b981;fill:none;stroke-width:2;flex-shrink:0}

/* Info side */
.contact-info{display:flex;flex-direction:column;gap:16px}
.ci-card{background:#fff;border-radius:16px;padding:24px;border:1px solid var(--border-l);box-shadow:0 4px 16px rgba(0,0,0,.04);display:flex;align-items:flex-start;gap:16px;transition:all .2s;text-decoration:none;color:inherit}
.ci-card:hover{border-color:var(--blue);box-shadow:0 8px 24px rgba(0,102,255,.1);transform:translateY(-2px)}
.ci-icon{width:48px;height:48px;border-radius:12px;background:var(--blue-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ci-icon svg{width:22px;height:22px;stroke:var(--blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.ci-body strong{display:block;font-size:12px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:5px}
.ci-body span{font-size:15px;font-weight:600;color:var(--text);line-height:1.4;display:block}
.ci-body small{font-size:12px;color:var(--text-3);display:block;margin-top:3px}

/* Hours card */
.ci-hours{background:#fff;border-radius:16px;padding:24px;border:1px solid var(--border-l);box-shadow:0 4px 16px rgba(0,0,0,.04)}
.ci-hours h3{font-size:13px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:16px;display:flex;align-items:center;gap:7px}
.ci-hours h3 svg{width:15px;height:15px;stroke:var(--blue);fill:none;stroke-width:2}
.ci-hours-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:13px}
.ci-hours-row:last-child{border-bottom:none;padding-bottom:0}
.ci-hours-row .day{color:var(--text-3);font-weight:500}
.ci-hours-row .time{color:var(--text);font-weight:700}
.ci-hours-row .closed{color:var(--text-4);font-weight:400;font-style:italic}

/* Map placeholder */
.ci-map{border-radius:16px;overflow:hidden;border:1px solid var(--border-l);height:180px;background:linear-gradient(135deg,#e8f1ff,#dbeafe);display:flex;align-items:center;justify-content:center;position:relative}
.ci-map-pin{display:flex;flex-direction:column;align-items:center;gap:8px}
.ci-map-pin svg{width:40px;height:40px;stroke:var(--blue);fill:none;stroke-width:1.5}
.ci-map-pin span{font-size:13px;font-weight:600;color:var(--blue)}

/* CTA strip phone */
.contact-strip{background:var(--blue);padding:48px 0;text-align:center}
.contact-strip-in{max-width:760px;margin:0 auto;padding:0 24px}
.contact-strip h2{font-size:28px;font-weight:900;color:#fff;letter-spacing:-.4px;margin-bottom:8px}
.contact-strip p{font-size:15px;color:rgba(255,255,255,.7);margin-bottom:24px}
.contact-strip a{display:inline-flex;align-items:center;gap:10px;background:#fff;color:var(--blue);padding:14px 32px;border-radius:50px;font-weight:700;font-size:16px;text-decoration:none;box-shadow:0 8px 24px rgba(0,0,0,.15);transition:all .2s}
.contact-strip a:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,0,0,.2)}
.contact-strip a svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.5}

@media(max-width:900px){
    .contact-wrap{grid-template-columns:1fr}
    .contact-hero h1{font-size:34px}
}
@media(max-width:600px){
    .cf-row{grid-template-columns:1fr}
    .contact-form-card{padding:24px}
    .contact-hero h1{font-size:28px}
}
@endsection
@section('content')

{{-- Hero --}}
<section class="contact-hero">
    <div class="contact-hero-dots"></div>
    <div class="contact-hero-in">
        <nav class="contact-breadcrumb">
            <a href="{{ route('home') }}">Strona główna</a>
            <x-icon name="chevron-right" size="14"/>
            <span>Kontakt</span>
        </nav>
        <div class="contact-hero-label">Skontaktuj się</div>
        <h1>Masz pytanie?<br><em>Jesteśmy tutaj.</em></h1>
        <p class="contact-hero-sub">Napisz, zadzwoń lub odwiedź nasz salon. Odpowiadamy szybko i bez zbędnych formalności.</p>
        <div class="contact-trust">
            <span class="contact-trust-pill">
                <x-icon name="clock" size="16"/>
                Odpowiedź w ciągu <b>24h</b>
            </span>
            <span class="contact-trust-pill">
                <x-icon name="map-pin" size="16"/>
                Salon w <b>Warszawie</b>
            </span>
            <span class="contact-trust-pill">
                <x-icon name="calendar" size="16"/>
                Pon–Sob <b>9:00–18:00</b>
            </span>
        </div>
    </div>
</section>

{{-- Main --}}
<div class="contact-wrap">

    {{-- Form --}}
    <div class="contact-form-card">
        <h2>Napisz do nas</h2>
        <p>Odpowiadamy zazwyczaj w ciągu kilku godzin w dni robocze.</p>

        @if(session('success'))
        <div class="cf-success">
            <x-icon name="check" size="18"/>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf
            <div class="cf-row">
                <div class="cf-group">
                    <label class="cf-label" for="name">Imię i nazwisko *</label>
                    <input type="text" id="name" name="name" class="cf-input" placeholder="Jan Kowalski" value="{{ old('name') }}" required>
                    @error('name')<span style="font-size:12px;color:#dc2626">{{ $message }}</span>@enderror
                </div>
                <div class="cf-group">
                    <label class="cf-label" for="phone">Telefon</label>
                    <input type="tel" id="phone" name="phone" class="cf-input" placeholder="+48 123 456 789" value="{{ old('phone') }}">
                </div>
            </div>
            <div class="cf-group">
                <label class="cf-label" for="email">Adres e-mail *</label>
                <input type="email" id="email" name="email" class="cf-input" placeholder="jan@kowalski.pl" value="{{ old('email') }}" required>
                @error('email')<span style="font-size:12px;color:#dc2626">{{ $message }}</span>@enderror
            </div>
            <div class="cf-group">
                <label class="cf-label" for="message">Wiadomość *</label>
                <textarea id="message" name="message" class="cf-textarea" placeholder="W czym możemy pomóc? Opisz szczegółowo swoje pytanie..." required>{{ old('message') }}</textarea>
                @error('message')<span style="font-size:12px;color:#dc2626">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="cf-submit">
                <x-icon name="send" size="16"/>
                Wyślij wiadomość
            </button>
        </form>
    </div>

    {{-- Info --}}
    <div class="contact-info">
        <a href="tel:+48515440623" class="ci-card">
            <div class="ci-icon">
                <x-icon name="phone" size="22"/>
            </div>
            <div class="ci-body">
                <strong>Zadzwoń do nas</strong>
                <span>+48 515 440 623</span>
                <small>Pn–Pt 9:00–18:00, Sb–Nd 10:00–15:00</small>
            </div>
        </a>

        <a href="mailto:kontakt@certicars.pl" class="ci-card">
            <div class="ci-icon">
                <x-icon name="mail" size="22"/>
            </div>
            <div class="ci-body">
                <strong>Napisz e-mail</strong>
                <span>kontakt@certicars.pl</span>
                <small>Odpiszemy w ciągu 24h</small>
            </div>
        </a>

        <div class="ci-card" style="cursor:default">
            <div class="ci-icon">
                <x-icon name="map-pin" size="16"/>
            </div>
            <div class="ci-body">
                <strong>Nasz salon</strong>
                <span>Lipnik</span>
                <small>Polska</small>
            </div>
        </div>

        <div class="ci-map">
            <div class="ci-map-pin">
                <x-icon name="map-pin" size="16"/>
                <span>Lipnik, Polska</span>
            </div>
        </div>

        <div class="ci-hours">
            <h3>
                <x-icon name="clock" size="16"/>
                Godziny otwarcia
            </h3>
            <div class="ci-hours-row"><span class="day">Poniedziałek – Piątek</span><span class="time">9:00 – 18:00</span></div>
            <div class="ci-hours-row"><span class="day">Sobota – Niedziela</span><span class="time">10:00 – 15:00</span></div>
        </div>
    </div>
</div>

{{-- Phone CTA --}}
<section class="contact-strip">
    <div class="contact-strip-in">
        <h2>Wolisz porozmawiać?</h2>
        <p>Dzwoń bezpośrednio — nasz konsultant chętnie odpowie na wszystkie pytania.</p>
        <a href="tel:+48515440623">
            <x-icon name="phone" size="22"/>
            +48 515 440 623
        </a>
    </div>
</section>

@endsection
