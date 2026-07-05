<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SEAPEDIA') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>%F0%9F%90%9F</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        * { font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <x-ui.navbar />
    <x-ui.flash />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-ui.footer />
    <x-ui.confirm-modal />
    <x-ui.detail-modal />

    @livewireScripts
</body>
</html>
