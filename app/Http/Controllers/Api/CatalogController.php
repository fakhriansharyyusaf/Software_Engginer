<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function products(Request $request)
    {
        $products = Product::with('store')
            ->when($request->query('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(12);

        return response()->json($products);
    }

    public function productDetail(Product $product)
    {
        return response()->json($product->load('store'));
    }

    public function storeDetail(Store $store)
    {
        return response()->json($store->load('products'));
    }
}
