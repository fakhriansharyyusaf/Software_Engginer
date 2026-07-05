<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|alpha_dash|min:3|max:30|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['username'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $defaultRoleIds = Role::whereIn('name', ['Buyer', 'Seller'])->pluck('id');
        $user->roles()->attach($defaultRoleIds);

        WalletService::ensureWallet($user);
        CartService::ensureCart($user);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($data)) {
            return response()->json(['message' => 'Kredensial tidak valid.'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles'),
            'roles' => $user->roles->pluck('name'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('roles', 'wallet', 'store');

        return response()->json([
            'user' => $user,
            'roles' => $user->roles->pluck('name'),
            'active_role' => $user->active_role,
        ]);
    }

    public function setActiveRole(Request $request)
    {
        $data = $request->validate(['role' => 'required|string']);
        $user = $request->user();

        if (! $user->roles->contains('name', $data['role'])) {
            return response()->json(['message' => 'Anda tidak memiliki peran tersebut.'], 422);
        }

        $user->forceFill(['active_role' => $data['role']])->save();

        return response()->json(['message' => 'Active role diperbarui.', 'active_role' => $data['role']]);
    }
}
