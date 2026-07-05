<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1">
                <span class="text-3xl font-bold text-blue-600">SEA</span><span class="text-3xl font-bold text-gray-800">PEDIA</span>
            </a>
            <p class="text-gray-500 text-sm mt-1">Marketplace Multi-Toko Terpercaya</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">
                {{ $isLogin ? 'Masuk ke Akun Anda' : 'Buat Akun Baru' }}
            </h2>

            <form wire:submit.prevent="{{ $isLogin ? 'login' : 'register' }}" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" wire:model="username"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="Masukkan username" required>
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if(!$isLogin)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="email@contoh.com" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" wire:model="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="••••••••" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if(!$isLogin)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                           placeholder="••••••••" required>
                </div>
                @endif

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition text-sm mt-2">
                    {{ $isLogin ? 'Masuk' : 'Daftar Sekarang' }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-5">
                {{ $isLogin ? 'Belum punya akun?' : 'Sudah punya akun?' }}
                <button wire:click="toggleMode" class="text-blue-600 font-medium hover:underline bg-transparent border-none cursor-pointer">
                    {{ $isLogin ? 'Daftar di sini' : 'Login di sini' }}
                </button>
            </p>
        </div>

    </div>
</div>
