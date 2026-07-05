<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Services\WalletService;
use App\Services\CartService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo accounts for evaluators. All passwords: "password123".
 *
 *   admin    / admin@seapedia.test    -> Admin only
 *   seller1  / seller1@seapedia.test  -> Seller only (has a store + products)
 *   buyer1   / buyer1@seapedia.test   -> Buyer only (has wallet balance)
 *   driver1  / driver1@seapedia.test  -> Driver only
 *   multi1   / multi1@seapedia.test   -> Buyer + Seller (must choose active role after login)
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        $makeUser = function (string $username, string $email, array $roleNames, string $activeRole = null) use ($roles) {
            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name' => ucfirst($username),
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'active_role' => $activeRole,
                ]
            );

            $user->roles()->syncWithoutDetaching(
                collect($roleNames)->map(fn ($r) => $roles[$r])->all()
            );

            WalletService::ensureWallet($user);
            if (in_array('Buyer', $roleNames, true)) {
                CartService::ensureCart($user);
            }

            return $user;
        };

        $admin = $makeUser('admin', 'admin@seapedia.test', ['Admin'], 'Admin');

        $seller1 = $makeUser('seller1', 'seller1@seapedia.test', ['Seller'], 'Seller');
        $buyer1 = $makeUser('buyer1', 'buyer1@seapedia.test', ['Buyer'], 'Buyer');
        $driver1 = $makeUser('driver1', 'driver1@seapedia.test', ['Driver'], 'Driver');
        $multi1 = $makeUser('multi1', 'multi1@seapedia.test', ['Buyer', 'Seller']);

        // Give buyer accounts starting balance so checkout can be demoed immediately.
        foreach ([$buyer1, $multi1] as $buyer) {
            WalletService::ensureWallet($buyer);

            if ((float) $buyer->wallet->balance === 0.0) {
                WalletService::credit($buyer, 5000000, 'topup', 'Saldo awal demo seeder');
            }
        }

        // Seller store + demo products.
        $store = Store::firstOrCreate(
            ['user_id' => $seller1->id],
            ['name' => 'Toko Lari Cepat', 'description' => 'Perlengkapan olahraga & lifestyle.']
        );

        if ($store->products()->count() === 0) {
            $store->products()->createMany([
                ['name' => 'Sepatu Lari Mekanik', 'description' => 'Ringan dan responsif untuk lari harian.', 'price' => 450000, 'stock' => 25],
                ['name' => 'Kaos Dry-Fit', 'description' => 'Menyerap keringat, adem dipakai.', 'price' => 120000, 'stock' => 60],
                ['name' => 'Botol Minum 1L', 'description' => 'Botol olahraga BPA-free.', 'price' => 65000, 'stock' => 40],
                ['name' => 'Tas Gym Mini', 'description' => 'Ringkas untuk membawa perlengkapan gym.', 'price' => 95000, 'stock' => 35],
                ['name' => 'Headlamp Outdoor', 'description' => 'Cahaya terang untuk hiking dan malam hari.', 'price' => 140000, 'stock' => 20],
            ]);
        }

        // Second store for multi-role account so single-store-checkout can be demoed
        // (buyer1 can try adding from two different stores).
        if ($multi1->hasRole('Seller')) {
            $store2 = Store::firstOrCreate(
                ['user_id' => $multi1->id],
                ['name' => 'TechGear ID', 'description' => 'Peralatan teknologi & gadget.']
            );

            if ($store2->products()->count() === 0) {
                $store2->products()->createMany([
                    ['name' => 'Keyboard Mekanikal', 'description' => 'Switch blue, RGB backlight.', 'price' => 850000, 'stock' => 15],
                    ['name' => 'Mouse Wireless', 'description' => 'Baterai tahan 3 bulan.', 'price' => 210000, 'stock' => 30],
                ]);
            }
        }
    }
}
