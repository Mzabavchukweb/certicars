@extends('admin.layouts.app')
@section('title','Profil')
@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px">
    <div class="card">
        <h2>Dane konta</h2>
        <form method="POST" action="{{ route('admin.profile.update') }}">@csrf @method('PATCH')
            <div class="field">
                <label>Imię i nazwisko</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <button type="submit" class="btn btn-blue"><i data-lucide="save"></i> Zapisz</button>
        </form>
    </div>
    <div class="card">
        <h2>Zmiana hasła</h2>
        <form method="POST" action="{{ route('admin.profile.password') }}">@csrf @method('PATCH')
            <div class="field">
                <label>Aktualne hasło</label>
                <input type="password" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="field">
                <label>Nowe hasło (min. 8 znaków)</label>
                <input type="password" name="password" autocomplete="new-password" required minlength="8">
            </div>
            <div class="field">
                <label>Potwierdź nowe hasło</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-dark"><i data-lucide="key"></i> Zmień hasło</button>
        </form>
    </div>
</div>
@endsection
