<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat peran (roles)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $guru = Role::firstOrCreate(['name' => 'guru']);
        $bendahara = Role::firstOrCreate(['name' => 'bendahara']);
        $kepalasekolah = Role::firstOrCreate(['name' => 'kepalasekolah']);
        $walikelas = Role::firstOrCreate(['name' => 'walikelas']);
        $siswa = Role::firstOrCreate(['name' => 'siswa']);

        // Daftar izin (permissions)
        $permissions = [
            // Manajemen Tabungan
            'tabungan.view',
            'tabungan.create',
            'tabungan.edit',
            'tabungan.delete',

            // Laporan Tabungan
            'laporan.tabungan.view',
            'laporan.tabungan.export',

            // Manajemen Sumber Dana BOS
            'bos.pemasukan.view',
            'bos.pemasukan.create',
            'bos.pemasukan.edit',
            'bos.pemasukan.delete',

            // Manajemen Pengeluaran Dana BOS
            'bos.pengeluaran.view',
            'bos.pengeluaran.create',
            'bos.pengeluaran.edit',
            'bos.pengeluaran.delete',
            'bos.pengeluaran.approve',

            // Laporan BOS
            'laporan.bos.view',
            'laporan.bos.export',
        ];

        // Buat dan berikan izin kepada admin
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $admin->givePermissionTo($permissions);

        // Berikan izin yang relevan untuk guru
        $guru->givePermissionTo([
            'laporan.bos.view',
            'tabungan.view',
            'tabungan.create',
            'tabungan.edit',
            'tabungan.delete',
        ]);

        // Berikan izin yang relevan untuk siswa
        $siswa->givePermissionTo([
            'tabungan.view',
        ]);

        // Berikan izin yang relevan untuk walikelas
        $walikelas->givePermissionTo([
            'tabungan.view',
            'tabungan.create',
            'tabungan.edit',
            'tabungan.delete',
        ]);

        // Berikan izin yang relevan untuk siswa
        $kepalasekolah->givePermissionTo([
            'bos.pemasukan.view',
            'bos.pengeluaran.view',

            'laporan.tabungan.view',
            'laporan.tabungan.export',

            // Laporan BOS
            'laporan.bos.view',
            'laporan.bos.export',
        ]);
    }
}
