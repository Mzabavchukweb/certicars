@extends('admin.layouts.app')
@section('title','Wiadomości')
@php
$activeFilters = array_filter([
    'q'      => request('q') ? ['label' => 'Szukaj', 'val' => request('q')] : null,
    'filter' => request('filter') && request('filter') !== 'spam' ? ['label' => 'Filtr', 'val' => ['unread'=>'Nieprzeczytane','read'=>'Przeczytane'][request('filter')] ?? request('filter')] : null,
]);
$chipUrl = fn($remove) => route('admin.messages.index', request()->except($remove));
@endphp
@section('content')
<div class="card">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <a href="{{ route('admin.messages.index') }}" class="btn btn-sm {{ $spamTab ? 'btn-outline' : 'btn-dark' }}"><i data-lucide="inbox"></i> Skrzynka ({{ $inboxCount }})</a>
        <a href="{{ route('admin.messages.index', ['filter' => 'spam']) }}" class="btn btn-sm {{ $spamTab ? 'btn-dark' : 'btn-outline' }}"><i data-lucide="shield-alert"></i> Spam ({{ $spamCount }})</a>
        @if(!$spamTab)
        <form method="POST" action="{{ route('admin.messages.spam.sweep') }}" style="margin-left:auto" data-confirm="Przejrzeć wiadomości w skrzynce i przenieść spam do osobnej zakładki? Nic nie zostanie skasowane." data-confirm-title="Przejrzeć skrzynkę" data-confirm-ok="Przejrzyj">@csrf
            <button type="submit" class="btn btn-sm btn-outline" data-no-loading><i data-lucide="filter"></i> Przejrzyj skrzynkę pod kątem spamu</button>
        </form>
        @endif
        @if($spamTab && $spamCount)
        <form method="POST" action="{{ route('admin.messages.spam.clear') }}" style="margin-left:auto" data-confirm="Usunąć wszystkie {{ $spamCount }} wiadomości oznaczonych jako spam?" data-confirm-title="Wyczyścić spam" data-confirm-ok="Usuń wszystko">@csrf
            <button type="submit" class="btn btn-sm btn-ghost-red" data-no-loading><i data-lucide="trash-2"></i> Usuń cały spam</button>
        </form>
        @endif
    </div>
    @if($spamTab)
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12.5px;color:#9a3412">
        Te wiadomości zostały uznane za spam i nie trafiły do skrzynki ani do licznika nieprzeczytanych. Jeśli któraś jest prawdziwa, kliknij <b>„To nie spam”</b> — wróci do skrzynki.
    </div>
    @endif

    <form method="GET" style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Szukaj: imię, email, telefon, treść" style="flex:1;min-width:240px;padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px">
        @if($spamTab)<input type="hidden" name="filter" value="spam">@endif
        <select name="filter" style="padding:10px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:#fff;{{ $spamTab ? 'display:none' : '' }}">
            <option value="">Wszystkie</option>
            <option value="unread" {{ request('filter')=='unread'?'selected':'' }}>Nieprzeczytane</option>
            <option value="read" {{ request('filter')=='read'?'selected':'' }}>Przeczytane</option>
        </select>
        <button type="submit" class="btn btn-dark" data-no-loading><i data-lucide="search"></i> Szukaj</button>
    </form>

    @if(count($activeFilters))
    <div class="filter-chips">
        @foreach($activeFilters as $key => $f)
            <span class="filter-chip"><span class="k">{{ $f['label'] }}:</span> {{ $f['val'] }} <a href="{{ $chipUrl($key) }}"><i data-lucide="x"></i></a></span>
        @endforeach
        <a href="{{ route('admin.messages.index') }}" class="filter-chip-clear">Wyczyść wszystkie</a>
    </div>
    @endif

    <div class="bulk-bar" id="bulkBar">
        <span><span class="count" id="bulkCount">0</span> zaznaczonych</span>
        <span class="sep">|</span>
        <form method="POST" action="{{ route('admin.messages.bulk') }}" style="display:inline-flex;gap:6px" id="bulkForm">@csrf
            <input type="hidden" name="action" id="bulkAction">
            <div id="bulkIdsContainer"></div>
            <button type="button" onclick="submitBulk('read')"><i data-lucide="mail-open"></i> Oznacz jako przeczytane</button>
            <button type="button" onclick="submitBulk('unread')"><i data-lucide="mail"></i> Nieprzeczytane</button>
            @if($spamTab)
            <button type="button" onclick="submitBulk('not_spam')"><i data-lucide="shield-check"></i> To nie spam</button>
            @else
            <button type="button" onclick="submitBulk('spam')"><i data-lucide="shield-alert"></i> To spam</button>
            @endif
            <button type="button" onclick="confirmBulkDelete()" style="background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.3)"><i data-lucide="trash-2"></i> Usuń</button>
        </form>
        <button class="close" type="button" onclick="clearSelection()"><i data-lucide="x"></i></button>
    </div>

    @if($messages->count())
    <table class="data-table responsive">
        <thead>
            <tr>
                <th style="width:28px"><input type="checkbox" id="selectAll"></th>
                <th style="width:30px"></th>
                <th>Od</th>
                <th>Wiadomość</th>
                <th>Kontakt</th>
                <th>Data</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($messages as $m)
        <tr style="{{ $m->is_read ? '' : 'font-weight:600' }}">
            <td><input type="checkbox" class="row-check" value="{{ $m->id }}"></td>
            <td>@if(!$m->is_read)<span title="Nieprzeczytane" style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--blue)"></span>@endif</td>
            <td><a href="{{ route('admin.messages.show',$m) }}"><strong>{{ $m->name }}</strong></a></td>
            <td style="max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><a href="{{ route('admin.messages.show',$m) }}" style="color:var(--text-2)">{{ \Illuminate\Support\Str::limit($m->message, 80) }}</a></td>
            <td style="font-size:12px;color:var(--text-2)">
                <a href="mailto:{{ $m->email }}" style="color:var(--blue)">{{ $m->email }}</a>
                @if($m->phone)<br><a href="tel:{{ $m->phone }}" style="color:var(--text-3)">{{ $m->phone }}</a>@endif
            </td>
            <td style="color:var(--text-3);font-size:12px">
                {{ $m->created_at->diffForHumans() }}
                @if($m->is_spam && $m->spam_reasons)
                <div style="margin-top:4px;color:#b45309;font-size:11px;max-width:220px;white-space:normal" title="{{ implode(' · ', $m->spam_reasons) }}">{{ \Illuminate\Support\Str::limit($m->spam_reasons[0] ?? '', 46) }}</div>
                @endif
            </td>
            <td style="text-align:right;white-space:nowrap">
                @if($m->is_spam)
                <form method="POST" action="{{ route('admin.messages.not-spam',$m) }}" style="display:inline">@csrf
                    <button type="submit" class="btn btn-outline btn-sm" title="To nie spam — wróć do skrzynki" data-no-loading><i data-lucide="shield-check"></i></button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.messages.spam',$m) }}" style="display:inline">@csrf
                    <button type="submit" class="btn btn-outline btn-sm" title="Oznacz jako spam" data-no-loading><i data-lucide="shield-alert"></i></button>
                </form>
                @endif
                <a href="{{ route('admin.messages.show',$m) }}" class="btn btn-outline btn-sm"><i data-lucide="eye"></i></a>
                <form method="POST" action="{{ route('admin.messages.destroy',$m) }}" style="display:inline" data-confirm="Usunąć tę wiadomość?" data-confirm-title="Usunąć wiadomość" data-confirm-ok="Usuń">@csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-ghost-red" data-no-loading><i data-lucide="trash-2"></i></button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Mobile cards --}}
    <div class="m-card">
        @foreach($messages as $m)
        <a href="{{ route('admin.messages.show',$m) }}" style="display:block;background:#fff;border:1px solid var(--border-l);border-radius:12px;padding:14px;margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;margin-bottom:4px;gap:8px">
                <strong style="font-size:13.5px;{{ !$m->is_read ? '' : 'font-weight:500' }}">
                    @if(!$m->is_read)<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--blue);margin-right:6px"></span>@endif
                    {{ $m->name }}
                </strong>
                <span style="font-size:11px;color:var(--text-3)">{{ $m->created_at->diffForHumans(null, true) }}</span>
            </div>
            <div style="font-size:12px;color:var(--text-3);margin-bottom:6px">{{ $m->email }}</div>
            <div style="font-size:12.5px;color:var(--text-2);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $m->message }}</div>
        </a>
        @endforeach
    </div>

    <div style="margin-top:20px;display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:var(--text-3);flex-wrap:wrap;gap:10px">
        <span>Razem: <strong style="color:var(--text)">{{ $messages->total() }}</strong></span>
        {{ $messages->links('pagination.custom') }}
    </div>
    @else
    <div class="empty-state">
        <div class="ic"><i data-lucide="inbox"></i></div>
        <h3>{{ count($activeFilters) ? 'Brak wyników' : 'Skrzynka jest pusta' }}</h3>
        <p>
            @if(count($activeFilters))
                Zmień filtry lub <a href="{{ route('admin.messages.index') }}" style="color:var(--blue);font-weight:600">wyczyść je</a>.
            @else
                Wiadomości z formularza kontaktowego pojawią się tutaj.
            @endif
        </p>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function(){
    const bar=document.getElementById('bulkBar'),cnt=document.getElementById('bulkCount'),all=document.getElementById('selectAll');
    const ids=()=>[...document.querySelectorAll('.row-check:checked')].map(c=>c.value);
    function refresh(){const s=ids();cnt.textContent=s.length;bar.classList.toggle('active',s.length>0)}
    all?.addEventListener('change',e=>{document.querySelectorAll('.row-check').forEach(c=>c.checked=e.target.checked);refresh()});
    document.querySelectorAll('.row-check').forEach(c=>c.addEventListener('change',refresh));
    window.clearSelection=()=>{document.querySelectorAll('.row-check,#selectAll').forEach(c=>c.checked=false);refresh()};
    window.submitBulk=(action)=>{
        const s=ids();if(!s.length)return;
        document.getElementById('bulkAction').value=action;
        document.getElementById('bulkIdsContainer').innerHTML=s.map(id=>`<input type="hidden" name="ids[]" value="${id}">`).join('');
        document.getElementById('bulkForm').submit();
    };
    window.confirmBulkDelete=async()=>{
        const s=ids();if(!s.length)return;
        const ok=await confirmAction('Usunąć '+s.length+' wiadomości?','Tej akcji nie można cofnąć.','Usuń '+s.length);
        if(ok)submitBulk('delete');
    };
})();
</script>
@endpush
@endsection
