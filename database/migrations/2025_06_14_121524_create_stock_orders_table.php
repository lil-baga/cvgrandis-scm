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
        Schema::create('stock_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade'); // Barang apa yang dipesan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Siapa (Administrator) yang memesan
            $table->integer('quantity_requested'); // Berapa jumlah yang dipesan
            $table->enum('status', ['pending', 'fulfilled', 'cancelled'])->default('pending'); // Status pesanan
            $table->foreignId('fulfilled_by')->nullable()->constrained('users'); // Siapa (Supplier) yang memenuhi
            $table->timestamp('fulfilled_at')->nullable(); // Kapan pesanan dipenuhi
            $table->timestamps(); // created_at (kapan pesanan dibuat), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_orders');
    }
};
