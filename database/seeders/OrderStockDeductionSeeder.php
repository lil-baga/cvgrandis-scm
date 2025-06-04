<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Stock;
use App\Models\OrderStockDeduction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class OrderStockDeductionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $allOrders = Order::all(); // Ambil semua order beserta data tanggalnya
        $allStockItems = Stock::all(); // Ambil semua item stok
        $now = Carbon::now();

        $minDeductionsPerStock = 30; // UBAH TARGET MINIMAL DEDUKSI PER STOCK_ID DI SINI

        if ($allOrders->isEmpty()) { // Cukup ada 1 order untuk bisa jalan, tapi idealnya lebih banyak
            $this->command->error('Tidak ada data Order untuk menjalankan OrderStockDeductionSeeder. Jalankan OrderSeeder terlebih dahulu.');
            return;
        }

        if ($allOrders->count() < $minDeductionsPerStock) {
            $this->command->warn("Peringatan: Jumlah order kurang dari {$minDeductionsPerStock} ({$allOrders->count()} order ditemukan). Setiap stok akan menggunakan semua order yang tersedia, maksimal {$allOrders->count()} kali per stok.");
        }

        if ($allStockItems->isEmpty()) {
            $this->command->error('Tidak ada data Stok. Jalankan StockSeeder terlebih dahulu. Data deduksi tidak dibuat.');
            return;
        }

        $deductions = [];

        foreach ($allStockItems as $stockItem) {
            // Ambil N order unik secara acak untuk setiap item stok
            // Jika jumlah order kurang dari N, ambil semua order yang ada
            $numberOfOrdersToSelect = min($minDeductionsPerStock, $allOrders->count());

            $selectedOrders = new \Illuminate\Database\Eloquent\Collection(); // Inisialisasi
            if ($numberOfOrdersToSelect > 0) {
                // Jika ingin benar-benar unik dan tidak peduli jika order yang sama dipakai item stok lain
                // $selectedOrders = $allOrders->random($numberOfOrdersToSelect);

                // Jika ingin mencoba mendapatkan order yang berbeda untuk setiap iterasi stok (lebih kompleks dan mungkin tidak selalu unik jika stok banyak & order sedikit)
                // Untuk seeder, random() sudah cukup baik untuk variasi.
                // Kita akan memastikan kita mengambil sejumlah order yang dibutuhkan.
                if ($allOrders->count() >= $numberOfOrdersToSelect) {
                    $selectedOrders = $allOrders->random($numberOfOrdersToSelect);
                } else {
                    $selectedOrders = $allOrders; // Ambil semua jika order lebih sedikit dari yang dibutuhkan
                }

                // Pastikan $selectedOrders selalu collection
                if (!$selectedOrders instanceof \Illuminate\Database\Eloquent\Collection) {
                    $selectedOrders = new \Illuminate\Database\Eloquent\Collection([$selectedOrders]);
                }
            }

            $deductionsForThisStockCount = 0;
            foreach ($selectedOrders as $order) {
                // Batasi hingga $minDeductionsPerStock per stok
                if ($deductionsForThisStockCount >= $minDeductionsPerStock) break;

                $quantityDeducted = 1;
                switch ($stockItem->type) {
                    case 'material':
                        $quantityDeducted = rand(2, 8); // Sedikit penyesuaian rentang
                        if (str_contains(strtolower($stockItem->name), 'roll') || str_contains(strtolower($stockItem->name), '(lbr)')) {
                            $quantityDeducted = rand(1, 3);
                        }
                        break;
                    case 'electricity':
                        $quantityDeducted = rand(10, 40); // Sedikit penyesuaian rentang
                        if (str_contains(strtolower($stockItem->name), 'power supply') || str_contains(strtolower($stockItem->name), '(roll')) {
                            $quantityDeducted = rand(1, 5);
                        }
                        break;
                    case 'tools':
                        $quantityDeducted = rand(1, 15); // Sedikit penyesuaian rentang
                        if (str_contains(strtolower($stockItem->name), 'set') || str_contains(strtolower($stockItem->name), 'box')) {
                            $quantityDeducted = rand(1, 2);
                        }
                        break;
                }

                $orderCreatedAt = Carbon::parse($order->created_at);
                $potentialMaxDate = $orderCreatedAt->copy()->addMonths(2); // Deduksi terjadi maks 2 bulan setelah order
                $maxDeductionDate = $now->min($potentialMaxDate);

                $deductionDateTime = $faker->dateTimeBetween($orderCreatedAt, $maxDeductionDate);
                if (Carbon::parse($deductionDateTime)->lessThan($orderCreatedAt)) {
                    $deductionDateTime = $orderCreatedAt; // Pastikan tidak sebelum order dibuat
                }

                $deductions[] = [
                    'order_id' => $order->id,
                    'stock_id' => $stockItem->id,
                    'quantity_deducted' => $quantityDeducted,
                    'created_at' => $deductionDateTime,
                    'updated_at' => $deductionDateTime,
                ];
                $deductionsForThisStockCount++;
            }
        }

        // Loop tambahan untuk memastikan total deduksi (jika diperlukan) sudah tidak relevan
        // karena target per item stok sudah tinggi.

        // Bulk insert untuk performa
        if (!empty($deductions)) {
            foreach (array_chunk($deductions, 500) as $chunk) { // Ukuran chunk bisa disesuaikan
                OrderStockDeduction::insert($chunk);
            }
        }

        $this->command->info(count($deductions) . ' order stock deductions seeded. Each stock item should have up to ' . min($minDeductionsPerStock, $allOrders->count()) . ' deduction records with different orders (if enough unique orders exist).');
    }
}
