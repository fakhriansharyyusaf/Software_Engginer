<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function wallet(Request $request)
    {
        $user = $request->user()->load('wallet');

        return response()->json([
            'balance' => $user->wallet->balance ?? 0,
            'transactions' => $user->walletTransactions()->latest()->paginate(20),
        ]);
    }

    public function topUp(Request $request)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:10000|max:50000000']);
        WalletService::credit($request->user(), (float) $data['amount'], 'topup', 'Top up saldo (dummy) via API');

        return response()->json(['message' => 'Top up berhasil.', 'balance' => $request->user()->wallet->fresh()->balance]);
    }

    public function addresses(Request $request)
    {
        return response()->json($request->user()->addresses);
    }

    public function addressStore(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        return response()->json($request->user()->addresses()->create($data), 201);
    }

    public function addressUpdate(Request $request, \App\Models\Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        $address->update($data);

        return response()->json($address);
    }

    public function addressDelete(Request $request, \App\Models\Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $address->delete();

        return response()->json(['message' => 'Alamat dihapus.']);
    }

    public function cart(Request $request)
    {
        $cart = CartService::ensureCart($request->user())->load('items.product.store');

        return response()->json($cart);
    }

    public function cartAdd(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($data['product_id']);

        try {
            $cart = CartService::addItem($request->user(), $product, $data['quantity']);

            return response()->json($cart->load('items.product'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function cartUpdate(Request $request, int $cartItemId)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:0']);
        $cart = CartService::updateQuantity($request->user(), $cartItemId, $data['quantity']);

        return response()->json($cart->load('items.product'));
    }

    public function cartClear(Request $request)
    {
        $cart = CartService::clear($request->user());

        return response()->json($cart);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'delivery_method' => 'required|in:instant,next_day,regular',
            'discount_code' => 'nullable|string',
        ]);

        try {
            $order = CheckoutService::checkout(
                $request->user(),
                $data['delivery_method'],
                $data['discount_code'] ?? null
            );

            return response()->json($order->load('items', 'statusHistories'), 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function orders(Request $request)
    {
        return response()->json(
            $request->user()->ordersAsBuyer()->with(['items', 'statusHistories', 'store'])->latest()->paginate(15)
        );
    }

    public function orderDetail(Request $request, \App\Models\Order $order)
    {
        $this->authorize('view', $order);

        return response()->json($order->load(['items', 'statusHistories', 'store', 'deliveryJob']));
    }
}
