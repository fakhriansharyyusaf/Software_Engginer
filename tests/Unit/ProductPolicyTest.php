<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function seller_can_update_products_for_their_own_store_even_without_active_role_selected(): void
    {
        $sellerRole = Role::create(['name' => 'Seller']);
        $user = User::factory()->create([
            'username' => 'seller-test',
            'active_role' => null,
        ]);
        $user->roles()->attach($sellerRole->id);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Toko Uji',
            'description' => 'Deskripsi toko',
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'name' => 'Produk Uji',
            'description' => 'Deskripsi produk',
            'price' => 100000,
            'stock' => 10,
        ]);

        $policy = new \App\Policies\ProductPolicy();

        $this->assertTrue($policy->update($user, $product));
    }
}
