<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $activityFilter = $request->query('activity_status', 'active'); // Default 'active'

        if ($user->hasRole('Administrator')) {
            // Logika untuk Administrator (sudah ada): menampilkan semua stok
            $search = $request->query('search');
            $query = Stock::query();
            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            if ($activityFilter == 'inactive') {
                $query->where('is_active', false);
            } else {
                $query->where('is_active', true);
            }

            $stocks = $query->orderBy('name', 'asc')->paginate(15);
            $totalItems = Stock::count();
            // ... (statistik lainnya)
            return view('dashboard.stock', compact('stocks', 'totalItems', /*...*/));
        } elseif ($user->hasRole('Supplier')) {
            // --- LOGIKA BARU UNTUK SUPPLIER ---

            // Query dasar untuk pesanan stok yang pending
            $pendingOrdersQuery = StockOrder::where('status', 'pending');

            // Hitung statistik berdasarkan query dasar
            $totalPendingOrders = $pendingOrdersQuery->count();
            $totalUniqueItemsRequested = $pendingOrdersQuery->distinct('stock_id')->count('stock_id');
            $totalQuantityRequested = $pendingOrdersQuery->sum('quantity_requested');

            // Ambil data untuk tabel dengan paginasi
            $pendingStockOrders = $pendingOrdersQuery->with(['stock', 'requester'])
                ->orderBy('created_at', 'asc')
                ->paginate(15);

            // Kirim data pesanan dan statistik ke view
            return view('dashboard.stock_order', compact(
                'pendingStockOrders',
                'totalPendingOrders',
                'totalUniqueItemsRequested',
                'totalQuantityRequested'
            ));
        }

        // Fallback
        return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses.');
    }

    public function orderStock(Request $request, Stock $stock)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        StockOrder::create([
            'stock_id' => $stock->id,
            'user_id' => Auth::id(),
            'quantity_requested' => $request->input('quantity'),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pesanan stok untuk ' . $stock->name . ' berhasil dibuat.');
    }
    public function toggle(Stock $stock)
    {
        try {
            // Balikkan nilai boolean is_active
            $stock->is_active = !$stock->is_active;
            $stock->save();

            $message = $stock->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Stok barang '{$stock->name}' berhasil {$message}.");
        } catch (\Exception $e) {
            Log::error("Gagal mengubah status stok #{$stock->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status stok.');
        }
    }

    public function fulfillOrder(Request $request, StockOrder $stockOrder)
    {
        if ($stockOrder->status !== 'pending') {
            return redirect()->back()->with('error', 'Pesanan stok ini sudah diproses sebelumnya.');
        }

        // Gunakan transaksi untuk menjaga konsistensi data
        DB::transaction(function () use ($stockOrder) {
            // 1. Update stok utama
            $stockItem = $stockOrder->stock; // Mengambil item stok terkait
            $stockItem->increment('stock', $stockOrder->quantity_requested);

            // 2. Update status stok utama jika perlu (misal dari 'out_of_stock')
            if ($stockItem->stock > $stockItem->low_stock) {
                $stockItem->status = 'in_stock';
            } elseif ($stockItem->stock > 0) {
                $stockItem->status = 'low_stock';
            }
            $stockItem->save();

            // 3. Update status pesanan stok
            $stockOrder->update([
                'status' => 'fulfilled',
                'fulfilled_by' => Auth::id(),
                'fulfilled_at' => now(),
            ]);
        });

        return redirect()->route('stock')->with('success', 'Pesanan stok berhasil dikonfirmasi dan stok telah ditambahkan.');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:stocks,name',
            'type' => ['required', Rule::in(['material', 'electricity', 'tools'])],
            'stock' => 'required|integer|min:0',
            'low_stock' => 'required|integer|min:0',
        ]);

        if ($validatedData['stock'] <= 0) {
            $validatedData['status'] = 'out_of_stock';
            $validatedData['stock'] = 0;
        } elseif ($validatedData['stock'] <= $validatedData['low_stock']) {
            $validatedData['status'] = 'low_stock';
        } else {
            $validatedData['status'] = 'in_stock';
        }

        try {
            Stock::create($validatedData);
            return redirect()->route('stock')->with('success', 'Barang stok berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal tambah stok: ' . $e->getMessage());
            return redirect()->route('stock')->with('error', 'Gagal menambahkan barang stok. Error: ' . $e->getMessage());
        }
    }

    public function show(Stock $stock)
    {
        return view('dashboard.stock_detail', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $rules = [];
        $dataToUpdate = [];

        if ($user->hasRole('Administrator')) {
            $rules = [
                'name'      => ['required', 'string', 'max:255', Rule::unique('stocks')->ignore($stock->id)],
                'type'      => ['required', Rule::in(['material', 'electricity', 'tools'])],
                'stock'     => 'required|integer|min:0',
                'low_stock' => 'required|integer|min:0',
            ];
            $validatedData = $request->validate($rules);
            $dataToUpdate = $validatedData;
        } elseif ($user->hasRole('Supplier')) {
            $rules = [
                'stock' => ['required', 'integer', 'min:0'],
            ];
            $validatedData = $request->validate($rules);

            // Hitung total stok baru
            $quantityToAdd = $validatedData['stock'];
            $newTotalStock = $stock->stock + $quantityToAdd;

            $dataToUpdate['stock'] = $newTotalStock;
        } else {
            return redirect()->route('stock.show', $stock->id)->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        // Tentukan status baru berdasarkan stok yang akan diupdate
        $currentStockForStatus = $dataToUpdate['stock']; // Ini sudah total stok baru
        $lowStockThresholdToCompare = ($user->hasRole('Administrator') && isset($dataToUpdate['low_stock']))
            ? $dataToUpdate['low_stock']
            : $stock->low_stock; // Untuk supplier, low_stock tidak berubah

        if ($currentStockForStatus <= 0) {
            $dataToUpdate['status'] = 'out_of_stock';
            $dataToUpdate['stock'] = 0; // Pastikan tidak negatif
        } elseif ($currentStockForStatus <= $lowStockThresholdToCompare) {
            $dataToUpdate['status'] = 'low_stock';
        } else {
            $dataToUpdate['status'] = 'in_stock';
        }

        try {
            $stock->update($dataToUpdate); // Hanya field di $dataToUpdate yang akan diupdate
            return redirect()->route('stock.show', $stock->id)->with('success', 'Stok barang berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Gagal update stok #{$stock->id}: " . $e->getMessage());

            $validator = Validator::make([], []); // Dummy validator jika $request->validator null
            if (isset($request->validator) && $request->validator->fails()) {
                $validator = $request->validator;
            }

            return redirect()->route('stock.show', $stock->id)
                ->withErrors($validator->errors()->isEmpty() ? ['update_error' => 'Gagal memperbarui stok barang: ' . $e->getMessage()] : $validator)
                ->withInput()
                ->with('open_edit_modal_on_error', true);
        }
    }

    public function destroy(Stock $stock)
    {
        // Pastikan hanya Administrator
        if (!Auth::user()->hasRole('Administrator')) {
            return redirect()->route('stock')->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        // Cek apakah stok pernah digunakan. Jika ya, paksa nonaktifkan, jangan hapus.
        if ($stock->stockDeductions()->exists()) {
            return redirect()->route('stock.show', $stock->id)
                ->with('error', 'Stok ini tidak bisa dihapus permanen karena memiliki riwayat penggunaan. Harap gunakan tombol "Non-aktifkan".');
        }

        // Jika tidak pernah digunakan, baru boleh hapus permanen
        try {
            $stock->delete();
            return redirect()->route('stock')->with('success', 'Stok barang berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            Log::error("Gagal hapus permanen stok #{$stock->id}: " . $e->getMessage());
            return redirect()->route('stock')->with('error', 'Gagal menghapus stok barang.');
        }
    }
}
