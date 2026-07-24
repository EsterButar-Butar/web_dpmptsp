<?php

use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\Landing\AnalysisController;
use App\Http\Controllers\Landing\ComparisonController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestmentMapController;

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


// Route::get('/tentang', function () {
//     return view('landing.about');
// })->name('about');


Route::get('/peta-investasi', [InvestmentMapController::class, 'index'])
    ->name('investment.map');


/*
|--------------------------------------------------------------------------
| ANALISIS
|--------------------------------------------------------------------------
*/

Route::get(
    '/analisis',
    [
        AnalysisController::class,
        'index',
    ]
)->name('analysis');


/*
|--------------------------------------------------------------------------
| AJAX KABUPATEN BERDASARKAN PROVINSI
|--------------------------------------------------------------------------
|
| Digunakan untuk mengambil daftar kabupaten berdasarkan provinsi
| tanpa melakukan reload halaman.
|
*/

Route::get(
    '/kabupaten/{provinsi}',
    [
        AnalysisController::class,
        'getKabupaten',
    ]
)->name('analysis.kabupaten');


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
| user     → user.profile
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
            'user.profile'
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


Route::get('/about', function () {
    return view('landing.about');
})->name('about');


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