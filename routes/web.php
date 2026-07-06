<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Landing Pages
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing.home');
})->name('home');


Route::get('/tentang', function () {
    return view('landing.about');
})->name('about');


Route::get('/peta-investasi', function () {
    return view('landing.map');
})->name('investment.map');


Route::get('/kontak', function () {
    return view('landing.contact');
})->name('contact');


Route::get('/faq', function () {
    return view('landing.faq');
})->name('faq');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([
    'auth',
    'verified'
])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');


    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');


    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';