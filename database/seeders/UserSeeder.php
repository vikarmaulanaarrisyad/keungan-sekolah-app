<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan peran sudah ada (jalankan RolesAndPermissionsSeeder dulu jika perlu)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $guruRole = Role::firstOrCreate(['name' => 'guru']);
        $siswaRole = Role::firstOrCreate(['name' => 'siswa']);

        // Buat admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'], // Cek berdasarkan email
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'username' => 'admin'
            ]
        );
        $admin->assignRole($adminRole);

        // Buat guru
        $guru = User::updateOrCreate(
            ['email' => 'guru@example.com'],
            [
                'name' => 'Guru 1',
                'password' => Hash::make('password'),
                'username' => 'guru'
            ]
        );
        $guru->assignRole($guruRole);

        // Buat siswa
        $siswa = User::updateOrCreate(
            ['email' => 'siswa@example.com'],
            [
                'name' => 'Siswa 1',
                'password' => Hash::make('password'),
                'username' => 'siswa'
            ]
        );
        $siswa->assignRole($siswaRole);
    }
}
