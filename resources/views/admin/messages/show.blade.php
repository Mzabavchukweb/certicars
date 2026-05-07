@extends('admin.layouts.app')
@section('title','Wiadomość')
@section('crumbs')<a href="{{ route('admin.messages.index') }}">Wiadomości</a> / #{{ $message->id }}@endsection
@section('actions')
<a href="{{ route('admin.messages.index') }}" class="btn btn-outline"><i data-lucide="arrow-left"></i> Wróć</a>
<form method="POST" action="{{ route('admin.messages.unread',$message) }}" style="display:inline">@csrf @method('PATCH')
    <button type="submit" class="btn btn-outline"><i data-lucide="mail"></i> Oznacz jako nieprzeczytane</button>
</form>
<form method="POST" action="{{ route('admin.messages.destroy',$message) }}" style="display:inline" data-confirm="Usunąć tę wiadomość?" data-confirm-title="Usunąć wiadomość" data-confirm-ok="Usuń">@csrf @method('DELETE')
    <button type="submit" class="btn btn-red"><i data-lucide="trash-2"></i> Usuń</button>
</form>
@endsection
@section('content')
<div style="display:grid;grid-template-columns:1fr 320px;gap:20px">
    <div class="card">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid var(--border-l)">
            <div style="width:48px;height:48px;background:var(--blue-bg);color:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px">{{ strtoupper(substr($message->name,0,1)) }}</div>
            <div>
                <div style="font-size:17px;font-weight:700">{{ $message->name }}</div>
                <div style="font-size:12.5px;color:var(--text-3)">{{ $message->created_at->format('d.m.Y H:i') }} · {{ $message->created_at->diffForHumans() }}</div>
            </div>
        </div>
        <div style="font-size:14.5px;line-height:1.7;white-space:pre-wrap;color:var(--text)">{{ $message->message }}</div>
    </div>

    <div>
        <div class="card">
            <h2>Kontakt</h2>
            <div style="margin-bottom:12px">
                <div style="font-size:11px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Email</div>
                <a href="mailto:{{ $message->email }}" style="color:var(--blue);font-weight:600;font-size:13.5px;word-break:break-all">{{ $message->email }}</a>
            </div>
            @if($message->phone)
            <div style="margin-bottom:12px">
                <div style="font-size:11px;color:var(--text-3);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Telefon</div>
                <a href="tel:{{ $message->phone }}" style="color:var(--text);font-weight:600;font-size:13.5px">{{ $message->phone }}</a>
            </div>
            @endif
            <a href="mailto:{{ $message->email }}?subject=Re: Twoja wiadomość do CertiCars" class="btn btn-blue" style="width:100%;justify-content:center"><i data-lucide="reply"></i> Odpowiedz e-mailem</a>
        </div>
        <div class="card">
            <h2>Metadane</h2>
            <dl style="font-size:12.5px">
                @if($message->ip)<dt style="color:var(--text-3);margin-bottom:2px">IP</dt><dd style="margin-bottom:10px;font-family:monospace">{{ $message->ip }}</dd>@endif
                @if($message->user_agent)<dt style="color:var(--text-3);margin-bottom:2px">User-Agent</dt><dd style="margin-bottom:10px;font-size:11.5px;word-break:break-all;color:var(--text-2)">{{ $message->user_agent }}</dd>@endif
                <dt style="color:var(--text-3);margin-bottom:2px">Status</dt>
                <dd>@if($message->read_at)<span class="badge-pill pill-gray">Przeczytane {{ $message->read_at->diffForHumans() }}</span>@else<span class="badge-pill pill-blue">Nieprzeczytane</span>@endif</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
