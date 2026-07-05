@php
    $navUser = auth()->user()?->load('roles', 'wallet');
    $navActiveRole = $navUser?->currentActiveRole();
    $navRoleIcons = ['Seller' => '🏪', 'Buyer' => '🛒', 'Driver' => '🚗', 'Admin' => '⚙️'];
    $navRoleRoutes = ['Seller' => 'seller.dashboard', 'Buyer' => 'buyer.dashboard', 'Driver' => 'driver.dashboard', 'Admin' => 'admin.dashboard'];
@endphp
<nav x-data="{ mobileOpen: false, roleMenu: false }" class="bg-white/90 backdrop-blur border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="h-16 flex items-center justify-between">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-lg shadow-sm">🐟</div>
                <span class="text-xl font-black tracking-tight"><span class="text-blue-600">SEA</span><span class="text-gray-800">PEDIA</span></span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('catalog') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition rounded-lg">Katalog</a>

                @auth
                    @if($navActiveRole && isset($navRoleRoutes[$navActiveRole]))
                        <a href="{{ route($navRoleRoutes[$navActiveRole]) }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition rounded-lg">
                            {{ $navRoleIcons[$navActiveRole] }} Menu {{ $navActiveRole }}
                        </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition rounded-lg">Dashboard</a>

                    @if(in_array($navActiveRole, ['Buyer', 'Seller', 'Driver']))
                        <span class="ml-1 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                            💰 Rp {{ number_format($navUser->wallet->balance ?? 0, 0, ',', '.') }}
                        </span>
                    @endif

                    {{-- Active role + switcher --}}
                    <div class="relative ml-2" x-on:click.outside="roleMenu = false">
                        <button x-on:click="roleMenu = !roleMenu" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 hover:border-blue-300 text-sm font-medium text-gray-700 transition">
                            <span>{{ $navRoleIcons[$navActiveRole] ?? '👤' }}</span>
                            <span>{{ $navActiveRole ?? 'Pilih Peran' }}</span>
                            @if($navUser->roles->count() > 1)
                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            @endif
                        </button>
                        @if($navUser->roles->count() > 1)
                            <div x-show="roleMenu" x-transition style="display:none;"
                                 class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden py-1">
                                @foreach($navUser->roles as $r)
                                    <form method="POST" action="{{ route('role.switch', $r->name) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex items-center gap-2 {{ $r->name === $navActiveRole ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">
                                            <span>{{ $navRoleIcons[$r->name] ?? '👤' }}</span> {{ $r->name }}
                                            @if($r->name === $navActiveRole)<span class="ml-auto text-xs">✓</span>@endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="ml-1">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-red-500 hover:bg-red-50 rounded-lg transition">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="ml-2 px-5 py-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold shadow-sm hover:shadow-md transition">
                        Login / Daftar
                    </a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button class="md:hidden p-2 text-gray-600" x-on:click="mobileOpen = !mobileOpen">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileOpen" style="display:none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-transition style="display:none;" class="md:hidden pb-4 space-y-1">
            <a href="{{ route('catalog') }}" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">🛍 Katalog</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">📊 Dashboard</a>
                @if($navActiveRole && isset($navRoleRoutes[$navActiveRole]))
                    <a href="{{ route($navRoleRoutes[$navActiveRole]) }}" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        {{ $navRoleIcons[$navActiveRole] }} Menu {{ $navActiveRole }}
                    </a>
                @endif
                @if($navUser->roles->count() > 1)
                    <div class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase">Ganti Peran</div>
                    @foreach($navUser->roles as $r)
                        <form method="POST" action="{{ route('role.switch', $r->name) }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-3 py-2 text-sm rounded-lg hover:bg-gray-50 {{ $r->name === $navActiveRole ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">
                                {{ $navRoleIcons[$r->name] ?? '👤' }} {{ $r->name }} @if($r->name === $navActiveRole) ✓ @endif
                            </button>
                        </form>
                    @endforeach
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-3 py-2 text-sm font-medium text-red-500 rounded-lg hover:bg-red-50">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-semibold text-blue-600 rounded-lg hover:bg-blue-50">Login / Daftar</a>
            @endauth
        </div>
    </div>
</nav>
