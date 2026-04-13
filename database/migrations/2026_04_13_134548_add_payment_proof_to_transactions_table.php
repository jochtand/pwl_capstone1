<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan kolom untuk nama file gambar, boleh kosong (nullable)
            $table->string('payment_proof')->nullable()->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('payment_proof');
        });
    }
};
