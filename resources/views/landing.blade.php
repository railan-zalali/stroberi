<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Strawberi') }} — Kelola Pembukuan & Stok</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ darkMode: false }" x-init="(() => { const d = localStorage.getItem('theme') === 'dark'; if (d) document.documentElement.classList.add('dark'); darkMode = d })()" class="font-sans antialiased bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100 scroll-smooth">
    <header class="fixed top-0 inset-x-0 z-50 border-b bg-white/80 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold">SB</div>
                <span class="text-lg font-semibold">Strawberi</span>
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm">
                <a href="#fitur" class="hover:text-red-700">Fitur</a>
                <a href="#manfaat" class="hover:text-red-700">Manfaat</a>
                <a href="#testimoni" class="hover:text-red-700">Testimoni</a>
                <a href="#harga" class="hover:text-red-700">Harga</a>
            </nav>
            <div class="flex items-center gap-3">
                <button @click="darkMode=!darkMode; document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="p-2 rounded-md border hover:bg-gray-50 dark:hover:bg-gray-800" aria-label="Toggle theme">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">Masuk</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex px-4 py-2 text-sm font-semibold rounded-md bg-red-600 text-white hover:bg-red-700">Coba Gratis</a>
                @endif
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">Dashboard</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="pt-24">
        <section class="relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-center">
                <div class="space-y-6">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight">Kelola Pembukuan, Stok, dan Supplier Strawberi dalam satu tempat</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Catat transaksi harian, pantau stok real-time, kelola pinjaman dan pembayaran supplier, serta buat laporan rapi hanya dalam beberapa klik.</p>
                    <div class="flex flex-wrap gap-3">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex px-5 py-3 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700">Mulai Gratis</a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex px-5 py-3 rounded-md border border-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800">Lihat Demo</a>
                        @endif
                    </div>
                    <div class="flex items-center gap-6 text-sm text-gray-500">
                        <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-600"></span>Laporan keuangan otomatis</div>
                        <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-600"></span>Ekspor PDF & Excel</div>
                        <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-600"></span>Stok terpusat & per item</div>
                    </div>
                </div>
                <div class="relative">
                    <div class="rounded-xl border bg-white shadow-sm p-4 sm:p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-red-50 p-4">
                                <div class="text-sm font-medium text-red-700">Transaksi</div>
                                <div class="mt-2 text-2xl font-bold">Rp 12.450.000</div>
                                <div class="mt-1 text-xs text-red-700">Minggu ini</div>
                            </div>
                            <div class="rounded-lg bg-green-50 p-4">
                                <div class="text-sm font-medium text-green-700">Stok</div>
                                <div class="mt-2 text-2xl font-bold">324 kg</div>
                                <div class="mt-1 text-xs text-green-700">Tersedia</div>
                            </div>
                            <div class="rounded-lg bg-orange-50 p-4">
                                <div class="text-sm font-medium text-orange-700">Supplier</div>
                                <div class="mt-2 text-2xl font-bold">18 mitra</div>
                                <div class="mt-1 text-xs text-orange-700">Aktif</div>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-4">
                                <div class="text-sm font-medium text-blue-700">Laporan</div>
                                <div class="mt-2 text-2xl font-bold">4 jenis</div>
                                <div class="mt-1 text-xs text-blue-700">Siap ekspor</div>
                            </div>
                        </div>
                        <div class="mt-6 rounded-lg border p-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium">Ringkasan Hari Ini</div>
                                <div class="text-xs text-gray-500">Simulasi</div>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">Penjualan</div>
                                    <div class="font-semibold">Rp 2.150.000</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">Pembelian</div>
                                    <div class="font-semibold">Rp 1.120.000</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">Penyesuaian</div>
                                    <div class="font-semibold">-12 kg</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl font-bold">Fitur Utama</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Didesain khusus untuk operasi kebun atau bisnis strawberry.</p>
                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="rounded-xl border p-6">
                        <div class="w-10 h-10 rounded-lg bg-red-600/10 text-red-700 flex items-center justify-center font-bold">T</div>
                        <div class="mt-4 font-semibold">Pembukuan Transaksi</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Catat pendapatan dan pengeluaran, lengkap dengan tipe pembayaran.</p>
                    </div>
                    <div class="rounded-xl border p-6">
                        <div class="w-10 h-10 rounded-lg bg-green-600/10 text-green-700 flex items-center justify-center font-bold">S</div>
                        <div class="mt-4 font-semibold">Manajemen Stok</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Pantau stok gabungan dan per item, dengan penyesuaian cepat.</p>
                    </div>
                    <div class="rounded-xl border p-6">
                        <div class="w-10 h-10 rounded-lg bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold">P</div>
                        <div class="mt-4 font-semibold">Supplier & Pinjaman</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Kelola pinjaman, pengembalian, dan posting stok ke pembukuan.</p>
                    </div>
                    <div class="rounded-xl border p-6">
                        <div class="w-10 h-10 rounded-lg bg-blue-600/10 text-blue-700 flex items-center justify-center font-bold">L</div>
                        <div class="mt-4 font-semibold">Laporan & Ekspor</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Ekspor PDF/Excel untuk keuangan, stok, dan supplier dengan mudah.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="manfaat" class="mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-center">
                <div class="rounded-xl border p-6 bg-white shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="font-semibold">Alur Kerja Sederhana</div>
                        <div class="text-xs text-gray-500">Simulasi</div>
                    </div>
                    <ol class="mt-4 space-y-3 text-sm">
                        <li class="flex items-start gap-3"><span class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs">1</span><span>Tambah transaksi penjualan atau pembelian.</span></li>
                        <li class="flex items-start gap-3"><span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-xs">2</span><span>Stok otomatis ter-update, termasuk penjualan global.</span></li>
                        <li class="flex items-start gap-3"><span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">3</span><span>Ekspor laporan keuangan, stok, atau supplier.</span></li>
                    </ol>
                </div>
                <div>
                    <h3 class="text-2xl font-bold">Fokus pada panen, bukan administrasi</h3>
                    <p class="mt-3 text-gray-600 dark:text-gray-300">Antarmuka sederhana, responsif, dan cepat. Didesain agar tim di lapangan atau admin gudang sama-sama nyaman dipakai.</p>
                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <img src="https://api.dicebear.com/9.x/initials/svg?seed=A" class="w-8 h-8 rounded-full border" alt="A">
                            <img src="https://api.dicebear.com/9.x/initials/svg?seed=B" class="w-8 h-8 rounded-full border" alt="B">
                            <img src="https://api.dicebear.com/9.x/initials/svg?seed=C" class="w-8 h-8 rounded-full border" alt="C">
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">Dipercaya tim kecil hingga kebun skala menengah.</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimoni" class="mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl font-bold">Apa kata pengguna</h2>
                <div class="mt-8 grid md:grid-cols-3 gap-6">
                    <div class="rounded-xl border p-6">
                        <div class="font-semibold">Lebih rapi</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Pembukuan jadi rapi dan bisa diekspor kapan pun untuk laporan mingguan.</p>
                        <div class="mt-4 text-xs text-gray-500">Kebun Strawberi Mawar</div>
                    </div>
                    <div class="rounded-xl border p-6">
                        <div class="font-semibold">Stok aman</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Pantauan stok detail per item membuat kami menghindari kehabisan di hari pasar.</p>
                        <div class="mt-4 text-xs text-gray-500">Tim Gudang Merah</div>
                    </div>
                    <div class="rounded-xl border p-6">
                        <div class="font-semibold">Supplier terkelola</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Pengembalian dan pinjaman transparan, hubungan mitra jadi lebih baik.</p>
                        <div class="mt-4 text-xs text-gray-500">Distribusi Sejahtera</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="harga" class="mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl font-bold">Harga</h2>
                <div class="mt-8 grid md:grid-cols-2 gap-6">
                    <div class="rounded-xl border p-6">
                        <div class="text-sm font-medium">Starter</div>
                        <div class="mt-2 text-3xl font-bold">Gratis</div>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li>Semua fitur inti</li>
                            <li>Ekspor PDF & Excel</li>
                            <li>Tanpa batas data</li>
                        </ul>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="mt-6 inline-flex px-4 py-2 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700">Mulai Gratis</a>
                        @endif
                    </div>
                    <div class="rounded-xl border p-6">
                        <div class="text-sm font-medium">Pro</div>
                        <div class="mt-2 text-3xl font-bold">Hubungi Kami</div>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li>Dukungan prioritas</li>
                            <li>Penyesuaian alur kerja</li>
                            <li>Pelatihan tim</li>
                        </ul>
                        <a href="mailto:info@strawberi.local" class="mt-6 inline-flex px-4 py-2 rounded-md border border-gray-300 font-semibold hover:bg-gray-50">Kontak</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-2xl font-bold">Siap mulai?</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Buat akun dan coba dalam hitungan menit.</p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex px-5 py-3 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700">Coba Gratis</a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex px-5 py-3 rounded-md border border-gray-300 font-semibold hover:bg-gray-50">Masuk</a>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <footer class="mt-24 border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
            <div>© {{ date('Y') }} Strawberi</div>
            <div class="flex items-center gap-4">
                <a href="#fitur" class="hover:text-red-700">Fitur</a>
                <a href="#harga" class="hover:text-red-700">Harga</a>
                <a href="mailto:info@strawberi.local" class="hover:text-red-700">Kontak</a>
            </div>
        </div>
    </footer>
</body>
</html>