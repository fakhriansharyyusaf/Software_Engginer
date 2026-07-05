<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;
    public int $quantity = 1;
    public ?string $conflictMessage = null;

    public function mount(Product $product)
    {
        $this->product = $product->load('store');
    }

    public function addToCart()
    {
        $user = Auth::user();

        if (! $user || $user->currentActiveRole() !== 'Buyer') {
            $this->addError('quantity', 'Login sebagai Buyer untuk menambahkan produk ke keranjang.');
            return;
        }

        $this->conflictMessage = null;

        try {
            CartService::addItem($user, $this->product, max(1, $this->quantity));
            session()->flash('cart_message', 'Produk ditambahkan ke keranjang.');
            return redirect()->route('buyer.cart');
        } catch (\RuntimeException $e) {
            $this->conflictMessage = $e->getMessage();
        }
    }

    public function clearCartAndAdd()
    {
        $user = Auth::user();

        if (! $user || $user->currentActiveRole() !== 'Buyer') {
            $this->addError('quantity', 'Login sebagai Buyer untuk menambahkan produk ke keranjang.');
            return;
        }

        CartService::clear($user);
        CartService::addItem($user, $this->product, max(1, $this->quantity));
        session()->flash('cart_message', 'Keranjang lama dikosongkan, produk baru ditambahkan.');
        return redirect()->route('buyer.cart');
    }

    public function render()
    {
        return view('livewire.public.product-detail');
    }
}
