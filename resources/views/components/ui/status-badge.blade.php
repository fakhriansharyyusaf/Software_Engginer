@php
    $colors = [
        'Sedang Dikemas' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Menunggu Pengirim' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Sedang Dikirim' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'Pesanan Selesai' => 'bg-green-50 text-green-700 border-green-200',
        'Dikembalikan' => 'bg-red-50 text-red-700 border-red-200',
    ];
    $class = $colors[$status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
@endphp
<span {{ $attributes->merge(['class' => "inline-block text-xs font-semibold px-2.5 py-1 rounded-full border $class"]) }}>
    {{ $status }}
</span>
