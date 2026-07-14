<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Operator\OperatorController;
use App\Http\Controllers\Operator\LqController;
use App\Http\Controllers\Operator\SsController;
use App\Http\Controllers\Operator\TipologiController;
use App\Http\Controllers\Operator\KlassenController;

Route::middleware([
    'auth',
    'verified',
    'role:operator',
])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {
        Route::get('/dashboard', [OperatorController::class, 'index'])->name('dashboard');
        Route::get('/aktivitas', [OperatorController::class, 'aktivitas'])->name('aktivitas');
        
        Route::get('/profile', [OperatorController::class, 'profile'])->name('profile');
        Route::post('/profile', [OperatorController::class, 'updateProfile'])->name('profile.update');
        
        Route::get('/settings', [OperatorController::class, 'settings'])->name('settings');
        Route::post('/settings/password', [OperatorController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/2fa', [OperatorController::class, 'toggle2FA'])->name('settings.2fa');

        // Analisis LQ Routes
        Route::get('/analisis-lq', [LqController::class, 'index'])->name('lq.index');
        Route::post('/analisis-lq/hitung', [LqController::class, 'store'])->name('lq.store');
        Route::put('/analisis-lq/{id}', [LqController::class, 'update'])->name('lq.update');
        Route::delete('/analisis-lq/{id}', [LqController::class, 'destroy'])->name('lq.destroy');
        Route::post('/analisis-lq/import', [LqController::class, 'import'])->name('lq.import');

        // Analisis SS Routes
        Route::get('/analisis-ss', [SsController::class, 'index'])->name('ss.index');
        Route::post('/analisis-ss/hitung', [SsController::class, 'store'])->name('ss.store');
        Route::put('/analisis-ss/{id}', [SsController::class, 'update'])->name('ss.update');
        Route::delete('/analisis-ss/{id}', [SsController::class, 'destroy'])->name('ss.destroy');
        Route::post('/analisis-ss/import', [SsController::class, 'import'])->name('ss.import');

        // Analisis Tipologi Sektor Routes
        Route::get('/analisis-tipologi', [TipologiController::class, 'index'])->name('tipologi.index');
        Route::post('/analisis-tipologi/hitung', [TipologiController::class, 'store'])->name('tipologi.store');
        Route::put('/analisis-tipologi/{id}', [TipologiController::class, 'update'])->name('tipologi.update');
        Route::delete('/analisis-tipologi/{id}', [TipologiController::class, 'destroy'])->name('tipologi.destroy');
        Route::post('/analisis-tipologi/import', [TipologiController::class, 'import'])->name('tipologi.import');

        // Analisis Klassen Routes
        Route::get('/analisis-klassen', [KlassenController::class, 'index'])->name('klassen.index');
        Route::post('/analisis-klassen/hitung', [KlassenController::class, 'store'])->name('klassen.store');
        Route::put('/analisis-klassen/{id}', [KlassenController::class, 'update'])->name('klassen.update');
        Route::delete('/analisis-klassen/{id}', [KlassenController::class, 'destroy'])->name('klassen.destroy');
        Route::post('/analisis-klassen/import', [KlassenController::class, 'import'])->name('klassen.import');
    });
