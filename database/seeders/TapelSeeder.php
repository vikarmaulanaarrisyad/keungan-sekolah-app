<?php

namespace Database\Seeders;

use App\Models\Tapel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar Tahun Pelajaran contoh
        $tapels = [
            ['nama' => '2023/2024', 'semester' => 'Ganjil', 'status' => 0],
            ['nama' => '2023/2024', 'semester' => 'Genap', 'status' => 0],
            ['nama' => '2024/2025', 'semester' => 'Ganjil', 'status' => 0],
            ['nama' => '2024/2025', 'semester' => 'Genap', 'status' => 0],
            ['nama' => '2025/2026', 'semester' => 'Ganjil', 'status' => 0],
            ['nama' => '2025/2026', 'semester' => 'Genap', 'status' => 1],
        ];

        // Masukkan data ke database
        foreach ($tapels as $tapel) {
            Tapel::firstOrCreate($tapel);
        }
    }
}
