@php
/** @var int $code */
/** @var string $title */
/** @var string $message */
$code    = $code ?? 500;
$title   = $title ?? 'Błąd';
$message = $message ?? 'Coś poszło nie tak.';
$icon    = $icon ?? 'alert-triangle';
$color   = $color ?? '#ef4444';
@endphp
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — CertiCars</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#fafafb;color:#0f0f10;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;-webkit-font-smoothing:antialiased}
        .err{background:#fff;border:1px solid #eeeef0;border-radius:20px;padding:48px 40px;max-width:520px;width:100%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.04)}
        .err-code{font-size:88px;font-weight:900;letter-spacing:-.04em;line-height:1;color:{{ $color }};margin-bottom:12px}
        .err-icon{width:56px;height:56px;border-radius:50%;background:{{ $color }}15;color:{{ $color }};display:flex;align-items:center;justify-content:center;margin:0 auto 18px}
        .err-icon svg{width:26px;height:26px}
        h1{font-size:22px;font-weight:800;letter-spacing:-.02em;margin-bottom:10px}
        p{font-size:14.5px;color:#4b5563;line-height:1.6;margin-bottom:26px}
        .btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:12px 22px;border-radius:50px;font-size:13.5px;font-weight:600;text-decoration:none;transition:all .15s;border:1px solid transparent}
        .btn-primary{background:#0066ff;color:#fff;box-shadow:0 4px 14px rgba(0,102,255,.35)}
        .btn-primary:hover{background:#0052cc;transform:translateY(-1px)}
        .btn-outline{background:#fff;border-color:#e5e5e7;color:#0f0f10}
        .btn-outline:hover{background:#f7f7f8;border-color:#0066ff}
        .btn svg{width:15px;height:15px}
        .hint{font-size:12px;color:#9ca3af;margin-top:22px}
        .hint a{color:#0066ff;font-weight:600;text-decoration:none}
    </style>
</head>
<body>
<div class="err" role="main">
    <div class="err-icon">
        <x-icon :name="$iconName ?? 'alert-circle'" size="38" :strokeWidth="2.2"/>
    </div>
    <div class="err-code">{{ $code }}</div>
    <h1>{{ $title }}</h1>
    <p>{{ $message }}</p>
    <div class="btns">
        <a href="{{ url('/') }}" class="btn btn-primary">
            <x-icon name="home" size="15" :strokeWidth="2.4"/>
            Strona główna
        </a>
        <a href="{{ url('/samochody') }}" class="btn btn-outline">
            <x-icon name="car" size="15" :strokeWidth="2.4"/>
            Oferta
        </a>
    </div>
    <div class="hint">Jeśli problem się powtarza, <a href="{{ url('/kontakt') }}">skontaktuj się z nami</a>.</div>
</div>
</body>
</html>
