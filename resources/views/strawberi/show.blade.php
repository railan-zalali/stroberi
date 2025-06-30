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
                                    <p class="text-sm text-gray-500">Harga Jual</p>
                                    <p class="text-lg font-semibold text-gray-800">Rp
                                        {{ number_format($strawberi->harga_jual, 0, ',', '.') }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Tanggal Masuk</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ $strawberi->tanggal_masuk->format('d M Y') }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Tanggal Kadaluarsa</p>
                                    <p class="text-lg font-semibold text-red-600">
                                        {{ $strawberi->tanggal_kadaluarsa->format('d M Y') }} (Kadaluarsa)</p>
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
                                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                                    <p class="text-lg font-semibold text-green-600">
                                        Rp {{ number_format($strawberi->supplier->total_pembayaran, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Sisa Pinjaman</p>
                                    <p
                                        class="text-lg font-semibold {{ $strawberi->supplier->total_pinjaman - $strawberi->supplier->total_pembayaran > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp
                                        {{ number_format($strawberi->supplier->total_pinjaman - $strawberi->supplier->total_pembayaran, 0, ',', '.') }}
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

                        <div class="h-64 bg-gray-50 rounded-lg p-4">
                            <!-- Di sini Anda bisa menambahkan grafik/chart menggunakan library seperti Chart.js -->
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500">Grafik pergerakan stok akan ditampilkan di sini</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('modal-jual').classList.remove('hidden')"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Jual
                        </button>
                        <form action="{{ route('strawberi.destroy', $strawberi) }}" method="POST" class="inline"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Data
                            </button>
                        </form>
                    </div>

                    <!-- Modal Jual Strawberi -->
                    <div id="modal-jual" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                            <button type="button" onclick="document.getElementById('modal-jual').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Jual Strawberi</h3>
                            <form method="POST" action="{{ route('strawberi.sell', $strawberi) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="jumlah_jual" class="block text-sm font-medium text-gray-700">Jumlah Jual (kg)</label>
                                    <input type="number" step="0.01" min="0.01" max="{{ $strawberi->stok_tersisa }}" name="jumlah_jual" id="jumlah_jual" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                    <small class="text-gray-500">Stok tersisa: {{ number_format($strawberi->stok_tersisa, 2) }} kg</small>
                                </div>
                                <div>
                                    <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual per kg (Rp)</label>
                                    <input type="number" step="1" min="0" name="harga_jual" id="harga_jual" value="{{ $strawberi->harga_jual }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                </div>
                                <div>
                                    <label for="tanggal_jual" class="block text-sm font-medium text-gray-700">Tanggal Jual</label>
                                    <input type="date" name="tanggal_jual" id="tanggal_jual" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                </div>
                                <div>
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" onclick="document.getElementById('modal-jual').classList.add('hidden')" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Jual</button>
                                </div>
                            </form>
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
                        @if($riwayatPenjualan->isEmpty())
                            <p class="text-gray-500">Belum ada penjualan untuk stok ini.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Jual (kg)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($riwayatPenjualan as $jual)
                                            @php
                                                preg_match('/Penjualan ([\d,.]+) kg/', $jual->keterangan, $matches);
                                                $jumlahJual = isset($matches[1]) ? floatval(str_replace(',', '.', $matches[1])) : '-';
                                            @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($jual->tanggal)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $jumlahJual }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">Rp {{ number_format($jual->jumlah / ($jumlahJual ?: 1), 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">Rp {{ number_format($jual->jumlah, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $jual->keterangan }}</td>
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
</x-app-layout>
