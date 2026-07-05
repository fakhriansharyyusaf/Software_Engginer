<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">🛒 Dashboard Buyer</h1>
            <div class="bg-blue-50 border border-blue-100 text-blue-700 text-sm font-medium px-4 py-2 rounded-xl">
                💰 Rp {{ number_format($user->wallet->balance ?? 0, 0, ',', '.') }}
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 flex-wrap">
            @foreach(['wallet' => 'Wallet', 'addresses' => 'Alamat', 'cart' => 'Keranjang', 'checkout' => 'Checkout', 'orders' => 'Riwayat', 'reports' => 'Laporan'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition {{ $tab === $key ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                    @if($key === 'cart' && $cart && $cart->items->count())
                        <span class="bg-red-500 text-white text-xs rounded-full px-1.5 ml-1">{{ $cart->items->count() }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- WALLET TAB --}}
        @if($tab === 'wallet')
            @if(session('wallet_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('wallet_message') }}</div>
            @endif
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Top Up Wallet</h2>
                <form wire:submit.prevent="topUp" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (dummy)</label>
                        <input type="number" wire:model="topupAmount"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                               placeholder="50000">
                        @error('topupAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2 rounded-lg transition">Top Up Sekarang</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800 text-sm">Riwayat Transaksi</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($walletHistory as $tx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-500">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tx->type }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tx->description }}</td>
                                <td class="px-4 py-3 text-right font-medium {{ $tx->amount >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $tx->amount >= 0 ? '+' : '' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ADDRESSES TAB --}}
        @if($tab === 'addresses')
            @if(session('address_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('address_message') }}</div>
            @endif
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editingAddressId ? 'Edit Alamat' : 'Tambah Alamat' }}</h2>
                <form wire:submit.prevent="saveAddress" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                        <input type="text" wire:model="addressLabel" placeholder="Rumah, Kantor..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                        <input type="text" wire:model="recipientName"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('recipientName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="text" wire:model="phone"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea wire:model="addressLine" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                        @error('addressLine') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                            <input type="text" wire:model="city"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <input type="text" wire:model="postalCode"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition">Simpan</button>
                        @if($editingAddressId)
                            <button type="button" wire:click="resetAddressForm"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition">Batal</button>
                        @endif
                    </div>
                </form>
            </div>

            <div class="space-y-3">
                @foreach($addresses as $address)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $address->label }}</span>
                                    @if($address->is_default)
                                        <span class="text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full">Utama</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ $address->recipient_name }} &bull; {{ $address->phone }}</p>
                                <p class="text-xs text-gray-500">{{ $address->address_line }}, {{ $address->city }} {{ $address->postal_code }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="editAddress({{ $address->id }})" class="text-xs text-blue-600 hover:underline">Edit</button>
                                @if(!$address->is_default)
                                    <button wire:click="makeDefaultAddress({{ $address->id }})" class="text-xs text-gray-500 hover:underline">Jadikan Utama</button>
                                @endif
                                <button x-on:click="$store.confirmModal.open('Hapus alamat ini?', () => $wire.deleteAddress({{ $address->id }}))"
                                        class="text-xs text-red-500 hover:underline">Hapus</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CART TAB --}}
        @if($tab === 'cart')
            <p class="text-xs text-gray-400 mb-4">Single-store checkout: satu keranjang hanya boleh berisi produk dari satu toko.</p>
            @if($cart && $cart->items->count())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-800">🏪 {{ $cart->store->name }}</span>
                        <button x-on:click="$store.confirmModal.open('Kosongkan seluruh keranjang? Tindakan ini tidak bisa dibatalkan.', () => $wire.clearCart())"
                                class="text-xs text-red-500 hover:underline">Kosongkan</button>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($cart->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        <div class="flex items-center gap-3">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/'.$item->product->image) }}" class="w-10 h-10 object-cover rounded-lg border border-gray-100 flex-shrink-0">
                                            @else
                                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">📦</div>
                                            @endif
                                            {{ $item->product->name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <input type="number" min="1" max="{{ $item->product->stock }}" value="{{ $item->quantity }}"
                                               wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                               class="w-16 border border-gray-200 rounded-lg px-2 py-1 text-center text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="removeItem({{ $item->id }})" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Subtotal: <span class="font-bold text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span></span>
                        <button wire:click="setTab('checkout')"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Lanjut ke Checkout →
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center py-16 text-gray-400">
                    <p class="text-4xl mb-2">🛒</p>
                    <p class="text-sm">Keranjang kosong. <a href="{{ route('catalog') }}" class="text-blue-600 hover:underline">Cari produk</a></p>
                </div>
            @endif
        @endif

        {{-- CHECKOUT TAB --}}
        @if($tab === 'checkout')
            @error('checkout') <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-2 text-sm mb-4">{{ $message }}</div> @enderror
            @if(!$cart || $cart->items->count() === 0)
                <div class="text-center py-16 text-gray-400">
                    <p class="text-4xl mb-2">🛒</p>
                    <p class="text-sm">Keranjang kosong, tidak ada yang bisa di-checkout.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-5">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                            <h3 class="font-semibold text-gray-800 text-sm mb-3">Metode Pengiriman</h3>
                            <div class="space-y-2">
                                @foreach(['instant' => ['label' => 'Instant', 'desc' => 'Rp 20.000 · SLA 3 jam'], 'next_day' => ['label' => 'Next Day', 'desc' => 'Rp 12.000 · SLA 24 jam'], 'regular' => ['label' => 'Regular', 'desc' => 'Rp 8.000 · SLA 72 jam']] as $val => $opt)
                                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 {{ $deliveryMethod === $val ? 'border-blue-400 bg-blue-50' : 'border-gray-200' }}">
                                        <input type="radio" wire:model="deliveryMethod" value="{{ $val }}" class="accent-blue-600">
                                        <div>
                                            <span class="text-sm font-medium text-gray-800">{{ $opt['label'] }}</span>
                                            <span class="text-xs text-gray-400 ml-2">{{ $opt['desc'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                            <h3 class="font-semibold text-gray-800 text-sm mb-3">Kode Voucher / Promo</h3>
                            <div class="flex gap-2">
                                <input type="text" wire:model="discountCode" placeholder="Contoh: SEAPEDIA10"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <button wire:click="previewDiscount" type="button"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition">Cek</button>
                            </div>
                            @if($discountPreview)
                                @if($discountPreview['ok'])
                                    <p class="text-green-600 text-xs mt-2">✓ Kode {{ strtoupper($discountPreview['type']) }} valid! Potongan Rp {{ number_format($discountPreview['amount'], 0, ',', '.') }}</p>
                                @else
                                    <p class="text-red-500 text-xs mt-2">{{ $discountPreview['message'] }}</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-fit">
                        <h3 class="font-semibold text-gray-800 text-sm mb-4">Ringkasan Belanja</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between text-gray-500"><span>Diskon</span><span class="text-green-500">- Rp {{ number_format($discountPreview['ok'] ?? false ? $discountPreview['amount'] : 0, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between text-gray-500"><span>Ongkos Kirim</span><span>Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between text-gray-500"><span>PPN 12%</span><span>Rp {{ number_format($ppn, 0, ',', '.') }}</span></div>
                            <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-800 text-base">
                                <span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">PPN dihitung dari (subtotal - diskon). Ongkir tidak dikenakan PPN.</p>
                        <button wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout"
                                class="w-full mt-4 bg-green-500 hover:bg-green-600 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-xl transition text-sm">
                            <span wire:loading.remove wire:target="checkout">💳 Bayar dengan Wallet</span>
                            <span wire:loading wire:target="checkout">⏳ Memproses pembayaran...</span>
                        </button>
                    </div>
                </div>
            @endif
        @endif

        {{-- ORDERS TAB --}}
        @if($tab === 'orders')
            @if(session('checkout_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('checkout_message') }}</div>
            @endif
            <div class="space-y-4">
                @forelse($orders as $order)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Order #{{ $order->id }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Toko: {{ $order->store->name }}</p>
                                <x-ui.status-badge :status="$order->status" class="mt-2" />
                            </div>
                            <p class="font-bold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                        <details class="mt-3">
                            <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">Detail &amp; riwayat status</summary>
                            <ul class="mt-2 space-y-1 pl-3 border-l-2 border-gray-100">
                                @foreach($order->items as $item)
                                    <li class="text-xs text-gray-500">{{ $item->product_name }} x{{ $item->quantity }} &mdash; Rp {{ number_format($item->subtotal, 0, ',', '.') }}</li>
                                @endforeach
                            </ul>
                            <ol class="mt-2 space-y-1 pl-3 border-l-2 border-blue-100">
                                @foreach($order->statusHistories as $h)
                                    <li class="text-xs text-gray-500">{{ $h->status }} &mdash; {{ $h->changed_at->format('d M Y H:i') }}</li>
                                @endforeach
                            </ol>
                        </details>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <p class="text-4xl mb-2">📋</p>
                        <p class="text-sm">Belum ada order.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        @endif

        {{-- REPORTS TAB --}}
        @if($tab === 'reports')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total Pengeluaran</p>
                <p class="text-3xl font-bold text-red-500">Rp {{ number_format($spendingTotal, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-3">Dari semua transaksi checkout yang berhasil dibayar.</p>
            </div>
        @endif

    </div>
</div>
