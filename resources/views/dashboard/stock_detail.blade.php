@extends('layout.master')

@section('title', 'Stock Detail - ' . $stock->name)

@section('content')
    <div class=" pt-36 p-4 sm:ml-0 max-w-3xl mx-auto">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p class="font-bold">Oops!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p class="font-bold">Terjadi Kesalahan Validasi (Saat Update):</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">Detail Stok - {{ $stock->name }}</h2>
                <a href="{{ route('stock') }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                    &larr; Kembali ke Daftar Stok
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                <div>
                    <h4 class="font-semibold text-gray-700">ID Barang:</h4>
                    <p class="text-gray-600">{{ $stock->id }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Nama Barang:</h4>
                    <p class="text-gray-600">{{ $stock->name }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Tipe:</h4>
                    <p class="text-gray-600">{{ ucfirst($stock->type) }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Stok Saat Ini:</h4>
                    <p class="text-gray-600">{{ $stock->stock }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Batas Stok Rendah:</h4>
                    <p class="text-gray-600">{{ $stock->low_stock }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Status:</h4>
                    <p class="text-gray-600">
                        <span
                            class="status-badge {{ $stock->status == 'in_stock'
                                ? 'status-in_stock'
                                : ($stock->status == 'low_stock'
                                    ? 'status-low_stock'
                                    : ($stock->status == 'out_of_stock'
                                        ? 'status-out_of_stock'
                                        : 'bg-gray-200 text-gray-800')) }}">
                            {{ str_replace('_', ' ', ucfirst($stock->status)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Dibuat Pada:</h4>
                    <p class="text-gray-600">{{ $stock->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Terakhir Diubah:</h4>
                    <p class="text-gray-600">{{ $stock->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Buat Pesanan Stok ke Supplier</h3>
                <form action="{{ route('stock.order', $stock->id) }}" method="POST">
                    @csrf
                    <div class="flex items-end space-x-3">
                        <div class="flex-grow">
                            <label for="quantity" class="block mb-1 text-sm font-medium text-gray-700">Jumlah yang
                                Dipesan:</label>
                            <input type="number" name="quantity" id="quantity"
                                class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required min="1">
                        </div>
                        <button type="submit"
                            class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 h-fit">
                            Buat Pesanan
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t flex space-x-3">
                @if (Auth::check() && (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Supplier')))
                    <button data-modal-target="editStockItemModalDetail" data-modal-toggle="editStockItemModalDetail"
                        onclick="populateEditStockModal(
                            '{{ $stock->id }}', 
                            '{{ addslashes($stock->name) }}', 
                            '{{ $stock->type }}', 
                            {{ $stock->stock }}, 
                            {{ $stock->low_stock }}
                        )"
                        type="button"
                        class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        {{ Auth::user()->hasRole('Administrator') ? 'Edit Data Lengkap' : 'Update Jumlah Stok' }}
                    </button>
                @endif

                @if (Auth::check() && Auth::user()->hasRole('Administrator'))
                    <button data-modal-target="deleteStockItemModal" data-modal-toggle="deleteStockItemModal"
                        onclick="prepareStockDeleteModal('{{ $stock->id }}', '{{ addslashes($stock->name) }}')"
                        type="button"
                        class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Hapus Data
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Edit Item --}}
    <div id="editStockItemModalDetail" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 id="editModalTitle" class="text-xl font-semibold text-gray-900">Edit Stok Barang</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="editStockItemModalDetail">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="editStockItemFormDetail" method="POST" class="p-4 md:p-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="edit_stock">
                    <div class="grid gap-4 mb-4 grid-cols-1">
                        <div id="editFieldNameContainer">
                            <label for="editDetailName" class="block mb-2 text-sm font-medium text-gray-900">Nama
                                Barang</label>
                            <input type="text" name="name" id="editDetailName"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                        </div>
                        <div id="editFieldTypeContainer">
                            <label for="editDetailType" class="block mb-2 text-sm font-medium text-gray-900">Tipe
                                Barang</label>
                            <select name="type" id="editDetailType"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                                <option value="material">Material</option>
                                <option value="electricity">Electricity</option>
                                <option value="tools">Tools</option>
                            </select>
                        </div>
                        {{-- Field Jumlah Stok --}}
                        <div>
                            <label for="editDetailStock" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Stok
                                Baru</label>
                            <input type="number" name="stock" id="editDetailStock"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required min="0">
                            <p id="currentStockInfo" class="mt-1 text-xs text-gray-500"></p>
                        </div>
                        <div id="editFieldLowStockContainer">
                            <label for="editDetailLowStock" class="block mb-2 text-sm font-medium text-gray-900">Batas
                                Stok
                                Rendah</label>
                            <input type="number" name="low_stock" id="editDetailLowStock"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required min="0">
                        </div>
                    </div>
                    <button type="submit" id="editModalSubmitButton"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteStockItemModal" tabindex="-1"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="deleteStockItemModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">Anda yakin ingin menghapus barang <strong
                            id="deleteStockItemNamePreview"></strong>?</h3>
                    <form id="deleteStockItemFormDetail" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Ya, Hapus
                        </button>
                        <button data-modal-hide="deleteStockItemModal" type="button"
                            class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            const IS_ADMIN = {{ Auth::check() && Auth::user()->hasRole('Administrator') ? 'true' : 'false' }};
            const IS_SUPPLIER = {{ Auth::check() && Auth::user()->hasRole('Supplier') ? 'true' : 'false' }};
            let currentStockForSupplierModal = 0;

            window.populateEditStockModal = function(id, name, type, currentStockQty, low_stock) {
                const form = document.getElementById('editStockItemFormDetail');
                let updateUrl = "{{ route('stock.update', ['stock' => ':id']) }}";
                form.action = updateUrl.replace(':id', id);

                const nameInput = document.getElementById('editDetailName');
                const typeSelect = document.getElementById('editDetailType');
                const stockInput = document.getElementById('editDetailStock');
                const stockLabel = document.querySelector('label[for="editDetailStock"]');
                const lowStockInput = document.getElementById('editDetailLowStock');
                const currentStockInfo = document.getElementById('currentStockInfo');
                const modalTitle = document.getElementById('editModalTitle');
                const submitButton = document.getElementById('editModalSubmitButton');

                const nameContainer = document.getElementById('editFieldNameContainer');
                const typeContainer = document.getElementById('editFieldTypeContainer');
                const lowStockContainer = document.getElementById('editFieldLowStockContainer');

                nameInput.readOnly = false;
                typeSelect.disabled = false;
                lowStockInput.readOnly = false;
                stockInput.readOnly = false;
                stockInput.min = "0";

                nameInput.classList.remove('bg-gray-200', 'cursor-not-allowed');
                typeSelect.classList.remove('bg-gray-200', 'cursor-not-allowed');
                lowStockInput.classList.remove('bg-gray-200', 'cursor-not-allowed');

                if (nameContainer) nameContainer.style.display = 'block';
                if (typeContainer) typeContainer.style.display = 'block';
                if (lowStockContainer) lowStockContainer.style.display = 'block';

                nameInput.value = name;
                typeSelect.value = type;
                stockInput.value = currentStockQty;
                lowStockInput.value = low_stock;
                if (stockLabel) stockLabel.textContent = 'Jumlah Stok Baru';
                if (currentStockInfo) currentStockInfo.textContent =
                    `Stok saat ini: ${currentStockQty}. Masukkan jumlah stok baru.`;

                if (IS_ADMIN) {
                    modalTitle.textContent = 'Edit Stok Barang Lengkap';
                    submitButton.textContent = 'Simpan Perubahan';
                } else if (IS_SUPPLIER) {
                    modalTitle.textContent = 'Tambah Jumlah Stok';
                    submitButton.textContent = 'Tambah Stok';

                    nameInput.readOnly = true;
                    typeSelect.disabled = true;
                    lowStockInput.readOnly = true;

                    nameInput.classList.add('bg-gray-200', 'cursor-not-allowed');
                    typeSelect.classList.add('bg-gray-200', 'cursor-not-allowed');
                    lowStockInput.classList.add('bg-gray-200', 'cursor-not-allowed');

                    if (nameContainer) nameContainer.style.display = 'none';
                    if (typeContainer) typeContainer.style.display = 'none';
                    if (lowStockContainer) lowStockContainer.style.display = 'none';

                    if (stockLabel) stockLabel.textContent = 'Jumlah Stok yang Ditambahkan';
                    stockInput.value = "";
                    stockInput.placeholder = "0";
                    stockInput.min = "0";
                    currentStockForSupplierModal =
                        currentStockQty;
                    if (currentStockInfo) {
                        currentStockInfo.textContent =
                            `Stok saat ini: ${currentStockQty}. Masukkan jumlah yang ingin ditambahkan.`;
                    }
                } else {
                    modalTitle.textContent = 'Lihat Stok Barang';
                    if (submitButton) submitButton.style.display = 'none';
                    nameInput.readOnly = true;
                    typeSelect.disabled = true;
                    stockInput.readOnly = true;
                    lowStockInput.readOnly = true;
                }
            }

            window.prepareStockDeleteModal = function(id, name) {
                const form = document.getElementById('deleteStockItemFormDetail');
                let deleteUrl = "{{ route('stock.destroy', ['stock' => ':id']) }}";
                form.action = deleteUrl.replace(':id', id);
                document.getElementById('deleteStockItemNamePreview').textContent = name;
            }

            document.addEventListener('DOMContentLoaded', function() {
                @if (session('open_edit_modal_on_error') && $errors->any() && isset($stock))
                    populateEditStockModal(
                        '{{ $stock->id }}',
                        '{{ addslashes($stock->name) }}',
                        '{{ $stock->type }}',
                        {{ $stock->stock }},
                        {{ $stock->low_stock }}
                    );

                    document.getElementById('editDetailName').value = "{{ old('name', addslashes($stock->name)) }}";
                    document.getElementById('editDetailType').value = "{{ old('type', $stock->type) }}";
                    document.getElementById('editDetailStock').value =
                        "{{ old('stock_input_value', IS_SUPPLIER ? '' : $stock->stock) }}";
                    document.getElementById('editDetailLowStock').value = "{{ old('low_stock', $stock->low_stock) }}";

                    const editForm = document.getElementById('editStockItemFormDetail');
                    if (editForm) {
                        let updateUrlOnError = "{{ route('stock.update', ['stock' => $stock->id]) }}";
                        editForm.action = updateUrlOnError;
                    }

                    const editModalEl = document.getElementById('editStockItemModalDetail');
                    if (editModalEl) {
                        let modalInstance = FlowbiteInstances.getInstance('Modal', 'editStockItemModalDetail');
                        if (!modalInstance) {
                            modalInstance = new Modal(editModalEl);
                        }
                        modalInstance.show();
                    }
                @endif
            });
        </script>
    @endpush
