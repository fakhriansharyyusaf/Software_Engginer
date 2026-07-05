<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

/**
 * Populates the marketplace with orders in every stage of the lifecycle,
 * using the REAL CheckoutService/OrderService flow (not raw inserts) so
 * wallet balances, stock levels, and status histories are all internally
 * consistent — exactly as if a real demo session had happened.
 *
 * This means the moment an evaluator logs in, they immediately see:
 *   - A completed order (so Seller income & Driver earnings are non-zero)
 *   - An order sitting at "Menunggu Pengirim" (driver1 can take it live)
 *   - An order sitting at "Sedang Dikemas" (seller1 can process it live)
 *   - An order that is already past its SLA (admin can run the overdue
 *     check live and immediately see a real refund happen)
 *
 * Idempotent: skipped entirely if buyer1 already has demo orders from a
 * previous run.
 */
class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyer1 = User::where('username', 'buyer1')->first();
        $driver1 = User::where('username', 'driver1')->first();

        if (! $buyer1 || ! $driver1) {
            return; // RoleSeeder/DemoSeeder didn't run yet — nothing to attach orders to.
        }

        if ($buyer1->ordersAsBuyer()->count() > 0) {
            return; // Already seeded.
        }

        $store = \App\Models\Store::where('name', 'Toko Lari Cepat')->first();
        if (! $store) {
            return;
        }

        $products = $store->products()->get()->keyBy('name');

        // --- Scenario 1: fully completed order (income + earnings visible immediately) ---
        $order1 = $this->makeOrder($buyer1, $products['Sepatu Lari Mekanik'], 1, 'regular');
        OrderService::sellerProcess($store->user, $order1);
        $job1 = $order1->fresh()->deliveryJob;
        OrderService::driverTakeJob($driver1, $job1);
        OrderService::driverCompleteJob($driver1, $job1->fresh());

        // --- Scenario 2: waiting for a driver (driver1 can take this live during a demo) ---
        $order2 = $this->makeOrder($buyer1, $products['Kaos Dry-Fit'], 2, 'next_day');
        OrderService::sellerProcess($store->user, $order2);

        // --- Scenario 3: fresh order awaiting Seller processing (seller1 can process this live) ---
        $this->makeOrder($buyer1, $products['Botol Minum 1L'], 3, 'instant');

        // --- Scenario 4: already overdue, ready for Admin's "Run Overdue Check" to act on live ---
        $order4 = $this->makeOrder($buyer1, $products['Sepatu Lari Mekanik'], 1, 'instant');
        OrderService::sellerProcess($store->user, $order4);
        // Backdate the SLA so it's already overdue without needing to simulate days forward.
        $order4->update(['sla_due_at' => now()->subHours(6)]);
        \App\Models\OrderStatusHistory::where('order_id', $order4->id)->update(['changed_at' => now()->subHours(8)]);
    }

    private function makeOrder(User $buyer, \App\Models\Product $product, int $qty, string $deliveryMethod): Order
    {
        CartService::clear($buyer);
        CartService::addItem($buyer, $product, $qty);

        return CheckoutService::checkout($buyer, $deliveryMethod, null);
    }
}
