<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">⚙️ Admin Panel</h1>

        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 w-fit flex-wrap">
            @foreach(['monitoring' => 'Monitoring', 'vouchers' => 'Voucher', 'promos' => 'Promo', 'overdue' => 'Waktu & Overdue'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === $key ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- MONITORING --}}
        @if($tab === 'monitoring')
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                @foreach([
                    ['label' => 'Users', 'value' => $userCount, 'icon' => '👤', 'color' => 'blue'],
                    ['label' => 'Toko', 'value' => $storeCount, 'icon' => '🏪', 'color' => 'indigo'],
                    ['label' => 'Produk', 'value' => $productCount, 'icon' => '📦', 'color' => 'purple'],
                    ['label' => 'Order', 'value' => $orderCount, 'icon' => '🛒', 'color' => 'green'],
                    ['label' => 'Voucher', 'value' => $voucherCount, 'icon' => '🎟', 'color' => 'yellow'],
                    ['label' => 'Promo', 'value' => $promoCount, 'icon' => '🏷', 'color' => 'orange'],
                    ['label' => 'Delivery Jobs', 'value' => $deliveryJobCount, 'icon' => '🚗', 'color' => 'red'],
                    ['label' => 'Overdue', 'value' => $overdueCount, 'icon' => '⚠️', 'color' => 'red'],
                ] as $stat)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                        <div class="text-2xl mb-1">{{ $stat['icon'] }}</div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                        <p class="text-xs text-gray-400">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-semibold text-gray-800 text-sm mb-3">Order per Status</h3>
                    <div class="space-y-2">
                        @forelse($ordersByStatus as $status => $count)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">{{ $status }}</span>
                                <span class="font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded-full text-xs">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm">Belum ada order.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 text-sm">10 Order Terbaru</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase">ID</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase">Buyer</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-500">#{{ $order->id }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $order->buyer->username }}</td>
                                    <td class="px-4 py-2"><x-ui.status-badge :status="$order->status" /></td>
                                    <td class="px-4 py-2 text-right text-gray-700 font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-sm">Belum ada order.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- VOUCHERS --}}
        @if($tab === 'vouchers')
            @if(session('voucher_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('voucher_message') }}</div>
            @endif
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Buat Voucher</h2>
                <form wire:submit.prevent="createVoucher" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                        <input type="text" wire:model="voucherCode"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('voucherCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select wire:model="voucherType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="percent">Persen (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                            <input type="number" wire:model="voucherValue"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @error('voucherValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kedaluwarsa</label>
                            <input type="date" wire:model="voucherExpiry"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @error('voucherExpiry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batas Penggunaan</label>
                            <input type="number" wire:model="voucherLimit"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition">Buat Voucher</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nilai</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kedaluwarsa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Terpakai</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($vouchers as $v)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono font-medium text-gray-800">
                                    <button type="button" x-data="{ copied: false }"
                                            x-on:click="navigator.clipboard.writeText('{{ $v->code }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="inline-flex items-center gap-1.5 hover:text-blue-600 transition" title="Klik untuk menyalin">
                                        {{ $v->code }}
                                        <span x-show="!copied" class="text-gray-300">📋</span>
                                        <span x-show="copied" style="display:none;" class="text-green-500 text-xs">✓ tersalin</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $v->discount_type }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $v->discount_type === 'percent' ? $v->discount_value.'%' : 'Rp '.number_format($v->discount_value,0,',','.') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $v->expiry_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $v->used_count }}/{{ $v->usage_limit }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                        x-on:click="$store.detailModal.open('Detail Voucher {{ $v->code }}', {
                                            'Kode': '{{ $v->code }}',
                                            'Tipe Diskon': '{{ $v->discount_type === 'percent' ? 'Persentase' : 'Nominal Tetap' }}',
                                            'Nilai': '{{ $v->discount_type === 'percent' ? $v->discount_value.'%' : 'Rp '.number_format($v->discount_value,0,',','.') }}',
                                            'Kedaluwarsa': '{{ $v->expiry_date->format('d M Y H:i') }}',
                                            'Batas Pemakaian': '{{ $v->usage_limit }}',
                                            'Sudah Terpakai': '{{ $v->used_count }}',
                                            'Sisa Kuota': '{{ max(0, $v->usage_limit - $v->used_count) }}',
                                            'Status': '{{ $v->expiry_date->isPast() ? 'Kedaluwarsa' : ($v->used_count >= $v->usage_limit ? 'Kuota Habis' : 'Aktif') }}',
                                            'Dibuat': '{{ $v->created_at->format('d M Y H:i') }}'
                                        })"
                                        class="text-xs text-blue-600 hover:underline">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada voucher.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100">{{ $vouchers->links() }}</div>
            </div>
        @endif

        {{-- PROMOS --}}
        @if($tab === 'promos')
            @if(session('promo_message'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('promo_message') }}</div>
            @endif
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Buat Promo</h2>
                <form wire:submit.prevent="createPromo" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                        <input type="text" wire:model="promoCode"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('promoCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select wire:model="promoType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="percent">Persen (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                            <input type="number" wire:model="promoValue"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @error('promoValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kedaluwarsa</label>
                        <input type="date" wire:model="promoExpiry"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('promoExpiry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition">Buat Promo</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nilai</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kedaluwarsa</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($promos as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono font-medium text-gray-800">
                                    <button type="button" x-data="{ copied: false }"
                                            x-on:click="navigator.clipboard.writeText('{{ $p->code }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="inline-flex items-center gap-1.5 hover:text-blue-600 transition" title="Klik untuk menyalin">
                                        {{ $p->code }}
                                        <span x-show="!copied" class="text-gray-300">📋</span>
                                        <span x-show="copied" style="display:none;" class="text-green-500 text-xs">✓ tersalin</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->discount_type }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->discount_type === 'percent' ? $p->discount_value.'%' : 'Rp '.number_format($p->discount_value,0,',','.') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->expiry_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                        x-on:click="$store.detailModal.open('Detail Promo {{ $p->code }}', {
                                            'Kode': '{{ $p->code }}',
                                            'Tipe Diskon': '{{ $p->discount_type === 'percent' ? 'Persentase' : 'Nominal Tetap' }}',
                                            'Nilai': '{{ $p->discount_type === 'percent' ? $p->discount_value.'%' : 'Rp '.number_format($p->discount_value,0,',','.') }}',
                                            'Kedaluwarsa': '{{ $p->expiry_date->format('d M Y H:i') }}',
                                            'Status': '{{ $p->expiry_date->isPast() ? 'Kedaluwarsa' : 'Aktif' }}',
                                            'Catatan': 'Promo tidak dibatasi kuota pemakaian (unlimited use sebelum kedaluwarsa)',
                                            'Dibuat': '{{ $p->created_at->format('d M Y H:i') }}'
                                        })"
                                        class="text-xs text-blue-600 hover:underline">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada promo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100">{{ $promos->links() }}</div>
            </div>
        @endif

        {{-- OVERDUE / TIME --}}
        @if($tab === 'overdue')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">Simulasi Waktu &amp; Overdue</h2>
                <p class="text-sm text-gray-500 mb-5">
                    Waktu simulasi: <strong class="text-gray-800">{{ $simulatedNow->format('d M Y H:i') }}</strong>
                    <span class="text-gray-400">(offset: {{ $timeOffsetDays }} hari)</span>
                </p>

                @if(session('time_message'))
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('time_message') }}</div>
                @endif
                @if(session('overdue_message'))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg px-4 py-2 text-sm mb-4">{{ session('overdue_message') }}</div>
                @endif

                <div class="flex gap-3 flex-wrap">
                    <button wire:click="simulateNextDay"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        ⏭ Simulasikan +1 Hari
                    </button>
                    <button wire:click="runOverdueCheck"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        🔍 Jalankan Pengecekan Overdue
                    </button>
                </div>

                <div class="mt-5 bg-gray-50 rounded-xl px-4 py-3 text-xs text-gray-500 leading-relaxed">
                    SLA: Instant = 3 jam &bull; Next Day = 24 jam &bull; Regular = 72 jam (dihitung dari checkout).
                    Order yang melewati SLA dan belum selesai akan di-refund otomatis ke wallet Buyer, stok dikembalikan,
                    pendapatan Seller dibatalkan, dan status berubah menjadi "Dikembalikan". Proses ini idempotent.
                </div>
            </div>
        @endif

    </div>
</div>
