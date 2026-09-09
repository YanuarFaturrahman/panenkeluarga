<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && in_array($user->peran, ['petani', 'koordinator'])) {
            if ($user->status_verifikasi === 'pending') {
                return redirect()->route('menunggu-verifikasi');
            }

            if ($user->status_verifikasi === 'ditolak') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'identitas' => 'Pendaftaran akun Anda ditolak oleh Admin.',
                ]);
            }
        }

        return $next($request);
    }
}