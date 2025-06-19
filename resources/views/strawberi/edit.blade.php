<!-- resources/views/strawberi/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Stok Strawberi') }}
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
                    <form method="POST" action="{{ route('strawberi.update', $strawberi) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jenis Strawberi -->
                            <div>
                                <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis
                                    Strawberi</label>
                                <div class="mt-1 flex space-x-4">
                                    <div class="flex items-center">
                                        <input id="segar" name="jenis" type="radio" value="segar"
                                            class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                            {{ old('jenis', $strawberi->jenis) == 'segar' ? 'checked' : '' }}>
                                        <label for="segar" class="ml-2 block text-sm text-gray-700">
                                            Segar
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="beku" name="jenis" type="radio" value="beku"
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                            {{ old('jenis', $strawberi->jenis) == 'beku' ? 'checked' : '' }}>
                                        <label for="beku" class="ml-2 block text-sm text-gray-700">
                                            Beku
                                        </label>
                                    </div>
                                </div>
                                @error('jenis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jumlah -->
                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah
                                    (kg)</label>
                                <div class="mt-1">
                                    <input type="number" step="0.01" name="jumlah" id="jumlah"
                                        value="{{ old('jumlah', $strawberi->jumlah) }}"
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
                                        value="{{ old('harga_beli', $strawberi->harga_beli) }}"
                                        class="focus:ring-red-500 focus:border-red-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('harga_beli')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Harga Jual -->
                            <div>
                                <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual
                                    (Rp)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="harga_jual" id="harga_jual"
                                        value="{{ old('harga_jual', $strawberi->harga_jual) }}"
                                        class="focus:ring-red-500 focus:border-red-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('harga_jual')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Masuk -->
                            <div>
                                <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal
                                    Masuk</label>
                                <div class="mt-1">
                                    <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                                        value="{{ old('tanggal_masuk', $strawberi->tanggal_masuk->format('Y-m-d')) }}"
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('tanggal_masuk')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Kadaluarsa -->
                            <div>
                                <label for="tanggal_kadaluarsa" class="block text-sm font-medium text-gray-700">Tanggal
                                    Kadaluarsa</label>
                                <div class="mt-1">
                                    <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa"
                                        value="{{ old('tanggal_kadaluarsa', $strawberi->tanggal_kadaluarsa->format('Y-m-d')) }}"
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('tanggal_kadaluarsa')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                                {{ old('supplier_id', $strawberi->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('keterangan', $strawberi->keterangan) }}</textarea>
                                </div>
                                @error('keterangan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('strawberi.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
