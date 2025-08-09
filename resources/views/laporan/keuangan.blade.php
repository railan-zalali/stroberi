<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Keuangan') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan.export.keuangan', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('laporan.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('laporan.keuangan') }}"
                        class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/4">
                            <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                            <select id="bulan" name="bulan"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create(null, $m)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-1/4">
                            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select id="tahun" name="tahun"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                @foreach (range(date('Y'), date('Y') - 5, -1) as $y)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-1/2 flex items-end">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Total Pemasukan</p>
                                <p class="text-2xl font-bold text-gray-700">Rp
                                    {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                                <p class="text-2xl font-bold text-gray-700">Rp
                                    {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Laba Bersih</p>
                                <p class="text-2xl font-bold {{ $laba >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($laba, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Monthly Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Keuangan Bulanan {{ $tahun }}
                        </h3>
                        <div class="h-80">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Pie Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div x-data="{ activeTab: 'pemasukan' }">
                            <div class="flex border-b border-gray-200 mb-4">
                                <button @click="activeTab = 'pemasukan'"
                                    :class="{ 'border-green-500 text-green-600': activeTab === 'pemasukan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pemasukan' }"
                                    class="py-4 px-6 border-b-2 font-medium text-sm">
                                    Pemasukan
                                </button>
                                <button @click="activeTab = 'pengeluaran'"
                                    :class="{ 'border-red-500 text-red-600': activeTab === 'pengeluaran', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pengeluaran' }"
                                    class="py-4 px-6 border-b-2 font-medium text-sm">
                                    Pengeluaran
                                </button>
                            </div>

                            <div x-show="activeTab === 'pemasukan'" class="h-64">
                                @if ($pemasukanKategori->count() > 0)
                                    <canvas id="pemasukanPieChart"></canvas>
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-500">
                                        Tidak ada data pemasukan pada periode ini
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'pengeluaran'" class="h-64">
                                @if ($pengeluaranKategori->count() > 0)
                                    <canvas id="pengeluaranPieChart"></canvas>
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-500">
                                        Tidak ada data pengeluaran pada periode ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Transaksi
                        {{ Carbon\Carbon::createFromDate($tahun, $bulan, 1)->format('F Y') }}</h3>

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
                                        Kategori
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
                                @forelse($transaksis as $transaksi)
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaksi->kategori ?? '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaksi->jenis == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                            Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ Str::limit($transaksi->keterangan, 50) ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada data transaksi pada periode ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $transaksis->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($monthlyChart, 'bulan')) !!},
                    datasets: [{
                            label: 'Pemasukan',
                            data: {!! json_encode(array_column($monthlyChart, 'pemasukan')) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode(array_column($monthlyChart, 'pengeluaran')) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Laba',
                            data: {!! json_encode(array_column($monthlyChart, 'laba')) !!},
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                            type: 'line'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Pemasukan Pie Chart
            @if ($pemasukanKategori->count() > 0)
                const pemasukanCtx = document.getElementById('pemasukanPieChart').getContext('2d');
                const pemasukanPieChart = new Chart(pemasukanCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode(
                            $pemasukanKategori->pluck('kategori')->map(function ($item) {
                                return $item ?? 'Tanpa Kategori';
                            }),
                        ) !!},
                        datasets: [{
                            data: {!! json_encode($pemasukanKategori->pluck('total')) !!},
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(14, 165, 233, 0.8)',
                                'rgba(168, 85, 247, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(236, 72, 153, 0.8)',
                                'rgba(79, 70, 229, 0.8)',
                                'rgba(45, 212, 191, 0.8)',
                                'rgba(251, 146, 60, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(14, 165, 233, 1)',
                                'rgba(168, 85, 247, 1)',
                                'rgba(249, 115, 22, 1)',
                                'rgba(236, 72, 153, 1)',
                                'rgba(79, 70, 229, 1)',
                                'rgba(45, 212, 191, 1)',
                                'rgba(251, 146, 60, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += 'Rp ' + context.parsed.toLocaleString('id-ID');
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            @endif

            // Pengeluaran Pie Chart
            @if ($pengeluaranKategori->count() > 0)
                const pengeluaranCtx = document.getElementById('pengeluaranPieChart').getContext('2d');
                const pengeluaranPieChart = new Chart(pengeluaranCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode(
                            $pengeluaranKategori->pluck('kategori')->map(function ($item) {
                                return $item ?? 'Tanpa Kategori';
                            }),
                        ) !!},
                        datasets: [{
                            data: {!! json_encode($pengeluaranKategori->pluck('total')) !!},
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(236, 72, 153, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(79, 70, 229, 0.8)',
                                'rgba(6, 182, 212, 0.8)',
                                'rgba(132, 204, 22, 0.8)'
                            ],
                            borderColor: [
                                'rgba(239, 68, 68, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(249, 115, 22, 1)',
                                'rgba(236, 72, 153, 1)',
                                'rgba(139, 92, 246, 1)',
                                'rgba(79, 70, 229, 1)',
                                'rgba(6, 182, 212, 1)',
                                'rgba(132, 204, 22, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += 'Rp ' + context.parsed.toLocaleString('id-ID');
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        </script>
    @endpush
</x-app-layout>
