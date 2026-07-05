<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-10">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-1">Katalog Produk</h1>
            <p class="text-gray-500 text-sm">Semua produk dari berbagai toko &mdash; SEAPEDIA adalah marketplace multi-toko.</p>
        </div>

        @if($apiError)
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $apiError }}
            </div>
        @endif

        {{-- Search --}}
        <div class="relative mb-8">
            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">🔍</span>
            <input type="text"
                   wire:model.live.debounce.400ms="search"
                   placeholder="Cari produk..."
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @forelse($products as $product)
                <a href="{{ route('product.detail', $product->id) }}"
                   class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group block">
                    <div class="h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm group-hover:bg-gray-200 transition relative overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover absolute inset-0">
                        @else
                            🖼 Gambar Produk
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 text-sm mb-1 truncate">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 mb-1">🏪 {{ $product->store->name }}</p>
                        <p class="text-xs text-gray-400 mb-2">Stok: {{ $product->stock }}</p>
                        <p class="text-base font-bold text-red-500">Rp {{ number_format((int) $product->price, 0, ',', '.') }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-4 text-center py-16 text-gray-400">
                    <p class="text-4xl mb-3">🔎</p>
                    <p class="text-sm">Tidak ada produk yang cocok dengan pencarianmu.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $products->links() }}
        </div>

    </div>
</div>
