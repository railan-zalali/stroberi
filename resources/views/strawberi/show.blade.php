<!-- resources/views/strawberi/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Stok Strawberi') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('strawberi.edit', $strawberi) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('strawberi.index') }}"
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Utama -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Strawberi</h3>

                            <div class="flex items-center mb-4">
                                @if ($strawberi->jenis == 'segar')
                                    <span class="p-2 mr-4 rounded-full bg-green-100 text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01" />
                                        </svg>
                                    </span>
                                    <span class="text-lg font-semibold text-gray-800">Strawberi Segar</span>
                                @else
                                    <span class="p-2 mr-4 rounded-full bg-blue-100 text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                    </span>
                                    <span class="text-lg font-semibold text-gray-800">Strawberi Beku</span>
                                @endif

                                <!-- Grade Badge -->
                                @php
                                    $gradeColors = [
                                        'a' => 'bg-green-100 text-green-600',
                                        'b' => 'bg-yellow-100 text-yellow-600',
                                        'c' => 'bg-red-100 text-red-600',
                                    ];
                                    $gradeColor = $gradeColors[$strawberi->grade] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span
                                    class="ml-2 px-3 py-1 rounded-full {{ $gradeColor }} text-xs font-bold uppercase">
                                    Grade {{ strtoupper($strawberi->grade) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Jumlah</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ number_format($strawberi->jumlah, 2) }} kg</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Total Nilai</p>
                                    <p class="text-lg font-semibold text-gray-800">Rp
                                        {{ number_format($strawberi->jumlah * $strawberi->harga_beli, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Harga Beli</p>
                                    <p class="text-lg font-semibold text-gray-800">Rp
                                        {{ number_format($strawberi->harga_beli, 0, ',', '.') }}</p>
                                </div>



                                <div>
                                    <p class="text-sm text-gray-500">Tanggal Masuk</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ $strawberi->tanggal_masuk->format('d M Y') }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Tanggal Kadaluarsa</p>
                                    <p
                                        class="text-lg font-semibold {{ $strawberi->isKadaluarsa() ? 'text-red-600' : ($strawberi->isHampirKadaluarsa() ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ $strawberi->tanggal_kadaluarsa->format('d M Y') }}
                                        @if ($strawberi->isKadaluarsa())
                                            (Kadaluarsa)
                                        @elseif($strawberi->isHampirKadaluarsa())
                                            ({{ $strawberi->tanggal_kadaluarsa->diffInDays(now()) }} hari lagi)
                                        @else
                                            {{ $strawberi->tanggal_kadaluarsa->diffInDays(now()) }} hari lagi
                                        @endif
                                    </p>
                                </div>

                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500">Keterangan</p>
                                    <p class="text-lg font-semibold text-gray-800">{{ $strawberi->keterangan ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Supplier -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Supplier</h3>

                            <div class="flex items-center mb-4">
                                <span class="p-2 mr-4 rounded-full bg-yellow-100 text-yellow-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </span>
                                <span
                                    class="text-lg font-semibold text-gray-800">{{ $strawberi->supplier->nama }}</span>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Alamat</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ $strawberi->supplier->alamat ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Telepon</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ $strawberi->supplier->telepon ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ $strawberi->supplier->email ?? '-' }}</p>
                                </div>

                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-500">Total Pinjaman</p>
                                    <p
                                        class="text-lg font-semibold {{ $strawberi->supplier->total_pinjaman > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        Rp {{ number_format($strawberi->supplier->total_pinjaman, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Total Pengembalian</p>
                                    <p class="text-lg font-semibold text-green-600">
                                        Rp {{ number_format($strawberi->supplier->total_pengembalian, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Sisa Pinjaman</p>
                                    <p
                                        class="text-lg font-semibold {{ $strawberi->supplier->sisa_pinjaman > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($strawberi->supplier->sisa_pinjaman, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('supplier.show', $strawberi->supplier) }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Lihat Detail Supplier
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik dan Grafik -->
                    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Stok Strawberi</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Grafik Stok -->
                            <div class="h-64 bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Pergerakan Stok</h4>
                                <canvas id="stockChart"></canvas>
                            </div>

                            <!-- Grafik Penjualan -->
                            <div class="h-64 bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Penjualan</h4>
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>

                        <!-- Informasi Kadaluarsa -->
                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <h4 class="text-md font-medium text-yellow-700 mb-2">Informasi Kadaluarsa</h4>
                            <p class="text-sm text-yellow-600">
                                @if ($strawberi->isKadaluarsa())
                                    Stok ini sudah kadaluarsa pada
                                    {{ $strawberi->tanggal_kadaluarsa->format('d M Y') }}.
                                @elseif($strawberi->isHampirKadaluarsa())
                                    Stok ini akan kadaluarsa dalam
                                    {{ $strawberi->tanggal_kadaluarsa->diffInDays(now()) }} hari lagi.
                                    Segera jual atau gunakan stok ini untuk menghindari kerugian.
                                @else
                                    Stok ini masih baik dan akan kadaluarsa dalam
                                    {{ $strawberi->tanggal_kadaluarsa->diffInDays(now()) }} hari lagi.
                                @endif
                            </p>
                        </div>
                    </div>

                    

                    <!-- Riwayat Penjualan -->
                    <div class="mt-12 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Penjualan Strawberi Ini</h3>
                        @php
                            $riwayatPenjualan = \App\Models\Transaksi::where('jenis', 'pemasukan')
                                ->where('kategori', 'Penjualan Strawberi')
                                ->where('keterangan', 'like', "%{$strawberi->id}%")
                                ->orderBy('tanggal', 'desc')
                                ->get();
                        @endphp
                        @if ($riwayatPenjualan->isEmpty())
                            <p class="text-gray-500">Belum ada penjualan untuk stok ini.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jumlah Jual (kg)</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Harga Jual</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($riwayatPenjualan as $jual)
                                            @php
                                                preg_match('/Penjualan ([\d,.]+) kg/', $jual->keterangan, $matches);
                                                $jumlahJual = isset($matches[1])
                                                    ? floatval(str_replace(',', '.', $matches[1]))
                                                    : '-';
                                            @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($jual->tanggal)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $jumlahJual }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">Rp
                                                    {{ number_format($jual->jumlah / ($jumlahJual ?: 1), 0, ',', '.') }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                                    Rp {{ number_format($jual->jumlah, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $jual->keterangan }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Script untuk Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk grafik stok
            const stockCtx = document.getElementById('stockChart').getContext('2d');
            const stockChart = new Chart(stockCtx, {
                type: 'line',
                data: {
                    labels: ['Stok Awal', 'Terjual', 'Rusak', 'Adjustment', 'Tersisa'],
                    datasets: [{
                        label: 'Jumlah (kg)',
                        data: [
                            {{ $strawberi->stok_awal }},
                            {{ $strawberi->stok_terjual }},
                            {{ $strawberi->stok_rusak }},
                            {{ $strawberi->stok_adjustment }},
                            {{ $strawberi->stok_tersisa }}
                        ],
                        backgroundColor: 'rgba(220, 38, 38, 0.2)',
                        borderColor: 'rgba(220, 38, 38, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Data untuk grafik penjualan
            @php
                $riwayatPenjualan = \App\Models\Transaksi::where('jenis', 'pemasukan')
                    ->where('kategori', 'Penjualan Strawberi')
                    ->where('keterangan', 'like', "%{$strawberi->id}%")
                    ->orderBy('tanggal', 'asc')
                    ->get();

                $labels = [];
                $data = [];

                foreach ($riwayatPenjualan as $jual) {
                    $labels[] = $jual->tanggal->format('d/m/Y');
                    $data[] = $jual->jumlah;
                }
            @endphp

            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: {!! json_encode($data) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ".");
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
                                        label += 'Rp ' + context.parsed.y.toString().replace(
                                            /\B(?=(\d{3})+(?!\d))/g, ".");
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
