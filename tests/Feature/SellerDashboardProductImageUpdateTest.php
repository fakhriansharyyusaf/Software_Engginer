<?php

namespace Tests\Feature;

use App\Livewire\Seller\SellerDashboard;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerDashboardProductImageUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function seller_can_update_product_image_from_dashboard(): void
    {
        Storage::fake('public');

        $sellerRole = Role::create(['name' => 'Seller']);
        $user = User::factory()->create([
            'username' => 'seller-image',
            'active_role' => 'Seller',
        ]);
        $user->roles()->attach($sellerRole->id);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Toko Foto',
            'description' => 'Toko untuk pengujian gambar',
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'name' => 'Produk Lama',
            'description' => 'Deskripsi lama',
            'price' => 100000,
            'stock' => 10,
            'image' => 'products/original.jpg',
        ]);
        Storage::disk('public')->put($product->image, 'original-content');

        Livewire::actingAs($user)
            ->test(SellerDashboard::class)
            ->set('editingProductId', $product->id)
            ->set('productName', 'Produk Baru')
            ->set('productDescription', 'Deskripsi baru')
            ->set('productPrice', 150000)
            ->set('productStock', 12)
            ->set('productImage', UploadedFile::fake()->create('new-photo.jpg', 100, 'image/jpeg'))
            ->call('saveProduct');

        $product->refresh();

        $this->assertNotSame('products/original.jpg', $product->image);
        $this->assertStringStartsWith('products/', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }
}
