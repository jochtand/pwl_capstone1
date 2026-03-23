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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke transaksi dan kategori tiketnya
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->onDelete('cascade');

            // Detail tiket fisik/digital
            $table->string('qr_code')->unique(); // Kode unik untuk generate QR
            $table->boolean('is_scanned')->default(false); // Default false (belum dipakai)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
