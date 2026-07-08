<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\AnalysisController;
use App\Http\Controllers\Landing\ComparisonController;


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


Route::get(
    '/analisis',
    [
        AnalysisController::class,
        'index'
    ]
)->name('analysis.index');


Route::get(
    '/perbandingan-sektor',
    [
        ComparisonController::class,
        'index'
    ]
)->name('comparison.index');

require __DIR__ . '/auth.php';

require __DIR__ . '/admin.php';

require __DIR__ . '/operator.php';

require __DIR__ . '/user.php';