<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Penjualan Global Strawberi (FEFO)') }}</h2>
            <a href="{{ route('strawberi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Kembali</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <form action="{{ route('strawberi.sell-global.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="sellGlobalForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis</label>
                                <select name="jenis" id="jenis" class="mt-1 w-full border rounded px-3 py-2" required>
                                    <option value="segar" {{ old('jenis') === 'segar' ? 'selected' : '' }}>Segar</option>
                                    <option value="beku" {{ old('jenis') === 'beku' ? 'selected' : '' }}>Beku</option>
                                </select>
                                @error('jenis')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Preferensi Grade</label>
                                <select name="preferensi_grade" id="preferensi_grade" class="mt-1 w-full border rounded px-3 py-2">
                                    <option value="">Campur (FEFO)</option>
                                    <option value="a" {{ old('preferensi_grade') === 'a' ? 'selected' : '' }}>Grade A</option>
                                    <option value="b" {{ old('preferensi_grade') === 'b' ? 'selected' : '' }}>Grade B</option>
                                    <option value="c" {{ old('preferensi_grade') === 'c' ? 'selected' : '' }}>Grade C</option>
                                </select>
                                @error('preferensi_grade')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah Jual (kg)</label>
                                <input type="number" step="0.01" min="0.01" name="jumlah_jual" id="jumlah_jual" value="{{ old('jumlah_jual') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                                @error('jumlah_jual')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Jika stok tidak cukup, sistem akan melakukan penjualan partial otomatis.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mode Harga</label>
                                <select id="price_mode" name="price_mode" class="mt-1 w-full border rounded px-3 py-2" required>
                                    <option value="manual" {{ old('price_mode') === 'manual' ? 'selected' : '' }}>Manual (isi harga jual per kg)</option>
                                    <option value="buy" {{ old('price_mode') === 'buy' ? 'selected' : '' }}>Harga beli rata-rata (weighted)</option>
                                </select>
                                @error('price_mode')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="harga_jual_group" class="md:col-span-2" style="display:none;">
                                <label class="block text-sm font-medium text-gray-700">Harga Jual per kg</label>
                                <input type="number" step="0.01" min="0" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}" class="mt-1 w-full border rounded px-3 py-2">
                                @error('harga_jual')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pembeli</label>
                                    <input type="text" name="pembeli" id="pembeli" value="{{ old('pembeli') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                                    @error('pembeli')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Jual</label>
                                    <input type="date" name="tanggal_jual" id="tanggal_jual" value="{{ old('tanggal_jual') ?? now()->format('Y-m-d') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                                    @error('tanggal_jual')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Bukti Pembayaran (opsional)</label>
                                <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="mt-1 w-full border rounded px-3 py-2">
                                @error('bukti_pembayaran')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Keterangan (opsional)</label>
                                <textarea name="keterangan" id="keterangan" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-2">
                            <a href="{{ route('strawberi.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700" id="submitBtn">Simpan Penjualan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Ringkasan & Perkiraan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis dipilih</span>
                            <span class="font-semibold" id="summaryJenis">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Preferensi grade</span>
                            <span class="font-semibold" id="summaryGrade">Campur (FEFO)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah jual</span>
                            <span class="font-semibold" id="summaryJumlah">0 kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mode harga</span>
                            <span class="font-semibold" id="summaryMode">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Perkiraan total</span>
                            <span class="font-semibold text-indigo-600" id="summaryTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Ringkasan Stok Tersedia</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Segar Total: <span class="font-semibold">{{ number_format($stokSegarTotal ?? 0, 2) }} kg</span></p>
                                <p class="text-gray-600">Grade A: <span class="font-semibold">{{ number_format($stokSegarA ?? 0, 2) }} kg</span></p>
                                <p class="text-gray-600">Grade B: <span class="font-semibold">{{ number_format($stokSegarB ?? 0, 2) }} kg</span></p>
                                <p class="text-gray-600">Grade C: <span class="font-semibold">{{ number_format($stokSegarC ?? 0, 2) }} kg</span></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Beku Total: <span class="font-semibold">{{ number_format($stokBekuTotal ?? 0, 2) }} kg</span></p>
                                <p class="text-gray-600">Grade A: <span class="font-semibold">{{ number_format($stokBekuA ?? 0, 2) }} kg</span></p>
                                <p class="text-gray-600">Grade B: <span class="font-semibold">{{ number_format($stokBekuB ?? 0, 2) }} kg</span></p>
                                <p class="text-gray-600">Grade C: <span class="font-semibold">{{ number_format($stokBekuC ?? 0, 2) }} kg</span></p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Penjualan FEFO mengambil stok dengan tanggal kadaluarsa terdekat terlebih dahulu. Jika preferensi grade tidak cukup, sistem akan mencampur grade otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const priceModeEl = document.getElementById('price_mode');
        const priceGroupEl = document.getElementById('harga_jual_group');
        const hargaInput = document.getElementById('harga_jual');
        const jumlahInput = document.getElementById('jumlah_jual');
        const jenisEl = document.getElementById('jenis');
        const gradeEl = document.getElementById('preferensi_grade');
        const summaryJenis = document.getElementById('summaryJenis');
        const summaryGrade = document.getElementById('summaryGrade');
        const summaryJumlah = document.getElementById('summaryJumlah');
        const summaryMode = document.getElementById('summaryMode');
        const summaryTotal = document.getElementById('summaryTotal');

        function formatRupiah(n) {
            const s = (Math.round((n || 0) * 100) / 100).toString();
            return 'Rp ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function toggleHargaJual() {
            const mode = priceModeEl.value;
            priceGroupEl.style.display = mode === 'manual' ? 'block' : 'none';
            summaryMode.textContent = mode === 'manual' ? 'Manual' : 'Weighted buy';
            updateSummary();
        }

        function updateSummary() {
            const jns = jenisEl.value;
            summaryJenis.textContent = jns.charAt(0).toUpperCase() + jns.slice(1);
            const gd = gradeEl.value;
            summaryGrade.textContent = gd ? ('Grade ' + gd.toUpperCase()) : 'Campur (FEFO)';
            const qty = parseFloat(jumlahInput.value || '0');
            summaryJumlah.textContent = (isNaN(qty) ? 0 : qty.toFixed(2)) + ' kg';
            const mode = priceModeEl.value;
            if (mode === 'manual') {
                const h = parseFloat(hargaInput.value || '0');
                const total = (isNaN(qty) || isNaN(h)) ? 0 : qty * h;
                summaryTotal.textContent = formatRupiah(total);
            } else {
                summaryTotal.textContent = 'Menggunakan harga beli rata-rata';
            }
        }

        priceModeEl.addEventListener('change', toggleHargaJual);
        jumlahInput.addEventListener('input', updateSummary);
        hargaInput.addEventListener('input', updateSummary);
        jenisEl.addEventListener('change', updateSummary);
        gradeEl.addEventListener('change', updateSummary);

        toggleHargaJual();
        updateSummary();
    </script>
</x-app-layout>
