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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // ID Pembeli (Siapa yang beli?)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // ID Kategori Tiket (Beli tiket apa?)
            $table->foreignId('ticket_category_id')->constrained()->cascadeOnDelete();
            // Total harga saat dia beli
            $table->integer('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
