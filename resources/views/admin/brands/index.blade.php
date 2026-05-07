@extends('admin.layouts.app')
@section('title','Marki')
@section('content')
<div style="display:grid;grid-template-columns:1fr 360px;gap:20px">
    <div class="card">
        <h2>Lista marek</h2>
        @if($brands->count())
        <table class="data-table">
            <thead><tr><th>Nazwa</th><th>Aut</th><th></th></tr></thead>
            <tbody>
            @foreach($brands as $b)
            <tr>
                <td>
                    <form method="POST" action="{{ route('admin.brands.update',$b) }}" style="display:flex;gap:8px">@csrf @method('PATCH')
                        <input type="text" name="name" value="{{ $b->name }}" style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                        <button type="submit" class="btn btn-outline btn-sm"><i data-lucide="save"></i></button>
                    </form>
                </td>
                <td><strong>{{ $b->cars_count }}</strong></td>
                <td style="text-align:right">
                    <form method="POST" action="{{ route('admin.brands.destroy',$b) }}" style="display:inline" data-confirm="Usunąć markę {{ $b->name }}?" data-confirm-title="Usunąć markę" data-confirm-ok="Usuń">@csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ghost-red" data-no-loading><i data-lucide="trash-2"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;display:flex;justify-content:center">{{ $brands->links('pagination.custom') }}</div>
        @else
        <p style="color:var(--text-3);text-align:center;padding:28px">Brak marek</p>
        @endif
    </div>
    <div class="card">
        <h2>Dodaj markę</h2>
        <form method="POST" action="{{ route('admin.brands.store') }}">@csrf
            <div class="field"><label>Nazwa marki *</label><input type="text" name="name" required></div>
            <button type="submit" class="btn btn-blue" style="width:100%;justify-content:center"><i data-lucide="plus"></i> Dodaj</button>
        </form>
    </div>
</div>
@endsection
