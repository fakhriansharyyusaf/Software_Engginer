<?php

namespace App\Livewire\Buyer;

use App\Services\BuyerDashboardDataService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BuyerDashboard extends Component
{
    use WithPagination;

    public string $tab = 'wallet';

    // --- Top up ---
    public $topupAmount = 50000;

    // --- Address form ---
    public ?int $editingAddressId = null;
    public string $addressLabel = 'Rumah';
    public string $recipientName = '';
    public string $phone = '';
    public string $addressLine = '';
    public string $city = '';
    public string $postalCode = '';

    // --- Checkout ---
    public string $deliveryMethod = 'regular';
    public string $discountCode = '';
    public ?array $discountPreview = null;

    public function mount()
    {
        if (request()->routeIs('buyer.cart')) {
            $this->tab = 'cart';
        }
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetErrorBag();
        $this->discountPreview = null;
    }

    // ---------- Wallet ----------
    public function topUp()
    {
        $this->validate(['topupAmount' => 'required|numeric|min:10000|max:50000000']);

        WalletService::credit(Auth::user(), (float) $this->topupAmount, 'topup', 'Top up saldo (dummy)');
        session()->flash('wallet_message', 'Top up berhasil.');
        $this->topupAmount = 50000;
    }

    // ---------- Address ----------
    public function editAddress(int $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $this->editingAddressId = $address->id;
        $this->addressLabel = $address->label;
        $this->recipientName = $address->recipient_name;
        $this->phone = $address->phone;
        $this->addressLine = $address->address_line;
        $this->city = $address->city;
        $this->postalCode = $address->postal_code;
    }

    public function resetAddressForm()
    {
        $this->reset(['editingAddressId', 'recipientName', 'phone', 'addressLine', 'city', 'postalCode']);
        $this->addressLabel = 'Rumah';
    }

    public function saveAddress()
    {
        $this->validate([
            'addressLabel' => 'required|string|max:50',
            'recipientName' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'addressLine' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postalCode' => 'required|string|max:10',
        ], [
            'phone.regex' => 'Format nomor HP tidak valid. Gunakan format 08xx, 62xx, atau +62xx.',
        ]);

        $data = [
            'label' => $this->addressLabel,
            'recipient_name' => $this->recipientName,
            'phone' => $this->phone,
            'address_line' => $this->addressLine,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
        ];

        if ($this->editingAddressId) {
            Auth::user()->addresses()->findOrFail($this->editingAddressId)->update($data);
        } else {
            $isFirst = Auth::user()->addresses()->count() === 0;
            Auth::user()->addresses()->create($data + ['is_default' => $isFirst]);
        }

        $this->resetAddressForm();
        session()->flash('address_message', 'Alamat tersimpan.');
    }

    public function deleteAddress(int $id)
    {
        Auth::user()->addresses()->findOrFail($id)->delete();
    }

    public function makeDefaultAddress(int $id)
    {
        Auth::user()->addresses()->update(['is_default' => false]);
        Auth::user()->addresses()->findOrFail($id)->update(['is_default' => true]);
    }

    // ---------- Cart ----------
    public function updateQuantity(int $cartItemId, int $quantity)
    {
        CartService::updateQuantity(Auth::user(), $cartItemId, $quantity);
    }

    public function removeItem(int $cartItemId)
    {
        CartService::removeItem(Auth::user(), $cartItemId);
    }

    public function clearCart()
    {
        CartService::clear(Auth::user());
    }

    // ---------- Checkout ----------
    public function previewDiscount()
    {
        $this->discountPreview = null;
        if (! $this->discountCode) {
            return;
        }

        $cart = Auth::user()->cart()->with('items.product')->first();
        $subtotal = $cart ? $cart->items->sum(fn ($i) => (float) $i->product->price * $i->quantity) : 0;

        try {
            $result = \App\Services\DiscountService::validate($this->discountCode, $subtotal);
            $this->discountPreview = [
                'ok' => true,
                'type' => $result['type'],
                'amount' => $result['amount'],
            ];
        } catch (\RuntimeException $e) {
            $this->discountPreview = ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkout()
    {
        try {
            $order = CheckoutService::checkout(
                Auth::user(),
                $this->deliveryMethod,
                $this->discountCode ?: null
            );

            $this->discountCode = '';
            $this->discountPreview = null;
            session()->flash('checkout_message', "Checkout berhasil! Order #{$order->id} dibuat.");
            $this->tab = 'orders';
        } catch (\RuntimeException $e) {
            $this->addError('checkout', $e->getMessage());
        }
    }

    public function render()
    {
        $viewModel = app(BuyerDashboardDataService::class)
            ->getViewModel($this->deliveryMethod, $this->discountPreview);

        return view('livewire.buyer.buyer-dashboard', $viewModel);
    }
}
