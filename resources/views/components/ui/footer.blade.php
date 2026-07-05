<footer class="bg-gray-900 text-white mt-auto">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-5 border-b border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-sm">🐟</div>
                <span class="text-lg font-black"><span class="text-blue-400">SEA</span>PEDIA</span>
            </div>
            <div class="flex gap-6 text-sm text-gray-400">
                <a href="{{ route('catalog') }}" class="hover:text-white transition">Katalog</a>
                @guest
                    <a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a>
                @endguest
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-4">&copy; {{ date('Y') }} SEAPEDIA &mdash; Marketplace Multi-Toko. Dibuat untuk COMPFEST 18 Software Engineering Academy.</p>
    </div>
</footer>
