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
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
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
        if ($order->image_ref && $order->original_filename) {
            $fileAttachmentDetails = (object) [
                'name' => $order->original_filename,
                'type' => $order->mime_type ?? 'application/octet-stream',
                'url' => route('order.attachments', ['orderId' => $order->id])
            ];
        }

        $allStockItems = Stock::orderBy('name', 'asc')->get();

        $deductedStockItemsForThisOrder = $order->stockDeductions->map(function ($deduction) {
            if ($deduction->stock) {
                return (object) [
                    'id' => $deduction->stock->id,
                    'name' => $deduction->stock->name,
                    'type' => $deduction->stock->type,
                    'current_stock_available' => $deduction->stock->stock,
                    'quantity_deducted_for_this_order' => $deduction->quantity_deducted
                ];
            }
            return null;
        })->filter()->values();

        return view('dashboard.order_detail', [
            'order' => $order,
            'fileAttachmentDetails' => $fileAttachmentDetails,
            'allStockItems' => $allStockItems,
            'deductedStockItems' => $deductedStockItemsForThisOrder
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
        $processedStockIds = [];

        if (!is_array($adjustments) || empty($adjustments)) {
            return redirect()->route('order.show', $order->id)->with('error', 'Tidak ada data penyesuaian stok yang dikirim.');
        }

        DB::beginTransaction();
        try {
            foreach ($adjustments as $stockId => $quantityToDeduct) {
                $quantityToDeduct = (int) $quantityToDeduct;

                if ($quantityToDeduct > 0) {
                    $stockItem = Stock::where('id', $stockId)->lockForUpdate()->first();

                    if ($stockItem) {
                        $originalStockLevel = $stockItem->stock;

                        if ($stockItem->stock < $quantityToDeduct) {
                            Log::warning("Stok tidak cukup untuk {$stockItem->name} (ID: {$stockId}) pada Order #{$order->id}. Diminta: {$quantityToDeduct}, Tersedia: {$stockItem->stock}. Stok diatur jadi 0.");
                            $actualDeducted = $stockItem->stock;
                            $stockItem->stock = 0;
                        } else {
                            $actualDeducted = $quantityToDeduct;
                            $stockItem->stock -= $quantityToDeduct;
                        }

                        if ($stockItem->stock <= 0) {
                            $stockItem->status = 'out_of_stock';
                            $stockItem->stock = 0;
                        } elseif ($stockItem->stock <= $stockItem->low_stock) {
                            $stockItem->status = 'low_stock';
                        } else {
                            $stockItem->status = 'in_stock';
                        }
                        $stockItem->save();

                        OrderStockDeduction::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'stock_id' => $stockItem->id,
                            ],
                            [
                                'quantity_deducted' => DB::raw("quantity_deducted + {$actualDeducted}")
                            ]
                        );

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

        $filePath = $order->path;

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            $fileName = $order->original_filename ?? basename($filePath);
            return Storage::disk('public');
        }

        abort(404, 'File lampiran tidak ditemukan.');
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
        $forecastDurationInput = (int) $request->input('duration', 30);
        $timeFrequency = $request->input('frequency', 'D');

        $historicalData = null;
        $forecastData = null;
        $errorForecast = null;
        $selectedStockName = null;

        $actualForecastSteps = 0;
        if ($timeFrequency === 'D') {
            $actualForecastSteps = $forecastDurationInput;
        } elseif ($timeFrequency === 'W') {
            $actualForecastSteps = ceil($forecastDurationInput / 7);
        } else {
            $actualForecastSteps = ceil($forecastDurationInput / 30);
        }

        if ($actualForecastSteps < 1) $actualForecastSteps = 1;

        if ($selectedStockId) {
            $selectedStock = Stock::find($selectedStockId);
            $selectedStockName = $selectedStock ? $selectedStock->name : 'Unknown Stock';

            $query = OrderStockDeduction::where('stock_id', $selectedStockId)
                ->join('orders', 'order_stock_deductions.order_id', '=', 'orders.id')
                ->select(DB::raw("SUM(order_stock_deductions.quantity_deducted) as value"))
                ->orderBy('date_period', 'asc')
                ->groupBy('date_period');

            if ($timeFrequency === 'D') {
                $query->addSelect(DB::raw("DATE(orders.created_at) as date_period"));
            } elseif ($timeFrequency === 'W') {
                $query->addSelect(DB::raw("DATE_FORMAT(orders.created_at, '%x-%v') as date_period"));
            } else {
                $query->addSelect(DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m-01') as date_period"));
            }

            $historicalDeductions = $query->get();

            $minDataPointsForSarima = 15;
            if ($timeFrequency === 'M') $minDataPointsForSarima = 24;

            if ($historicalDeductions->count() >= $minDataPointsForSarima) {
                $historicalDataForPython = $historicalDeductions->map(function ($item) {
                    return ['date_period' => (string)$item->date_period, 'value' => (int)$item->value];
                })->toJson();

                $sarimaOrder = '1,1,1';
                $seasonalOrder = '1,1,0,12';

                if ($timeFrequency === 'D') {
                    $seasonalOrder = '1,1,0,7';
                } elseif ($timeFrequency === 'W') {
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
                ])->timeout(120);

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
