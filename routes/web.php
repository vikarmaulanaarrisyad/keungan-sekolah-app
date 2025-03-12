<?php

use App\Http\Controllers\Admin\KelasController;
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
    });
});
