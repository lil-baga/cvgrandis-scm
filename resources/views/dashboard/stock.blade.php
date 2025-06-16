@extends('layout.master')

@section('title', 'Stock List')

@section('content')
    <div class="pt-20 p-4 sm:ml-0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Jenis Barang</h3>
                <p class="text-3xl font-bold text-blue-500">{{ $totalItems ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-semibold text-gray-700">Barang Stok Rendah</h3>
                <p class="text-3xl font-bold text-yellow-500">{{ $lowStockItems ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-semibold text-gray-700">Barang Habis Stok</h3>
                <p class="text-3xl font-bold text-red-500">{{ $outOfStockItems ?? 0 }}</p>
            </div>
        </div>

        {{-- Filter dan Tombol Tambah --}}
        <div class="mb-4 p-6 bg-white shadow-md rounded-lg flex flex-col sm:flex-row justify-between items-center">
            <div class="flex items-center space-x-2 mb-4 sm:mb-0">
                <a href="{{ route('stock', ['activity_status' => 'active']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg {{ ($activityFilter ?? 'inactive') == 'active' ? 'bg-blue-700 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' }}">
                    Stok Aktif
                </a>
                <a href="{{ route('stock', ['activity_status' => 'inactive']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg {{ ($activityFilter ?? 'active') == 'inactive' ? 'bg-blue-700 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' }}">
                    Stok Non-Aktif
                </a>
            </div>
            <div class="flex items-center w-full sm:w-auto">
                <form action="{{ route('stock') }}" method="GET" class="flex-grow sm:mr-4">
                    <input type="hidden" name="activity_status" value="{{ $activityFilter ?? 'active' }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" id="searchInput" name="search" value="{{ request('search') }}"
                            class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari Nama Barang...">
                    </div>
                </form>
                <button data-modal-target="addItemModal" data-modal-toggle="addItemModal" type="button"
                    class="ml-2 w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                    Tambah Barang
                </button>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full text-sm text-gray-700 table">
                <thead class="text-xs text-center text-gray-900 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No.</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="px-6 py-3">Tipe</th>
                        <th scope="col" class="px-6 py-3">Stok</th>
                        <th scope="col" class="px-6 py-3">Batas Rendah</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Terakhir Diubah</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stocks as $stockItem)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-6 py-4 text-center text-gray-900">
                                {{ ($stocks->currentPage() - 1) * $stocks->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $stockItem->name }}</td>
                            <td class="px-6 py-4 text-center text-gray-900">{{ ucfirst($stockItem->type) }}</td>
                            <td class="px-6 py-4 text-center">{{ $stockItem->stock }}</td>
                            <td class="px-6 py-4 text-center">{{ $stockItem->low_stock }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="status-badge {{ $stockItem->status == 'in_stock'
                                        ? 'status-in_stock'
                                        : ($stockItem->status == 'low_stock'
                                            ? 'status-low_stock'
                                            : ($stockItem->status == 'out_of_stock'
                                                ? 'status-out_of_stock'
                                                : 'bg-gray-200 text-gray-800')) }}">
                                    {{ str_replace('_', ' ', ucfirst($stockItem->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $stockItem->updated_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                {{-- Tombol Aksi yang berubah --}}
                                @if ($stockItem->is_active)
                                    <a href="{{ route('stock.show', $stockItem->id) }}"
                                        class="font-medium text-blue-600 hover:underline mr-3">Detail</a>
                                    <button type="button"
                                        onclick="showConfirmationModal(
                                                '{{ route('stock.toggle', $stockItem->id) }}',
                                                'POST',
                                                'Anda yakin ingin menonaktifkan barang <strong>{{ addslashes($stockItem->name) }}</strong>?',
                                                'bg-red-500 hover:bg-red-600',
                                                'Ya, Non-aktifkan'
                                            )"
                                        class="text-red-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                        Non-aktifkan
                                    </button>
                                @else
                                    <a href="{{ route('stock.show', $stockItem->id) }}"
                                        class="font-medium text-gray-600 hover:underline mr-3">Detail</a>
                                    <button type="button"
                                        onclick="showConfirmationModal(
                                            '{{ route('stock.toggle', $stockItem->id) }}',
                                            'POST',
                                            'Anda yakin ingin mengaktifkan kembali barang <strong>{{ addslashes($stockItem->name) }}</strong>?',
                                            'bg-green-500 hover:bg-green-600',
                                            'Ya, Aktifkan'
                                        )"
                                        class="text-green-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                        Aktifkan Kembali
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                @if (request('search'))
                                    Barang dengan nama "{{ request('search') }}" tidak ditemukan.
                                @else
                                    Belum ada data stok barang.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($stocks instanceof \Illuminate\Pagination\LengthAwarePaginator && $stocks->hasPages())
                <div class="p-4">
                    {{ $stocks->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="addItemModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Tambah Barang Stok Baru</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="addItemModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="addItemForm" action="{{ route('stock.store') }}" method="POST" class="p-4 md:p-5">
                    @csrf
                    <div class="grid gap-4 mb-4 grid-cols-1">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Barang</label>
                            <input type="text" name="name" id="add_name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Masukkan nama barang" required value="{{ old('name') }}">
                        </div>
                        <div>
                            <label for="type" class="block mb-2 text-sm font-medium text-gray-900">Tipe Barang</label>
                            <select name="type" id="add_type"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>-- Pilih Tipe --
                                </option>
                                <option value="material" {{ old('type') == 'material' ? 'selected' : '' }}>Material
                                </option>
                                <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>
                                    Electricity</option>
                                <option value="tools" {{ old('type') == 'tools' ? 'selected' : '' }}>Tools</option>
                            </select>
                        </div>
                        <div>
                            <label for="stock" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Stok
                                Awal</label>
                            <input type="number" name="stock" id="add_stock"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="0" required min="0" value="{{ old('stock') }}">
                        </div>
                        <div>
                            <label for="low_stock" class="block mb-2 text-sm font-medium text-gray-900">Batas Stok
                                Rendah</label>
                            <input type="number" name="low_stock" id="add_low_stock"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Contoh: 10" required min="0" value="{{ old('low_stock') }}">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah
                        Barang</button>
                </form>
            </div>
        </div>
    </div>

    <div id="confirmationModal" tabindex="-1"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="confirmationModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 id="confirmationModalMessage" class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                        Apakah Anda yakin?</h3>
                    <form id="confirmationModalForm" method="POST">
                        @csrf
                        <input type="hidden" id="confirmationModalMethod" name="_method" value="POST">

                        <button id="confirmationModalConfirmButton" type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Ya, Saya Yakin
                        </button>
                        <button data-modal-hide="confirmationModal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            Batal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <style>
        .status-badge {
            padding: 0.25em 0.6em;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            text-transform: capitalize;
        }

        .status-in_stock {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-low_stock {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-out_of_stock {
            background-color: #FEE2E2;
            color: #991B1B;
        }
    </style>
@endsection
