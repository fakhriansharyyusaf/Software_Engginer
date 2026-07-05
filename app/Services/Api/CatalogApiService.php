<?php

namespace App\Services\Api;

use App\Http\Controllers\Api\CatalogController;
use Illuminate\Http\Request;

class CatalogApiService
{
    public function getProducts(?string $search = null): array
    {
        $request = Request::create('/api/catalog/products', 'GET', ['search' => $search]);
        $response = app(CatalogController::class)->products($request);

        $payload = $response->getData(true);

        if ($response->getStatusCode() >= 400) {
            return [
                'products' => [],
                'meta' => [
                    'error' => $payload['message'] ?? 'Gagal memuat katalog.',
                ],
            ];
        }

        $products = array_map(function ($product) {
            return [
                'id' => $product['id'] ?? null,
                'name' => $product['name'] ?? '-',
                'price' => $product['price'] ?? 0,
                'stock' => $product['stock'] ?? 0,
                'image' => $product['image'] ?? null,
                'store' => [
                    'name' => $product['store']['name'] ?? '-',
                ],
            ];
        }, $payload['data'] ?? []);

        return [
            'products' => $products,
            'meta' => [
                'current_page' => $payload['current_page'] ?? 1,
                'last_page' => $payload['last_page'] ?? 1,
                'per_page' => $payload['per_page'] ?? 12,
            ],
        ];
    }
}
