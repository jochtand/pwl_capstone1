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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();

            // Foreign Key untuk menyambungkan tiket ini ke event tertentu
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            // Kolom detail kategori tiket
            $table->string('name'); // Contoh: 'VIP', 'Regular', 'Presale 1'
            $table->decimal('price', 15, 2); // Decimal cocok untuk harga/uang
            $table->integer('quota'); // Jumlah tiket yang tersedia

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
