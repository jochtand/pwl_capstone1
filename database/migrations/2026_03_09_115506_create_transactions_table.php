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

            // Foreign Key ke tabel users (siapa pembeli yang melakukan transaksi ini)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Kolom detail transaksi sesuai ERD
            $table->decimal('total_amount', 15); // Total harga belanjaan

            // Enum untuk status pembayaran. Kita kasih nilai default 'pending' (menunggu pembayaran)
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');

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
