<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Redirect berdasarkan role
        switch ($user->role) {
            case 1:
                return redirect()->route('superadmin.dashboard');
            case 2:
                return redirect()->route('kepalaro.home');
            case 3:
                return redirect()->route('kepalagudang.dashboard');
            default: // user biasa
                return redirect()->route('home');
        }
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}