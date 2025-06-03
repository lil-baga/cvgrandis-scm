<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Stock;
use App\Models\OrderStockDeduction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderStockDeductionSeeder extends Seeder
{
    public function run(): void
    {
        $orderIds = Order::pluck('id')->toArray();
        $stockItems = Stock::all(); // Ambil semua info stok
        $now = Carbon::now();

        if (empty($orderIds) || $stockItems->isEmpty()) {
            $this->command->info('Tidak ada order atau stok untuk membuat OrderStockDeductions. Jalankan OrderSeeder dan StockSeeder terlebih dahulu.');
            return;
        }

        // Pemetaan layanan ke tipe stok yang mungkin digunakan
        $serviceToStockTypes = [
            'neon_box' => ['material', 'electricity'],
            'backdrop_event' => ['material', 'tools'],
            'interior_design' => ['material', 'tools'],
            'lettering' => ['material'],
            'event_organizer' => ['material', 'tools'], // EO mungkin butuh banyak material backdrop, tools
            'custom_advertising' => ['material', 'electricity'],
            'booth_pameran' => ['material', 'tools'],
            'signage_kantor' => ['material'],
        ];

        $deductions = [];
        for ($i = 0; $i < 100; $i++) {
            $randomOrder = Order::find(array_rand(array_flip($orderIds))); // Pilih order acak
            if (!$randomOrder) continue;

            // Dapatkan tipe stok yang relevan berdasarkan service order
            $relevantStockTypes = $serviceToStockTypes[$randomOrder->service] ?? ['material', 'tools', 'electricity']; // Fallback jika service tidak ada di map

            // Filter stok item berdasarkan tipe yang relevan
            $relevantStockItems = $stockItems->whereIn('type', $relevantStockTypes);
            
            if ($relevantStockItems->isEmpty()) continue; // Lewati jika tidak ada stok relevan

            $randomStockItem = $relevantStockItems->random();

            // Pastikan kombinasi order_id dan stock_id unik jika ada unique constraint di DB
            // Untuk seeder ini, kita izinkan duplikasi untuk simulasi beberapa kali pengambilan
            
            $deductions[] = [
                'order_id' => $randomOrder->id,
                'stock_id' => $randomStockItem->id,
                'quantity_deducted' => rand(1, 5), // Jumlah acak 1-5
                'created_at' => $randomOrder->created_at->addDays(rand(0, 7)), // Waktu deduksi setelah order dibuat
                'updated_at' => $randomOrder->created_at->addDays(rand(0, 7)),
            ];
        }
        
        // Bulk insert untuk performa
        // Pisahkan menjadi chunk jika datanya sangat besar
        foreach (array_chunk($deductions, 200) as $chunk) {
            OrderStockDeduction::insert($chunk);
        }
    }
}