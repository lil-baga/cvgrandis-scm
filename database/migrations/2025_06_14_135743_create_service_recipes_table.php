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
            $table->string('service_code'); 
            $table->foreignId('stock_id')->constrained('stocks');
            $table->decimal('quantity_per_unit', 8, 2); 
            $table->string('unit_of_measure');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_recipes');
    }
};