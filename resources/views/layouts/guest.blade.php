<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Strawberi') }} — Kelola Pembukuan & Stok</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Anti-FOUC Theme Script -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Vite Assets -->
        @php
            $isProduction = app()->environment('production');
            $manifestPath = $isProduction
                ? base_path('../public_html/build/manifest.json')
                : public_path('build/manifest.json');
        @endphp

        @if ($isProduction && file_exists($manifestPath))
            @php $manifest = json_decode(file_get_contents($manifestPath), true); @endphp
            <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
            <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
          class="font-sans antialiased bg-rose-50/70 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen relative flex flex-col justify-center items-center py-10 px-4 transition-colors duration-300">
        
        <!-- Ambient Decorative Glow -->
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-b from-red-200/40 via-rose-100/20 to-transparent dark:from-red-950/40 dark:via-rose-950/15 pointer-events-none blur-3xl -z-10"></div>
        <div class="fixed -bottom-20 -right-20 w-80 h-80 bg-red-300/30 dark:bg-red-950/20 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <!-- Strawberi Logo & Branding -->
        <div class="mb-6 text-center">
            <a href="/" class="inline-flex flex-col items-center group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-red-600 to-rose-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-red-500/25 ring-4 ring-white dark:ring-gray-900 group-hover:scale-105 transition-transform duration-200">
                    🍓
                </div>
                <span class="mt-3 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Strawberi</span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Kelola Pembukuan & Stok UMKM</span>
            </a>
        </div>

        <!-- Auth Form Card -->
        <div class="w-full sm:max-w-md bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl shadow-xl shadow-rose-900/5 dark:shadow-black/50 rounded-2xl p-6 sm:p-8 border border-rose-100 dark:border-gray-800 transition-colors duration-300">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} Strawberi. All rights reserved.
        </div>
    </body>
</html>


