<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Akun Khusus Organizer (Admin)
        // Kita pakai firstOrCreate agar kalau kodenya dijalankan 2x tidak error duplicate
        User::firstOrCreate(
            ['email' => 'panitia.konser@gmail.com'],
            [
                'name' => 'Panitia Konser Bandung',
                'password' => Hash::make('12345678'), // Password angka 1 sampai 8
                'role' => 'organizer'
            ]
        );

        // Membuat Akun User Biasa
        User::firstOrCreate(
            ['email' => 'jonathan@gmail.com'],
            [
                'name' => 'Jonathan',
                'password' => Hash::make('12345678'),
                'role' => 'user'
            ]
        );
    }
}
