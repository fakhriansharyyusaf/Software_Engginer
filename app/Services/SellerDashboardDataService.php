<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class SellerDashboardDataService
{
    public function getViewModel(): array
    {
        $user = Auth::user();
        $store = $user?->store;

        $products = $store ? $store->products()->latest()->paginate(10, ['*'], 'productsPage') : collect();
        $orders = $store
            ? Order::where('store_id', $store->id)->with(['items', 'buyer', 'statusHistories'])->latest()->paginate(10, ['*'], 'ordersPage')
            : collect();

        $incomeTotal = $store
            ? WalletTransaction::where('user_id', $user->id)->where('type', 'seller_income')->sum('amount')
                + WalletTransaction::where('user_id', $user->id)->where('type', 'seller_income_reversal')->sum('amount')
            : 0;

        return [
            'store' => $store,
            'products' => $products,
            'orders' => $orders,
            'incomeTotal' => $incomeTotal,
        ];
    }
}
