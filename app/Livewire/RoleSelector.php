<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RoleSelector extends Component
{
    // HAPUS baris `public $roles = [];`

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function selectRole(string $roleName)
    {
        // Ambil data langsung dari Auth saat tombol diklik (lebih aman)
        $user = Auth::user();

        if ($user->roles->contains('name', $roleName)) {
            $user->setActiveRole($roleName);
            return redirect()->route('dashboard');
        }

        session()->flash('error', 'Peran tidak valid.');
    }

    public function render()
    {
        // Kirim data ke Blade langsung dari fungsi render
        return view('components.role-selector', [
            'roles' => Auth::user()->roles
        ]);
    }
}