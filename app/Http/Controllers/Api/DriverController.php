<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryJob;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function availableJobs()
    {
        return response()->json(
            DeliveryJob::where('status', 'available')
                ->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_WAITING_DRIVER))
                ->with(['order.store'])
                ->latest()
                ->paginate(15)
        );
    }

    public function jobDetail(DeliveryJob $job)
    {
        return response()->json($job->load(['order.items', 'order.store', 'order.buyer']));
    }

    public function takeJob(Request $request, DeliveryJob $job)
    {
        try {
            $job = OrderService::driverTakeJob($request->user(), $job);

            return response()->json($job);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function completeJob(Request $request, DeliveryJob $job)
    {
        try {
            $job = OrderService::driverCompleteJob($request->user(), $job);

            return response()->json($job);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function myJobs(Request $request)
    {
        $driverId = $request->user()->id;

        return response()->json([
            'active' => DeliveryJob::where('driver_id', $driverId)->where('status', 'taken')->with('order.store')->get(),
            'history' => DeliveryJob::where('driver_id', $driverId)->where('status', 'delivered')->with('order.store')->latest()->paginate(15),
            'total_earnings' => \App\Models\WalletTransaction::where('user_id', $driverId)->where('type', 'driver_earning')->sum('amount'),
        ]);
    }
}
