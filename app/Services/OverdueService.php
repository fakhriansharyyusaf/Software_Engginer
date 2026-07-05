<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

/**
 * Overdue handling: any order that has NOT reached "Pesanan Selesai" or
 * "Dikembalikan" by the time TimeService::now() passes its sla_due_at
 * is automatically refunded and returned.
 *
 * This is meant to be triggered by Admin (manual trigger button), but
 * could equally be wired to a scheduler/cron/queue worker — the logic
 * itself is time-source agnostic (uses TimeService::now()).
 *
 * Idempotency: guarded by orders.overdue_processed_at. An order is only
 * ever processed once, preventing double refund / double stock
 * restoration / double income reversal.
 */
class OverdueService
{
    /**
     * @return \Illuminate\Support\Collection<int, Order> orders that were refunded/returned in this run
     */
    public static function run(): \Illuminate\Support\Collection
    {
        $now = TimeService::now();

        $candidates = Order::whereIn('status', [
            Order::STATUS_PACKING,
            Order::STATUS_WAITING_DRIVER,
            Order::STATUS_SHIPPING,
        ])
            ->whereNotNull('sla_due_at')
            ->whereNull('overdue_processed_at')
            ->where('sla_due_at', '<', $now)
            ->get();

        $processed = collect();

        foreach ($candidates as $order) {
            $result = self::processOne($order->id, $now);
            if ($result) {
                $processed->push($result);
            }
        }

        return $processed;
    }

    public static function processOne(int $orderId, ?\Carbon\Carbon $now = null): ?Order
    {
        $now ??= TimeService::now();

        return DB::transaction(function () use ($orderId, $now) {
            $order = Order::where('id', $orderId)->lockForUpdate()->first();

            // Idempotency guard: already processed, or already in a final state.
            if (! $order || $order->overdue_processed_at !== null) {
                return null;
            }
            if (in_array($order->status, [Order::STATUS_DONE, Order::STATUS_RETURNED], true)) {
                return null;
            }

            // 1. Restore stock for each item.
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            // 2. Refund the buyer (order was already paid at checkout).
            WalletService::credit(
                $order->buyer,
                (float) $order->total,
                'refund',
                "Refund otomatis order #{$order->id} (overdue)",
                Order::class,
                $order->id
            );

            // 3. Reverse seller income if it was already recorded for this order.
            $alreadyRecorded = WalletTransaction::where('reference_type', Order::class)
                ->where('reference_id', $order->id)
                ->where('type', 'seller_income')
                ->exists();

            $alreadyReversed = WalletTransaction::where('reference_type', Order::class)
                ->where('reference_id', $order->id)
                ->where('type', 'seller_income_reversal')
                ->exists();

            if ($alreadyRecorded && ! $alreadyReversed) {
                WalletService::debit(
                    $order->store->user,
                    (float) $order->subtotal - (float) $order->discount,
                    'seller_income_reversal',
                    "Pembatalan pendapatan order #{$order->id} (overdue)",
                    Order::class,
                    $order->id
                );
            }

            // 4. Finalize order status.
            $order->update([
                'status' => Order::STATUS_RETURNED,
                'overdue_processed_at' => $now,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_RETURNED,
                'note' => 'Pesanan melewati batas SLA dan diproses auto-refund/return otomatis.',
                'changed_at' => $now,
            ]);

            return $order->fresh(['statusHistories']);
        });
    }
}
