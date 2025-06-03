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

    public function forecast(Request $request)
    {
        $stocks = Stock::orderBy('name')->get();
        $selectedStockId = $request->input('stock_id');
        $forecastDurationInput = (int) $request->input('duration', 30); // Durasi input dari user
        $timeFrequency = $request->input('frequency', 'M'); // Default Bulanan untuk LSTM agar data tidak terlalu banyak

        $historicalData = null;
        $forecastData = null;
        $errorForecast = null;
        $selectedStockName = null;
        $actualForecastSteps = $forecastDurationInput; // Inisialisasi

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
                // Untuk LSTM, jika durasi 30 hari, maka steps = 30
                $actualForecastSteps = $forecastDurationInput;
            } elseif ($timeFrequency === 'W') {
                $query->addSelect(DB::raw("DATE_FORMAT(orders.created_at, '%x-%v') as date_period"));
                // Jika durasi 30 hari (sekitar 4 minggu), steps = 4. Jika 90 hari (sekitar 12 minggu), steps = 12
                $actualForecastSteps = ceil($forecastDurationInput / 7);
            } else { // Default Bulanan ('M')
                $query->addSelect(DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m-01') as date_period"));
                // Jika durasi 30 hari (1 bulan), steps = 1. Jika 90 hari (3 bulan), steps = 3
                $actualForecastSteps = ceil($forecastDurationInput / 30);
            }

            $historicalDeductions = $query->get();

            // Minimal data untuk LSTM (misal, sequence_length + beberapa data untuk training)
            $minDataPointsForLstm = 12 + 10; // Contoh: sequence 12, minimal 10 data training
            if ($historicalDeductions->count() >= $minDataPointsForLstm) {
                $historicalDataForPython = $historicalDeductions->map(function ($item) {
                    return ['date_period' => $item->date_period, 'value' => (int)$item->value];
                })->toJson();

                // Parameter untuk script Python LSTM (bisa disesuaikan)
                $sequenceLength = 12; // Jika data bulanan, ini berarti 1 tahun historis untuk prediksi
                if ($timeFrequency === 'W') $sequenceLength = 8; // Misal 8 minggu
                if ($timeFrequency === 'D') $sequenceLength = 30; // Misal 30 hari
                if ($historicalDeductions->count() <= $sequenceLength + 2) { // Jika data sangat mepet
                    $sequenceLength = floor($historicalDeductions->count() * 0.6); // Kurangi sequence length
                    if ($sequenceLength < 3) $sequenceLength = 3; // Batas bawah
                }


                $epochs = 100; // Jumlah epoch bisa ditingkatkan untuk model yang lebih baik
                $batchSize = 1;

                $pythonPath = 'python3';
                $scriptPath = base_path('scripts/lstm_forecaster.py');

                Log::info("Running LSTM: {$pythonPath} {$scriptPath} '{$historicalDataForPython}' {$actualForecastSteps} {$timeFrequency} {$sequenceLength} {$epochs} {$batchSize}");

                $process = new Process([
                    $pythonPath,
                    $scriptPath,
                    $historicalDataForPython,
                    (string)$actualForecastSteps,
                    $timeFrequency,
                    (string)$sequenceLength,
                    (string)$epochs,
                    (string)$batchSize
                ]);
                $process->setTimeout(120); // Set timeout lebih lama untuk training LSTM
                $process->run();

                if (!$process->isSuccessful()) {
                    $errorForecast = "Error Python LSTM: " . $process->getErrorOutput();
                    Log::error("LSTM Process Failed: " . $process->getErrorOutput() . " | Output: " . $process->getOutput());
                } else {
                    $outputJson = $process->getOutput();
                    Log::info("LSTM Output: " . $outputJson);
                    $decodedOutput = json_decode($outputJson, true);
                    if (isset($decodedOutput['error'])) {
                        $errorForecast = "Error from LSTM script: " . $decodedOutput['error'] . (isset($decodedOutput['trace']) ? " Trace: " . $decodedOutput['trace'] : "");
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
                $errorForecast = "Tidak cukup data historis (perlu min. {$minDataPointsForLstm} periode, ditemukan {$historicalDeductions->count()}) untuk membuat forecast LSTM untuk item ini dengan frekuensi '{$timeFrequency}'.";
            }
        }

        return view('dashboard.forecast', compact(
            'stocks',
            'selectedStockId',
            'forecastDurationInput', // Kirim durasi input asli
            'timeFrequency',
            'historicalData',
            'forecastData',
            'errorForecast',
            'selectedStockName'
        ));
    }
}
