<?php

namespace App\Services;

use App\Models\DeliveryJob;
use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class DriverDashboardDataService
{
    public function getViewModel(): array
    {
        $driver = Auth::user()?->load('wallet');

        if (! $driver) {
            return [
                'driver' => null,
                'availableJobs' => collect(),
                'myActiveJobs' => collect(),
                'myHistory' => collect(),
                'totalEarnings' => 0,
            ];
        }

        $availableJobs = DeliveryJob::where('status', 'available')
            ->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_WAITING_DRIVER))
            ->with(['order.store', 'order.buyer'])
            ->latest()
            ->paginate(10, ['*'], 'availablePage');

        $myActiveJobs = DeliveryJob::where('driver_id', $driver->id)
            ->where('status', 'taken')
            ->with(['order.store', 'order.buyer'])
            ->latest()
            ->get();

        $myHistory = DeliveryJob::where('driver_id', $driver->id)
            ->where('status', 'delivered')
            ->with(['order.store'])
            ->latest()
            ->paginate(10, ['*'], 'historyPage');

        $totalEarnings = WalletTransaction::where('user_id', $driver->id)
            ->where('type', 'driver_earning')
            ->sum('amount');

        return [
            'driver' => $driver,
            'availableJobs' => $availableJobs,
            'myActiveJobs' => $myActiveJobs,
            'myHistory' => $myHistory,
            'totalEarnings' => $totalEarnings,
        ];
    }
}
