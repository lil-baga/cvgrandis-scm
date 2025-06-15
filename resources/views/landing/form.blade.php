@extends('layout.master')

@section('title', 'Grandis.id - Form Pemesanan')
@section('content')
    <div class="bg-gray-100 font-sans min-h-screen w-full">
        <div class="max-w-2xl mx-auto py-10 px-4">
            <h1 class="text-2xl font-bold text-center mb-6">Form Pemesanan Proyek</h1>

            {{-- Tampilkan error validasi jika ada --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Terjadi Kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('order.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Nama Lengkap
                    </label>
                    <input id="name" name="name"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="text"
                        value="{{ old('name') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input id="email" name="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="email"
                        value="{{ old('email') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone_number">
                        Nomor Telepon
                    </label>
                    <input id="phone_number" name="phone_number"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="tel"
                        value="{{ old('phone_number') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="service">
                        Jenis Layanan
                    </label>
                    <select id="service" name="service" class="shadow border rounded w-full py-2 px-3 text-gray-700"
                        required>
                        <option value="" disabled {{ old('service') ? '' : 'selected' }}>-- Pilih Layanan --</option>
                        {{-- Pastikan value ini cocok dengan 'service_code' di tabel resep Anda --}}
                        <option value="neon" {{ old('service') == 'neon' ? 'selected' : '' }}>Neon Box</option>
                        <option value="backdrop" {{ old('service') == 'backdrop' ? 'selected' : '' }}>Backdrop Acara
                        </option>
                        <option value="interior" {{ old('service') == 'interior' ? 'selected' : '' }}>Design Interior
                        </option>
                        <option value="lettering" {{ old('service') == 'lettering' ? 'selected' : '' }}>Letter Akrilik &
                            Stainless</option>
                        <option value="event" {{ old('service') == 'event' ? 'selected' : '' }}>Event Organizer & RnD
                        </option>
                    </select>
                </div>

                {{-- Kontainer untuk input tambahan yang muncul secara kondisional --}}
                <div id="extra_service" class="mb-4 {{ old('service') ? '' : 'hidden' }} space-y-4">

                    {{-- Input untuk Ukuran (Lebar & Tinggi) --}}
                    <div id="sizeInputs" class="hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Ukuran</label>
                        <div class="flex gap-4">
                            <input type="number" name="width" placeholder="Lebar (dalam meter)" step="0.01"
                                class="w-1/2 border rounded-lg px-4 py-3 shadow-sm" value="{{ old('width') }}" />
                            <input type="number" name="height" placeholder="Tinggi (dalam meter)" step="0.01"
                                class="w-1/2 border rounded-lg px-4 py-3 shadow-sm" value="{{ old('height') }}" />
                        </div>
                    </div>

                    {{-- Input untuk Kuantitas (Jumlah Unit) --}}
                    <div id="quantityInput" class="hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="quantity">Jumlah Unit / Huruf</label>
                        <input type="number" name="quantity" id="quantity" placeholder="Masukkan jumlah"
                            class="w-full border rounded-lg px-4 py-3 shadow-sm" value="{{ old('quantity') }}" />
                    </div>

                    {{-- Input Deskripsi dan File (selalu muncul jika layanan dipilih) --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Deskripsi Proyek (Warna, Bahan, Detail lain)
                        </label>
                        <textarea id="description" name="description"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" rows="4">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            File Desain/Referensi (JPG, PNG, PDF, ZIP)
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="image_ref_input"
                                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk
                                            unggah</span> atau seret dan lepas</p>
                                    <p class="text-xs text-gray-500">JPG, PNG, PDF, ZIP (MAX: 10MB)</p>
                                    <p id="fileNameDisplay" class="mt-2 text-sm text-gray-600 font-semibold"></p>
                                </div>
                                <input id="image_ref_input" name="image_ref" type="file" class="hidden"
                                    accept=".jpg,.jpeg,.png,.pdf,.zip" />
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Input tersembunyi untuk status awal --}}
                <input name="status" type="hidden" value="in_queue" />

                <div class="flex items-center justify-center mt-6">
                    <button
                        class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                        Kirim Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service');
            const extraServiceDiv = document.getElementById('extra_service');
            const sizeInputsDiv = document.getElementById('sizeInputs');
            const quantityInputDiv = document.getElementById('quantityInput');
            const widthInput = sizeInputsDiv.querySelector('input[name="width"]');
            const heightInput = sizeInputsDiv.querySelector('input[name="height"]');
            const quantityInput = quantityInputDiv.querySelector('input[name="quantity"]');

            function toggleInputs() {
                const selectedService = serviceSelect.value;
                extraServiceDiv.classList.add('hidden');
                sizeInputsDiv.classList.add('hidden');
                quantityInputDiv.classList.add('hidden');
                widthInput.required = false;
                heightInput.required = false;
                quantityInput.required = false;

                if (selectedService) {
                    extraServiceDiv.classList.remove('hidden');

                    if (selectedService === 'neon' || selectedService === 'backdrop' || selectedService === 'lettering') {
                        sizeInputsDiv.classList.remove('hidden');
                        quantityInputDiv.classList.remove('hidden');
                        widthInput.required = true;
                        heightInput.required = true;
                        quantityInput.required = true;
                    }
                    // Untuk 'interior' dan 'event', tidak ada input numerik yang wajib,
                    // hanya deskripsi dan file yang muncul.
                }
            }

            // Jalankan saat halaman dimuat untuk menangani old input jika ada error validasi
            toggleInputs();

            // Jalankan saat pilihan layanan diubah
            serviceSelect.addEventListener('change', toggleInputs);

            // JavaScript untuk menampilkan nama file pada dropzone
            const fileInput = document.getElementById('image_ref_input');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            if (fileInput && fileNameDisplay) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        fileNameDisplay.textContent = this.files[0].name;
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                });
            }
        });
    </script>
@endpush
