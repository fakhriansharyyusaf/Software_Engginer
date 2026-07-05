<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
    public function storeShow(Request $request)
    {
        return response()->json($request->user()->store);
    }

    public function storeSave(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stores', 'name')->ignore(optional($user->store)->id)],
            'description' => 'nullable|string|max:1000',
        ]);

        $store = $user->store
            ? tap($user->store)->update($data)
            : $user->store()->create($data);

        return response()->json($store);
    }

    public function products(Request $request)
    {
        abort_unless($request->user()->store, 422, 'Buat toko terlebih dahulu.');

        return response()->json($request->user()->store->products()->latest()->paginate(15));
    }

    public function productStore(Request $request)
    {
        $user = $request->user();
        abort_unless($user->store, 422, 'Buat toko terlebih dahulu.');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        return response()->json($user->store->products()->create($data), 201);
    }

    public function productUpdate(Request $request, \App\Models\Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($data);

        return response()->json($product);
    }

    public function productDelete(Request $request, \App\Models\Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return response()->json(['message' => 'Produk dihapus.']);
    }

    public function orders(Request $request)
    {
        abort_unless($request->user()->store, 422, 'Anda belum memiliki toko.');

        return response()->json(
            Order::where('store_id', $request->user()->store->id)
                ->with(['items', 'statusHistories', 'buyer'])
                ->latest()
                ->paginate(15)
        );
    }

    public function processOrder(Request $request, Order $order)
    {
        try {
            $order = OrderService::sellerProcess($request->user(), $order);

            return response()->json($order);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
