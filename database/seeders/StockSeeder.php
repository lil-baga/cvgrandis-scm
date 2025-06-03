<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $stocksData = [
            // Materials
            ['name' => 'Akrilik Bening 3mm (Lembar 122x244cm)', 'type' => 'material', 'stock' => 20, 'low_stock' => 5],
            ['name' => 'Akrilik Susu 3mm (Lembar 122x244cm)', 'type' => 'material', 'stock' => 15, 'low_stock' => 4],
            ['name' => 'Stainless Steel Plat Mirror 0.8mm (Lembar 122x244cm)', 'type' => 'material', 'stock' => 10, 'low_stock' => 2],
            ['name' => 'HPL Taco Solid White (Lembar)', 'type' => 'material', 'stock' => 25, 'low_stock' => 5],
            ['name' => 'HPL Taco Motif Kayu Oak (Lembar)', 'type' => 'material', 'stock' => 20, 'low_stock' => 5],
            ['name' => 'ACP Seven PVDF Silver (Lembar)', 'type' => 'material', 'stock' => 12, 'low_stock' => 3],
            ['name' => 'Multipleks 12mm (Lembar)', 'type' => 'material', 'stock' => 30, 'low_stock' => 10],
            ['name' => 'Multipleks 18mm (Lembar)', 'type' => 'material', 'stock' => 20, 'low_stock' => 5],
            ['name' => 'Besi Hollow 2x2cm (Batang 6m)', 'type' => 'material', 'stock' => 50, 'low_stock' => 10],
            ['name' => 'Besi Hollow 4x4cm (Batang 6m)', 'type' => 'material', 'stock' => 30, 'low_stock' => 8],
            ['name' => 'Lem Akrilik (Botol)', 'type' => 'material', 'stock' => 10, 'low_stock' => 3],
            ['name' => 'Lem HPL Kuning (Kaleng)', 'type' => 'material', 'stock' => 15, 'low_stock' => 4],
            ['name' => 'Cat Dasar Besi Meni (Kg)', 'type' => 'material', 'stock' => 10, 'low_stock' => 2],
            ['name' => 'Cat Duco Hitam Doff (Liter)', 'type' => 'material', 'stock' => 8, 'low_stock' => 2],
            ['name' => 'Stiker Vinyl Oracal 651 Putih (Roll 50m)', 'type' => 'material', 'stock' => 3, 'low_stock' => 1],
            ['name' => 'Flexi Banner Korcin 340gsm (Roll 50m)', 'type' => 'material', 'stock' => 2, 'low_stock' => 1],
            ['name' => 'Kain Backdrop Hitam (Meter)', 'type' => 'material', 'stock' => 100, 'low_stock' => 20],

            // Electricity
            ['name' => 'LED Modul Samsung 3 Mata Putih 12V (Pcs)', 'type' => 'electricity', 'stock' => 1000, 'low_stock' => 200],
            ['name' => 'LED Strip SMD5050 Putih 12V (Roll 5m)', 'type' => 'electricity', 'stock' => 50, 'low_stock' => 10],
            ['name' => 'Power Supply Jaring 12V 10A (Pcs)', 'type' => 'electricity', 'stock' => 20, 'low_stock' => 5],
            ['name' => 'Power Supply Rainproof 12V 20A (Pcs)', 'type' => 'electricity', 'stock' => 15, 'low_stock' => 3],
            ['name' => 'Kabel Serabut Merah-Hitam 2x0.75mm (Roll 100m)', 'type' => 'electricity', 'stock' => 5, 'low_stock' => 1],
            ['name' => 'Adaptor 12V 2A (Pcs)', 'type' => 'electricity', 'stock' => 30, 'low_stock' => 10],
            ['name' => 'Saklar On/Off (Pcs)', 'type' => 'electricity', 'stock' => 50, 'low_stock' => 15],

            // Tools (Consumables)
            ['name' => 'Mata Bor Besi Set HSS (Set)', 'type' => 'tools', 'stock' => 10, 'low_stock' => 2],
            ['name' => 'Mata Gerinda Potong 4" Tipis (Box 25pcs)', 'type' => 'tools', 'stock' => 5, 'low_stock' => 1],
            ['name' => 'Amplas Kayu Lembar Grit #120 (Pack 50lbr)', 'type' => 'tools', 'stock' => 3, 'low_stock' => 1],
            ['name' => 'Isi Lem Tembak Besar (Pack 1kg)', 'type' => 'tools', 'stock' => 5, 'low_stock' => 2],
            ['name' => 'Sekrup Gipsum 1 inch (Box)', 'type' => 'tools', 'stock' => 20, 'low_stock' => 5],
            ['name' => 'Double Tape Foam Hijau (Roll)', 'type' => 'tools', 'stock' => 10, 'low_stock' => 3],
        ];

        foreach ($stocksData as $data) {
            $status = 'in_stock';
            if ($data['stock'] <= 0) {
                $status = 'out_of_stock';
            } elseif ($data['stock'] <= $data['low_stock']) {
                $status = 'low_stock';
            }
            Stock::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'stock' => $data['stock'],
                'low_stock' => $data['low_stock'],
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}