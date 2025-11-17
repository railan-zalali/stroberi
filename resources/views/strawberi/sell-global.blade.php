<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penjualan Global Strawberi (FEFO)') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8">

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('strawberi.sell-global.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Jenis</label>
                <select name="jenis" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="segar" {{ old('jenis') === 'segar' ? 'selected' : '' }}>Segar</option>
                    <option value="beku" {{ old('jenis') === 'beku' ? 'selected' : '' }}>Beku</option>
                </select>
                @error('jenis')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Preferensi Grade (opsional)</label>
                <select name="preferensi_grade" class="mt-1 w-full border rounded px-3 py-2">
                    <option value="">-- Campur sesuai FEFO --</option>
                    <option value="a" {{ old('preferensi_grade') === 'a' ? 'selected' : '' }}>Grade A</option>
                    <option value="b" {{ old('preferensi_grade') === 'b' ? 'selected' : '' }}>Grade B</option>
                    <option value="c" {{ old('preferensi_grade') === 'c' ? 'selected' : '' }}>Grade C</option>
                </select>
                @error('preferensi_grade')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Jumlah Jual (kg)</label>
                <input type="number" step="0.01" min="0.01" name="jumlah_jual" value="{{ old('jumlah_jual') }}"
                    class="mt-1 w-full border rounded px-3 py-2" required>
                @error('jumlah_jual')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-600 mt-1">Jika stok tidak cukup, sistem akan melakukan penjualan partial
                    otomatis.</p>
            </div>

            <div>
                <label class="block text-sm font-medium">Mode Harga</label>
                <select id="price_mode" name="price_mode" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="manual" {{ old('price_mode') === 'manual' ? 'selected' : '' }}>Manual (isi harga jual
                        per kg)</option>

                </select>
                @error('price_mode')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div id="harga_jual_group" style="display:none;">
                <label class="block text-sm font-medium">Harga Jual per kg</label>
                <input type="number" step="0.01" min="0" name="harga_jual" value="{{ old('harga_jual') }}"
                    class="mt-1 w-full border rounded px-3 py-2">
                @error('harga_jual')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Pembeli</label>
                <input type="text" name="pembeli" value="{{ old('pembeli') }}"
                    class="mt-1 w-full border rounded px-3 py-2" required>
                @error('pembeli')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Tanggal Jual</label>
                <input type="date" name="tanggal_jual" value="{{ old('tanggal_jual') ?? now()->format('Y-m-d') }}"
                    class="mt-1 w-full border rounded px-3 py-2" required>
                @error('tanggal_jual')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Bukti Pembayaran (opsional)</label>
                <input type="file" name="bukti_pembayaran" class="mt-1 w-full border rounded px-3 py-2">
                @error('bukti_pembayaran')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Keterangan (opsional)</label>
                <textarea name="keterangan" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Penjualan</button>
                <a href="{{ route('strawberi.index') }}" class="ml-3 text-gray-700">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function toggleHargaJual() {
            const mode = document.getElementById('price_mode').value;
            const group = document.getElementById('harga_jual_group');
            group.style.display = mode === 'manual' ? 'block' : 'none';
        }
        document.getElementById('price_mode').addEventListener('change', toggleHargaJual);
        // init
        toggleHargaJual();
    </script>
</x-app-layout>
