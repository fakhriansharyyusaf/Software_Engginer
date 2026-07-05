<div class="min-h-screen bg-linear-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <span class="text-3xl font-bold text-blue-600">SEA</span><span class="text-3xl font-bold text-gray-800">PEDIA</span>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="text-4xl mb-4">👤</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Pilih Peran Anda</h2>
            <p class="text-gray-500 text-sm mb-6">Akun ini memiliki beberapa peran. Pilih satu untuk melanjutkan.</p>

            @if (session()->has('error'))
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-2 text-sm mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col gap-3">
                @foreach($roles as $role)
                    <button wire:click="selectRole('{{ $role->name }}')"
                            class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 hover:border-blue-400 hover:bg-blue-50 text-gray-700 font-medium transition text-sm">
                        @if($role->name === 'Seller') 🏪
                        @elseif($role->name === 'Buyer') 🛒
                        @elseif($role->name === 'Driver') 🚗
                        @elseif($role->name === 'Admin') ⚙️
                        @endif
                        Masuk sebagai {{ $role->name }}
                    </button>
                @endforeach
            </div>
        </div>

    </div>
</div>
