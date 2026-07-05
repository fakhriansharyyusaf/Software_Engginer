<?php

namespace App\Services;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class BuyerDashboardDataService
{
    public function getViewModel(string $deliveryMethod, ?array $discountPreview = null): array
    {
        $user = Auth::user()?->load(['wallet', 'addresses']);

        if (! $user) {
            return [
                'user' => null,
                'cart' => null,
                'subtotal' => 0,
                'deliveryFee' => 0,
                'ppn' => 0,
                'total' => 0,
                'orders' => collect(),
                'addresses' => collect(),
                'spendingTotal' => 0,
                'walletHistory' => collect(),
            ];
        }

        $cart = $user->cart()->with('items.product.store')->first();
        $subtotal = 0;
        if ($cart) {
            foreach ($cart->items as $item) {
                $subtotal += (float) $item->product->price * $item->quantity;
            }
        }

        $discountAmount = ($discountPreview['ok'] ?? false) ? (float) ($discountPreview['amount'] ?? 0) : 0;
        $deliveryFee = CheckoutService::DELIVERY_FEES[$deliveryMethod] ?? 0;
        $ppn = round(($subtotal - $discountAmount) * CheckoutService::PPN_RATE, 2);
        $total = $subtotal - $discountAmount + $deliveryFee + $ppn;

        $orders = $user->ordersAsBuyer()->with(['items', 'statusHistories', 'store'])->latest()->paginate(10);
        $spendingTotal = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'payment')
            ->sum('amount');

        return [
            'user' => $user,
            'cart' => $cart,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'ppn' => $ppn,
            'total' => $total,
            'orders' => $orders,
            'addresses' => $user->addresses,
            'spendingTotal' => abs((float) $spendingTotal),
            'walletHistory' => $user->walletTransactions()->latest()->take(15)->get(),
        ];
    }
}
