@extends('layout.master')
@section('title', 'Stock Order List')
@section('content')
<div class="pt-20 p-4 sm:ml-0">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <h3 class="text-lg font-semibold text-gray-700">Pesanan Stok Masuk</h3>
            <p class="text-3xl font-bold text-blue-500">{{ $totalPendingOrders ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <h3 class="text-lg font-semibold text-gray-700">Jenis Barang Dipesan</h3>
            <p class="text-3xl font-bold text-indigo-500">{{ $totalUniqueItemsRequested ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <h3 class="text-lg font-semibold text-gray-700">Total Kuantitas Dipesan</h3>
            <p class="text-3xl font-bold text-teal-500">{{ $totalQuantityRequested ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <h2 class="text-2xl font-semibold text-gray-800 p-6">Daftar Pesanan Stok (Menunggu Konfirmasi)</h2>
        @if(session('success'))
            <div class="m-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4"><p>{{ session('success') }}</p></div>
        @endif
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Tanggal Pesan</th>
                    <th scope="col" class="px-6 py-3">Nama Barang</th>
                    <th scope="col" class="px-6 py-3">Dipesan Oleh</th>
                    <th scope="col" class="px-6 py-3 text-center">Jumlah Dipesan</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingStockOrders as $order)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="px-6 py-4">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 font-medium">{{ $order->stock->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $order->requester->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center font-bold">{{ $order->quantity_requested }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('stock.fulfill', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg px-4 py-2 text-xs">
                                    Konfirmasi Pemenuhan
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center p-4">Tidak ada pesanan stok yang menunggu.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($pendingStockOrders->hasPages())
            <div class="p-4">{{ $pendingStockOrders->links() }}</div>
        @endif
    </div>
</div>
@endsection