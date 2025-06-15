<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_recipes', function (Blueprint $table) {
            $table->id();
            // Layanan apa yang menggunakan resep ini
            // Cocokkan dengan value di dropdown form, misal: 'neon_box', 'backdrop'
            $table->string('service_code'); 
            
            // Barang stok apa yang dibutuhkan
            $table->foreignId('stock_id')->constrained('stocks');
            
            // Berapa banyak stok yang dibutuhkan per "unit ukuran"
            // Contoh: 1 m² neon box butuh 1 m² akrilik, jadi quantity_per_unit = 1
            // Contoh: 1 m² neon box butuh 15 LED, jadi quantity_per_unit = 15
            $table->decimal('quantity_per_unit', 8, 2); 
            
            // Unit ukuran untuk resep ini
            // 'per_sq_meter' -> dihitung per meter persegi (luas)
            // 'per_unit' -> dihitung per jumlah pesanan (misal: 1 paket EO butuh 1 set alat tulis)
            // 'per_meter_length' -> dihitung per meter panjang/keliling
            $table->string('unit_of_measure'); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_recipes');
    }
};