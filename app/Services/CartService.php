<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

/**
 * Single-store checkout rule: one cart may only contain products from
 * one store at a time. If a Buyer tries to add a product from another
 * store, we reject it with a clear error so the UI can ask them to
 * clear the cart first.
 */
class CartService
{
    public static function ensureCart(User $user): Cart
    {
        return $user->cart ?? $user->cart()->create([]);
    }

    /**
     * @throws \RuntimeException when the product belongs to a different store than what's already in the cart
     */
    public static function addItem(User $user, Product $product, int $quantity = 1): Cart
    {
        $cart = self::ensureCart($user)->fresh(['items.product']);

        if ($cart->store_id !== null && $cart->store_id !== $product->store_id) {
            throw new \RuntimeException(
                'Keranjang kamu sudah berisi produk dari toko lain. Kosongkan keranjang terlebih dahulu untuk belanja dari toko ini (single-store checkout).'
            );
        }

        if ($cart->store_id === null) {
            $cart->update(['store_id' => $product->store_id]);
        }

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create(['product_id' => $product->id, 'quantity' => $quantity]);
        }

        return $cart->fresh(['items.product']);
    }

    public static function updateQuantity(User $user, int $cartItemId, int $quantity): Cart
    {
        $cart = self::ensureCart($user);
        $item = $cart->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        $cart->refresh();
        if ($cart->items()->count() === 0) {
            $cart->update(['store_id' => null]);
        }

        return $cart->fresh(['items.product']);
    }

    public static function removeItem(User $user, int $cartItemId): Cart
    {
        return self::updateQuantity($user, $cartItemId, 0);
    }

    public static function clear(User $user): Cart
    {
        $cart = self::ensureCart($user);
        $cart->items()->delete();
        $cart->update(['store_id' => null]);

        return $cart->fresh(['items.product']);
    }
}
