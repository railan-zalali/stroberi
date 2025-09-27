<!-- resources/views/strawberi/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Stok Strawberi') }}
            </h2>
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
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <form method="POST" action="{{ route('strawberi.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jenis Strawberi -->
                            <div>
                                <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis
                                    Strawberi</label>
                                <div class="mt-1 flex space-x-4">
                                    <div class="flex items-center">
                                        <input id="segar" name="jenis" type="radio" value="segar"
                                            class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                            {{ old('jenis') == 'segar' ? 'checked' : '' }}>
                                        <label for="segar" class="ml-2 block text-sm text-gray-700">
                                            Segar
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="beku" name="jenis" type="radio" value="beku"
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                            {{ old('jenis') == 'beku' ? 'checked' : '' }}>
                                        <label for="beku" class="ml-2 block text-sm text-gray-700">
                                            Beku
                                        </label>
                                    </div>
                                </div>
                                @error('jenis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Grade Strawberi -->
                            <div>
                                <label for="grade" class="block text-sm font-medium text-gray-700">Grade
                                    Strawberi</label>
                                <div class="mt-1 flex space-x-4">
                                    <div class="flex items-center">
                                        <input id="grade-a" name="grade" type="radio" value="a"
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300"
                                            {{ old('grade') == 'a' ? 'checked' : '' }}>
                                        <label for="grade-a" class="ml-2 block text-sm text-gray-700">
                                            Grade A
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="grade-b" name="grade" type="radio" value="b"
                                            class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300"
                                            {{ old('grade') == 'b' ? 'checked' : '' }}>
                                        <label for="grade-b" class="ml-2 block text-sm text-gray-700">
                                            Grade B
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="grade-c" name="grade" type="radio" value="c"
                                            class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                            {{ old('grade') == 'c' ? 'checked' : '' }}>
                                        <label for="grade-c" class="ml-2 block text-sm text-gray-700">
                                            Grade C
                                        </label>
                                    </div>
                                </div>
                                @error('grade')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jumlah -->
                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah
                                    (kg)</label>
                                <div class="mt-1">
                                    <input type="number" step="0.01" name="jumlah" id="jumlah"
                                        value="{{ old('jumlah') }}"
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('jumlah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Harga Beli -->
                            <div>
                                <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli
                                    (Rp)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="harga_beli" id="harga_beli"
                                        value="{{ old('harga_beli') }}"
                                        class="focus:ring-red-500 focus:border-red-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('harga_beli')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>



                            <!-- Tanggal Masuk -->
                            <div>
                                <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal
                                    Masuk</label>
                                <div class="mt-1">
                                    <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                                        value="{{ old('tanggal_masuk', date('Y-m-d')) }}"
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('tanggal_masuk')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Kadaluarsa - Auto calculated based on jenis -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa</label>
                                <div class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Otomatis dihitung:</span><br>
                                        • Strawberi Segar: 2 hari dari tanggal masuk<br>
                                        • Strawberi Beku: 1 bulan dari tanggal masuk
                                    </p>
                                </div>
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label for="supplier_id"
                                    class="block text-sm font-medium text-gray-700">Supplier</label>
                                <div class="mt-1">
                                    <select id="supplier_id" name="supplier_id"
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Pilih Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <label for="keterangan"
                                    class="block text-sm font-medium text-gray-700">Keterangan</label>
                                <div class="mt-1">
                                    <textarea id="keterangan" name="keterangan" rows="3"
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('keterangan') }}</textarea>
                                </div>
                                @error('keterangan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                                    <!-- Pilihan Metode Pembayaran -->
                                    <div>
                                        <div class="flex items-center space-x-2 mb-4">
                                            <input id="metode-tunai" name="metode_pembayaran" type="radio" value="tunai"
                                                {{ old('metode_pembayaran', 'tunai') == 'tunai' ? 'checked' : '' }}
                                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                                onclick="togglePaymentOptions('tunai')">
                                            <label for="metode-tunai" class="text-sm font-medium text-gray-700 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Pembayaran Tunai
                                            </label>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input id="metode-kredit" name="metode_pembayaran" type="radio" value="kredit"
                                                {{ old('metode_pembayaran') == 'kredit' ? 'checked' : '' }}
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                onclick="togglePaymentOptions('kredit')">
                                            <label for="metode-kredit" class="text-sm font-medium text-gray-700 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                Pembayaran Kredit (Pinjaman)
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Opsi Tambahan untuk Pembayaran Tunai -->
                                    <div id="opsi-tunai" class="border-l border-gray-200 pl-4">
                                        <div class="flex items-center mb-2">
                                            <input id="buat_transaksi" name="buat_transaksi" type="checkbox" value="1" 
                                                {{ old('buat_transaksi', true) ? 'checked' : '' }}
                                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <label for="buat_transaksi" class="ml-2 block text-sm text-gray-700">
                                                Buat transaksi pengeluaran otomatis
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="tambah_pinjaman" name="tambah_pinjaman" type="checkbox" value="1"
                                                {{ old('tambah_pinjaman', false) ? 'checked' : '' }}
                                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <label for="tambah_pinjaman" class="ml-2 block text-sm text-gray-700">
                                                Tambahkan ke total pinjaman supplier
                                                <span class="text-xs text-gray-500 block">(Untuk kasus khusus)</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Informasi untuk Pembayaran Kredit -->
                                    <div id="opsi-kredit" class="border-l border-gray-200 pl-4 hidden">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Info:</span> Dengan memilih pembayaran kredit, sistem akan:
                                        </p>
                                        <ul class="text-xs text-gray-500 list-disc list-inside mt-1">
                                            <li>Otomatis menambahkan ke total pinjaman supplier</li>
                                            <li>Mencatat transaksi pengeluaran dengan kategori kredit</li>
                                            <li>Memungkinkan pembayaran di kemudian hari</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                function togglePaymentOptions(method) {
                                    if (method === 'tunai') {
                                        document.getElementById('opsi-tunai').classList.remove('hidden');
                                        document.getElementById('opsi-kredit').classList.add('hidden');
                                    } else {
                                        document.getElementById('opsi-tunai').classList.add('hidden');
                                        document.getElementById('opsi-kredit').classList.remove('hidden');
                                    }
                                }
                                
                                // Initialize on page load
                                document.addEventListener('DOMContentLoaded', function() {
                                    const selectedMethod = document.querySelector('input[name="metode_pembayaran"]:checked').value;
                                    togglePaymentOptions(selectedMethod);
                                });
                            </script>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('strawberi.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
