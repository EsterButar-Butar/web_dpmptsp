<?php

use App\Http\Controllers\Admin\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/pengguna', [PenggunaController::class, 'index'])
            ->name('pengguna.index');

        Route::post('/pengguna', [PenggunaController::class, 'store'])
            ->name('pengguna.store');

        Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])
            ->name('pengguna.update');

        Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])
            ->name('pengguna.destroy');

    });