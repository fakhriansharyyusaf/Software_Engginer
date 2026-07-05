<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\AuthForm;
use App\Livewire\Buyer\BuyerDashboard;
use App\Livewire\Dashboard;
use App\Livewire\Driver\DriverDashboard;
use App\Livewire\HomePage;
use App\Livewire\RoleSelector;
use App\Livewire\Seller\SellerDashboard;
use App\Livewire\Storefront\ProductCatalog;
use App\Livewire\Storefront\ProductDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FullAppSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\DemoSeeder::class);
        $this->seed(\Database\Seeders\ProductImageSeeder::class);
        $this->seed(\Database\Seeders\VoucherPromoSeeder::class);
        $this->seed(\Database\Seeders\DemoOrderSeeder::class);
    }

    public function test_home_page_renders()
    {
        Livewire::test(HomePage::class)->assertOk();
    }

    public function test_product_catalog_renders()
    {
        Livewire::test(ProductCatalog::class)->assertOk();
    }

    public function test_product_detail_renders_for_guest_and_buyer()
    {
        $product = Product::first();
        Livewire::test(ProductDetail::class, ['product' => $product])->assertOk();

        $buyer = User::where('username', 'buyer1')->first();
        Livewire::actingAs($buyer)
            ->test(ProductDetail::class, ['product' => $product])
            ->assertOk();
    }

    public function test_auth_form_renders()
    {
        Livewire::test(AuthForm::class)->assertOk();
    }

    public function test_role_selector_renders_for_multi_role_user()
    {
        $multi = User::where('username', 'multi1')->first();
        Livewire::actingAs($multi)->test(RoleSelector::class)->assertOk();
    }

    public function test_dashboard_renders_for_every_role()
    {
        foreach (['admin', 'seller1', 'buyer1', 'driver1'] as $username) {
            $user = User::where('username', $username)->first();
            Livewire::actingAs($user)->test(Dashboard::class)->assertOk();
        }
    }

    public function test_seller_dashboard_renders_and_all_tabs_work()
    {
        $seller = User::where('username', 'seller1')->first();
        $seller->setActiveRole('Seller');

        Livewire::actingAs($seller)
            ->test(SellerDashboard::class)
            ->assertOk()
            ->call('setTab', 'products')->assertOk()
            ->call('setTab', 'orders')->assertOk()
            ->call('setTab', 'store')->assertOk();
    }

    public function test_buyer_dashboard_renders_and_all_tabs_work()
    {
        $buyer = User::where('username', 'buyer1')->first();
        $buyer->setActiveRole('Buyer');

        Livewire::actingAs($buyer)
            ->test(BuyerDashboard::class)
            ->assertOk()
            ->call('setTab', 'wallet')->assertOk()
            ->call('setTab', 'address')->assertOk()
            ->call('setTab', 'cart')->assertOk()
            ->call('setTab', 'orders')->assertOk();
    }

    public function test_driver_dashboard_renders_and_all_tabs_work()
    {
        $driver = User::where('username', 'driver1')->first();
        $driver->setActiveRole('Driver');

        Livewire::actingAs($driver)
            ->test(DriverDashboard::class)
            ->assertOk()
            ->call('setTab', 'available')->assertOk()
            ->call('setTab', 'active')->assertOk()
            ->call('setTab', 'history')->assertOk();
    }

    public function test_admin_dashboard_renders_and_all_tabs_work()
    {
        $admin = User::where('username', 'admin')->first();
        $admin->setActiveRole('Admin');

        Livewire::actingAs($admin)
            ->test(AdminDashboard::class)
            ->assertOk()
            ->call('setTab', 'monitoring')->assertOk()
            ->call('setTab', 'vouchers')->assertOk()
            ->call('setTab', 'promos')->assertOk()
            ->call('setTab', 'time')->assertOk()
            ->call('simulateNextDay')->assertOk()
            ->call('runOverdueCheck')->assertOk();
    }

    public function test_full_http_routes_for_every_role()
    {
        $routes = [
            'admin' => '/admin',
            'seller1' => '/seller',
            'buyer1' => '/buyer',
            'driver1' => '/driver',
        ];

        foreach ($routes as $username => $path) {
            $user = User::where('username', $username)->first();
            $this->actingAs($user)->get($path)->assertOk();
        }

        // Cross-role access must be forbidden.
        $buyer = User::where('username', 'buyer1')->first();
        $this->actingAs($buyer)->get('/seller')->assertForbidden();
        $this->actingAs($buyer)->get('/admin')->assertForbidden();
    }

    public function test_seller_can_create_edit_delete_product()
    {
        $seller = User::where('username', 'seller1')->first();
        $seller->setActiveRole('Seller');

        $component = Livewire::actingAs($seller)->test(SellerDashboard::class)
            ->set('productName', 'Produk Uji Coba')
            ->set('productDescription', 'Deskripsi uji coba')
            ->set('productPrice', 99000)
            ->set('productStock', 10)
            ->call('saveProduct')
            ->assertOk()
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', ['name' => 'Produk Uji Coba', 'price' => 99000]);

        $product = \App\Models\Product::where('name', 'Produk Uji Coba')->first();
        $this->assertNotNull($product->image, 'A placeholder image should be auto-generated.');

        $component->call('editProduct', $product->id)
            ->assertSet('productName', 'Produk Uji Coba')
            ->set('productStock', 5)
            ->call('saveProduct')
            ->assertOk();

        $product->refresh();
        $this->assertEquals(5, $product->stock);

        $component->call('deleteProduct', $product->id)->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_buyer_wallet_topup_and_address_crud()
    {
        $buyer = User::where('username', 'buyer1')->first();
        $buyer->setActiveRole('Buyer');
        $balanceBefore = (float) $buyer->wallet->fresh()->balance;

        Livewire::actingAs($buyer)->test(BuyerDashboard::class)
            ->set('topupAmount', 100000)
            ->call('topUp')
            ->assertOk()
            ->assertHasNoErrors();

        $buyer->wallet->refresh();
        $this->assertEquals($balanceBefore + 100000, (float) $buyer->wallet->balance);

        $component = Livewire::actingAs($buyer)->test(BuyerDashboard::class)
            ->set('recipientName', 'Budi Tester')
            ->set('phone', '081234567890')
            ->set('addressLine', 'Jl. Uji Coba No. 1')
            ->set('city', 'Jakarta')
            ->set('postalCode', '12345')
            ->call('saveAddress')
            ->assertOk()
            ->assertHasNoErrors();

        $this->assertDatabaseHas('addresses', ['recipient_name' => 'Budi Tester']);

        $address = \App\Models\Address::where('recipient_name', 'Budi Tester')->first();
        $component->call('deleteAddress', $address->id)->assertOk();
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_single_store_cart_conflict_is_rejected()
    {
        $buyer = User::where('username', 'buyer1')->first();
        $buyer->setActiveRole('Buyer');

        $storeA = \App\Models\Store::where('name', 'Toko Lari Cepat')->first();
        $storeB = \App\Models\Store::where('name', 'TechGear ID')->first();
        $productA = $storeA->products()->first();
        $productB = $storeB->products()->first();

        \App\Services\CartService::clear($buyer);
        \App\Services\CartService::addItem($buyer, $productA, 1);

        $this->expectException(\RuntimeException::class);
        \App\Services\CartService::addItem($buyer, $productB, 1);
    }

    public function test_admin_can_create_voucher_and_promo()
    {
        $admin = User::where('username', 'admin')->first();
        $admin->setActiveRole('Admin');

        Livewire::actingAs($admin)->test(AdminDashboard::class)
            ->set('voucherCode', 'TESTVOUCHER')
            ->set('voucherType', 'fixed')
            ->set('voucherValue', 5000)
            ->set('voucherExpiry', now()->addDays(10)->format('Y-m-d'))
            ->set('voucherLimit', 5)
            ->call('createVoucher')
            ->assertOk()
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vouchers', ['code' => 'TESTVOUCHER']);

        Livewire::actingAs($admin)->test(AdminDashboard::class)
            ->set('promoCode', 'TESTPROMO')
            ->set('promoType', 'percent')
            ->set('promoValue', 15)
            ->set('promoExpiry', now()->addDays(10)->format('Y-m-d'))
            ->call('createPromo')
            ->assertOk()
            ->assertHasNoErrors();

        $this->assertDatabaseHas('promos', ['code' => 'TESTPROMO']);
    }

    public function test_register_creates_buyer_and_seller_roles_and_redirects_to_role_select()
    {
        Livewire::test(AuthForm::class)
            ->set('isLogin', false)
            ->set('username', 'usernew')
            ->set('email', 'usernew@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('role.select'));

        $user = User::where('username', 'usernew')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Buyer'));
        $this->assertTrue($user->hasRole('Seller'));
        $this->assertNotNull($user->wallet, 'A wallet should be created on registration.');
        $this->assertNotNull($user->cart, 'A cart should be created on registration.');
    }

    public function test_login_single_role_user_redirects_straight_to_dashboard()
    {
        Livewire::test(AuthForm::class)
            ->set('isLogin', true)
            ->set('username', 'buyer1')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_multi_role_user_redirects_to_role_select()
    {
        Livewire::test(AuthForm::class)
            ->set('isLogin', true)
            ->set('username', 'multi1')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('role.select'));
    }

    public function test_login_creates_sanctum_token_for_web_session()
    {
        Livewire::test(AuthForm::class)
            ->set('isLogin', true)
            ->set('username', 'buyer1')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $user = User::where('username', 'buyer1')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->tokens()->exists());
    }

    public function test_login_with_wrong_password_fails()
    {
        Livewire::test(AuthForm::class)
            ->set('isLogin', true)
            ->set('username', 'buyer1')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors('username');

        $this->assertGuest();
    }
}
