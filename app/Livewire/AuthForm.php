<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Services\WalletService;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthForm extends Component
{
    // Mode form: true = Login, false = Register
    public $isLogin = true; 

    // Properti Form
    public $username;
    public $email;
    public $password;
    public $password_confirmation;

    public function toggleMode()
    {
        $this->isLogin = !$this->isLogin;
        $this->resetErrorBag();
    }

    public function register()
    {
        $this->validate([
            'username' => 'required|alpha_dash|min:3|max:30|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $this->username,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Secara default, mari kita berikan peran 'Buyer' dan 'Seller'
        // agar fitur multi-role bisa langsung dites[cite: 87].
        $defaultRoleIds = Role::whereIn('name', ['Buyer', 'Seller'])->pluck('id');

        if ($defaultRoleIds->isEmpty()) {
            // Role belum di-seed (jalankan: php artisan db:seed --class=RoleSeeder)
            $this->addError('username', 'Peran default belum tersedia. Hubungi administrator.');
            return;
        }

        $user->roles()->attach($defaultRoleIds);

        // Siapkan resource dasar supaya role Buyer langsung bisa dipakai.
        WalletService::ensureWallet($user);
        CartService::ensureCart($user);

        // Langsung login setelah register
        Auth::login($user);

        $token = $user->createToken('web')->plainTextToken;
        session(['api_token' => $token]);

        return $this->handlePostLogin();
    }

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Bersihkan sisa active_role dari sesi & DB sebelumnya (mis. browser dipakai bergantian,
        // atau user sebelumnya login dengan role lain) supaya navbar tidak menampilkan role basi
        // sebelum RoleSelector benar-benar dipakai.
        session()->forget('active_role');

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session()->regenerate();

            $user = Auth::user();
            $token = $user->createToken('web')->plainTextToken;
            session(['api_token' => $token]);

            if ($user->roles->count() > 1) {
                $user->forceFill(['active_role' => null])->save();
            }

            return $this->handlePostLogin();
        }

        $this->addError('username', 'Kredensial tidak valid.');
    }

    private function handlePostLogin()
    {
        $user = Auth::user();
        $roles = $user->roles; // Mengambil semua peran yang dimiliki user [cite: 88]

        // Admin selalu punya 1 peran (Admin) dan langsung masuk dashboard admin.
        if ($roles->count() > 1) {
            // User dengan >1 peran non-admin TIDAK BOLEH masuk dashboard
            // sebelum memilih active role terlebih dahulu.
            return redirect()->route('role.select');
        }

        if ($roles->isEmpty()) {
            $this->addError('username', 'Akun ini belum memiliki peran. Hubungi administrator.');
            Auth::logout();

            return null;
        }

        $user->setActiveRole($roles->first()->name);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('components.auth-form');
    }
}