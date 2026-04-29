<?php $__env->startSection('title','Kontakt'); ?>
<?php $__env->startSection('description','Skontaktuj się z CertiCars. Zadzwoń, napisz lub odwiedź nasz salon w Warszawie. Odpowiadamy szybko.'); ?>
<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


<section class="contact-hero">
    <div class="contact-hero-dots"></div>
    <div class="contact-hero-in">
        <nav class="contact-breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Strona główna</a>
            <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span>Kontakt</span>
        </nav>
        <div class="contact-hero-label">Skontaktuj się</div>
        <h1>Masz pytanie?<br><em>Jesteśmy tutaj.</em></h1>
        <p class="contact-hero-sub">Napisz, zadzwoń lub odwiedź nasz salon. Odpowiadamy szybko i bez zbędnych formalności.</p>
        <div class="contact-trust">
            <span class="contact-trust-pill">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Odpowiedź w ciągu <b>24h</b>
            </span>
            <span class="contact-trust-pill">
                <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                Salon w <b>Warszawie</b>
            </span>
            <span class="contact-trust-pill">
                <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.21 12"/><path d="M4 4h16v8H4z"/></svg>
                Pon–Sob <b>9:00–18:00</b>
            </span>
        </div>
    </div>
</section>


<div class="contact-wrap">

    
    <div class="contact-form-card">
        <h2>Napisz do nas</h2>
        <p>Odpowiadamy zazwyczaj w ciągu kilku godzin w dni robocze.</p>

        <?php if(session('success')): ?>
        <div class="cf-success">
            <svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('contact.submit')); ?>">
            <?php echo csrf_field(); ?>
            <div class="cf-row">
                <div class="cf-group">
                    <label class="cf-label" for="name">Imię i nazwisko *</label>
                    <input type="text" id="name" name="name" class="cf-input" placeholder="Jan Kowalski" value="<?php echo e(old('name')); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="cf-group">
                    <label class="cf-label" for="phone">Telefon</label>
                    <input type="tel" id="phone" name="phone" class="cf-input" placeholder="+48 123 456 789" value="<?php echo e(old('phone')); ?>">
                </div>
            </div>
            <div class="cf-group">
                <label class="cf-label" for="email">Adres e-mail *</label>
                <input type="email" id="email" name="email" class="cf-input" placeholder="jan@kowalski.pl" value="<?php echo e(old('email')); ?>" required>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="cf-group">
                <label class="cf-label" for="message">Wiadomość *</label>
                <textarea id="message" name="message" class="cf-textarea" placeholder="W czym możemy pomóc? Opisz szczegółowo swoje pytanie..." required><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="cf-submit">
                <svg viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4 20-7z"/><path d="M22 2 11 13"/></svg>
                Wyślij wiadomość
            </button>
        </form>
    </div>

    
    <div class="contact-info">
        <a href="tel:+48123456789" class="ci-card">
            <div class="ci-icon">
                <svg viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg>
            </div>
            <div class="ci-body">
                <strong>Zadzwoń do nas</strong>
                <span>+48 123 456 789</span>
                <small>Pon–Pt 9:00–18:00, Sob 10:00–14:00</small>
            </div>
        </a>

        <a href="mailto:kontakt@certicars.pl" class="ci-card">
            <div class="ci-icon">
                <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>
            <div class="ci-body">
                <strong>Napisz e-mail</strong>
                <span>kontakt@certicars.pl</span>
                <small>Odpiszemy w ciągu 24h</small>
            </div>
        </a>

        <div class="ci-card" style="cursor:default">
            <div class="ci-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
            </div>
            <div class="ci-body">
                <strong>Nasz salon</strong>
                <span>ul. Przykładowa 15</span>
                <small>00-001 Warszawa</small>
            </div>
        </div>

        <div class="ci-map">
            <div class="ci-map-pin">
                <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="3"/></svg>
                <span>ul. Przykładowa 15, Warszawa</span>
            </div>
        </div>

        <div class="ci-hours">
            <h3>
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Godziny otwarcia
            </h3>
            <div class="ci-hours-row"><span class="day">Poniedziałek – Piątek</span><span class="time">9:00 – 18:00</span></div>
            <div class="ci-hours-row"><span class="day">Sobota</span><span class="time">10:00 – 14:00</span></div>
            <div class="ci-hours-row"><span class="day">Niedziela</span><span class="closed">— zamknięte</span></div>
        </div>
    </div>
</div>


<section class="contact-strip">
    <div class="contact-strip-in">
        <h2>Wolisz porozmawiać?</h2>
        <p>Dzwoń bezpośrednio — nasz konsultant chętnie odpowie na wszystkie pytania.</p>
        <a href="tel:+48123456789">
            <svg viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg>
            +48 123 456 789
        </a>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/pages/contact.blade.php ENDPATH**/ ?>