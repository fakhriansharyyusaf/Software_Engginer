<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class DashboardDataService
{
    public function getDashboardViewModel(): array
    {
        $user = Auth::user()?->load(['roles', 'store', 'wallet']);

        if (! $user) {
            return [
                'user' => null,
                'activeRole' => null,
                'roles' => collect(),
                'walletBalance' => 0,
            ];
        }

        return [
            'user' => $user,
            'activeRole' => $user->currentActiveRole(),
            'roles' => $user->roles,
            'walletBalance' => $user->wallet->balance ?? 0,
        ];
    }
}
