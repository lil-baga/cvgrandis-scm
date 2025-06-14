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

        <!-- Layanan -->
        <section class="py-14 px-6 bg-white">
            <h2 class="text-3xl font-semibold text-center text-blue-900 mb-10">Layanan Kami</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 max-w-6xl mx-auto">

                <!-- Card Template -->
                <div class="card bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
                    <img src="https://i.pinimg.com/736x/f0/e4/79/f0e47953720d08f7e96db915631e9d89.jpg" alt="Advertising"
                        class="layanan-img">
                    <h3 class="font-semibold text-lg mt-4 text-blue-800">Advertising</h3>
                    <p class="text-sm text-gray-600 mt-1">Produksi neon box, banner backlite, dan cutting stiker.</p>
                </div>

                <div class="card bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
                    <img src="https://i.pinimg.com/736x/ab/43/8a/ab438a2f529892176c8a7769efa28c5c.jpg" alt="Event Property"
                        class="layanan-img">
                    <h3 class="font-semibold text-lg mt-4 text-blue-800">Event Property</h3>
                    <p class="text-sm text-gray-600 mt-1">Gate, backdrop, meja, kursi, dan perlengkapan event lainnya.</p>
                </div>

                <div class="card bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
                    <img src="https://i.pinimg.com/736x/41/02/ee/4102eea8b523310cb18aed13608b342f.jpg" alt="Interior"
                        class="layanan-img">
                    <h3 class="font-semibold text-lg mt-4 text-blue-800">Interior Design</h3>
                    <p class="text-sm text-gray-600 mt-1">Desain dan produksi interior dengan HPL dan ACP.</p>
                </div>

                <div class="card bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
                    <img src="https://i.pinimg.com/736x/9f/b5/56/9fb556bd2b8ea1a892076970778760a5.jpg" alt="Letter Akrilik"
                        class="layanan-img">
                    <h3 class="font-semibold text-lg mt-4 text-blue-800">Letter Akrilik</h3>
                    <p class="text-sm text-gray-600 mt-1">Tulisan menyala atau tidak, dari akrilik dan stainless.</p>
                </div>

                <div class="card bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
                    <img src="https://i.pinimg.com/736x/ad/9b/67/ad9b6767729f8ea9bdedacb829b64dda.jpg" alt="Event Organizer"
                        class="layanan-img">
                    <h3 class="font-semibold text-lg mt-4 text-blue-800">Event Organizer & R&D</h3>
                    <p class="text-sm text-gray-600 mt-1">Pelaksanaan event dan penelitian lokal hingga nasional.</p>
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
