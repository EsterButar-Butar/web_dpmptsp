<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Landing\AnalysisController;
use App\Http\Controllers\Landing\ComparisonController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| PUBLIC LANDING PAGES
|--------------------------------------------------------------------------
|
| Halaman publik yang dapat diakses oleh semua pengunjung
| tanpa harus melakukan autentikasi.
|
*/

Route::get('/', function () {
    return view('landing.home');
})->name('home');


Route::get('/tentang', function () {
    return view('landing.home');
})->name('about');


Route::get('/peta-investasi', function () {
    return view('landing.map');
})->name('investment.map');


Route::get(
    '/analisis',
    [
        AnalysisController::class,
        'index',
    ]
)->name('analysis');


Route::get(
    '/perbandingan-sektor',
    [
        ComparisonController::class,
        'index',
    ]
)->name('comparison');


/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECT
|--------------------------------------------------------------------------
|
| Route /dashboard menjadi pusat redirect setelah login.
|
| admin    → admin.dashboard
| operator → operator.dashboard
| user     → profile.show
|
*/

Route::get('/dashboard', function () {

    $user = auth()->user();

    return match ($user->role) {

        'admin' => redirect()->route(
            'admin.dashboard'
        ),

        'operator' => redirect()->route(
            'operator.dashboard'
        ),

        'user' => redirect()->route(
            'profile.show'
        ),

        default => abort(
            403,
            'Role pengguna tidak dikenali.'
        ),
    };

})
    ->middleware([
        'auth',
        'verified',
    ])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| PROFILE ROUTES
|--------------------------------------------------------------------------
|
| Route profil dapat digunakan oleh:
|
| - admin
| - operator
| - user
|
*/

Route::middleware([
    'auth',
])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Tampilkan Profil
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [
            ProfileController::class,
            'show',
        ]
    )->name('profile.show');


    /*
    |--------------------------------------------------------------------------
    | Edit Profil
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile/edit',
        [
            ProfileController::class,
            'edit',
        ]
    )->name('profile.edit');


    /*
    |--------------------------------------------------------------------------
    | Update Profil
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/profile',
        [
            ProfileController::class,
            'update',
        ]
    )->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | Hapus Akun
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/profile',
        [
            ProfileController::class,
            'destroy',
        ]
    )->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
|
| Route Laravel Breeze:
|
| - Login
| - Register
| - Forgot Password
| - Reset Password
| - Email Verification
| - Logout
|
*/

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/admin.php';


/*
|--------------------------------------------------------------------------
| OPERATOR ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/operator.php';


/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/user.php';