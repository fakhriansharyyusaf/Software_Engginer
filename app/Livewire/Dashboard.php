<?php

namespace App\Livewire;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchRole(string $roleName)
    {
        $user = Auth::user();

        if ($user->roles->contains('name', $roleName)) {
            $user->setActiveRole($roleName);
        }
    }

    public function render()
    {
        $viewModel = app(DashboardDataService::class)->getDashboardViewModel();

        return view('components.dashboard', $viewModel);
    }
}
