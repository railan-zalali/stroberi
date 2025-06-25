<!-- resources/views/transaksi/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Transaksi') }}
            </h2>
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <form method="POST" action="{{ route('transaksi.update', $transaksi) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jenis Transaksi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                                <div class="mt-4 flex space-x-4">
                                    <div class="flex items-center">
                                        <input id="pemasukan" name="jenis" type="radio" value="pemasukan" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300" {{ old('jenis', $transaksi->jenis) == 'pemasukan' ? 'checked' : '' }}>
                                        <label for="pemasukan" class="ml-2 block text-sm text-gray-700 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                            Pemasukan
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="pengeluaran" name="jenis" type="radio" value="pengeluaran" class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300" {{ old('jenis', $transaksi->jenis) == 'pengeluaran' ? 'checked' : '' }}>
                                        <label for="pengeluaran" class="ml-2 block text-sm text-gray-700 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                            Pengeluaran
                                        </label>
                                    </div>
                                </div>
                                @error('jenis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jumlah -->
                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $transaksi->jumlah) }}" class="focus:ring-green-500 focus:border-green-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('jumlah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <div class="mt-1">
                                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('tanggal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <div class="mt-1">
                                    <select id="kategori" name="kategori" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Pilih Kategori</option>
                                        <option value="Penjualan" {{ old('kategori', $transaksi->kategori) == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                                        <option value="Pembelian" {{ old('kategori', $transaksi->kategori) == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                                        <option value="Gaji" {{ old('kategori', $transaksi->kategori) == 'Gaji' ? 'selected' : '' }}>Gaji</option>
                                        <option value="Sewa" {{ old('kategori', $transaksi->kategori) == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                        <option value="Operasional" {{ old('kategori', $transaksi->kategori) == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                        <option value="Lainnya" {{ old('kategori', $transaksi->kategori) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                @error('kategori')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                <div class="mt-1">
                                    <textarea id="keterangan" name="keterangan" rows="3" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                                </div>
                                @error('keterangan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>