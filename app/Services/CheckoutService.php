<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

/**
 * Delivery fee & SLA rules (documented in README):
 *   Instant   -> fee Rp 20.000, SLA 3 hours  (must reach "Pesanan Selesai" within 3 hours of checkout)
 *   Next Day  -> fee Rp 12.000, SLA 24 hours
 *   Regular   -> fee Rp  8.000, SLA 72 hours
 *
 * PPN 12% is calculated on (subtotal - discount). Delivery fee is not taxed.
 *   total = subtotal - discount + delivery_fee + ppn
 */
class CheckoutService
{
    public const DELIVERY_FEES = [
        'instant' => 20000,
        'next_day' => 12000,
        'regular' => 8000,
    ];

    public const SLA_HOURS = [
        'instant' => 3,
        'next_day' => 24,
        'regular' => 72,
    ];

    public const PPN_RATE = 0.12;

    /**
     * @throws \RuntimeException on any validation failure (empty cart, insufficient stock,
     *         insufficient wallet balance, invalid discount code)
     */
    public static function checkout(User $buyer, string $deliveryMethod, ?string $discountCode, ?string $addressNote = null): Order
    {
        if (! array_key_exists($deliveryMethod, self::DELIVERY_FEES)) {
            throw new \RuntimeException('Metode pengiriman tidak valid.');
        }

        return DB::transaction(function () use ($buyer, $deliveryMethod, $discountCode) {
            $cart = $buyer->cart()->with('items.product')->lockForUpdate()->first();

            if (! $cart || $cart->items->isEmpty() || ! $cart->store_id) {
                throw new \RuntimeException('Keranjang kosong.');
            }

            $subtotal = 0;
            $lockedProducts = [];
            foreach ($cart->items as $item) {
                $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (! $product || $product->stock < $item->quantity) {
                    throw new \RuntimeException("Stok produk \"{$item->product->name}\" tidak mencukupi.");
                }

                $lockedProducts[$item->id] = $product;
                $subtotal += (float) $product->price * $item->quantity;
            }

            $discountAmount = 0;
            $voucher = null;
            $promo = null;

            if ($discountCode) {
                $result = DiscountService::validate($discountCode, $subtotal);
                $discountAmount = $result['amount'];
                if ($result['type'] === 'voucher') {
                    $voucher = $result['model'];
                } else {
                    $promo = $result['model'];
                }
            }

            $deliveryFee = self::DELIVERY_FEES[$deliveryMethod];
            $ppn = round(($subtotal - $discountAmount) * self::PPN_RATE, 2);
            $total = $subtotal - $discountAmount + $deliveryFee + $ppn;

            if ((float) ($buyer->wallet->balance ?? 0) < $total) {
                throw new \RuntimeException('Saldo wallet tidak mencukupi untuk checkout ini.');
            }

            $now = TimeService::now();

            $order = Order::create([
                'buyer_id' => $buyer->id,
                'store_id' => $cart->store_id,
                'delivery_method' => $deliveryMethod,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'delivery_fee' => $deliveryFee,
                'ppn' => $ppn,
                'total' => $total,
                'voucher_id' => $voucher?->id,
                'promo_id' => $promo?->id,
                'status' => Order::STATUS_PACKING,
                'sla_due_at' => $now->copy()->addHours(self::SLA_HOURS[$deliveryMethod]),
            ]);

            foreach ($cart->items as $item) {
                $product = $lockedProducts[$item->id];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $product->price * $item->quantity,
                ]);

                // Reduce stock safely; never allow it to go negative.
                \App\Models\Product::where('id', $item->product_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_PACKING,
                'note' => 'Pesanan dibuat setelah checkout.',
                'changed_at' => $now,
            ]);

            WalletService::debit(
                $buyer,
                $total,
                'payment',
                "Pembayaran order #{$order->id}",
                Order::class,
                $order->id
            );

            if ($voucher) {
                $voucher->increment('used_count');
            }

            $cart->items()->delete();
            $cart->update(['store_id' => null]);

            return $order->fresh(['items', 'statusHistories']);
        });
    }
}
