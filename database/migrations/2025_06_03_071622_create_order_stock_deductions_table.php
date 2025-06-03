<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_stock_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade'); // atau onDelete('restrict') jika Anda tidak ingin stok terhapus jika masih terkait
            $table->integer('quantity_deducted'); // Jumlah stok yang dikurangi untuk pesanan ini
            $table->timestamps(); // Waktu pencatatan pengurangan

            // Opsional: Tambahkan unique constraint jika satu item stok hanya bisa dikurangi sekali per order melalui interface ini
            // $table->unique(['order_id', 'stock_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_stock_deductions');
    }
};