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
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->id();

            // 3 Foreign Key yang menyambungkan siapa yang ngantri, di event apa, dan tiket jenis apa
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->onDelete('cascade');

            // Status antrean (pending = masih ngantri, notified = dihubungi untuk bayar, expired = hangus)
            $table->enum('status', ['pending', 'notified', 'expired'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
    }
};
