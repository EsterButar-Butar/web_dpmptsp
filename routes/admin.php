<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
|
| Semua route khusus Admin.
|
| Alur akses:
| 1. Pengguna harus sudah login.
| 2. Email pengguna harus sudah diverifikasi.
| 3. Pengguna harus memiliki role "admin".
| 4. Admin dapat mengakses dashboard admin.
|
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ADMIN DASHBOARD
        |--------------------------------------------------------------------------
        |
        | URL        : /admin/dashboard
        | View       : admin.dashboard
        | Route Name : admin.dashboard
        |
        */

        Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    });