<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API counterpart of CheckActiveRole. Since API requests are stateless
 * (Sanctum token auth, no session), the active role is read from the
 * user row (users.active_role) instead of the session — the SAME value
 * that the web app persists via User::setActiveRole().
 */
class CheckActiveRoleApi
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $user->active_role) {
            return response()->json(['message' => 'Active role belum dipilih. Panggil POST /api/auth/active-role terlebih dahulu.'], 409);
        }

        if (! empty($roles) && ! in_array($user->active_role, $roles, true)) {
            return response()->json(['message' => 'Akses ditolak untuk peran aktif Anda.'], 403);
        }

        return $next($request);
    }
}
