<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        $hasSellerRole = $user->roles()->where('name', 'Seller')->exists()
            || $user->currentActiveRole() === 'Seller';

        return $hasSellerRole
            && $user->store
            && $user->store->id === $product->store_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }
}
