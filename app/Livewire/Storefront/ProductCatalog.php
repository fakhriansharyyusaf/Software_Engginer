<?php

namespace App\Livewire\Storefront;

use App\Services\Api\CatalogApiService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $pageName = 'page';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $apiService = app(CatalogApiService::class);
        $payload = $apiService->getProducts($this->search);

        $items = collect($payload['products'] ?? [])
            ->map(function ($product) {
                $item = (object) $product;
                $item->store = (object) ($product['store'] ?? []);

                return $item;
            });

        $page = $this->getPage();
        $perPage = $payload['meta']['per_page'] ?? 9;
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $products = new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => $this->pageName]
        );

        return view('livewire.public.product-catalog', [
            'products' => $products,
            'apiError' => $payload['meta']['error'] ?? null,
        ]);
    }
}
