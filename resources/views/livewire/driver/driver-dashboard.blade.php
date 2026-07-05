<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">🚗 Dashboard Driver</h1>
            <div class="flex gap-3">
                <div class="bg-orange-50 border border-orange-100 text-orange-700 text-sm font-medium px-4 py-2 rounded-xl">
                    🏆 Rp {{ number_format($totalEarnings, 0, ',', '.') }}
                </div>
                <div class="bg-blue-50 border border-blue-100 text-blue-700 text-sm font-medium px-4 py-2 rounded-xl">
                    💰 Rp {{ number_format($driver->wallet->balance ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 w-fit">
            @foreach(['available' => 'Job Tersedia', 'active' => 'Job Aktif', 'history' => 'Riwayat'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === $key ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if(session('job_message'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2 text-sm mb-4">{{ session('job_message') }}</div>
        @endif
        @error('job') <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-2 text-sm mb-4">{{ $message }}</div> @enderror

        {{-- AVAILABLE JOBS --}}
        @if($tab === 'available')
            <p class="text-xs text-gray-400 mb-4">Hanya order dengan status "Menunggu Pengirim" yang bisa diambil.</p>
            <div class="space-y-3">
                @forelse($availableJobs as $job)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Job #{{ $job->id }} &mdash; Order #{{ $job->order->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">🏪 {{ $job->order->store->name }} &bull; {{ $job->order->delivery_method }}</p>
                        </div>
                        <button wire:click="takeJob({{ $job->id }})" wire:loading.attr="disabled" wire:target="takeJob({{ $job->id }})"
                                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            <span wire:loading.remove wire:target="takeJob({{ $job->id }})">Ambil Job</span>
                            <span wire:loading wire:target="takeJob({{ $job->id }})">Mengambil...</span>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <p class="text-4xl mb-2">📭</p>
                        <p class="text-sm">Belum ada job tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $availableJobs->links() }}</div>
        @endif

        {{-- ACTIVE JOBS --}}
        @if($tab === 'active')
            <div class="space-y-3">
                @forelse($myActiveJobs as $job)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Job #{{ $job->id }} &mdash; Order #{{ $job->order->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">🏪 {{ $job->order->store->name }} &bull; Buyer: {{ $job->order->buyer->username }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Diambil: {{ $job->taken_at?->format('d M Y H:i') }}</p>
                        </div>
                        <button wire:click="completeJob({{ $job->id }})" wire:loading.attr="disabled" wire:target="completeJob({{ $job->id }})"
                                class="bg-green-500 hover:bg-green-600 disabled:opacity-60 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            <span wire:loading.remove wire:target="completeJob({{ $job->id }})">✓ Selesai</span>
                            <span wire:loading wire:target="completeJob({{ $job->id }})">Menyimpan...</span>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <p class="text-4xl mb-2">🚗</p>
                        <p class="text-sm">Tidak ada job aktif.</p>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- HISTORY --}}
        @if($tab === 'history')
            <div class="space-y-3">
                @forelse($myHistory as $job)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                        <p class="font-semibold text-gray-800 text-sm">Job #{{ $job->id }} &mdash; Order #{{ $job->order->id }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">🏪 {{ $job->order->store->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Selesai: {{ $job->delivered_at?->format('d M Y H:i') }}</p>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <p class="text-4xl mb-2">📋</p>
                        <p class="text-sm">Belum ada riwayat.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $myHistory->links() }}</div>
        @endif

    </div>
</div>
