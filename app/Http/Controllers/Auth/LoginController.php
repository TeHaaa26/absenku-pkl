<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'NIP atau Email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Cek apakah login dengan email atau NIP
        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';
        
        $authCredentials = [
            $fieldType => $credentials['login'],
            'password' => $credentials['password'],
            'status' => 'aktif', // Hanya user aktif yang bisa login
        ];

        if (Auth::attempt($authCredentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'login' => 'NIP/Email atau password salah, atau akun tidak aktif.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }

    private function redirectBasedOnRole()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('guru.dashboard');
    }
}