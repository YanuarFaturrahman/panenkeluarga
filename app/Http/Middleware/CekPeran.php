<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $userPeran = strtolower($user->peran ?? $user->role ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userPeran, $allowedRoles)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}