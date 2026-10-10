<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil ProductSeeder di sini
        $this->call([
            ProductSeeder::class,
        ]);

        // Buat user kasir bawaan tanpa bentrok
        User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir Utama',
                'password' => bcrypt('password'),
            ]
        );
    }
}
