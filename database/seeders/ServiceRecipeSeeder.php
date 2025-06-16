<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceRecipe;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class ServiceRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service_recipes')->truncate();
        
        $stockIds = Stock::pluck('id', 'name')->toArray();

        $this->command->info("Membuat resep berdasarkan stok yang tersedia...");

        $recipes = [
            // --- Resep untuk 'neon' ---
            // Bahan utama per meter persegi
            ['service' => 'neon', 'stock_name' => 'Akrilik Susu 3mm (Lembar 122x244cm)', 'quantity' => 1.05, 'unit' => 'per_sq_meter'], // Akrilik depan, +5% waste
            ['service' => 'neon', 'stock_name' => 'Multipleks 12mm (Lembar)', 'quantity' => 1.05, 'unit' => 'per_sq_meter'], // Papan belakang, +5% waste (Contoh penggunaan, bisa juga plat besi)
            // Bahan rangka per meter keliling
            ['service' => 'neon', 'stock_name' => 'Besi Hollow 2x2cm (Batang 6m)', 'quantity' => 0.17, 'unit' => 'per_meter_perimeter'], // 1m keliling butuh 1/6 batang
            ['service' => 'neon', 'stock_name' => 'LED Modul Samsung 3 Mata Putih 12V (Pcs)', 'quantity' => 20, 'unit' => 'per_meter_perimeter'], // Asumsi 20 modul per meter
            // Kebutuhan listrik per unit pesanan
            ['service' => 'neon', 'stock_name' => 'Power Supply Jaring 12V 10A (Pcs)', 'quantity' => 1, 'unit' => 'per_unit'], // Asumsi 1 neon box = 1 power supply
            ['service' => 'neon', 'stock_name' => 'Kabel Serabut Merah-Hitam 2x0.75mm (Roll 100m)', 'quantity' => 0.05, 'unit' => 'per_unit'], // Asumsi 1 neon box butuh 5 meter (5% dari roll 100m)

            // --- Resep untuk 'backdrop' ---
            ['service' => 'backdrop', 'stock_name' => 'Multipleks 12mm (Lembar)', 'quantity' => 0.35, 'unit' => 'per_sq_meter'], // 1 lembar (~2.97m2) untuk ~3m2
            ['service' => 'backdrop', 'stock_name' => 'Besi Hollow 4x4cm (Batang 6m)', 'quantity' => 0.5, 'unit' => 'per_sq_meter'], // Asumsi per m2 butuh 3 meter rangka (0.5 batang)
            ['service' => 'backdrop', 'stock_name' => 'Flexi Banner Korcin 340gsm (Roll 50m)', 'quantity' => 0.02, 'unit' => 'per_sq_meter'], // 1m2 banner butuh 1m2 flexi (1/50 dari roll 50m)
            ['service' => 'backdrop', 'stock_name' => 'Sekrup Gipsum 1 inch (Box)', 'quantity' => 0.01, 'unit' => 'per_sq_meter'], // Asumsi 1 box untuk 100 m2

            // --- Resep untuk 'lettering' ---
            ['service' => 'lettering', 'stock_name' => 'Stainless Steel Plat Mirror 0.8mm (Lembar 122x244cm)', 'quantity' => 0.04, 'unit' => 'per_unit'], // Asumsi 1 huruf butuh area 30x30 cm (0.09 m2), atau ~4% dari lembar
            ['service' => 'lettering', 'stock_name' => 'Lem Akrilik (Botol)', 'quantity' => 0.02, 'unit' => 'per_unit'], // Asumsi 1 botol untuk 50 huruf

            // --- Resep untuk 'interior' (contoh: panel dinding per m2) ---
            ['service' => 'interior', 'stock_name' => 'HPL Taco Motif Kayu Oak (Lembar)', 'quantity' => 0.35, 'unit' => 'per_sq_meter'],
            ['service' => 'interior', 'stock_name' => 'Multipleks 18mm (Lembar)', 'quantity' => 0.35, 'unit' => 'per_sq_meter'],
            ['service' => 'interior', 'stock_name' => 'Lem HPL Kuning (Kaleng)', 'quantity' => 0.1, 'unit' => 'per_sq_meter'], // 1 kaleng untuk 10 m2

            // --- Resep untuk 'event' (per paket/event) ---
            ['service' => 'event', 'stock_name' => 'Kain Backdrop Hitam (Meter)', 'quantity' => 20, 'unit' => 'per_unit'], // 1 event butuh 20m kain
            ['service' => 'event', 'stock_name' => 'Amplas Kayu Lembar Grit #120 (Pack 50lbr)', 'quantity' => 5, 'unit' => 'per_unit'], // Asumsi 1 event butuh 5 lbr amplas
        ];

        foreach ($recipes as $recipe) {
            if (isset($stockIds[$recipe['stock_name']])) {
                ServiceRecipe::create([
                    'service_code' => $recipe['service'],
                    'stock_id' => $stockIds[$recipe['stock_name']],
                    'quantity_per_unit' => $recipe['quantity'],
                    'unit_of_measure' => $recipe['unit'],
                ]);
            } else {
                $this->command->warn("Peringatan: Resep untuk '{$recipe['stock_name']}' dilewati karena nama stok tidak ditemukan di database.");
            }
        }

        $this->command->info(count($recipes) . ' service recipes seeded successfully.');
    }
}