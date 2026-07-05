<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Logout & role-switch as plain (non-Livewire) routes so the shared
 * navbar works identically no matter which Livewire full-page
 * component is currently mounted (Seller/Buyer/Driver/Admin/etc. all
 * share this one navbar, but only Dashboard has its own logout()/
 * switchRole() Livewire methods — these routes make the actions
 * available everywhere).
 */
class SessionController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchRole(Request $request, string $role)
    {
        $user = $request->user();

        if ($user->roles->contains('name', $role)) {
            $user->setActiveRole($role);
        }

        return back();
    }
}
