<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
|
| Semua route pada file ini hanya dapat diakses oleh pengguna yang:
|
| 1. Sudah login
| 2. Sudah melakukan verifikasi email
| 3. Memiliki role "user"
|
*/

Route::middleware([
    'auth',
    'verified',
    'role:user',
])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | USER DASHBOARD
        |--------------------------------------------------------------------------
        |
        | URL        : /user/dashboard
        | View       : user.dashboard
        | Route Name : user.dashboard
        |
        */

        Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    });