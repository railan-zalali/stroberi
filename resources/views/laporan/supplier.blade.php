<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Supplier') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan.export-supplier') }}"
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
            <!-- Analysis Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Top Suppliers by Volume -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Supplier (Volume)</h3>

                        @if ($topSuppliersByVolume->count() > 0)
                            <div class="space-y-4">
                                @foreach ($topSuppliersByVolume as $supplier)
                                    <div class="flex items-center">
                                        <div class="w-32 text-sm font-medium text-gray-900">{{ $supplier->nama }}</div>
                                        <div class="flex-1">
                                            <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-200">
                                                <div style="width: {{ ($supplier->total_kg / $topSuppliersByVolume->max('total_kg')) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-24 text-right text-sm text-gray-600">
                                            {{ number_format($supplier->total_kg, 2) }} kg</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-md text-gray-500 text-center">
                                Tidak ada data supplier
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Top Suppliers by Value -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Supplier (Nilai)</h3>

                        @if ($topSuppliersByValue->count() > 0)
                            <div class="space-y-4">
                                @foreach ($topSuppliersByValue as $supplier)
                                    <div class="flex items-center">
                                        <div class="w-32 text-sm font-medium text-gray-900">{{ $supplier->nama }}</div>
                                        <div class="flex-1">
                                            <div class="overflow-hidden h-2 text-xs flex rounded bg-green-200">
                                                <div style="width: {{ ($supplier->total_nilai / $topSuppliersByValue->max('total_nilai')) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-600">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-36 text-right text-sm text-gray-600">Rp
                                            {{ number_format($supplier->total_nilai, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-md text-gray-500 text-center">
                                Tidak ada data supplier
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Suppliers with Debt -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Supplier dengan Pinjaman Tertinggi</h3>

                        @if ($suppliersWithDebt->count() > 0)
                            <div class="space-y-4">
                                @foreach ($suppliersWithDebt as $supplier)
                                    <div class="flex items-center">
                                        <div class="w-32 text-sm font-medium text-gray-900">{{ $supplier->nama }}</div>
                                        <div class="flex-1">
                                            <div class="overflow-hidden h-2 text-xs flex rounded bg-red-200">
                                                <div style="width: {{ (($supplier->total_pinjaman - $supplier->total_pembayaran) / ($suppliersWithDebt->max('total_pinjaman') - $suppliersWithDebt->min('total_pembayaran'))) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-600">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-36 text-right text-sm text-red-600">Rp
                                            {{ number_format($supplier->total_pinjaman - $supplier->total_pembayaran, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-md text-gray-500 text-center">
                                Tidak ada supplier dengan pinjaman
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Supplier</p>
                                <p class="text-xl font-bold text-gray-900">{{ $suppliers->total() }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm font-medium text-gray-500 mb-1">Supplier Aktif</p>
                                <p class="text-xl font-bold text-green-600">
                                    {{ $suppliers->where('status', 'aktif')->count() }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Pinjaman</p>
                                <p class="text-xl font-bold text-red-600">Rp
                                    {{ number_format($suppliers->sum('total_pinjaman'), 0, ',', '.') }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Pembayaran</p>
                                <p class="text-xl font-bold text-green-600">Rp
                                    {{ number_format($suppliers->sum('total_pembayaran'), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Supplier</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Telepon
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Pinjaman
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Pembayaran
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sisa Pinjaman
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Kg
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Nilai
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($suppliers as $supplier)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full overflow-hidden">
                                                    @if ($supplier->foto)
                                                        <img src="{{ Storage::url($supplier->foto) }}"
                                                            alt="{{ $supplier->nama }}"
                                                            class="h-full w-full object-cover">
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-full w-full text-gray-300 p-2" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $supplier->nama }}</div>
                                                    <div class="text-sm text-gray-500">{{ $supplier->email ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $supplier->telepon ?? '-' }}</div>
                                        </td>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm {{ $supplier->total_pinjaman - $supplier->total_pembayaran > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            Rp
                                            {{ number_format($supplier->total_pinjaman - $supplier->total_pembayaran, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($supplier->total_kg ?? 0, 2) }} kg
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($supplier->total_nilai ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $supplier->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($supplier->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada data supplier
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
