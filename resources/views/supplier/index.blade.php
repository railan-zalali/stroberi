<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Supplier') }}
            </h2>
            <a href="{{ route('supplier.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Tambah Supplier') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('supplier.index') }}" class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/3">
                            <label for="search" class="block text-sm font-medium text-gray-700">Cari Supplier</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Cari nama, alamat, telepon...">
                            </div>
                        </div>

                        <div class="w-full md:w-1/3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Semua</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="w-full md:w-1/3 flex items-end">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Suppliers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($suppliers as $supplier)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-16 w-16 bg-gray-100 rounded-full overflow-hidden mr-4">
                                    @if ($supplier->foto)
                                        <img src="{{ Storage::url($supplier->foto) }}" alt="{{ $supplier->nama }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full text-gray-300 p-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800">{{ $supplier->nama }}</h2>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $supplier->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 mt-4">
                                @if ($supplier->alamat)
                                    <div class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-gray-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $supplier->alamat }}</span>
                                    </div>
                                @endif

                                @if ($supplier->telepon)
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $supplier->telepon }}</span>
                                    </div>
                                @endif

                                @if ($supplier->email)
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $supplier->email }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-500">Total Pinjaman:</span>
                                    <span
                                        class="text-sm font-semibold {{ $supplier->total_pinjaman > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        Rp {{ number_format($supplier->total_pinjaman, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-500">Total Pembayaran:</span>
                                    <span class="text-sm font-semibold text-green-600">
                                        Rp {{ number_format($supplier->total_pembayaran, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Sisa Pinjaman:</span>
                                    <span
                                        class="text-sm font-semibold {{ $supplier->total_pinjaman - $supplier->total_pembayaran > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp
                                        {{ number_format($supplier->total_pinjaman - $supplier->total_pembayaran, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6 flex space-x-2">
                                <a href="{{ route('supplier.show', $supplier) }}"
                                    class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full">
                                    Detail
                                </a>
                                <a href="{{ route('supplier.edit', $supplier) }}"
                                    class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-3">
                        <div class="p-6 text-center text-gray-500">
                            Tidak ada data supplier. Silakan tambahkan supplier baru.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
