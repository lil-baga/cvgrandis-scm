@extends('layout.master')

@section('title', 'Order List')
@section('content')
    <div class="pt-20 p-4 sm:ml-0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-base font-semibold text-gray-700">Total Semua Pesanan</h3>
                <p class="text-3xl font-bold text-gray-700">{{ $totalOrders ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-base font-semibold text-gray-700">Dalam Antrian</h3>
                <p class="text-3xl font-bold text-yellow-500">{{ $totalInQueue ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-base font-semibold text-gray-700">Sedang Dikerjakan</h3>
                <p class="text-3xl font-bold text-blue-500">{{ $totalOnGoing ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <h3 class="text-base font-semibold text-gray-700">Selesai</h3>
                <p class="text-3xl font-bold text-green-500">{{ $totalFinished ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="mb-4 px-4 sm:ml-0">
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <div class="p-6 flex flex-col md:flex-row justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 md:mb-0">Customer Orders</h2>
                <form action="{{ route('order.list') }}" method="GET" class="flex items-center space-x-2">
                    <label for="status_filter" class="text-sm font-medium text-gray-700">Filter by Status:</label>
                    <select name="status" id="status_filter"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2">
                        <option value="">All Statuses</option>
                        <option value="in_queue" {{ request()->get('status') == 'in_queue' ? 'selected' : '' }}>
                            Dalam Antrian (In Queue)
                        </option>
                        <option value="on_going" {{ request()->get('status') == 'on_going' ? 'selected' : '' }}>
                            Sedang Dikerjakan (On Going)
                        </option>
                        <option value="finished" {{ request()->get('status') == 'finished' ? 'selected' : '' }}>
                            Selesai (Finished)
                        </option>
                    </select>
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">
                        Filter
                    </button>
                    @if (request()->get('status'))
                        <a href="{{ route('order.list') }}"
                            class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                            Clear Filter
                        </a>
                    @endif
                </form>
            </div>

            <table class="w-full text-sm text-left text-gray-700 table">
                <thead class="text-xs text-center text-gray-900 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No.</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pesan</th>
                        <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Nomor Telepon</th>
                        <th scope="col" class="px-6 py-3">Layanan</th>
                        <th scope="col" class="px-6 py-3">Deskripsi</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @forelse ($orders as $order)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 text-center text-gray-900">
                                {{ $order->orderDate ? $order->orderDate->format('d M Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $order->customerName }}</td>
                            <td class="px-6 py-4">{{ $order->customerEmail }}</td>
                            <td class="px-6 py-4">{{ $order->customerPhone }}</td>
                            <td class="px-6 py-4 text-center text-gray-900">
                                {{ $order->service_display_name ?? Str::title(str_replace('_', ' ', $order->service)) }}
                            </td>
                            <td class="px-6 py-4">{{ Str::limit($order->itemDescription, 50) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="status-badge {{ $order->status == 'in_queue'
                                        ? 'status-in_queue'
                                        : ($order->status == 'on_going'
                                            ? 'status-on_going'
                                            : ($order->status == 'finished'
                                                ? 'status-finished'
                                                : 'bg-gray-200 text-gray-800')) }}">
                                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('order.show', $order->orderId) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-4">
                                @if (request()->get('status'))
                                    No orders found matching the status
                                    "{{ str_replace('_', ' ', ucfirst(request()->get('status'))) }}".
                                @else
                                    No orders found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-4">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
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
    </style>
@endsection
