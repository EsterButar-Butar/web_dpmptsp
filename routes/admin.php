<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\DataWilayahController;
use App\Http\Controllers\Admin\DataKbliController;
use App\Http\Controllers\Admin\DataHsCodeController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\MoneyCurrencyController;

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

        /*
        |--------------------------------------------------------------------------
        | Pengguna
        |--------------------------------------------------------------------------
        */

        Route::get('/pengguna', [PenggunaController::class, 'index'])
            ->name('pengguna.index');

        Route::post('/pengguna', [PenggunaController::class, 'store'])
            ->name('pengguna.store');

        Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])
            ->name('pengguna.update');

        Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])
            ->name('pengguna.destroy');

        /*
        |--------------------------------------------------------------------------
        | Data Wilayah
        |--------------------------------------------------------------------------
        */

        Route::get('/data-wilayah', [DataWilayahController::class, 'index'])
            ->name('data-wilayah.index');

        Route::post('/data-wilayah', [DataWilayahController::class, 'store'])
            ->name('data-wilayah.store');

        Route::put('/data-wilayah/{dataWilayah}', [DataWilayahController::class, 'update'])
            ->name('data-wilayah.update');

        Route::delete('/data-wilayah/{dataWilayah}', [DataWilayahController::class, 'destroy'])
            ->name('data-wilayah.destroy');

        /*
        |--------------------------------------------------------------------------
        | Data KBLI
        |--------------------------------------------------------------------------
        */

        Route::get('/data-kbli', [DataKbliController::class, 'index'])
            ->name('data-kbli.index');

        Route::post('/data-kbli', [DataKbliController::class, 'store'])
            ->name('data-kbli.store');

        Route::put('/data-kbli/{id}', [DataKbliController::class, 'update'])
            ->name('data-kbli.update');

        Route::delete('/data-kbli/{id}', [DataKbliController::class, 'destroy'])
            ->name('data-kbli.destroy');

        /*
        |--------------------------------------------------------------------------
        | HS Code
        |--------------------------------------------------------------------------
        */

        Route::get('/hs-code', [DataHsCodeController::class, 'index'])
            ->name('hs-code.index');

        Route::post('/hs-code', [DataHsCodeController::class, 'store'])
            ->name('hs-code.store');

        Route::put('/hs-code/{id}', [DataHsCodeController::class, 'update'])
            ->name('hs-code.update');

        Route::delete('/hs-code/{id}', [DataHsCodeController::class, 'destroy'])
            ->name('hs-code.destroy');

        /*
        |--------------------------------------------------------------------------
        | Profil Admin
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [AdminProfileController::class, 'index'])
            ->name('profile.index');

        Route::put('/profile', [AdminProfileController::class, 'update'])
            ->name('profile.update');

        /*
        |--------------------------------------------------------------------------
        | Pengaturan Admin
        |--------------------------------------------------------------------------
        */

        Route::get('/settings', [AdminSettingsController::class, 'index'])
            ->name('settings.index');

        Route::put(
            '/settings/password',
            [AdminSettingsController::class, 'updatePassword']
        )->name('settings.password');

        Route::put(
            '/settings/two-factor',
            [AdminSettingsController::class, 'toggleTwoFactor']
        )->name('settings.2fa');

        Route::get(
            '/money-currency',
            [MoneyCurrencyController::class, 'index']
        )->name('money-currency.index');

        Route::post(
            '/money-currency/convert',
            [MoneyCurrencyController::class, 'convert']
        )->name('money-currency.convert');
                    });