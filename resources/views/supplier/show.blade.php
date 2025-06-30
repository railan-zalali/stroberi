<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Supplier') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('supplier.edit', $supplier) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('supplier.index') }}"
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informasi Supplier -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col items-center mb-6">
                            <div class="w-32 h-32 bg-gray-100 rounded-full overflow-hidden mb-4">
                                @if ($supplier->foto)
                                    <img src="{{ Storage::url($supplier->foto) }}" alt="{{ $supplier->nama }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full text-gray-300 p-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @endif
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $supplier->nama }}</h2>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $supplier->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mt-2">
                                {{ ucfirst($supplier->status) }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            @if ($supplier->alamat)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Alamat</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->alamat }}</p>
                                </div>
                            @endif

                            @if ($supplier->telepon)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Telepon</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->telepon }}</p>
                                </div>
                            @endif

                            @if ($supplier->email)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->email }}</p>
                                </div>
                            @endif

                            @if ($supplier->keterangan)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Keterangan</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->keterangan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Keuangan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Keuangan</h3>

                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-500">Total Pinjaman</h4>
                                    <span
                                        class="text-lg font-semibold {{ $supplier->total_pinjaman > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        Rp {{ number_format($supplier->total_pinjaman, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-500">Total Pembayaran</h4>
                                    <span class="text-lg font-semibold text-green-600">
                                        Rp {{ number_format($supplier->total_pembayaran, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-500">Sisa Pinjaman</h4>
                                    <span
                                        class="text-lg font-semibold {{ $sisaPinjaman > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Ringkasan Transaksi Strawberi</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                                        <p class="text-sm font-medium text-blue-800 mb-1">Total Kg</p>
                                        <p class="text-xl font-bold text-blue-900">{{ number_format($totalKg, 2) }} kg
                                        </p>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-4 text-center">
                                        <p class="text-sm font-medium text-green-800 mb-1">Total Nilai</p>
                                        <p class="text-xl font-bold text-green-900">Rp
                                            {{ number_format($totalNilai, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Tambah Pembayaran -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Tambah Pembayaran</h4>
                            <form action="{{ route('supplier.pembayaran', $supplier) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="jumlah_pembayaran"
                                            class="block text-sm font-medium text-gray-700">Jumlah Pembayaran
                                            (Rp)</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran"
                                                required
                                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="tanggal_pembayaran"
                                            class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                                        <div class="mt-1">
                                            <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran"
                                                value="{{ date('Y-m-d') }}" required
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="keterangan_pembayaran"
                                            class="block text-sm font-medium text-gray-700">Keterangan
                                            Pembayaran</label>
                                        <div class="mt-1">
                                            <textarea id="keterangan_pembayaran" name="keterangan_pembayaran" rows="2"
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>

                                    <div>
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Simpan Pembayaran
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aksi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi</h3>

                        <div class="space-y-3">
                            <a href="{{ route('supplier.edit', $supplier) }}"
                                class="inline-flex justify-center items-center w-full px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Supplier
                            </a>

                            <a href="{{ route('strawberi.create') }}?supplier_id={{ $supplier->id }}"
                                class="inline-flex justify-center items-center w-full px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Stok Strawberi
                            </a>

                            <form action="{{ route('supplier.destroy', $supplier) }}" method="POST" class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini? Semua data terkait supplier ini juga akan dihapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex justify-center items-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Supplier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs untuk Strawberi dan Transaksi -->
            <div class="mt-6">
                <div x-data="{ activeTab: 'strawberi' }">
                    <!-- Tab Headers -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'strawberi'"
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'strawberi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'strawberi' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Strawberi
                            </button>
                            <button @click="activeTab = 'transaksi'"
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'transaksi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'transaksi' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Transaksi
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Contents -->
                    <div class="mt-6">
                        <!-- Strawberi Tab -->
                        <div x-show="activeTab === 'strawberi'"
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Daftar Strawberi</h3>
                                    <a href="{{ route('strawberi.create') }}?supplier_id={{ $supplier->id }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tambah Stok
                                    </a>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
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
                                                    Harga Beli
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Harga Jual
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal Masuk
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kadaluarsa
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($strawberis as $strawberi)
                                                <tr
                                                    class="{{ $strawberi->tanggal_kadaluarsa->isPast() ? 'bg-red-50' : ($strawberi->tanggal_kadaluarsa->diffInDays(now()) <= 7 ? 'bg-yellow-50' : '') }}">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div
                                                                class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                                                                @if ($strawberi->jenis == 'segar')
                                                                    <span
                                                                        class="p-2 rounded-full bg-green-100 text-green-600">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="h-5 w-5" fill="none"
                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01" />
                                                                        </svg>
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="p-2 rounded-full bg-blue-100 text-blue-600">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="h-5 w-5" fill="none"
                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                                                        </svg>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ ucfirst($strawberi->jenis) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">
                                                            {{ number_format($strawberi->jumlah, 2) }} kg</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">Rp
                                                            {{ number_format($strawberi->harga_beli, 0, ',', '.') }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">Rp
                                                            {{ number_format($strawberi->harga_jual, 0, ',', '.') }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">
                                                            {{ $strawberi->tanggal_masuk->format('d/m/Y') }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($strawberi->tanggal_kadaluarsa->isPast())
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                Kadaluarsa
                                                                ({{ $strawberi->tanggal_kadaluarsa->format('d/m/Y') }})
                                                            </span>
                                                        @elseif($strawberi->tanggal_kadaluarsa->diffInDays(now()) <= 7)
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                {{ $strawberi->tanggal_kadaluarsa->diffInDays(now()) }}
                                                                hari lagi
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                {{ $strawberi->tanggal_kadaluarsa->format('d/m/Y') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <div class="flex space-x-2">
                                                            <a href="{{ route('strawberi.show', $strawberi) }}"
                                                                class="text-blue-600 hover:text-blue-900">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-5 w-5" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7"
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        Tidak ada data strawberi
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $strawberis->links() }}
                                </div>
                            </div>
                        </div>

                        <!-- Transaksi Tab -->
                        <div x-show="activeTab === 'transaksi'"
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Daftar Transaksi</h3>
                                    <a href="{{ route('transaksi.create') }}?supplier_id={{ $supplier->id }}&kategori=Pembayaran%20Supplier"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tambah Transaksi
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
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi
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
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <div class="flex space-x-2">
                                                            <a href="{{ route('transaksi.show', $transaksi) }}"
                                                                class="text-blue-600 hover:text-blue-900">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-5 w-5" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6"
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        Tidak ada data transaksi
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $transaksis->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
