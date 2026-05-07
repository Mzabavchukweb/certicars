<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required', 'email', 'max:200', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        $user->update($data);
        return back()->with('success', 'Profil zaktualizowany.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages(['current_password' => 'Aktualne hasło jest nieprawidłowe.']);
        }

        $request->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Hasło zmienione.');
    }
}
