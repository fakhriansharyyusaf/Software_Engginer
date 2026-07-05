<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\AuthForm;
use App\Livewire\RoleSelector;
use App\Livewire\Dashboard;
use App\Livewire\Storefront\ProductCatalog;
use App\Livewire\Storefront\ProductDetail;
use App\Livewire\Seller\SellerDashboard;
use App\Livewire\Buyer\BuyerDashboard;
use App\Livewire\Driver\DriverDashboard;
use App\Livewire\Admin\AdminDashboard;
use App\Http\Controllers\SessionController;

// ---------- Public ----------
Route::get('/', HomePage::class)->name('home');
Route::get('/katalog', ProductCatalog::class)->name('catalog');
Route::get('/produk/{product}', ProductDetail::class)->name('product.detail');

// ---------- Auth ----------
Route::get('/login', AuthForm::class)->name('login')->middleware('guest');
Route::get('/select-role', RoleSelector::class)->name('role.select')->middleware('auth');

// Global session actions — available from the shared navbar regardless of
// which Livewire full-page component is currently mounted.
Route::post('/logout', [SessionController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/switch-role/{role}', [SessionController::class, 'switchRole'])->name('role.switch')->middleware('auth');

Route::get('/dashboard', Dashboard::class)
    ->name('dashboard')
    ->middleware(['auth', 'active_role']);

// ---------- Seller ----------
Route::get('/seller', SellerDashboard::class)
    ->name('seller.dashboard')
    ->middleware(['auth', 'active_role:Seller']);

// ---------- Buyer ----------
Route::get('/buyer', BuyerDashboard::class)
    ->name('buyer.dashboard')
    ->middleware(['auth', 'active_role:Buyer']);
// Convenience alias so links like route('buyer.cart') land on the buyer hub (cart tab).
Route::get('/buyer/cart', BuyerDashboard::class)
    ->name('buyer.cart')
    ->middleware(['auth', 'active_role:Buyer']);

// ---------- Driver ----------
Route::get('/driver', DriverDashboard::class)
    ->name('driver.dashboard')
    ->middleware(['auth', 'active_role:Driver']);

// ---------- Admin ----------
Route::get('/admin', AdminDashboard::class)
    ->name('admin.dashboard')
    ->middleware(['auth', 'active_role:Admin']);
