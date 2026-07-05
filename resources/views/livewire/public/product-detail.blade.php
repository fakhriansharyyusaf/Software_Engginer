<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-col md:flex-row gap-0">

                {{-- Gambar --}}
                <div class="md:w-80 h-72 md:h-auto bg-gray-100 flex items-center justify-center text-gray-400 text-sm flex-shrink-0 relative overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-5xl">📦</span>
                            <span class="text-xs">Belum ada foto</span>
                        </div>
                    @endif
                </div>

                {{-- Detail --}}
                <div class="flex-1 p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>
                    <p class="text-3xl font-bold text-red-500 mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mb-4">Stok tersedia: <span class="font-medium text-gray-700">{{ $product->stock }}</span></p>

                    {{-- Info Toko --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-5">
                        <p class="font-semibold text-blue-700 text-sm">🏪 {{ $product->store->name }}</p>
                        @if($product->store->description)
                            <p class="text-blue-600 text-xs mt-1">{{ $product->store->description }}</p>
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Deskripsi Produk</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>

                    {{-- Aksi --}}
                    @auth
                        @if(auth()->user()->currentActiveRole() === 'Buyer')
                            @if($conflictMessage)
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl px-4 py-3 mb-4 text-sm">
                                    {{ $conflictMessage }}
                                    <div class="mt-3">
                                        <button wire:click="clearCartAndAdd"
                                                class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg transition">
                                            Kosongkan Keranjang &amp; Tambahkan Ini
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center gap-3">
                                <input type="number" wire:model="quantity" min="1" max="{{ $product->stock }}"
                                       class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 text-center">
                                <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart"
                                        class="flex-1 bg-green-500 hover:bg-green-600 disabled:opacity-60 text-white font-medium py-2 rounded-lg transition text-sm">
                                    <span wire:loading.remove wire:target="addToCart">🛒 Tambah ke Keranjang</span>
                                    <span wire:loading wire:target="addToCart">⏳ Menambahkan...</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Satu keranjang hanya bisa berisi produk dari satu toko.</p>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-500">
                                Beralih ke peran <strong>Buyer</strong> untuk membeli produk ini.
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition text-sm">
                            Login untuk Membeli
                        </a>
                    @endauth

                </div>
            </div>
        </div>
    </div>

</div>
