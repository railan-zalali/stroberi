<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Laporan') }} {{ $laporan->bulan }} {{ $laporan->tahun }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan.download-pdf', $laporan) }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    {{ __('Download PDF') }}
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
            <!-- Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Keuangan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-green-800 mb-1">Total Pemasukan</p>
                            <p class="text-2xl font-bold text-green-600">Rp
                                {{ number_format($laporan->total_pemasukan, 0, ',', '.') }}</p>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-red-800 mb-1">Total Pengeluaran</p>
                            <p class="text-2xl font-bold text-red-600">Rp
                                {{ number_format($laporan->total_pengeluaran, 0, ',', '.') }}</p>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-blue-800 mb-1">Laba Bersih</p>
                            <p class="text-2xl font-bold {{ $laporan->laba >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($laporan->laba, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rincian Kerugian Stok -->
            @if(isset($kerugianStok) && ($kerugianStok['total_nilai'] > 0 || count($kerugianStok['detail']) > 0))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-orange-500">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-orange-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            Rincian Kerugian Stok Bulan Ini
                        </h3>
                        <span class="text-xs text-gray-500">Batch masuk pada periode ini yang rusak / kadaluarsa</span>
                    </div>

                    <!-- Summary boxes kerugian -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                            <p class="text-xs font-medium text-orange-700 uppercase tracking-wide mb-1">Total Stok Hilang</p>
                            <p class="text-2xl font-bold text-orange-600">{{ number_format($kerugianStok['total_rusak_kg'], 2, ',', '.') }} <span class="text-base font-normal">kg</span></p>
                            <p class="text-xs text-orange-500 mt-1">Rusak + Kadaluarsa</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <p class="text-xs font-medium text-red-700 uppercase tracking-wide mb-1">Estimasi Nilai Kerugian</p>
                            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($kerugianStok['total_nilai'], 0, ',', '.') }}</p>
                            <p class="text-xs text-red-500 mt-1">Berdasarkan harga beli per batch</p>
                        </div>
                    </div>

                    <!-- Detail tabel kerugian -->
                    @if(count($kerugianStok['detail']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-orange-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Batch</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Tgl Masuk</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Tgl Kadaluarsa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Penyebab</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-orange-700 uppercase tracking-wider">Rusak (kg)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-orange-700 uppercase tracking-wider">Kadaluarsa (kg)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-orange-700 uppercase tracking-wider">Harga Beli/kg</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-orange-700 uppercase tracking-wider">Estimasi Rugi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($kerugianStok['detail'] as $item)
                                <tr class="hover:bg-orange-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item['batch'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item['jenis'] == 'segar' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ ucfirst($item['jenis']) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['tgl_masuk'] }}</td>
                                    <td class="px-4 py-3 text-red-600 font-medium">{{ $item['tgl_kadaluarsa'] }}</td>
                                    <td class="px-4 py-3">
                                        @if($item['stok_rusak'] > 0 && $item['stok_kadaluarsa'] > 0)
                                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Rusak + Kadaluarsa</span>
                                        @elseif($item['stok_rusak'] > 0)
                                            <span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700">Stok Rusak</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Kadaluarsa</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700">{{ number_format($item['stok_rusak'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">{{ number_format($item['stok_kadaluarsa'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item['harga_beli'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-red-600">Rp {{ number_format($item['nilai_rugi'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-orange-100">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right text-xs font-bold text-orange-800 uppercase">Total Kerugian</td>
                                    <td class="px-4 py-3 text-right font-bold text-orange-800">{{ number_format($kerugianStok['total_rusak_kg'], 2, ',', '.') }} kg</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 text-right font-bold text-red-700">Rp {{ number_format($kerugianStok['total_nilai'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('laporan.show', $laporan) }}" class="flex flex-wrap gap-4">
                        <div class="w-full md:w-1/4">
                            <label for="tipe_transaksi" class="block text-sm font-medium text-gray-700">Tipe Transaksi</label>
                            <select id="tipe_transaksi" name="tipe_transaksi"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="">Semua Transaksi</option>
                                <option value="biasa" {{ request('tipe_transaksi') == 'biasa' ? 'selected' : '' }}>Transaksi Biasa</option>
                                <option value="pinjaman" {{ request('tipe_transaksi') == 'pinjaman' ? 'selected' : '' }}>Pinjaman</option>
                                <option value="pengembalian" {{ request('tipe_transaksi') == 'pengembalian' ? 'selected' : '' }}>Pengembalian Pinjaman</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Daily Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Harian</h3>
                        <div class="h-80">
                            <canvas id="dailyChart"></canvas>
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
                                @if (isset($chartData['pemasukanKategori']) && count($chartData['pemasukanKategori']) > 0)
                                    <canvas id="pemasukanPieChart"></canvas>
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-500">
                                        Tidak ada data pemasukan pada periode ini
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'pengeluaran'" class="h-64">
                                @if (isset($chartData['pengeluaranKategori']) && count($chartData['pengeluaranKategori']) > 0)
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Transaksi</h3>

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
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Daily Chart
            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
            const dailyChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($chartData['harian'], 'tanggal')) !!},
                    datasets: [{
                            label: 'Pemasukan',
                            data: {!! json_encode(array_column($chartData['harian'], 'pemasukan')) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(16, 185, 129, 1)'
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode(array_column($chartData['harian'], 'pengeluaran')) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(239, 68, 68, 1)'
                        },
                        {
                            label: 'Laba',
                            data: {!! json_encode(array_column($chartData['harian'], 'laba')) !!},
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(59, 130, 246, 1)'
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
            @if (isset($chartData['pemasukanKategori']) && count($chartData['pemasukanKategori']) > 0)
                const pemasukanCtx = document.getElementById('pemasukanPieChart').getContext('2d');
                const pemasukanLabels = [];
                const pemasukanData = [];

                @foreach ($chartData['pemasukanKategori'] as $kategori => $total)
                    pemasukanLabels.push('{{ $kategori ?? 'Tanpa Kategori' }}');
                    pemasukanData.push({{ $total }});
                @endforeach

                const pemasukanPieChart = new Chart(pemasukanCtx, {
                    type: 'pie',
                    data: {
                        labels: pemasukanLabels,
                        datasets: [{
                            data: pemasukanData,
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
            @if (isset($chartData['pengeluaranKategori']) && count($chartData['pengeluaranKategori']) > 0)
                const pengeluaranCtx = document.getElementById('pengeluaranPieChart').getContext('2d');
                const pengeluaranLabels = [];
                const pengeluaranData = [];

                @foreach ($chartData['pengeluaranKategori'] as $kategori => $total)
                    pengeluaranLabels.push('{{ $kategori ?? 'Tanpa Kategori' }}');
                    pengeluaranData.push({{ $total }});
                @endforeach

                const pengeluaranPieChart = new Chart(pengeluaranCtx, {
                    type: 'pie',
                    data: {
                        labels: pengeluaranLabels,
                        datasets: [{
                            data: pengeluaranData,
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
