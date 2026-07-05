@php
    // Generic: picks up ANY session flash key ending in "_message" (store_message,
    // product_message, wallet_message, checkout_message, job_message, etc.) plus
    // the plain "message" and "error" keys — so every existing Livewire component
    // gets a toast automatically, no per-page wiring needed.
    $flashes = collect(session()->all())
        ->filter(fn ($v, $k) => is_string($v) && (str_ends_with($k, '_message') || in_array($k, ['message', 'error'])))
        ->map(fn ($v, $k) => ['key' => $k, 'text' => $v, 'type' => $k === 'error' ? 'error' : 'success']);
@endphp
@if($flashes->isNotEmpty())
<div class="fixed top-20 right-4 z-[100] space-y-2 w-full max-w-xs" x-data>
    @foreach($flashes as $flash)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="opacity-0"
             class="rounded-xl shadow-lg border px-4 py-3 flex items-start gap-2 text-sm
                    {{ $flash['type'] === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-white border-green-200 text-gray-700' }}">
            <span class="text-lg leading-none">{{ $flash['type'] === 'error' ? '⚠️' : '✅' }}</span>
            <span class="flex-1">{{ $flash['text'] }}</span>
            <button x-on:click="show = false" class="text-gray-400 hover:text-gray-600 leading-none">✕</button>
        </div>
    @endforeach
</div>
@endif
