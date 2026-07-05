<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">🏪 Dashboard Seller</h1>

        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 w-fit flex-wrap">
            @foreach(['store' => 'Profil Toko', 'products' => 'Produk', 'orders' => 'Order Masuk', 'reports' => 'Laporan'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === $key ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- STORE TAB --}}
        @if($tab === 'store')
            @if(session('store_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('store_message') }}</div>
            @endif
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Profil Toko</h2>
                <form wire:submit.prevent="saveStore" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko (harus unik)</label>
                        <input type="text" wire:model="storeName"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('storeName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea wire:model="storeDescription" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">Simpan Toko</button>
                </form>
            </div>
        @endif

        {{-- PRODUCTS TAB --}}
        @if($tab === 'products')
            @if(!$store)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 text-sm">
                    Buat profil toko dulu di tab "Profil Toko" sebelum menambah produk.
                </div>
            @else
                @if(session('product_message'))
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('product_message') }}</div>
                @endif
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-lg mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editingProductId ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2>
                    <form wire:submit.prevent="saveProduct" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                            <input type="text" wire:model="productName"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @error('productName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea wire:model="productDescription" rows="2"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                <input type="number" wire:model="productPrice"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                @error('productPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                <input type="number" wire:model="productStock"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                @error('productStock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
                            <input type="file" wire:model="productImage" accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                            @error('productImage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            {{-- Preview foto baru --}}
                            @if($productImage)
                                <div class="mt-2">
                                    <img src="{{ $productImage->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-400 mt-1">Preview foto baru</p>
                                </div>
                            @elseif($editingProductId)
                                @php $editingProduct = $store?->products()->find($editingProductId); @endphp
                                @if($editingProduct?->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$editingProduct->image) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-400 mt-1">Foto saat ini (kosongkan jika tidak ingin mengubah)</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                                {{ $editingProductId ? 'Update' : 'Tambah Produk' }}
                            </button>
                            @if($editingProductId)
                                <button type="button" wire:click="resetProductForm"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition">Batal</button>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Foto</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Stok</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-100">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-xl">📦</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $product->stock }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button wire:click="editProduct({{ $product->id }})" class="text-blue-600 hover:underline text-xs mr-3">Edit</button>
                                        <button x-on:click="$store.confirmModal.open('Hapus produk &quot;{{ addslashes($product->name) }}&quot;? Tindakan ini tidak bisa dibatalkan.', () => $wire.deleteProduct({{ $product->id }}))"
                                                class="text-red-500 hover:underline text-xs">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada produk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100">{{ $products->links() }}</div>
                </div>
            @endif
        @endif

        {{-- ORDERS TAB --}}
        @if($tab === 'orders')
            @if(session('order_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('order_message') }}</div>
            @endif
            @error('order') <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-2 text-sm mb-4">{{ $message }}</div> @enderror

            <div class="space-y-4">
                @forelse($orders as $order)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Order #{{ $order->id }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Buyer: {{ $order->buyer->username }} &bull; {{ $order->delivery_method }}</p>
                                <x-ui.status-badge :status="$order->status" class="mt-2" />
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                @if($order->status === 'Sedang Dikemas')
                                    <button wire:click="processOrder({{ $order->id }})" wire:loading.attr="disabled" wire:target="processOrder({{ $order->id }})"
                                            class="mt-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                        <span wire:loading.remove wire:target="processOrder({{ $order->id }})">Proses Pesanan</span>
                                        <span wire:loading wire:target="processOrder({{ $order->id }})">Memproses...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <details class="mt-3">
                            <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">Riwayat status ({{ $order->statusHistories->count() }})</summary>
                            <ol class="mt-2 space-y-1 pl-4">
                                @foreach($order->statusHistories as $h)
                                    <li class="text-xs text-gray-500">{{ $h->status }} &mdash; {{ $h->changed_at->format('d M Y H:i') }}</li>
                                @endforeach
                            </ol>
                        </details>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <p class="text-4xl mb-2">📦</p>
                        <p class="text-sm">Belum ada order masuk.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->links() : '' }}</div>
        @endif

        {{-- REPORTS TAB --}}
        @if($tab === 'reports')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total Pendapatan Bersih</p>
                <p class="text-3xl font-bold text-green-500">Rp {{ number_format($incomeTotal, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-3">Pendapatan dicatat saat order "Pesanan Selesai". Reversal otomatis jika terjadi refund overdue.</p>
            </div>
        @endif

    </div>
</div>
