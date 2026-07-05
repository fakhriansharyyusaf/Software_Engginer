{{--
    Generic read-only "detail" modal driven by an Alpine store, so any
    page can show a detail view for a row without a dedicated route.
    Usage: x-on:click="$store.detailModal.open('Detail Voucher', { Kode: '...', Tipe: '...' })"
--}}
<div x-data x-show="$store.detailModal.show" style="display:none;" x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center px-4"
     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div class="absolute inset-0 bg-gray-900/40" x-on:click="$store.detailModal.close()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800" x-text="$store.detailModal.title"></h3>
            <button x-on:click="$store.detailModal.close()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <dl class="space-y-2.5">
            <template x-for="(value, key) in $store.detailModal.fields" :key="key">
                <div class="flex justify-between gap-4 text-sm border-b border-gray-50 pb-2">
                    <dt class="text-gray-400" x-text="key"></dt>
                    <dd class="text-gray-800 font-medium text-right" x-text="value"></dd>
                </div>
            </template>
        </dl>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('detailModal')) {
            Alpine.store('detailModal', {
                show: false,
                title: '',
                fields: {},
                open(title, fields) {
                    this.title = title;
                    this.fields = fields;
                    this.show = true;
                },
                close() {
                    this.show = false;
                },
            });
        }
    });
</script>
