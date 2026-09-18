@extends('admin.layouts.app')
@section('title','Rejestr błędów')
@section('content')
<style>
.el-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px}
.el-head h1{font-size:20px;font-weight:800;margin:0}
.el-sub{font-size:13px;color:var(--text-3,#6b7280);margin-top:4px}
.el-filters{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:14px}
.el-filters a,.el-filters button{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:99px;border:1px solid var(--border,#e5e7eb);background:#fff;font-size:12.5px;font-weight:600;color:var(--text-2,#4b5563);text-decoration:none;cursor:pointer}
.el-filters a.active{background:#0066ff;border-color:#0066ff;color:#fff}
.el-filters input{padding:7px 12px;border:1px solid var(--border,#e5e7eb);border-radius:99px;font-size:13px;min-width:220px}
.el-row{border:1px solid var(--border-l,#eef1f6);border-radius:12px;padding:12px 14px;margin-bottom:8px;background:#fff}
.el-row.level-error{border-left:4px solid #ef4444}
.el-row.level-warning{border-left:4px solid #f59e0b}
.el-row.level-info{border-left:4px solid #3b82f6}
.el-top{display:flex;gap:10px;align-items:baseline;flex-wrap:wrap;font-size:12px;color:var(--text-3,#6b7280)}
.el-src{font-family:ui-monospace,monospace;font-size:11.5px;background:#f3f4f6;padding:2px 7px;border-radius:6px;color:#111}
.el-msg{font-size:14px;font-weight:600;color:#111;margin:6px 0 4px;word-break:break-word}
.el-meta{font-size:12px;color:var(--text-3,#6b7280);word-break:break-all}
.el-ctx{margin-top:8px}
.el-ctx summary{cursor:pointer;font-size:12px;color:#0066ff;font-weight:600}
.el-ctx pre{margin:8px 0 0;background:#0f172a;color:#e2e8f0;font-size:11.5px;padding:10px 12px;border-radius:8px;overflow:auto;max-height:360px;white-space:pre-wrap;word-break:break-word}
.el-empty{padding:40px;text-align:center;color:var(--text-3,#6b7280)}
</style>

<div class="card">
    <div class="el-head">
        <div>
            <h1>Rejestr błędów</h1>
            <div class="el-sub">Każdy wyjątek aplikacji, nieudany zapis auta i nieudane wgranie pliku. Ostatnie 24 h: <b>{{ $last24 }}</b>.</div>
        </div>
        <form method="POST" action="{{ route('admin.errors.clear') }}" onsubmit="return confirm('Wyczyścić cały rejestr?')">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Wyczyść rejestr</button>
        </form>
    </div>

    <form method="GET" class="el-filters">
        <a href="{{ route('admin.errors.index') }}" class="{{ !request('source') && !request('level') ? 'active' : '' }}">Wszystkie</a>
        <a href="{{ route('admin.errors.index', ['level' => 'error']) }}" class="{{ request('level') === 'error' ? 'active' : '' }}">Tylko błędy</a>
        @foreach($sources as $s)
            <a href="{{ route('admin.errors.index', ['source' => $s->source]) }}" class="{{ request('source') === $s->source ? 'active' : '' }}">{{ $s->source }} <span style="opacity:.7">({{ $s->n }})</span></a>
        @endforeach
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Szukaj w treści…">
        <button type="submit">Szukaj</button>
    </form>

    @forelse($logs as $log)
        <div class="el-row level-{{ $log->level }}">
            <div class="el-top">
                <span>{{ $log->created_at->format('d.m.Y H:i:s') }}</span>
                <span class="el-src">{{ $log->source }}</span>
                @if($log->user) <span>{{ $log->user->email }}</span> @endif
                @if($log->car_id) <a href="{{ route('admin.cars.edit', $log->car_id) }}" style="color:#0066ff">auto #{{ $log->car_id }}</a> @endif
            </div>
            <div class="el-msg">{{ $log->message }}</div>
            @if($log->url)
                <div class="el-meta">{{ $log->method }} {{ $log->url }}</div>
            @endif
            @if($log->context)
                <details class="el-ctx">
                    <summary>Szczegóły</summary>
                    <pre>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                </details>
            @endif
        </div>
    @empty
        <div class="el-empty">Brak wpisów — żadnych błędów nie odnotowano.</div>
    @endforelse

    <div style="margin-top:14px">{{ $logs->links() }}</div>
</div>
@endsection
