<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\AnalysisController;
use App\Http\Controllers\Landing\ComparisonController;


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
        'index'
    ]

)->name('analysis');


Route::get(
    '/perbandingan-sektor',
    [
        ComparisonController::class,
        'index'
    ]

)->name('comparison');


/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECT
|--------------------------------------------------------------------------
|
| Route /dashboard menjadi pintu masuk utama setelah pengguna login.
|
| admin    → admin.dashboard
| operator → operator.dashboard
| user     → user.dashboard
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
        'profile.edit'
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
| Semua pengguna yang telah login dapat:
|
| - melihat halaman edit profil;
| - memperbarui data profil;
| - menghapus akun.
|
*/

Route::middleware([
    'auth',
])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Edit Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [
            ProfileController::class,
            'edit',
        ]
    )->name('profile.edit');


    /*
    |--------------------------------------------------------------------------
    | Update Profile
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
    | Delete Profile
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
| Route autentikasi dari Laravel Breeze:
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
|
| Route khusus pengguna dengan role admin.
|
*/

require __DIR__ . '/admin.php';


/*
|--------------------------------------------------------------------------
| OPERATOR ROUTES
|--------------------------------------------------------------------------
|
| Route khusus pengguna dengan role operator.
|
*/

require __DIR__ . '/operator.php';


/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
|
| Route khusus pengguna dengan role user.
|
*/

require __DIR__ . '/user.php';