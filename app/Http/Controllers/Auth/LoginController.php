<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // PERBAIKAN: Cek semua guard sebelum menampilkan form login
        // Jika sudah login di salah satu guard, langsung lempar ke dashboard
        if (Auth::guard('web')->check() || Auth::guard('guru')->check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $isEmail = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL);

        // 1. LOGIN WEB (ADMIN/SISWA)
        $field = $isEmail ? 'email' : 'nisn';
        if (Auth::guard('web')->attempt([$field => $credentials['login'], 'password' => $credentials['password'], 'status' => 'aktif'], $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole();
        }

        // 2. LOGIN GURU
        $fieldGuru = $isEmail ? 'email' : 'nip';
        if (Auth::guard('guru')->attempt([$fieldGuru => $credentials['login'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'login' => 'Kredensial tidak cocok atau akun tidak aktif.',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout dari semua kemungkinan guard
        Auth::guard('guru')->logout();
        Auth::guard('web')->logout();

        // Hapus session dan token CSRF secara total
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Tambahkan header untuk mencegah user menekan tombol 'back' setelah logout
        return redirect('/login')
            ->with('success', 'Anda berhasil logout.')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    private function redirectBasedOnRole()
    {
        // Prioritas cek Guard Guru
        if (Auth::guard('guru')->check()) {
            return redirect()->route('guru.dashboard');
        }

        // Cek Guard Web
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('siswa.dashboard');
        }

        return redirect('/login');
    }
}