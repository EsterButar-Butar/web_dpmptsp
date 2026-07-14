<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\DataWilayahController;

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/pengguna', [PenggunaController::class, 'index'])
            ->name('pengguna.index');

        Route::post('/pengguna', [PenggunaController::class, 'store'])
            ->name('pengguna.store');

        Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])
            ->name('pengguna.update');

        Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])
            ->name('pengguna.destroy');

        Route::get('/data-wilayah', [DataWilayahController::class, 'index'])
            ->name('data-wilayah.index');

        Route::post('/data-wilayah', [DataWilayahController::class, 'store'])
            ->name('data-wilayah.store');

        Route::put('/data-wilayah/{dataWilayah}', [DataWilayahController::class, 'update'])
            ->name('data-wilayah.update');

        Route::delete('/data-wilayah/{dataWilayah}', [DataWilayahController::class, 'destroy'])
            ->name('data-wilayah.destroy');

    });