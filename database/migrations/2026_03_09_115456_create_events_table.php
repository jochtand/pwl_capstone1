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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke tabel users (menandakan siapa organizer pembuat event-nya)
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');

            // Kolom-kolom detail event sesuai ERD
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('location');

            // nullable() artinya boleh dikosongkan dulu (misal saat baru buat event belum punya gambar)
            $table->string('banner_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
