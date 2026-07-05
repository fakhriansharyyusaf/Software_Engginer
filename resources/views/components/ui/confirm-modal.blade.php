{{--
    Reusable confirm dialog, replacing the browser's native confirm().
    Usage from any Livewire component's Blade view (Alpine + $wire magic
    both work anywhere inside a Livewire root element):

        <button x-on:click="$store.confirmModal.open('Hapus produk ini? Tindakan ini tidak bisa dibatalkan.', () => $wire.deleteProduct({{ $product->id }}))">
            Hapus
        </button>
--}}
<div x-data x-show="$store.confirmModal.show" style="display:none;" x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center px-4"
     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div class="absolute inset-0 bg-gray-900/40" x-on:click="$store.confirmModal.cancel()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center text-xl mb-3">⚠️</div>
        <h3 class="text-base font-semibold text-gray-800 mb-1">Konfirmasi</h3>
        <p class="text-sm text-gray-500 mb-5" x-text="$store.confirmModal.message"></p>
        <div class="flex gap-2 justify-end">
            <button x-on:click="$store.confirmModal.cancel()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
            <button x-on:click="$store.confirmModal.confirm()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('confirmModal')) {
            Alpine.store('confirmModal', {
                show: false,
                message: '',
                onConfirm: null,
                open(message, onConfirm) {
                    this.message = message;
                    this.onConfirm = onConfirm;
                    this.show = true;
                },
                confirm() {
                    if (typeof this.onConfirm === 'function') this.onConfirm();
                    this.show = false;
                },
                cancel() {
                    this.show = false;
                },
            });
        }
    });
</script>
