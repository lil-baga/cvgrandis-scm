<?php

namespace App\Http\Controllers; // Atau App\Http\Controllers\Admin; sesuai struktur Anda

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // PENTING: Import Auth
use Illuminate\Support\Facades\Log;   // Opsional untuk logging
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $query = Stock::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $stocks = $query->orderBy('name', 'asc')->paginate(15);

        $totalItems = Stock::count();
        $lowStockItems = Stock::where('status', 'low_stock')->count();
        $outOfStockItems = Stock::where('status', 'out_of_stock')->count();

        // Pastikan view path sudah benar, misal 'admin.stocks.index' jika mengikuti konvensi sebelumnya
        return view('dashboard.stock', compact('stocks', 'totalItems', 'lowStockItems', 'outOfStockItems', 'user'));
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
            // Pastikan nama route 'dashboard' adalah route ke daftar stok
            return redirect()->route('stock')->with('success', 'Barang stok berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal tambah stok: ' . $e->getMessage());
            return redirect()->route('stock')->with('error', 'Gagal menambahkan barang stok. Error: ' . $e->getMessage());
        }
    }

    public function show(Stock $stock)
    {
        // Pastikan view path sudah benar, misal 'admin.stocks.show'
        return view('dashboard.stock_detail', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $rules = [];
        $dataToUpdate = []; // Data yang akan di-pass ke $stock->update()

        if ($user->hasRole('Administrator')) {
            $rules = [
                'name'      => ['required', 'string', 'max:255', Rule::unique('stocks')->ignore($stock->id)],
                'type'      => ['required', Rule::in(['material', 'electricity', 'tools'])],
                'stock'     => 'required|integer|min:0', // Untuk Admin, ini adalah total stok baru
                'low_stock' => 'required|integer|min:0',
            ];
            $validatedData = $request->validate($rules);
            $dataToUpdate = $validatedData; // Admin update semua field yang divalidasi

        } elseif ($user->hasRole('Supplier')) {
            $rules = [
                // Untuk Supplier, input 'stock' dari form adalah 'jumlah yang ditambahkan'
                'stock' => ['required', 'integer', 'min:0'],
            ];
            $validatedData = $request->validate($rules);

            // Hitung total stok baru
            $quantityToAdd = $validatedData['stock'];
            $newTotalStock = $stock->stock + $quantityToAdd;

            $dataToUpdate['stock'] = $newTotalStock;
            // Field lain (name, type, low_stock) tidak diubah oleh Supplier dari form ini

        } else {
            // Pastikan nama route 'admin.stocks.show' atau 'dashboard.stock.show' sesuai
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

            // Dapatkan validator instance dari request jika validasi gagal sebelum try-catch
            // Jika error terjadi di DB, $request->validator mungkin null.
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
        // Tambahkan pengecekan role di sini juga
        if (!Auth::user()->hasRole('Administrator')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk menghapus data.');
        }

        try {
            $stock->delete();
            return redirect()->route('dashboard')->with('success', 'Stok barang berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Gagal hapus stok #{$stock->id}: " . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal menghapus stok barang. Error: ' . $e->getMessage());
        }
    }
}
