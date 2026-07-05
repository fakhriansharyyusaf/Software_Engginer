<?php

namespace App\Services;

use App\Models\DeliveryJob;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Store;
use App\Models\User;
use App\Models\Voucher;

class AdminDashboardDataService
{
    public function getViewModel(): array
    {
        return [
            'userCount' => User::count(),
            'storeCount' => Store::count(),
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'ordersByStatus' => Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'voucherCount' => Voucher::count(),
            'promoCount' => Promo::count(),
            'deliveryJobCount' => DeliveryJob::count(),
            'overdueCount' => Order::whereNotNull('overdue_processed_at')->count(),
            'vouchers' => Voucher::latest()->paginate(8, ['*'], 'vouchersPage'),
            'promos' => Promo::latest()->paginate(8, ['*'], 'promosPage'),
            'recentOrders' => Order::with(['buyer', 'store'])->latest()->take(10)->get(),
            'timeOffsetDays' => TimeService::offsetDays(),
            'simulatedNow' => TimeService::now(),
        ];
    }
}
