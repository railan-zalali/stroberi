<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Stok Strawberi') }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('strawberi.sell-global.form') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7a1 1 0 00.9 1.3H17m-10 0a1 1 0 101 1 1 1 0 00-1-1zm10 0a1 1 0 101 1 1 1 0 00-1-1z" />
                    </svg>
                    {{ __('Jual Global') }}
                </a>
                <a href="{{ route('strawberi.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Tambah Stok') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Jenis Stok -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Berdasarkan Jenis</h3>
                        <div class="space-y-4">
                            <!-- Stok Segar -->
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok Segar</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokSegar ?? 0 }} kg</p>
                                </div>
                            </div>

                            <!-- Stok Beku -->
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok Beku</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokBeku ?? 0 }} kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rincian Stok per Jenis & Grade (vertikal) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Rincian Jenis & Grade</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">Segar A</div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok segar grade A</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokSegarA ?? 0 }} kg</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">Segar B</div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok segar grade B</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokSegarB ?? 0 }} kg</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">Segar C</div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok segar grade C</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokSegarC ?? 0 }} kg</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">Beku A</div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok beku grade A</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokBekuA ?? 0 }} kg</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">Beku B</div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok beku grade B</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokBekuB ?? 0 }} kg</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">Beku C</div>
                                <div>
                                    <p class="text-gray-500 text-sm">Stok beku grade C</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $stokBekuC ?? 0 }} kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Tambahan</h3>
                        <div class="space-y-4">

                            <!-- Hampir Kadaluarsa -->
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Hampir Kadaluarsa</p>
                                    <p class="text-xl font-bold text-gray-700">{{ $kadaluarsa ?? 0 }} kg</p>
                                </div>
                            </div>

                            <!-- Total Stok -->
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Total Stok</p>
                                    <p class="text-xl font-bold text-gray-700">
                                        {{ ($stokSegar ?? 0) + ($stokBeku ?? 0) }} kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('strawberi.index') }}"
                        class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/5">
                            <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                            <select id="jenis" name="jenis"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="">Semua</option>
                                <option value="segar" {{ request('jenis') == 'segar' ? 'selected' : '' }}>Segar
                                </option>
                                <option value="beku" {{ request('jenis') == 'beku' ? 'selected' : '' }}>Beku
                                </option>
                            </select>
                        </div>

                        <div class="w-full md:w-1/5">
                            <label for="grade" class="block text-sm font-medium text-gray-700">Grade</label>
                            <select id="grade" name="grade"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="">Semua</option>
                                <option value="a" {{ request('grade') == 'a' ? 'selected' : '' }}>Grade A
                                </option>
                                <option value="b" {{ request('grade') == 'b' ? 'selected' : '' }}>Grade B
                                </option>
                                <option value="c" {{ request('grade') == 'c' ? 'selected' : '' }}>Grade C
                                </option>
                            </select>
                        </div>

                        <div class="w-full md:w-1/5">
                            <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                            <select id="supplier" name="supplier_id"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="">Semua</option>
                                @foreach ($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-2/5">
                            <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="focus:ring-red-500 focus:border-red-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                    placeholder="Cari berdasarkan keterangan...">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-r-md font-semibold text-xs text-white bg-red-600 hover:bg-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Strawberry List Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
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
                                        Grade
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok Tersisa
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga Beli
                                    </th>

                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Supplier
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
                                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                                                    @if ($strawberi->jenis == 'segar')
                                                        <span class="p-2 rounded-full bg-green-100 text-green-600">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01" />
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <span class="p-2 rounded-full bg-blue-100 text-blue-600">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
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
                                            @php
                                                $gradeColors = [
                                                    'a' => 'bg-green-100 text-green-800',
                                                    'b' => 'bg-yellow-100 text-yellow-800',
                                                    'c' => 'bg-red-100 text-red-800',
                                                ];
                                                $gradeColor =
                                                    $gradeColors[$strawberi->grade] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $gradeColor }}">
                                                {{ strtoupper($strawberi->grade ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($strawberi->stok_tersisa, 2) }} kg</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Rp
                                                {{ number_format($strawberi->harga_beli, 0, ',', '.') }}</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $strawberi->supplier->nama }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $strawberi->tanggal_masuk->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- @php
                                                $sisaHari = $strawberi->sisa_hari_kadaluarsa;
                                                $badgeColor = 'bg-green-100 text-green-800';

                                                if ($sisaHari < 0) {
                                                    $badgeColor = 'bg-red-100 text-red-800';
                                                } elseif ($sisaHari <= 7) {
                                                    $badgeColor = 'bg-yellow-100 text-yellow-800';
                                                }
                                            @endphp --}}
                                            <div class="flex flex-col space-y-1">
                                                {{-- <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    {{ $strawberi->text_kadaluarsa }}
                                                </span> --}}
                                                <span class="text-xs text-gray-500">
                                                    {{ $strawberi->tanggal_kadaluarsa->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('strawberi.show', $strawberi) }}"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                @unless($strawberi->is_posted)
                                                    <form action="{{ route('strawberi.destroy', $strawberi) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Posted</span>
                                                @endunless
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9"
                                            class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Tidak ada data stok strawberi
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
        </div>
    </div>
</x-app-layout>
