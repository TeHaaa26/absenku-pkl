<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Jika guru sudah login, lempar ke dashboard guru
                if ($guard === 'guru') {
                    return redirect()->route('guru.dashboard');
                }

                // Jika admin/siswa sudah login (guard web)
                $user = Auth::guard('web')->user();
                if ($user) {
                    if ($user->role === 'admin') {
                        return redirect()->route('admin.dashboard');
                    }
                    return redirect()->route('siswa.dashboard');
                }
            }
        }

        return $next($request);
    }
}
