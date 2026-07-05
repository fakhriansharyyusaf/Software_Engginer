<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-10">

        {{-- Greeting --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Selamat datang, {{ $user->username }} 👋</h1>
            <p class="text-gray-500 text-sm mt-1">
                Peran yang kamu miliki:
                @foreach($roles as $role)
                    <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $role->name }}</span>
                @endforeach
            </p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            {{-- Peran Aktif --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Peran Aktif</p>
                <div class="flex items-center gap-3">
                    <span class="text-2xl">
                        @if($activeRole === 'Seller') 🏪
                        @elseif($activeRole === 'Buyer') 🛒
                        @elseif($activeRole === 'Driver') 🚗
                        @elseif($activeRole === 'Admin') ⚙️
                        @endif
                    </span>
                    <span class="text-xl font-bold text-gray-800">{{ $activeRole }}</span>
                </div>
                @if($roles->count() > 1)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($roles as $role)
                            @if($role->name !== $activeRole)
                                <button wire:click="switchRole('{{ $role->name }}')"
                                        class="text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 text-gray-600 px-3 py-1 rounded-full transition">
                                    Ganti ke {{ $role->name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Saldo Wallet --}}
            <div class="bg-gradient-to-br from-blue-600 to-blue-400 rounded-2xl shadow-sm p-5 text-white">
                <p class="text-xs text-blue-200 uppercase tracking-wide mb-1">Saldo Wallet</p>
                <p class="text-2xl font-bold">Rp {{ number_format($walletBalance, 0, ',', '.') }}</p>
                <p class="text-xs text-blue-100 mt-1">Digunakan untuk transaksi di SEAPEDIA</p>
            </div>
        </div>

        {{-- Menu --}}
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Menu Peran: {{ $activeRole }}</h2>
        <div class="flex flex-wrap gap-3">
            @if($activeRole === 'Seller')
                <a href="{{ route('seller.dashboard') }}"
                   class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-3 rounded-xl transition text-sm shadow-sm">
                    🏪 Kelola Toko &amp; Produk
                </a>
            @elseif($activeRole === 'Buyer')
                <a href="{{ route('buyer.dashboard') }}"
                   class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-medium px-5 py-3 rounded-xl transition text-sm shadow-sm">
                    🛒 Wallet, Cart &amp; Checkout
                </a>
            @elseif($activeRole === 'Driver')
                <a href="{{ route('driver.dashboard') }}"
                   class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-medium px-5 py-3 rounded-xl transition text-sm shadow-sm">
                    🚗 Cari &amp; Kelola Job
                </a>
            @elseif($activeRole === 'Admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white font-medium px-5 py-3 rounded-xl transition text-sm shadow-sm">
                    ⚙️ Admin Panel
                </a>
            @endif
            <a href="{{ route('catalog') }}"
               class="flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-medium px-5 py-3 rounded-xl transition text-sm shadow-sm">
                🛍 Lihat Katalog
            </a>
        </div>

    </div>
</div>
