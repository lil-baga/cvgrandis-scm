@extends('layout.master')

@section('title', 'Landing')
@section('content')

    <body class="bg-white text-gray-800 font-sans">
        <!-- Hero Section -->
        <header class="bg-blue-900 text-white py-12 shadow-md">
            <div class="max-w-6xl mx-auto px-4 text-center">
                <h1 class="text-4xl font-bold mb-2">CV Grandis Nusantara</h1>
                <p class="text-lg">Advertising • Event Organizer • Interior • Research & Development</p>
            </div>
        </header>

        <!-- About Us -->
        <section class="py-12 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-semibold mb-4">Tentang Kami</h2>
                <p class="text-gray-600">CV Grandis Nusantara adalah perusahaan penyedia jasa Advertising, Event Organizer,
                    Interior Design, dan Research & Development yang berdiri sejak 13 Oktober 2022 di Jember. Kami
                    berkomitmen pada profesionalisme, efisiensi, dan tanggung jawab untuk memberikan layanan terbaik kepada
                    klien kami.</p>
            </div>
        </section>

        <!-- Services -->
        <section class="py-12">
            <div class="max-w-6xl mx-auto px-4`">
                <h2 class="text-2xl font-semibold text-center mb-8">Layanan Kami</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div class="bg-white shadow-md rounded p-6 text-center">
                        <h3 class="text-lg font-bold mb-2">Advertising</h3>
                        <svg class="block mx-auto my-4" xmlns="http://www.w3.org/2000/svg" height="64"
                            viewBox="0 0 24 24" fill="#1C398E">
                            <path fill="#1C398E"
                                d="M12 15c-1.84 0-2-.86-2-1H8c0 .92.66 2.55 3 2.92V18h2v-1.08c2-.34 3-1.63 3-2.92c0-1.12-.52-3-4-3c-2 0-2-.63-2-1s.7-1 2-1s1.39.64 1.4 1h2A3 3 0 0 0 13 7.12V6h-2v1.09C9 7.42 8 8.71 8 10c0 1.12.52 3 4 3c2 0 2 .68 2 1s-.62 1-2 1z" />
                            <path fill="#1C398E" d="M5 2H2v2h2v17a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4h2V2H5zm13 18H6V4h12z" />
                        </svg>
                        <p class="text-sm text-gray-600">Produksi neon box, banner, dan cutting stiker untuk memperkuat
                            identitas brand Anda.</p>
                    </div>
                    <div class="bg-white shadow-md rounded p-6 text-center">
                        <h3 class="text-lg font-bold mb-2">Event Property</h3>
                        <svg class="block mx-auto my-4" xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                            viewBox="0 0 24 24" fill="#1C398E">
                            <path fill="#1C398E"
                                d="m21.706 5.291l-2.999-2.998A.996.996 0 0 0 18 2H6a.996.996 0 0 0-.707.293L2.294 5.291A.994.994 0 0 0 2 5.999V19c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5.999a.994.994 0 0 0-.294-.708zM6.414 4h11.172l.999.999H5.415L6.414 4zM4 19V6.999h16L20.002 19H4z" />
                            <path fill="#1C398E" d="M15 12H9v-2H7v4h10v-4h-2z" />
                        </svg>
                        <p class="text-sm text-gray-600">Pembuatan kebutuhan event seperti gate, backdrop, meja, kursi, dan
                            lain-lain.</p>
                    </div>
                    <div class="bg-white shadow-md rounded p-6 text-center">
                        <h3 class="text-lg font-bold mb-2">Design Interior</h3>
                        <svg class="block mx-auto my-4" xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                            viewBox="0 0 24 24" fill="#1C398E">
                            <path fill="#1C398E"
                                d="M18 2H7c-1.103 0-2 .897-2 2v3c0 1.103.897 2 2 2h11c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zM7 7V4h11l.002 3H7z" />
                            <path fill="#1C398E"
                                d="M13 15v-2c0-1.103-.897-2-2-2H4V5c-1.103 0-2 .897-2 2v4c0 1.103.897 2 2 2h7v2a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1z" />
                        </svg>
                        <p class="text-sm text-gray-600">Desain interior profesional dengan material HPL dan ACP untuk ruang
                            Anda.</p>
                    </div>
                    <div class="bg-white shadow-md rounded p-6 text-center">
                        <h3 class="text-lg font-bold mb-2">Letter Akrilik & Stainless</h3>
                        <svg class="block mx-auto my-4" xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                            viewBox="0 0 24 24" fill="#1C398E">
                            <path fill="#1C398E"
                                d="M6 10v4c0 1.103.897 2 2 2h3v-2H8v-4h3V8H8c-1.103 0-2 .897-2 2zm7 0v4c0 1.103.897 2 2 2h3v-2h-3v-4h3V8h-3c-1.103 0-2 .897-2 2z" />
                            <path fill="#1C398E"
                                d="M20 4H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zM4 18V6h16l.002 12H4z" />
                        </svg>
                        <p class="text-sm text-gray-600">Pembuatan logo atau signage dari bahan akrilik & stainless sesuai
                            kebutuhan Anda.</p>
                    </div>
                    <div class="bg-white shadow-md rounded p-6 text-center">
                        <h3 class="text-lg font-bold mb-2">Event Organizer & RnD</h3>
                        <svg class="block mx-auto my-4" xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                            viewBox="0 0 24 24" fill="#1C398E">
                            <path fill="#1C398E"
                                d="M9 20h6v2H9zm7.906-6.288C17.936 12.506 19 11.259 19 9c0-3.859-3.141-7-7-7S5 5.141 5 9c0 2.285 1.067 3.528 2.101 4.73c.358.418.729.851 1.084 1.349c.144.206.38.996.591 1.921H8v2h8v-2h-.774c.213-.927.45-1.719.593-1.925c.352-.503.726-.94 1.087-1.363zm-2.724.213c-.434.617-.796 2.075-1.006 3.075h-2.351c-.209-1.002-.572-2.463-1.011-3.08a20.502 20.502 0 0 0-1.196-1.492C7.644 11.294 7 10.544 7 9c0-2.757 2.243-5 5-5s5 2.243 5 5c0 1.521-.643 2.274-1.615 3.413c-.373.438-.796.933-1.203 1.512z" />
                        </svg>
                        <p class="text-sm text-gray-600">Penyelenggaraan event dan penelitian untuk menunjang kegiatan
                            perusahaan.</p>
                    </div>
                </div>
            </div>
        </section>
        @guest
        <section class="py-12 bg-blue-50">
            <div class="max-w-3xl mx-auto text-center px-4">
                <h2 class="text-xl font-semibold mb-4">Siap memulai proyek dengan kami?</h2>
                <a href="/order-form" class="bg-blue-900 text-white px-6 py-3 rounded hover:bg-blue-800">Ajukan
                    Pesanan Sekarang</a>
            </div>
        </section>
        @else
        @endguest
    </body>
@endsection
