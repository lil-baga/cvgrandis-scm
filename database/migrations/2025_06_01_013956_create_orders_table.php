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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('service');
            $table->text('description');
            $table->binary('image_ref')->nullable();
            $table->enum('status', ['in_queue', 'on_going', 'finished']);
            $table->string('original_filename')->nullable(); // Nama file asli dari klien
            $table->string('mime_type')->nullable();       // Tipe MIME file
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
