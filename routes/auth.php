<?php

use App\Http\Auth\LoginController;
use App\Http\Auth\RegisterController;

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
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
| - Forgot Password
| - Reset Password
|
*/

Route::middleware('guest')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    |
    | GET  /register
    | Menampilkan halaman registrasi.
    |
    | POST /register
    | Menyimpan pengguna baru.
    |
    */

    Route::get(
        '/register',
        [
            RegisterController::class,
            'create',
        ]
    )->name('register');


    Route::post(
        '/register',
        [
            RegisterController::class,
            'store',
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    |
    | GET  /login
    | Menampilkan halaman login.
    |
    | POST /login
    | Memproses autentikasi pengguna.
    |
    */

    Route::get(
        '/login',
        [
            LoginController::class,
            'create',
        ]
    )->name('login');


    Route::post(
        '/login',
        [
            LoginController::class,
            'store',
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD
    |--------------------------------------------------------------------------
    |
    | Menampilkan form lupa password dan mengirimkan
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
    |
    | Menampilkan halaman reset password berdasarkan token
    | dan menyimpan password baru.
    |
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
| Route berikut hanya dapat diakses oleh pengguna
| yang sudah berhasil login.
|
| Fitur:
| - Email Verification
| - Resend Verification Email
| - Confirm Password
| - Update Password
| - Logout
|
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION NOTICE
    |--------------------------------------------------------------------------
    |
    | Menampilkan halaman pemberitahuan bahwa pengguna
    | harus melakukan verifikasi email.
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
    | Memproses link verifikasi email.
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
    | Meminta pengguna memasukkan password kembali
    | sebelum mengakses fitur sensitif.
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
    | Memperbarui password pengguna yang sedang login.
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
    | LOGOUT CUSTOM
    |--------------------------------------------------------------------------
    |
    | Logout ditangani oleh LoginController custom.
    |
    */

    Route::post(
        '/logout',
        [
            LoginController::class,
            'destroy',
        ]
    )->name('logout');

});