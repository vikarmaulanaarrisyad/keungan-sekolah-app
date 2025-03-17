<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\RombelController;
use App\Http\Controllers\Admin\SetorTabunganController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\StoreTabunganController;
use App\Http\Controllers\Admin\TabunganController;
use App\Http\Controllers\Admin\TapelController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin'], function () {
        // tahun pelajaran
        Route::get('/tapel/data', [TapelController::class, 'data'])->name('tapel.data');
        Route::resource('/tapel', TapelController::class)->except('create', 'edit', 'destroy');
        Route::put('/tahunpelajaran/update-status/{id}', [TapelController::class, 'updateStatus'])->name('tapel.update_status');

        // Kelas
        Route::get('/kelas/data', [KelasController::class, 'data'])->name('kelas.data');
        Route::resource('/kelas', KelasController::class)->except('create', 'edit');

        // Guru
        Route::get('/guru/data', [GuruController::class, 'data'])->name('guru.data');
        Route::resource('/guru', GuruController::class);

        // Siswa
        Route::get('/siswa/data', [SiswaController::class, 'data'])->name('siswa.data');
        Route::get('/get-siswa/{rombel_id}', [SiswaController::class, 'getByRombel'])->name('siswa.getByRombel');
        Route::resource('/siswa', SiswaController::class);

        // Rombel
        Route::get('/rombel/data', [RombelController::class, 'data'])->name('rombel.data');
        Route::resource('/rombel', RombelController::class);
        Route::get('/rombel/{id}/detail', [RombelController::class, 'detail'])->name('rombel.detail');
        Route::get('/rombel/{rombel_id}/siswa', [RombelController::class, 'getDataSiswa'])->name('rombel.getDataSiswa');
        Route::get('/rombel/{id}/siswa/data', [RombelController::class, 'getSiswaRombel'])->name('rombel.getSiswaRombel');
        Route::post('/rombel/add-siswa', [RombelController::class, 'addSiswa'])->name('rombel.addSiswa');
        Route::delete('/siswa/rombel/delete', [RombelController::class, 'removeSiswa'])->name('siswa.rombel.delete');

        // Tabungan
        route::get('/tabungan/data', [TabunganController::class, 'data'])->name('tabungan.data');
        Route::resource('/tabungan', TabunganController::class);

        // Store Tabungan
        Route::get('/transaksi/setor-tabungan/data', [SetorTabunganController::class, 'data'])->name('setor.tabungan.data');
        Route::resource('/transaksi/setor-tabungan', SetorTabunganController::class);
    });
});
