<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckActiveRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $activeRole = session('active_role') ?? Auth::user()?->active_role;

        // 2. Jika user belum memilih peran aktif, tetapi user punya role Seller/Buyer/Driver/Admin
        //    maka izinkan akses menggunakan role yang tersimpan di database.
        if (!$activeRole) {
            $user = Auth::user();
            if ($user && $user->roles()->whereIn('name', $roles)->exists()) {
                $user->setActiveRole($roles[0]);
                $activeRole = $roles[0];
            } else {
                return redirect()->route('role.select');
            }
        }

        // 3. Jika rute ini butuh peran spesifik, cek di sini.
        if (!empty($roles) && !in_array($activeRole, $roles)) {
            abort(403, 'Akses ditolak. Peran aktif Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}