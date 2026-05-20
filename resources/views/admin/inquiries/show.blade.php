@extends('admin.layouts.app')
@section('title','Zapytanie')
@section('crumbs')<a href="{{ route('admin.inquiries.index') }}">Zapytania</a> / #{{ $inquiry->id }}@endsection
@section('actions')
<a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline"><i data-lucide="arrow-left"></i> Wróć</a>
<form method="POST" action="{{ route('admin.inquiries.unread',$inquiry) }}" style="display:inline">@csrf @method('PATCH')
    <button type="submit" class="btn btn-outline"><i data-lucide="mail"></i> Oznacz jako nieprzeczytane</button>
</form>
<form method="POST" action="{{ route('admin.inquiries.destroy',$inquiry) }}" style="display:inline" data-confirm="Usunąć to zapytanie?" data-confirm-title="Usunąć zapytanie" data-confirm-ok="Usuń">@csrf @method('DELETE')
    <button type="submit" class="btn btn-red"><i data-lucide="trash-2"></i> Usuń</button>
</form>
@endsection
@section('content')
<div style="display:grid;grid-template-columns:1fr 320px;gap:20px">
    <div class="card">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid var(--border-l)">
            <div style="width:48px;height:48px;background:var(--blue-bg);color:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px">{{ strtoupper(substr($inquiry->name,0,1)) }}</div>
            <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <span style="font-size:17px;font-weight:700">{{ $inquiry->name }}</span>
                    @if($inquiry->type === 'financing')
                        <span class="badge-pill pill-blue">Finansowanie</span>
                    @else
                        <span class="badge-pill pill-gray">Wiadomość</span>
                    @endif
                </div>
                <div style="font-size:12.5px;color:var(--text-3)">{{ $inquiry->created_at->format('d.m.Y H:i') }} · {{ $inquiry->created_at->diffForHumans() }}</div>
            </div>
        </div>

        @if($inquiry->car_title)
        <div style="background:var(--bg);border:1px solid var(--border-l);border-radius:10px;padding:14px;margin-bottom:18px;display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;background:var(--blue-bg);color:var(--blue);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i data-lucide="car" style="width:18px;height:18px"></i></div>
            <div style="flex:1;min-width:0">
                <div style="font-size:11px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:2px">Dotyczy samochodu</div>
                <div style="font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    @if($inquiry->car)
                        <a href="{{ route('catalog.show', $inquiry->car->slug) }}" target="_blank" style="color:var(--blue)">{{ $inquiry->car_title }}</a>
                    @else
                        {{ $inquiry->car_title }}
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($inquiry->message)
        <div style="font-size:14.5px;line-height:1.7;white-space:pre-wrap;color:var(--text)">{{ $inquiry->message }}</div>
        @else
        <div style="font-size:13px;color:var(--text-3);font-style:italic">Brak treści wiadomości.</div>
        @endif
    </div>

    <div>
        <div class="card">
            <h2>Kontakt</h2>
            @if($inquiry->phone)
            <div style="margin-bottom:12px">
                <div style="font-size:11px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Telefon</div>
                <a href="tel:{{ $inquiry->phone }}" style="color:var(--text);font-weight:600;font-size:13.5px">{{ $inquiry->phone }}</a>
            </div>
            @endif
            @if($inquiry->email)
            <div style="margin-bottom:12px">
                <div style="font-size:11px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Email</div>
                <a href="mailto:{{ $inquiry->email }}" style="color:var(--blue);font-weight:600;font-size:13.5px;word-break:break-all">{{ $inquiry->email }}</a>
            </div>
            @endif
            @if($inquiry->phone)
            <a href="tel:{{ $inquiry->phone }}" class="btn btn-blue" style="width:100%;justify-content:center;margin-bottom:8px"><i data-lucide="phone"></i> Zadzwoń</a>
            @endif
            @if($inquiry->email)
            <a href="mailto:{{ $inquiry->email }}?subject=Re: Zapytanie — {{ $inquiry->car_title ?? 'CertiCars' }}" class="btn btn-outline" style="width:100%;justify-content:center"><i data-lucide="reply"></i> Odpowiedz e-mailem</a>
            @endif
        </div>
        <div class="card">
            <h2>Metadane</h2>
            <dl style="font-size:12.5px">
                @if($inquiry->ip)<dt style="color:var(--text-3);margin-bottom:2px">IP</dt><dd style="margin-bottom:10px;font-family:monospace">{{ $inquiry->ip }}</dd>@endif
                @if($inquiry->user_agent)<dt style="color:var(--text-3);margin-bottom:2px">User-Agent</dt><dd style="margin-bottom:10px;font-size:11.5px;word-break:break-all;color:var(--text-2)">{{ $inquiry->user_agent }}</dd>@endif
                <dt style="color:var(--text-3);margin-bottom:2px">Status</dt>
                <dd>@if($inquiry->read_at)<span class="badge-pill pill-gray">Przeczytane {{ $inquiry->read_at->diffForHumans() }}</span>@else<span class="badge-pill pill-blue">Nieprzeczytane</span>@endif</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
