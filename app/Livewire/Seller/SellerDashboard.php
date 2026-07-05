<?php

namespace App\Livewire\Seller;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductPlaceholderImage;
use App\Services\SellerDashboardDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SellerDashboard extends Component
{
    use WithPagination, WithFileUploads;

    public string $tab = 'store';

    // --- Store form ---
    public string $storeName = '';
    public string $storeDescription = '';

    // --- Product form ---
    public ?int $editingProductId = null;
    public string $productName = '';
    public string $productDescription = '';
    public $productPrice = '';
    public $productStock = '';
    public $productImage = null; // Livewire TemporaryUploadedFile while editing the form

    public function mount()
    {
        $store = Auth::user()->store;
        if ($store) {
            $this->storeName = $store->name;
            $this->storeDescription = (string) $store->description;
        }
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetErrorBag();
    }

    // ---------- Store management ----------
    public function saveStore()
    {
        $user = Auth::user();

        $this->validate([
            'storeName' => 'required|string|max:255|unique:stores,name,'.optional($user->store)->id,
            'storeDescription' => 'nullable|string|max:1000',
        ]);

        $store = $user->store()->first();

        if ($store) {
            $this->authorize('update', $store);
            $store->update([
                'name' => $this->storeName,
                'description' => $this->storeDescription,
            ]);
        } else {
            $store = $user->store()->create([
                'name' => $this->storeName,
                'description' => $this->storeDescription,
            ]);
        }

        session()->flash('store_message', 'Profil toko tersimpan.');
    }

    // ---------- Product management ----------
    public function editProduct(int $productId)
    {
        $product = Auth::user()->store->products()->findOrFail($productId);
        $this->authorize('update', $product);

        $this->editingProductId = $product->id;
        $this->productName = $product->name;
        $this->productDescription = (string) $product->description;
        $this->productPrice = $product->price;
        $this->productStock = $product->stock;
        $this->tab = 'products';
    }

    public function resetProductForm()
    {
        $this->reset(['editingProductId', 'productName', 'productDescription', 'productPrice', 'productStock', 'productImage']);
    }

    public function saveProduct()
    {
        $store = Auth::user()->store;

        if (! $store) {
            $this->addError('productName', 'Buat profil toko terlebih dahulu sebelum menambah produk.');
            return;
        }

        $this->validate([
            'productName' => 'required|string|max:255',
            'productDescription' => 'nullable|string|max:1000',
            'productPrice' => 'required|numeric|min:0',
            'productStock' => 'required|integer|min:0',
            'productImage' => 'nullable|file|image|max:2048', // max 2MB
        ]);

        $data = [
            'name' => $this->productName,
            'description' => $this->productDescription,
            'price' => $this->productPrice,
            'stock' => $this->productStock,
        ];

        if ($this->editingProductId) {
            $product = $store->products()->findOrFail($this->editingProductId);
            $this->authorize('update', $product);

            if ($this->productImage instanceof \Illuminate\Http\UploadedFile || $this->productImage instanceof \Livewire\TemporaryUploadedFile) {
                $newImagePath = $this->productImage->store('products', 'public');

                if ($product->image && $product->image !== $newImagePath) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $newImagePath;
            } elseif (! $product->image) {
                // Never leave a product without a picture — generate a branded placeholder.
                $data['image'] = ProductPlaceholderImage::generateAndStore($product->id, $this->productName);
            }

            $product->update($data);
        } else {
            if ($this->productImage instanceof \Illuminate\Http\UploadedFile || $this->productImage instanceof \Livewire\TemporaryUploadedFile) {
                $data['image'] = $this->productImage->store('products', 'public');
            }

            $product = $store->products()->create($data);

            if (! $product->image) {
                $product->update(['image' => ProductPlaceholderImage::generateAndStore($product->id, $product->name)]);
            }
        }

        $this->resetProductForm();
        session()->flash('product_message', 'Produk tersimpan.');
    }

    public function deleteProduct(int $productId)
    {
        $store = Auth::user()->store;
        $product = $store->products()->findOrFail($productId);
        $this->authorize('delete', $product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        session()->flash('product_message', 'Produk dihapus.');
    }

    // ---------- Orders ----------
    public function processOrder(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        try {
            OrderService::sellerProcess(Auth::user(), $order);
            session()->flash('order_message', "Order #{$order->id} diproses, menunggu Driver.");
        } catch (\RuntimeException $e) {
            $this->addError('order', $e->getMessage());
        }
    }

    public function render()
    {
        $viewModel = app(SellerDashboardDataService::class)->getViewModel();

        return view('livewire.seller.seller-dashboard', $viewModel);
    }
}
