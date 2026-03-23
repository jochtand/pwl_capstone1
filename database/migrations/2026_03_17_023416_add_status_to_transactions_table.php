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
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan status pembayaran (default-nya: pending)
            $table->string('payment_status')->default('pending')->after('total_price');

            // Menambahkan status penggunaan tiket (default-nya: available/bisa dipakai)
            $table->string('ticket_status')->default('available')->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'ticket_status']);
        });
    }
};
