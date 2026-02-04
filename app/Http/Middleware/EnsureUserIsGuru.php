<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini!
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsGuru
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah yang login menggunakan guard guru
        if (!Auth::guard('guru')->check()) {
            return redirect()->route('login')->with('error', 'Akses khusus Guru.');
        }

        return $next($request);
    }
}