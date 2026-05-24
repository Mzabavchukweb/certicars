<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Temporary diagnostic logging — remove after root cause confirmed
        $user = User::where('email', $credentials['email'])->first();
        Log::warning('admin.login.failed', [
            'email'       => $credentials['email'],
            'user_exists' => $user !== null,
            'hash_ok'     => $user ? Hash::check($credentials['password'], $user->password) : null,
            'is_admin'    => $user?->is_admin,
            'ip'          => $request->ip(),
        ]);

        return back()->withErrors(['email' => 'Nieprawidłowe dane logowania.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
