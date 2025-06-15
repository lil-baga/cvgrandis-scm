@extends('layout.master')

@section('title', 'Pesanan Sukses')
@section('content')
    <div class="h-full w-full bg-white rounded-xl shadow-lg pt-64 p-8 text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4">✅ Pesanan Berhasil Dikirim</h1>
        <p class="text-gray-700 mb-6">Terima kasih! Pesanan Anda telah kami terima dan akan segera diproses oleh tim kami.
        </p>

        <a href="https://wa.me/6285236710100?text=Halo%20Grandis%20Nusantara%2C%20saya%20ingin%20menanyakan%20update%20pesanan%20saya."
            target="_blank"
            class="inline-block bg-green-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-green-500 transition mb-4">
            Hubungi via WhatsApp
        </a>

        <p class="text-sm text-gray-600">Untuk update status pesanan, silakan hubungi nomor WhatsApp kami di atas.</p>
        <div class="mt-6">
            <a href="{{ route('landing') }}" class="text-blue-700 hover:underline text-sm">← Kembali ke Beranda</a>
        </div>
    </div>
@endsection
