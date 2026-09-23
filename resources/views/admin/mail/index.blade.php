@extends('admin.layouts.app')
@section('title','Poczta')
@section('content')
<style>
.ml-card{background:#fff;border:1px solid var(--border-l,#eef1f6);border-radius:14px;padding:18px 20px;margin-bottom:14px}
.ml-card h2{font-size:15.5px;font-weight:800;margin:0 0 4px}
.ml-card .sub{font-size:12.5px;color:var(--text-3,#6b7280);margin-bottom:14px}
.ml-row{display:flex;gap:12px;padding:9px 0;border-bottom:1px solid #f1f4f8;font-size:13px;align-items:flex-start}
.ml-row:last-child{border-bottom:none}
.ml-row .k{flex:0 0 40%;color:var(--text-2,#4b5563);font-weight:600}
.ml-row .v{flex:1;word-break:break-word;font-family:ui-monospace,monospace;font-size:12px}
.ml-ok{color:#059669;font-weight:700}
.ml-bad{color:#dc2626;font-weight:700}
.ml-warn{color:#b45309;font-weight:700}
.ml-verdict{border-radius:10px;padding:12px 14px;font-size:13.5px;line-height:1.5;margin-bottom:12px}
.ml-verdict.ok{background:#f0fdf6;border:1px solid #a7f3d0;color:#065f46}
.ml-verdict.bad{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.ml-verdict.unknown{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.ml-steps{background:#0f172a;color:#e2e8f0;border-radius:8px;padding:10px 12px;font-family:ui-monospace,monospace;font-size:11.5px;line-height:1.55;overflow:auto;white-space:pre-wrap;margin-top:10px}
.ml-form{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.ml-form input{flex:1;min-width:240px;padding:10px 14px;border:1px solid var(--border,#e5e7eb);border-radius:9px;font-size:13px}
</style>

<div class="ml-card">
    <h2>Wysyłka ze strony (powiadomienia o zapytaniach)</h2>
    <div class="sub">Tak serwer strony ma ustawioną pocztę wychodzącą. „Tryb: log” oznacza, że maile nigdzie nie idą — zapisują się tylko do pliku.</div>
    @foreach($cfg as $k => $v)
        <div class="ml-row">
            <span class="k">{{ ucfirst($k) }}</span>
            <span class="v">
                @if($k === 'mailer' && $v === 'log')<span class="ml-bad">log — maile NIE są wysyłane</span>
                @elseif($k === 'haslo' && $v === 'BRAK')<span class="ml-bad">BRAK</span>
                @else{{ $v ?: '—' }}@endif
            </span>
        </div>
    @endforeach

    <div style="margin-top:16px">
        <form method="POST" action="{{ route('admin.mail.send') }}" class="ml-form">@csrf
            <input type="email" name="to" value="{{ auth()->user()->email }}" placeholder="adres e-mail do testu" required>
            <button type="submit" class="btn btn-dark btn-sm"><i data-lucide="send"></i> Wyślij testową wiadomość</button>
        </form>
    </div>
</div>

<div class="ml-card">
    <h2>Odbiór poczty na {{ $mailbox }}</h2>
    <div class="sub">Rozmowa z serwerem pocztowym z rekordu MX. Żadna wiadomość nie jest wysyłana — sprawdzamy tylko, czy serwer przyjmuje ten adres.</div>
    <div class="ml-verdict {{ $przyjmuje['ok'] === true ? 'ok' : ($przyjmuje['ok'] === false ? 'bad' : 'unknown') }}">
        {{ $przyjmuje['wniosek'] }}
    </div>
    @if(!empty($przyjmuje['kroki']))
    <details>
        <summary style="cursor:pointer;font-size:12.5px;color:#0066ff;font-weight:600">Pokaż rozmowę z serwerem</summary>
        <div class="ml-steps">@foreach($przyjmuje['kroki'] as $s)&gt; {{ $s['cmd'] }}
{{ $s['resp'] }}

@endforeach</div>
    </details>
    @endif
</div>

<div class="ml-card">
    <h2>Wpisy DNS domeny</h2>
    <div class="sub">MX decyduje o odbiorze, SPF i DKIM o tym, czy wysyłane wiadomości nie trafiają do spamu.</div>
    @foreach($dns as $k => $v)
        <div class="ml-row">
            <span class="k">{{ $k }}</span>
            <span class="v">@if($v === 'BRAK' || str_starts_with($v, 'BRAK'))<span class="ml-bad">{{ $v }}</span>@else{{ $v }}@endif</span>
        </div>
    @endforeach
</div>

<div class="ml-card">
    <h2>Połączenie z serwerem pocztowym</h2>
    <div class="sub">Czy serwer strony dosięga portów poczty. Zablokowany port 25 na hostingu jest normalny i nie psuje odbioru poczty w skrzynce.</div>
    @foreach($porty as $k => $p)
        <div class="ml-row">
            <span class="k">{{ $k }}</span>
            <span class="v">@if($p['ok'])<span class="ml-ok">OK</span> — {{ $p['info'] }}@else<span class="ml-warn">brak</span> — {{ $p['info'] }}@endif</span>
        </div>
    @endforeach
</div>
@endsection
