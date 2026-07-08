<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| OPERATOR ROUTES
|--------------------------------------------------------------------------
|
| Semua route khusus Operator.
|
| Alur akses:
| 1. Pengguna harus sudah login.
| 2. Email pengguna harus sudah diverifikasi.
| 3. Pengguna harus memiliki role "operator".
| 4. Operator dapat mengakses dashboard operator.
|
*/

Route::middleware([
    'auth',
    'verified',
    'role:operator',
])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | OPERATOR DASHBOARD
        |--------------------------------------------------------------------------
        |
        | URL        : /operator/dashboard
        | View       : operator.dashboard
        | Route Name : operator.dashboard
        |
        */

        Route::get('/dashboard', function () {
        return view('operator.dashboard');
    })->name('dashboard');

    });