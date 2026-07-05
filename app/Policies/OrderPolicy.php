<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        $role = $user->currentActiveRole();

        return match ($role) {
            'Buyer' => $order->buyer_id === $user->id,
            'Seller' => $user->store && $user->store->id === $order->store_id,
            'Driver' => $order->driver_id === $user->id,
            'Admin' => true,
            default => false,
        };
    }

    public function process(User $user, Order $order): bool
    {
        return $user->currentActiveRole() === 'Seller'
            && $user->store
            && $user->store->id === $order->store_id;
    }
}
