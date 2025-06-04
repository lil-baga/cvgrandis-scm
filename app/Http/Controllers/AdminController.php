<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStockDeduction;
use App\Models\Stock;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    //
    public function order(Request $request)
    {
        $statusFilter = $request->query('status');

        $query = Order::query();
        $totalOrders = Order::count();
        $totalInQueue = Order::where('status', 'in_queue')->count();
        $totalOnGoing = Order::where('status', 'on_going')->count();
        $totalFinished = Order::where('status', 'finished')->count();

        if ($statusFilter && in_array($statusFilter, ['in_queue', 'on_going', 'finished'])) {
            $query->where('status', $statusFilter);
        }

        $ordersFromDB = $query->orderBy('created_at', 'desc')->paginate(15);

        $processedOrders = $ordersFromDB->getCollection()->map(function ($order) {
            $fileAttachment = null;
            if ($order->image_ref) {
                $fileName = $order->original_filename ?? "attachment_order_{$order->id}.dat";
                $fileType = $order->mime_type ?? $this->getMimeTypeFromBinary($order->image_ref);
                $fileAttachment = [
                    'name' => $fileName,
                    'type' => $fileType,
                    'url' => route('order.attachments', $order->id)
                ];
            }
            return (object) [
                'orderId' => $order->id,
                'orderDate' => $order->created_at,
                'customerName' => $order->name,
                'customerEmail' => $order->email,
                'customerPhone' => $order->phone_number,
                'service' => $order->service,
                'service_display_name' => $order->service_display_name,
                'itemDescription' => $order->description,
                'status' => $order->status,
                'fileAttachment' => $fileAttachment ? (object)$fileAttachment : null,
            ];
        });

        $paginatedProcessedOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedOrders,
            $ordersFromDB->total(),
            $ordersFromDB->perPage(),
            $ordersFromDB->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );


        return view('dashboard.order', [
            'orders' => $paginatedProcessedOrders,
            'totalOrders' => $totalOrders,
            'totalInQueue' => $totalInQueue,
            'totalOnGoing' => $totalOnGoing,
            'totalFinished' => $totalFinished,
        ]);
    }

    public function show(Order $order)
    {
        $fileAttachmentDetails = null;
        if ($order->image_ref) {
            $fileName = $order->original_filename ?? "attachment_order_{$order->id}.dat";
            $fileType = $order->mime_type ?? $this->getMimeTypeFromBinary($order->image_ref);
            $fileAttachmentDetails = (object) [
                'name' => $fileName,
                'type' => $fileType,
                'url' => route('order.attachments', $order->id)
            ];
        }

        $stockItems = Stock::orderBy('name', 'asc')->get();

        return view('dashboard.order_detail', [
            'order' => $order,
            'fileAttachmentDetails' => $fileAttachmentDetails,
            'stockItems' => $stockItems
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'status' => [
                'required',
                Rule::in(['in_queue', 'on_going', 'finished']),
            ],
        ]);

        try {
            $order->status = $validatedData['status'];
            $order->save();

            return redirect()->route('order.show', $order->id)
                ->with('success', 'Status pesanan berhasil diperbarui menjadi "' . str_replace('_', ' ', ucfirst($order->status)) . '".');
        } catch (\Exception $e) {
            return redirect()->route('order.show', $order->id)
                ->with('error', 'Gagal memperbarui status pesanan. Silakan coba lagi.');
        }
    }

    public function adjustStockForOrder(Request $request, Order $order)
    {
        $adjustments = $request->input('adjustments', []);
        $adjustedItemsCount = 0;
        $processedStockIds = []; // Untuk melacak stok yang sudah diproses dalam batch ini

        if (!is_array($adjustments) || empty($adjustments)) {
            return redirect()->route('order.show', $order->id)->with('error', 'Tidak ada data penyesuaian stok yang dikirim.');
        }

        DB::beginTransaction();
        try {
            foreach ($adjustments as $stockId => $quantityToDeduct) {
                $quantityToDeduct = (int) $quantityToDeduct;

                if ($quantityToDeduct > 0) {
                    // Menggunakan lockForUpdate untuk mencegah race condition jika ada banyak request bersamaan
                    $stockItem = Stock::where('id', $stockId)->lockForUpdate()->first();

                    if ($stockItem) {
                        $originalStockLevel = $stockItem->stock; // Simpan level stok asli sebelum dikurangi

                        if ($stockItem->stock < $quantityToDeduct) {
                            Log::warning("Stok tidak cukup untuk {$stockItem->name} (ID: {$stockId}) pada Order #{$order->id}. Diminta: {$quantityToDeduct}, Tersedia: {$stockItem->stock}. Stok diatur jadi 0.");
                            $actualDeducted = $stockItem->stock; // Hanya bisa mengurangi sebanyak stok yang ada
                            $stockItem->stock = 0;
                        } else {
                            $actualDeducted = $quantityToDeduct;
                            $stockItem->stock -= $quantityToDeduct;
                        }

                        // Update status stok item
                        if ($stockItem->stock <= 0) {
                            $stockItem->status = 'out_of_stock';
                            $stockItem->stock = 0; // Pastikan tidak negatif
                        } elseif ($stockItem->stock <= $stockItem->low_stock) {
                            $stockItem->status = 'low_stock';
                        } else {
                            $stockItem->status = 'in_stock';
                        }
                        $stockItem->save();

                        // CATAT PENGURANGAN STOK KE TABEL order_stock_deductions
                        // Cek apakah item ini sudah dicatat pengurangannya untuk order ini sebelumnya
                        // Jika ya, mungkin Anda ingin mengupdate jumlahnya atau membuat record baru (tergantung logika bisnis)
                        // Untuk contoh ini, kita buat record baru setiap kali ada penyesuaian.
                        // Atau jika ingin memastikan unik per order per stock, gunakan updateOrCreate.
                        OrderStockDeduction::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'stock_id' => $stockItem->id,
                            ],
                            [
                                // Jika ditemukan, tambahkan quantity_deducted, jika tidak buat baru.
                                // Ini akan mengakumulasi jika item yang sama di-adjust berkali-kali untuk order yang sama.
                                'quantity_deducted' => DB::raw("quantity_deducted + {$actualDeducted}")
                            ]
                        );
                        // Jika Anda ingin setiap penyesuaian adalah record baru (bukan akumulasi):
                        // OrderStockDeduction::create([
                        //     'order_id' => $order->id,
                        //     'stock_id' => $stockItem->id,
                        //     'quantity_deducted' => $actualDeducted, // Gunakan jumlah yang benar-benar dikurangi
                        // ]);

                        $adjustedItemsCount++;
                        $processedStockIds[] = $stockId;
                    } else {
                        Log::warning("Item Stok dengan ID: {$stockId} tidak ditemukan saat penyesuaian untuk Order #{$order->id}.");
                    }
                }
            }
            DB::commit();

            if ($adjustedItemsCount > 0) {
                return redirect()->route('order.show', $order->id)->with('success', "{$adjustedItemsCount} jenis item stok berhasil disesuaikan untuk pesanan ini.");
            } else {
                return redirect()->route('order.show', $order->id)->with('info', "Tidak ada jumlah stok yang valid untuk diubah.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal penyesuaian stok untuk Order #{$order->id}: " . $e->getMessage());
            return redirect()->route('order.show', $order->id)->with('error', 'Terjadi kesalahan saat menyesuaikan stok. Error: ' . $e->getMessage());
        }
    }

    public function serveAttachment($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->image_ref) {
            $originalFilename = $order->original_filename ?? null;
            $mimeType = $order->mime_type ?? $this->getMimeTypeFromBinary($order->image_ref);

            $downloadFilename = "attachment_order_{$order->id}";

            if ($originalFilename && pathinfo($originalFilename, PATHINFO_EXTENSION)) {
                $downloadFilename = $originalFilename;
            } else {
                $extension = $this->getExtensionFromMimeType($mimeType);
                if ($originalFilename) {
                    $downloadFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '.' . $extension;
                } else {
                    $downloadFilename .= '.' . $extension;
                }
            }

            $downloadFilename = preg_replace('/[^A-Za-z0-9_.-]/', '_', $downloadFilename);


            return Response::make($order->image_ref, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $downloadFilename . '"'
            ]);
        }

        abort(404, 'File not found.');
    }

    private function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/zip' => 'zip',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];
        return $mimeMap[strtolower($mimeType)] ?? 'dat';
    }

    private function getMimeTypeFromBinary($binaryData): string
    {
        if (empty($binaryData)) return 'application/octet-stream';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $binaryData);
            finfo_close($finfo);
            return $mimeType ?: 'application/octet-stream';
        }
        if (str_starts_with($binaryData, "\xFF\xD8\xFF")) return 'image/jpeg';
        if (str_starts_with($binaryData, "\x89PNG\r\n\x1a\n")) return 'image/png';
        if (str_starts_with($binaryData, "%PDF-")) return 'application/pdf';
        return 'application/octet-stream';
    }

    // Di dalam AdminController.php

    public function forecast(Request $request)
    {
        $stocks = Stock::orderBy('name')->get();
        $selectedStockId = $request->input('stock_id');
        // Durasi input dari user (misal 30 hari, 90 hari)
        $forecastDurationInput = (int) $request->input('duration', 30);
        // Frekuensi data yang akan diagregasi dan diforecast
        $timeFrequency = $request->input('frequency', 'M'); // Default Bulanan ('M'), bisa 'D', 'W'

        $historicalData = null;
        $forecastData = null;
        $errorForecast = null;
        $selectedStockName = null;

        // Tentukan jumlah langkah forecast berdasarkan durasi input dan frekuensi
        $actualForecastSteps = 0;
        if ($timeFrequency === 'D') {
            $actualForecastSteps = $forecastDurationInput;
        } elseif ($timeFrequency === 'W') {
            $actualForecastSteps = ceil($forecastDurationInput / 7);
        } else { // Default Bulanan ('M')
            $actualForecastSteps = ceil($forecastDurationInput / 30);
        }
        // Pastikan minimal 1 step
        if ($actualForecastSteps < 1) $actualForecastSteps = 1;


        if ($selectedStockId) {
            $selectedStock = Stock::find($selectedStockId);
            $selectedStockName = $selectedStock ? $selectedStock->name : 'Unknown Stock';

            // 1. Agregasi data historis
            $query = OrderStockDeduction::where('stock_id', $selectedStockId)
                ->join('orders', 'order_stock_deductions.order_id', '=', 'orders.id')
                ->select(DB::raw("SUM(order_stock_deductions.quantity_deducted) as value"))
                ->orderBy('date_period', 'asc')
                ->groupBy('date_period');

            if ($timeFrequency === 'D') {
                $query->addSelect(DB::raw("DATE(orders.created_at) as date_period"));
            } elseif ($timeFrequency === 'W') {
                // Menggunakan format TAHUN-MINGGUKE agar bisa di-parse pandas dan di-asfreq('W')
                $query->addSelect(DB::raw("DATE_FORMAT(orders.created_at, '%x-%v') as date_period"));
            } else { // Default Bulanan ('M')
                $query->addSelect(DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m-01') as date_period"));
            }

            $historicalDeductions = $query->get();

            // Minimal data untuk SARIMA (misal, 2 * periode musiman)
            // Ini akan dicek lebih detail di script Python juga
            $minDataPointsForSarima = 15; // Angka awal, sesuaikan
            if ($timeFrequency === 'M') $minDataPointsForSarima = 24; // Misal 2 tahun data bulanan

            if ($historicalDeductions->count() >= $minDataPointsForSarima) {
                $historicalDataForPython = $historicalDeductions->map(function ($item) {
                    // Pastikan date_period adalah string yang bisa di-parse pd.to_datetime
                    // Jika mingguan '%x-%v', Python mungkin perlu parsing khusus atau kita ubah formatnya di sini
                    // Untuk '%x-%v', pandas bisa parse dengan format='%G-%V' (ISO year and week)
                    // atau kita bisa konversi ke tanggal awal minggu di sini.
                    // Untuk simplicity, kita biarkan dan pastikan Python bisa handle.
                    return ['date_period' => (string)$item->date_period, 'value' => (int)$item->value];
                })->toJson();

                // 2. Tentukan Parameter SARIMA
                // (p,d,q) untuk non-musiman, (P,D,Q,s) untuk musiman
                // 's' adalah panjang siklus musiman (misal 7 untuk harian-mingguan, 12 untuk bulanan-tahunan, 52 untuk mingguan-tahunan)
                $sarimaOrder = '1,1,1'; // Contoh (p,d,q)
                $seasonalOrder = '1,1,0,12'; // Contoh (P,D,Q,s) untuk data bulanan dengan siklus tahunan (s=12)

                if ($timeFrequency === 'D') {
                    $seasonalOrder = '1,1,0,7'; // Musiman mingguan untuk data harian
                } elseif ($timeFrequency === 'W') {
                    // Jika data mingguan, musiman tahunan s=52. Jika data tidak cukup, mungkin s=4 (musim per bulan)
                    $seasonalOrder = (count($historicalDeductions) > 104) ? '1,1,0,52' : '1,1,0,4';
                }

                $pythonPath = 'python';
                $scriptPath = base_path('scripts/sarima_forecaster.py');

                Log::info("Running SARIMA: {$pythonPath} {$scriptPath} '{$historicalDataForPython}' {$actualForecastSteps} {$sarimaOrder} {$seasonalOrder} {$timeFrequency}");

                $pendingProcess = Process::command([
                    $pythonPath,
                    $scriptPath,
                    $historicalDataForPython,
                    (string)$actualForecastSteps,
                    $sarimaOrder,
                    $seasonalOrder,
                    $timeFrequency
                ])->timeout(120); // Set timeout

                $result = $pendingProcess->run();

                if (!$result->successful()) {
                    $errorOutput = $result->errorOutput();
                    $standardOutput = $result->output();
                    $errorForecast = "Error Python SARIMA: " . $errorOutput . ($standardOutput ? " | Output: " . $standardOutput : "");
                    Log::error("SARIMA Process Failed: " . $errorOutput . " | Output: " . $standardOutput);
                } else {
                    $outputJson = $result->output();
                    Log::info("SARIMA Output: " . $outputJson);
                    $decodedOutput = json_decode($outputJson, true);
                    if (isset($decodedOutput['error'])) {
                        $errorForecast = "Error from SARIMA script: " . $decodedOutput['error'] . (isset($decodedOutput['trace']) ? " Trace: " . $decodedOutput['trace'] : "");
                        Log::error($errorForecast);
                    } else {
                        $historicalData = [
                            'labels' => $decodedOutput['historical_dates'] ?? [],
                            'data' => $decodedOutput['historical_values'] ?? []
                        ];
                        $forecastData = [
                            'labels' => $decodedOutput['forecast_dates'] ?? [],
                            'data' => $decodedOutput['forecast_values'] ?? []
                        ];
                    }
                }
            } else {
                $errorForecast = "Tidak cukup data historis (perlu min. {$minDataPointsForSarima} periode '{$timeFrequency}', ditemukan {$historicalDeductions->count()}) untuk membuat forecast SARIMA untuk item ini.";
            }
        }

        return view('dashboard.forecast', compact(
            'stocks',
            'selectedStockId',
            'forecastDurationInput',
            'timeFrequency',
            'historicalData',
            'forecastData',
            'errorForecast',
            'selectedStockName'
        ));
    }
}
