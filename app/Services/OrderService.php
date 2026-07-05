<?php

namespace App\Services;

use App\Models\DeliveryJob;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Driver earning rule (documented in README): a Driver earns 80% of the
 * order's delivery_fee, credited to their wallet when they confirm the
 * job as completed.
 */
class OrderService
{
    public const DRIVER_EARNING_RATE = 0.8;

    /**
     * Seller processes an order: Sedang Dikemas -> Menunggu Pengirim,
     * and a delivery job becomes available for Drivers.
     *
     * @throws \RuntimeException when the order does not belong to the seller's store
     *                            or is not in a processable state
     */
    public static function sellerProcess(User $seller, Order $order): Order
    {
        if (! $seller->store || $order->store_id !== $seller->store->id) {
            throw new \RuntimeException('Anda tidak berhak memproses pesanan ini.');
        }

        if ($order->status !== Order::STATUS_PACKING) {
            throw new \RuntimeException('Pesanan tidak dalam status yang bisa diproses.');
        }

        return DB::transaction(function () use ($order) {
            $now = TimeService::now();

            $order->update(['status' => Order::STATUS_WAITING_DRIVER]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_WAITING_DRIVER,
                'note' => 'Pesanan telah diproses oleh Seller dan menunggu Driver.',
                'changed_at' => $now,
            ]);

            DeliveryJob::create([
                'order_id' => $order->id,
                'status' => 'available',
            ]);

            return $order->fresh(['statusHistories', 'deliveryJob']);
        });
    }

    /**
     * @throws \RuntimeException if the job was already taken by another driver
     */
    public static function driverTakeJob(User $driver, DeliveryJob $job): DeliveryJob
    {
        return DB::transaction(function () use ($driver, $job) {
            $locked = DeliveryJob::where('id', $job->id)->lockForUpdate()->first();

            if ($locked->status !== 'available' || $locked->driver_id !== null) {
                throw new \RuntimeException('Job ini sudah diambil oleh Driver lain.');
            }

            $now = TimeService::now();

            $locked->update([
                'status' => 'taken',
                'driver_id' => $driver->id,
                'taken_at' => $now,
            ]);

            $order = $locked->order;
            $order->update(['status' => Order::STATUS_SHIPPING, 'driver_id' => $driver->id]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_SHIPPING,
                'note' => "Driver #{$driver->id} mengambil dan mengirim pesanan.",
                'changed_at' => $now,
            ]);

            return $locked->fresh(['order']);
        });
    }

    /**
     * @throws \RuntimeException when the job does not belong to this driver or isn't in-progress
     */
    public static function driverCompleteJob(User $driver, DeliveryJob $job): DeliveryJob
    {
        return DB::transaction(function () use ($driver, $job) {
            $locked = DeliveryJob::where('id', $job->id)->lockForUpdate()->first();

            if ($locked->driver_id !== $driver->id || $locked->status !== 'taken') {
                throw new \RuntimeException('Job ini tidak bisa diselesaikan oleh Anda.');
            }

            $now = TimeService::now();

            $locked->update(['status' => 'delivered', 'delivered_at' => $now]);

            $order = $locked->order;
            $order->update(['status' => Order::STATUS_DONE]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => Order::STATUS_DONE,
                'note' => 'Driver mengonfirmasi pesanan telah selesai diantar.',
                'changed_at' => $now,
            ]);

            // Credit seller income
            WalletService::credit(
                $order->store->user,
                (float) $order->subtotal - (float) $order->discount,
                'seller_income',
                "Pendapatan dari order #{$order->id}",
                Order::class,
                $order->id
            );

            // Credit driver earning
            $earning = (float) $order->delivery_fee * self::DRIVER_EARNING_RATE;
            WalletService::credit(
                $driver,
                $earning,
                'driver_earning',
                "Ongkir order #{$order->id}",
                Order::class,
                $order->id
            );

            return $locked->fresh(['order']);
        });
    }
}
