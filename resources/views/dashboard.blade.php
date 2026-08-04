<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Financial Summary Card -->
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Ringkasan Keuangan Bulan Ini</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Pemasukan</span>
                                <span class="text-green-600 dark:text-green-400 font-semibold">Rp
                                    {{ number_format($pemasukan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Pengeluaran</span>
                                <span class="text-red-600 dark:text-red-400 font-semibold">Rp
                                    {{ number_format($pengeluaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Laba</span>
                                <span class="text-lg font-bold {{ $laba >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    Rp {{ number_format($laba, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Stock Summary Card -->
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Ringkasan Stok</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="p-2 rounded-full bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01" />
                                        </svg>
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">Strawberi Segar</span>
                                </div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($stokSegar, 2) }} kg</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">Strawberi Beku</span>
                                </div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($stokBeku, 2) }} kg</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Total Stok</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                    {{ number_format($stokSegar + $stokBeku, 2) }} kg
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts Card -->
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Notifikasi</h3>
                        @if (count($expiringStrawberi) > 0)
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <span class="p-2 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                    <span class="text-red-600 dark:text-red-400 font-medium">Stok Hampir Kadaluarsa</span>
                                </div>
                                <ul class="space-y-2">
                                    @foreach ($expiringStrawberi as $strawberi)
                                        <li class="bg-red-50 dark:bg-red-950/40 p-3 rounded-md text-sm text-gray-800 dark:text-gray-200 border border-red-100 dark:border-red-900/40">
                                            <div class="flex justify-between items-center">
                                                <span>{{ number_format($strawberi->jumlah, 2) }} kg
                                                    {{ ucfirst($strawberi->jenis) }}</span>
                                                <span
                                                    class="font-medium {{ $strawberi->is_expired ? 'text-red-700 dark:text-red-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $strawberi->days_remaining_text }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="pt-2">
                                    <a href="{{ route('strawberi.index') }}"
                                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">
                                        Lihat semua &rarr;
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-green-50 dark:bg-green-950/40 p-4 rounded-md text-green-700 dark:text-green-300 text-sm border border-green-100 dark:border-green-900/40">
                                Tidak ada stok yang akan kadaluarsa dalam 7 hari mendatang.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions and Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Transaksi Terbaru</h3>
                            <a href="{{ route('transaksi.index') }}"
                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">
                                Lihat semua &rarr;
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Jenis
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                    @forelse($recentTransaksis as $transaksi)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $transaksi->tanggal->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaksi->jenis == 'pemasukan' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300' }}">
                                                    {{ ucfirst($transaksi->jenis) }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaksi->jenis == 'pemasukan' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $transaksi->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
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
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Grafik Keuangan Bulanan</h3>
                        <div class="h-64 bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('transaksi.create') }}"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Transaksi</span>
                        </a>

                        <a href="{{ route('strawberi.create') }}"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Stok</span>
                        </a>

                        <a href="{{ route('supplier.create') }}"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Supplier</span>
                        </a>

                        <a href="{{ route('laporan.create') }}"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 flex flex-col items-center justify-center text-center">
                            <span class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Buat Laporan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

        <script>
            // Get data from PHP
            const monthlyLabels = @json($monthLabels);
            const monthlyPemasukan = @json($monthlyData['pemasukan']);
            const monthlyPengeluaran = @json($monthlyData['pengeluaran']);

            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                            label: 'Pemasukan',
                            data: monthlyPemasukan,
                            backgroundColor: 'rgba(34, 197, 94, 0.5)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Pengeluaran',
                            data: monthlyPengeluaran,
                            backgroundColor: 'rgba(239, 68, 68, 0.5)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
