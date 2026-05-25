<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie — CertiCars</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',system-ui,sans-serif;background:#f7f7f8;color:#0a0a0a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .login-box{background:#fff;border-radius:16px;padding:40px 36px;width:100%;max-width:420px;box-shadow:0 8px 32px rgba(0,0,0,.08);border:1px solid #eeeef0}
        .login-logo{display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:8px}
        .login-logo .ic{width:38px;height:38px;background:#0066ff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff}
        .login-logo .ic i{width:20px;height:20px;stroke-width:2.5}
        .login-logo .tx{font-size:22px;font-weight:800;letter-spacing:-.4px}
        .login-logo .tx span{color:#0066ff}
        .login-box h1{text-align:center;font-size:20px;font-weight:700;margin:18px 0 6px;letter-spacing:-.3px}
        .login-box p.sub{text-align:center;color:#555;font-size:14px;margin-bottom:28px}
        .field{margin-bottom:16px}
        .field label{display:block;font-size:12.5px;font-weight:600;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.3px}
        .field input{width:100%;padding:12px 14px;border:1px solid #e5e5e7;border-radius:10px;font-size:14px;font-family:inherit;transition:border-color .15s,box-shadow .15s}
        .field input:focus{outline:none;border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.1)}
        .pw-wrap{position:relative}
        .pw-wrap input{padding-right:44px}
        .pw-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:4px;cursor:pointer;color:#888;display:flex;align-items:center;justify-content:center;border-radius:6px;transition:color .15s}
        .pw-toggle:hover{color:#0066ff}
        .pw-toggle:focus-visible{outline:2px solid #0066ff;outline-offset:2px}
        .pw-toggle i{width:16px;height:16px}
        .field-check{display:flex;align-items:center;gap:8px;font-size:13px;color:#555;margin-bottom:20px}
        .btn-submit{width:100%;padding:13px;background:#0066ff;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;justify-content:center;gap:7px}
        .btn-submit:hover{background:#0052cc}
        .btn-submit i{width:16px;height:16px}
        .error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
        .error i{width:15px;height:15px}
        .back{display:block;text-align:center;margin-top:20px;font-size:13px;color:#555;text-decoration:none}
        .back:hover{color:#0066ff}
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <span class="ic"><i data-lucide="check"></i></span>
            <span class="tx">Certi<span>Cars</span></span>
        </div>
        <h1>Panel administracyjny</h1>
        <p class="sub">Zaloguj się do zarządzania ofertą</p>

        @if(session('status'))
        <div class="error" style="background:#ecfdf5;border-color:#a7f3d0;color:#047857"><i data-lucide="check-circle"></i> {{ session('status') }}</div>
        @endif
        @if($errors->any())
        <div class="error"><i data-lucide="alert-circle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Hasło</span>
                    <a href="{{ route('password.request') }}" style="color:#0066ff;font-weight:600;text-transform:none;letter-spacing:0;font-size:12px;text-decoration:none">Nie pamiętam</a>
                </label>
                <div class="pw-wrap">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="pw-toggle" id="pwToggle" aria-label="Pokaż hasło">
                        <i data-lucide="eye" id="pwIcon"></i>
                    </button>
                </div>
            </div>
            <label class="field-check">
                <input type="checkbox" name="remember" value="1"> Zapamiętaj mnie
            </label>
            <button type="submit" class="btn-submit"><i data-lucide="log-in"></i> Zaloguj się</button>
        </form>
        <a href="{{ route('home') }}" class="back">← Wróć do strony głównej</a>
    </div>
    <script>
    window.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();

        const btn = document.getElementById('pwToggle');
        const input = document.getElementById('password');
        const icon = document.getElementById('pwIcon');
        if (btn && input && icon) {
            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-label', show ? 'Ukryj hasło' : 'Pokaż hasło');
                icon.setAttribute('data-lucide', show ? 'eye-off' : 'eye');
                lucide.createIcons();
            });
        }
    });
    </script>
</body>
</html>
