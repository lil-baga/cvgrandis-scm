@extends('layout.master')

@section('title', 'Order Detail - ' . $order->name)

@section('content')
    <div class="pt-10 p-4 sm:ml-0 max-w-3xl mx-auto">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Terjadi Kesalahan Validasi:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">Detail Pesanan - {{ $order->name }}</h2>
                <a href="{{ route('order.list') }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                    &larr; Kembali ke Daftar Pesanan
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <h4 class="font-semibold text-gray-700">Nama Pelanggan:</h4>
                    <p class="text-gray-600">{{ $order->name }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Email:</h4>
                    <p class="text-gray-600">{{ $order->email }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Nomor Telepon:</h4>
                    <p class="text-gray-600">{{ $order->phone_number }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Jenis Layanan:</h4>
                    <p class="text-gray-600">
                        {{ $order->service_display_name ?? Str::title(str_replace('_', ' ', $order->service)) }}</p>
                </div>
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-gray-700">Deskripsi Proyek:</h4>
                    <p class="text-gray-600 whitespace-pre-wrap">{{ $order->description ?: '-' }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Status Pesanan Saat Ini:</h4>
                    <p class="text-gray-600">
                        <span
                            class="status-badge {{ $order->status == 'in_queue' ? 'status-in_queue' : ($order->status == 'on_going' ? 'status-on_going' : ($order->status == 'finished' ? 'status-finished' : 'bg-gray-200 text-gray-800')) }}">
                            {{ str_replace('_', ' ', ucfirst($order->status)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Tanggal Pesan:</h4>
                    <p class="text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Stok Barang yang Digunakan untuk Pesanan Ini</h3>
                @if ($order->stockDeductions && $order->stockDeductions->count() > 0)
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                        @foreach ($order->stockDeductions as $deduction)
                            <li>
                                {{ $deduction->stock->name ?? 'Nama Stok Tidak Diketahui' }} (Tipe:
                                {{ $deduction->stock->type ?? 'N/A' }}) -
                                Jumlah Digunakan: {{ $deduction->quantity_deducted }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">Belum ada data pengurangan stok yang tercatat untuk pesanan ini.</p>
                @endif
            </div>
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Ubah Status Pesanan</h3>
                <form action="{{ route('order.update', $order->id) }}" method="POST">
                    @csrf
                    <div class="flex items-end space-x-3">
                        <div class="flex-grow">
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status Baru:</label>
                            <select id="status" name="status"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="in_queue" {{ $order->status == 'in_queue' ? 'selected' : '' }}>Dalam Antrian
                                    (In Queue)</option>
                                <option value="on_going" {{ $order->status == 'on_going' ? 'selected' : '' }}>Sedang
                                    Dikerjakan (On Going)</option>
                                <option value="finished" {{ $order->status == 'finished' ? 'selected' : '' }}>Selesai
                                    (Finished)</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center h-fit">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>


            <div class="mt-8 pt-6 border-t">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Pengurangan Stok untuk Proyek Ini</h3>
                    <button type="button" data-modal-target="selectStockItemModal" data-modal-toggle="selectStockItemModal"
                        class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2">
                        Pilih Barang Stok
                    </button>
                </div>

                <form action="{{ route('order.adjust', $order->id) }}" method="POST">
                    @csrf
                    <div id="selectedStockForAdjustmentList" class="space-y-3 mb-4">
                        <p class="text-sm text-gray-500 if-empty-list">Belum ada barang stok yang dipilih untuk dikurangi.
                        </p>
                    </div>
                    <button type="submit" id="saveStockAdjustmentsButton"
                        class="w-full sm:w-auto text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center hidden">
                        Simpan Perubahan Stok Proyek
                    </button>
                </form>
            </div>

            @if ($fileAttachmentDetails && $fileAttachmentDetails->url)
                <div class="mt-6 pt-4 border-t">
                    <h4 class="font-semibold text-gray-700 mb-2">File Lampiran: {{ $fileAttachmentDetails->name }}</h4>
                    @php
                        $fileType = $fileAttachmentDetails->type;
                        $fileUrl = $fileAttachmentDetails->url;
                        $originalFileName = $fileAttachmentDetails->name;
                        $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
                    @endphp

                    @if (Str::startsWith($fileType, 'image/'))
                        <img src="{{ asset($order->path) }}" alt="Preview Lampiran: {{ $originalFileName }}"
                            class="max-w-full h-auto rounded-lg border max-h-96 mb-2">
                        <a href="{{ asset($order->path) }}" download="{{ $originalFileName }}"
                            class="inline-flex items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Unduh File</a>
                    @elseif ($fileType === 'application/pdf' || $extension === 'pdf')
                        <div class="mb-2">
                            <p class="text-sm text-gray-600 mb-1">Preview PDF:</p>
                            <iframe src="{{ asset($order->path) }}" class="w-full h-full border rounded-lg" frameborder="0"
                                title="PDF Preview {{ $originalFileName }}"></iframe>
                        </div>
                    @else
                        <p class="text-sm text-gray-600 mb-2">Preview tidak tersedia untuk tipe file ini.</p>
                        <a href="{{ asset($order->path) }}" download="{{ $originalFileName }}"
                            class="inline-flex items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Unduh File</a>
                    @endif

                </div>
            @else
                <div class="mt-6 pt-4 border-t">
                    <h4 class="font-semibold text-gray-700">File Lampiran:</h4>
                    <p class="text-gray-500">Tidak ada file lampiran untuk pesanan ini.</p>
                </div>
            @endif
        </div>
    </div>

    <div id="selectStockItemModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Pilih Barang Stok</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="selectStockItemModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
                    <input type="text" id="searchStockModalInput"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mb-4"
                        placeholder="Cari nama barang stok...">
                    <div id="availableStockListModal" class="max-h-96 overflow-y-auto space-y-2">
                        <p class="text-gray-500">Memuat daftar stok...</p>
                    </div>
                </div>
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" data-modal-hide="selectStockItemModal"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">Selesai
                        Memilih</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .status-badge {
            padding: 0.25em 0.6em;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            text-transform: capitalize;
        }

        .status-in_queue {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-on_going {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-finished {
            background-color: #D1FAE5;
            color: #065F46;
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

@push('script')
    <script>
        const allAvailableStockItems = @json($allStockItems ?? []);
        let selectedStockForAdjustment = [];

        const selectedStockListDiv = document.getElementById('selectedStockForAdjustmentList');
        const saveAdjustmentsButton = document.getElementById('saveStockAdjustmentsButton');
        const emptyListMessage = selectedStockListDiv.querySelector('.if-empty-list');

        const availableStockListModalDiv = document.getElementById('availableStockListModal');
        const searchStockModalInput = document.getElementById('searchStockModalInput');

        function renderAvailableStockItems(filterTerm = '') {
            availableStockListModalDiv.innerHTML = '';
            const filteredItems = allAvailableStockItems.filter(item =>
                item.name.toLowerCase().includes(filterTerm.toLowerCase())
            );

            if (filteredItems.length === 0) {
                availableStockListModalDiv.innerHTML = '<p class="text-gray-500">Tidak ada barang stok ditemukan.</p>';
                return;
            }

            filteredItems.forEach(item => {
                const isAlreadySelected = selectedStockForAdjustment.some(selected => selected.id === item.id);

                const itemDiv = document.createElement('div');
                itemDiv.className = 'p-3 border rounded-md flex justify-between items-center ' + (
                    isAlreadySelected ? 'bg-gray-200 cursor-not-allowed' : 'hover:bg-gray-50');
                itemDiv.innerHTML = `
                <div>
                    <p class="font-medium text-gray-800">${item.name} (Tipe: ${item.type})</p>
                    <p class="text-xs text-gray-500">Stok Tersedia: ${item.stock}, Batas Rendah: ${item.low_stock}</p>
                </div>
                <button type="button" 
                        class="add-stock-to-list-btn text-sm font-medium rounded-lg px-3 py-1.5 focus:outline-none ${isAlreadySelected ? 'bg-gray-400 text-gray-700' : 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300'}"
                        data-stock-id="${item.id}" 
                        data-stock-name="${item.name}" 
                        data-stock-current="${item.stock}"
                        ${isAlreadySelected ? 'disabled' : ''}>
                    ${isAlreadySelected ? 'Sudah Dipilih' : 'Pilih'}
                </button>
            `;
                availableStockListModalDiv.appendChild(itemDiv);
            });

            document.querySelectorAll('.add-stock-to-list-btn:not([disabled])').forEach(button => {
                button.addEventListener('click', function() {
                    const stockId = this.dataset.stockId;
                    const stockName = this.dataset.stockName;
                    const currentStock = parseInt(this.dataset.stockCurrent);

                    addStockItemToAdjustmentList({
                        id: stockId,
                        name: stockName,
                        stock: currentStock
                    });

                    this.textContent = 'Sudah Dipilih';
                    this.disabled = true;
                    this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    this.classList.add('bg-gray-400', 'text-gray-700');
                });
            });
        }

        function addStockItemToAdjustmentList(stockItemData) {
            if (selectedStockForAdjustment.some(item => item.id === stockItemData.id)) {
                console.warn("Item sudah dipilih:", stockItemData.name);
                return;
            }

            selectedStockForAdjustment.push(stockItemData);
            renderSelectedStockItems();
        }

        function removeStockItemFromAdjustmentList(stockId) {
            selectedStockForAdjustment = selectedStockForAdjustment.filter(item => item.id !== stockId);
            renderSelectedStockItems();
            renderAvailableStockItems(searchStockModalInput.value);
        }

        function renderSelectedStockItems() {
            selectedStockListDiv.innerHTML = '';

            if (selectedStockForAdjustment.length === 0) {
                selectedStockListDiv.innerHTML =
                    '<p class="text-sm text-gray-500 if-empty-list">Belum ada barang stok yang dipilih untuk dikurangi.</p>';
                saveAdjustmentsButton.classList.add('hidden');
                return;
            }

            saveAdjustmentsButton.classList.remove('hidden');

            selectedStockForAdjustment.forEach(item => {
                const div = document.createElement('div');
                div.className = 'grid grid-cols-12 gap-2 items-center p-2 border rounded-md';
                div.innerHTML = `
                <div class="col-span-5">
                    <span class="text-sm font-medium text-gray-900 block truncate" title="${item.name}">${item.name}</span>
                    <span class="text-xs text-gray-500 block">Stok Tersedia: ${item.stock}</span>
                </div>
                <div class="col-span-4">
                    <input type="number" name="adjustments[${item.id}]"
                        min="0" max="${item.stock}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                        placeholder="Jml. dipakai" required>
                </div>
                <div class="col-span-3 flex justify-end">
                    <button type="button" onclick="removeStockItemFromAdjustmentList('${item.id}')" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                </div>
            `;
                selectedStockListDiv.appendChild(div);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectStockModalEl = document.getElementById('selectStockItemModal');
            if (selectStockModalEl) {}

            if (searchStockModalInput) {
                searchStockModalInput.addEventListener('keyup', function() {
                    renderAvailableStockItems(this.value);
                });
            }

            const openSelectStockModalButton = document.querySelector('[data-modal-target="selectStockItemModal"]');
            if (openSelectStockModalButton) {
                openSelectStockModalButton.addEventListener('click', function() {
                    renderAvailableStockItems(searchStockModalInput
                        .value);
                });
            }
            renderSelectedStockItems();
        });
    </script>
@endpush
