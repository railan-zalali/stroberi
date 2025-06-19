<!-- resources/views/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Financial Summary Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Keuangan Bulan Ini</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Pemasukan</span>
                                <span class="text-green-600 font-semibold">Rp
                                    {{ number_format($pemasukan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Pengeluaran</span>
                                <span class="text-red-600 font-semibold">Rp
                                    {{ number_format($pengeluaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                                <span class="font-medium">Laba</span>
                                <span class="text-lg font-bold {{ $laba >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($laba, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Summary Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Stok</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="p-2 rounded-full bg-green-100 text-green-600 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01" />
                                        </svg>
                                    </span>
                                    <span class="text-gray-500">Strawberi Segar</span>
                                </div>
                                <span class="font-semibold">{{ number_format($stokSegar, 2) }} kg</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="p-2 rounded-full bg-blue-100 text-blue-600 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                    </span>
                                    <span class="text-gray-500">Strawberi Beku</span>
                                </div>
                                <span class="font-semibold">{{ number_format($stokBeku, 2) }} kg</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                                <span class="font-medium">Total Stok</span>
                                <span class="text-lg font-bold text-gray-800">
                                    {{ number_format($stokSegar + $stokBeku, 2) }} kg
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Notifikasi</h3>
                        @if (count($expiringStrawberi) > 0)
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <span class="p-2 rounded-full bg-red-100 text-red-600 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                    <span class="text-red-600 font-medium">Stok Hampir Kadaluarsa</span>
                                </div>
                                <ul class="space-y-2">
                                    @foreach ($expiringStrawberi as $strawberi)
                                        <li class="bg-red-50 p-3 rounded-md text-sm">
                                            <div class="flex justify-between items-center">
                                                <span>{{ number_format($strawberi->jumlah, 2) }} kg
                                                    {{ ucfirst($strawberi->jenis) }}</span>
                                                <span class="text-red-600 font-medium">
                                                    {{ $strawberi->tanggal_kadaluarsa->diffInDays(now()) }} hari lagi
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="pt-2">
                                    <a href="{{ route('strawberi.index') }}"
                                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        Lihat semua &rarr;
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-green-50 p-4 rounded-md text-green-700 text-sm">
                                Tidak ada stok yang akan kadaluarsa dalam 7 hari mendatang.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions and Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Transaksi Terbaru</h3>
                            <a href="{{ route('transaksi.index') }}"
                                class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Lihat semua &rarr;
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentTransaksis as $transaksi)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaksi->tanggal->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaksi->jenis == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($transaksi->jenis) }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaksi->jenis == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                                Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaksi->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                Belum ada transaksi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Monthly Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Keuangan Bulanan</h3>
                        <div class="h-64 bg-gray-50 rounded-lg p-4">
                            <!-- Di sini Anda bisa menambahkan grafik/chart menggunakan library seperti Chart.js -->
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500">Grafik akan ditampilkan di sini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('transaksi.create') }}"
                            class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-green-100 text-green-600 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700">Tambah Transaksi</span>
                        </a>

                        <a href="{{ route('strawberi.create') }}"
                            class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-red-100 text-red-600 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700">Tambah Stok</span>
                        </a>

                        <a href="{{ route('supplier.create') }}"
                            class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-blue-100 text-blue-600 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700">Tambah Supplier</span>
                        </a>

                        <a href="{{ route('laporan.create') }}"
                            class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-yellow-100 text-yellow-600 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700">Buat Laporan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
