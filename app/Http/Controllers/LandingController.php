<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Stock;
use App\Models\OrderStockDeduction;
use App\Models\ServiceRecipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        return view('landing.index', compact('user'));
    }

    public function form()
    {
        return view('landing.form');
    }

        public function interval()
    {
        return view('landing.interval');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:orders,email',
            'phone_number' => 'required|string|max:20',
            'service' => 'required|string|in:neon,backdrop,interior,letter,event',
            'description' => 'nullable|string',
            'image_ref' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:10240',
            'width' => 'nullable|required_if:service,neon,backdrop,interior|numeric|min:0.01',
            'height' => 'nullable|required_if:service,neon,backdrop,interior|numeric|min:0.01',
            'quantity' => 'nullable|required_if:service,lettering,event|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $orderData = $request->only(['name', 'email', 'phone_number', 'service', 'description', 'status']);

            // --- PERBAIKAN LOGIKA PENYIMPANAN FILE ---
            if ($request->hasFile('image_ref')) {
                $file = $request->file('image_ref');
                if (is_array($file)) {
                    $file = $file[0] ?? null;
                }

                if ($file && $file->isValid()) {
                    // Buat nama file yang unik
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileNameToStore = Str::slug($originalName) . '_' . time() . '.' . $extension;
                    $fileName = $file->getClientOriginalName();
                    $categoryFolder = Str::slug($request->service);
                    $fileLocation = 'order_attachments/' . $categoryFolder . '/';
                    $path = $fileLocation . $fileName;
                    // dd($path);
                    $file->move(public_path($fileLocation), $fileName);
                    // Simpan file ke disk 'public' di dalam folder 'order_attachments'
                    // $path = $file->storeAs('order_attachments', $fileNameToStore, 'public');
                    
                    // // Alih-alih menyimpan data biner, kita simpan path-nya
                    // $orderData['image_ref'] = $path; // Simpan path ke kolom 'image_ref'
                    // $orderData['original_filename'] = $file->getClientOriginalName();
                    // $orderData['mime_type'] = $file->getClientMimeType();
                }
                $order = Order::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'service' => $request->service,
                    'description' => $request->description,
                    'image_ref' => $request->image_ref,
                    'width' => $request->width,
                    'height' => $request->height,
                    'quantity' => $request->quantity,
                    'original_filename' => $originalName,
                    'mime_type' => $file->getClientMimeType(),
                    'path' => $path,
                ]);
            }
            // Jika tidak ada file, 'image_ref' tidak akan ada di $orderData dan akan menjadi null di DB

            // Buat record order menggunakan mass assignment

            // Logika deduksi stok otomatis (kode ini sudah benar dari sebelumnya)
            $recipes = ServiceRecipe::where('service_code', $order->service)->get();

            if ($recipes->isEmpty()) {
                Log::warning("Tidak ada resep SCM untuk layanan '{$order->service}' pada Order ID: {$order->id}.");
                DB::commit();
                return redirect()->route('landing')->with('success', 'Pemesanan Anda berhasil dikirim. Tim kami akan segera menghitung kebutuhan bahan.');
            }

            $width = (float)$request->input('width', 0);
            $height = (float)$request->input('height', 0);
            $quantity = (int)$request->input('quantity', 1);
            $luas = $width * $height;
            $keliling = 2 * ($width + $height);

            foreach ($recipes as $recipe) {
                $stockToDeduct = Stock::where('id', $recipe->stock_id)->lockForUpdate()->first();
                if (!$stockToDeduct) continue;

                $quantityToDeduct = 0;
                switch ($recipe->unit_of_measure) {
                    case 'per_sq_meter':
                        $quantityToDeduct = $recipe->quantity_per_unit * $luas;
                        break;
                    case 'per_meter_perimeter':
                        $quantityToDeduct = $recipe->quantity_per_unit * $keliling;
                        break;
                    case 'per_unit':
                        $quantityToDeduct = $recipe->quantity_per_unit * $quantity;
                        break;
                }

                $quantityToDeduct = ceil($quantityToDeduct);

                if ($quantityToDeduct > 0) {
                    if ($stockToDeduct->stock < $quantityToDeduct) {
                        throw new \Exception("Stok tidak cukup untuk '{$stockToDeduct->name}'. Kebutuhan: {$quantityToDeduct}, Tersedia: {$stockToDeduct->stock}.");
                    }
                    $stockToDeduct->decrement('stock', $quantityToDeduct);

                    if ($stockToDeduct->stock <= 0) $stockToDeduct->status = 'out_of_stock';
                    elseif ($stockToDeduct->stock <= $stockToDeduct->low_stock) $stockToDeduct->status = 'low_stock';
                    else $stockToDeduct->status = 'in_stock';
                    $stockToDeduct->save();

                    OrderStockDeduction::create([
                        'order_id' => $order->id,
                        'stock_id' => $stockToDeduct->id,
                        'quantity_deducted' => $quantityToDeduct
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('interval')->with('success', 'Pemesanan Anda berhasil dikirim dan stok telah dialokasikan secara otomatis.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan order: ' . $e->getMessage() . ' ---- TRACE: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())->withInput();
        }
    }
}
