<?php

use App\Http\Controllers\User\UserProfileController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
|
| Route hanya dapat diakses oleh:
|
| 1. User sudah login
| 2. Email sudah diverifikasi
| 3. Role adalah user
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
        */

        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | USER PROFILE
        |--------------------------------------------------------------------------
        |
        | GET   /user/profile
        | PATCH /user/profile
        |
        */

        Route::get(
            '/profile',
            [UserProfileController::class, 'edit']
        )->name('profile.edit');


        Route::patch(
            '/profile',
            [UserProfileController::class, 'update']
        )->name('profile.update');
    });