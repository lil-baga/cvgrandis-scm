@extends('layout.master')

@section('title', 'Form Pemesanan')
@section('content')
    <div class="bg-gray-100 font-sans min-h-screen w-full">
        <div class="max-w-2xl mx-auto py-10 px-4">
            <h1 class="text-2xl font-bold text-center mb-6">Form Pemesanan Proyek</h1>

            <form action="{{ route('order.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Nama Lengkap
                    </label>
                    <input id="name" name="name"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="text"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input id="email" name="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="email"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone_number">
                        Nomor Telepon
                    </label>
                    <input id="phone_number" name="phone_number"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="tel"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="service">
                        Jenis Layanan
                    </label>
                    <select id="service" name="service" class="shadow border rounded w-full py-2 px-3 text-gray-700"
                        required>
                        <option value="" disabled selected>-- Pilih Layanan --</option>
                        <option value="neon">Neon Box</option>
                        <option value="backdrop">Backdrop Acara</option>
                        <option value="interior">Design Interior</option>
                        <option value="lettering">Letter Akrilik & Stainless</option>
                        <option value="event">Event Organizer & RnD</option>
                    </select>
                </div>

                <div id="extra_service" class="mb-4 hidden">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi Proyek (Ukuran, Bahan, Warna, dll.)
                    </label>
                    <textarea id="description" name="description"
                        class="shadow appearance-none border rounded w-full py-2 px-3 mb-4 text-gray-700" rows="4"></textarea>

                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image_ref_dropzone">
                        File Desain/Referensi (JPG, PNG, PDF, ZIP)
                    </label>
                    <div class="flex items-center justify-center w-full mb-4">
                        <label for="image_ref_input"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik
                                        untuk unggah</span> atau seret dan lepas</p>
                                <p class="text-xs text-gray-500">JPG, PNG, PDF, ZIP (MAX: 10MB)</p>
                                {{-- Tempat untuk menampilkan nama file yang dipilih --}}
                                <p id="fileNameDisplay" class="mt-2 text-sm text-gray-600"></p>
                            </div>
                            {{-- Input file yang sebenarnya, disembunyikan --}}
                            <input id="image_ref_input" name="image_ref" type="file" class="hidden"
                                accept=".jpg,.jpeg,.png,.pdf,.zip"/>
                            <input id="initial_status" name="status" type="text" class="hidden" value="in_queue"/>
                        </label>
                    </div>

                    <div class="flex items-center justify-center mb-4">
                        <button class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded" type="submit">
                            Kirim Pesanan
                        </button>
                    </div>
            </form>

            <p class="text-center text-sm text-gray-500">&copy; 2025 CV Grandis Nusantara</p>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectEl = document.getElementById('service');
            const extraInputDiv = document.getElementById('extra_service'); // Nama div kontainer

            selectEl.addEventListener('change', function() {
                if (this.value !== "") {
                    extraInputDiv.classList.remove('hidden');
                } else {
                    extraInputDiv.classList.add('hidden');
                }
            });

            // JavaScript untuk menampilkan nama file pada dropzone
            const fileInput = document.getElementById('image_ref_input');
            const fileNameDisplay = document.getElementById('fileNameDisplay');

            if (fileInput && fileNameDisplay) { // Pastikan elemen ada
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        if (this.files.length === 1) {
                            fileNameDisplay.textContent = this.files[0].name;
                        } else {
                            fileNameDisplay.textContent = this.files.length + ' file dipilih';
                        }
                    } else {
                        fileNameDisplay.textContent = ''; // Kosongkan jika tidak ada file dipilih
                    }
                });
            }
        });
    </script>
@endpush
