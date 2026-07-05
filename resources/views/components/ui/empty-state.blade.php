@props(['icon' => '📭', 'text' => 'Belum ada data.'])
<div class="text-center py-16 text-gray-400">
    <p class="text-4xl mb-3">{{ $icon }}</p>
    <p class="text-sm">{{ $text }}</p>
    {{ $slot }}
</div>
