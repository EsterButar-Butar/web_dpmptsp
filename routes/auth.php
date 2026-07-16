<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
|
| Route ini hanya dapat diakses oleh pengguna yang belum login.
|
| Fitur:
| - Register
| - Login
| - Lupa password
| - Reset password
|
*/

Route::middleware('guest')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/register',
        [
            RegisteredUserController::class,
            'create',
        ]
    )->name('register');


    Route::post(
        '/register',
        [
            RegisteredUserController::class,
            'store',
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/login',
        [
            AuthenticatedSessionController::class,
            'create',
        ]
    )->name('login');


    Route::post(
        '/login',
        [
            AuthenticatedSessionController::class,
            'store',
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD
    |--------------------------------------------------------------------------
    |
    | Menampilkan halaman lupa password dan mengirim
    | link reset password ke email pengguna.
    |
    */

    Route::get(
        '/forgot-password',
        [
            PasswordResetLinkController::class,
            'create',
        ]
    )->name('password.request');


    Route::post(
        '/forgot-password',
        [
            PasswordResetLinkController::class,
            'store',
        ]
    )->name('password.email');


    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reset-password/{token}',
        [
            NewPasswordController::class,
            'create',
        ]
    )->name('password.reset');


    Route::post(
        '/reset-password',
        [
            NewPasswordController::class,
            'store',
        ]
    )->name('password.store');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
|
| Semua route di dalam group ini hanya dapat diakses
| oleh pengguna yang sudah login.
|
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION NOTICE
    |--------------------------------------------------------------------------
    |
    | Menampilkan halaman pemberitahuan verifikasi email.
    |
    */

    Route::get(
        '/verify-email',
        EmailVerificationPromptController::class
    )->name('verification.notice');


    /*
    |--------------------------------------------------------------------------
    | VERIFY EMAIL
    |--------------------------------------------------------------------------
    |
    | Memproses link verifikasi yang dikirim melalui email.
    |
    */

    Route::get(
        '/verify-email/{id}/{hash}',
        VerifyEmailController::class
    )
        ->middleware([
            'signed',
            'throttle:6,1',
        ])
        ->name('verification.verify');


    /*
    |--------------------------------------------------------------------------
    | RESEND VERIFICATION EMAIL
    |--------------------------------------------------------------------------
    |
    | Mengirim ulang email verifikasi.
    |
    */

    Route::post(
        '/email/verification-notification',
        [
            EmailVerificationNotificationController::class,
            'store',
        ]
    )
        ->middleware('throttle:6,1')
        ->name('verification.send');


    /*
    |--------------------------------------------------------------------------
    | CONFIRM PASSWORD
    |--------------------------------------------------------------------------
    |
    | Meminta pengguna mengonfirmasi password sebelum
    | melakukan tindakan sensitif.
    |
    */

    Route::get(
        '/confirm-password',
        [
            ConfirmablePasswordController::class,
            'show',
        ]
    )->name('password.confirm');


    Route::post(
        '/confirm-password',
        [
            ConfirmablePasswordController::class,
            'store',
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk mengganti password pengguna
    | yang sedang login.
    |
    */

    Route::put(
        '/password',
        [
            PasswordController::class,
            'update',
        ]
    )->name('password.update');


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    |
    | Logout hanya memiliki SATU route.
    |
    | Method : POST
    | URL    : /logout
    | Name   : logout
    |
    */

    Route::post(
        '/logout',
        [
            AuthenticatedSessionController::class,
            'destroy',
        ]
    )->name('logout');

});