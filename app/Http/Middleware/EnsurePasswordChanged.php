<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Redirect authenticated users with must_change_password = true
     * to the mandatory password change page before accessing the app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            if (! $request->routeIs('password.change', 'password.change.update', 'logout')) {
                return redirect()->route('password.change')
                    ->with('warning', 'Demi keamanan akun, Anda wajib mengganti kata sandi sementara sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
