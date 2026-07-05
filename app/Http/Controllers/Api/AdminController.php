<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryJob;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Store;
use App\Models\User;
use App\Models\Voucher;
use App\Services\OverdueService;
use App\Services\TimeService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function monitoring()
    {
        return response()->json([
            'users' => User::count(),
            'stores' => Store::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'orders_by_status' => Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'vouchers' => Voucher::count(),
            'promos' => Promo::count(),
            'delivery_jobs' => DeliveryJob::count(),
            'overdue_orders' => Order::whereNotNull('overdue_processed_at')->count(),
            'simulated_now' => TimeService::now()->toDateTimeString(),
            'time_offset_days' => TimeService::offsetDays(),
        ]);
    }

    public function vouchers()
    {
        return response()->json(Voucher::latest()->paginate(20));
    }

    public function voucherStore(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
            'usage_limit' => 'required|integer|min:1',
        ]);
        $data['code'] = strtoupper($data['code']);

        return response()->json(Voucher::create($data), 201);
    }

    public function voucherShow(Voucher $voucher)
    {
        return response()->json($voucher);
    }

    public function promos()
    {
        return response()->json(Promo::latest()->paginate(20));
    }

    public function promoStore(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
        ]);
        $data['code'] = strtoupper($data['code']);

        return response()->json(Promo::create($data), 201);
    }

    public function promoShow(Promo $promo)
    {
        return response()->json($promo);
    }

    public function simulateNextDay()
    {
        $offset = TimeService::simulateNextDay();

        return response()->json(['offset_days' => $offset, 'simulated_now' => TimeService::now()->toDateTimeString()]);
    }

    public function runOverdueCheck()
    {
        $processed = OverdueService::run();

        return response()->json([
            'processed_count' => $processed->count(),
            'processed_order_ids' => $processed->pluck('id'),
        ]);
    }
}
