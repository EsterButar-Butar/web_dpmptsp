<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }


    /**
     * Memproses login pengguna.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validasi dan autentikasi login
        $request->authenticate();

        // Membuat ulang session ID untuk keamanan
        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT SETELAH LOGIN
        |--------------------------------------------------------------------------
        |
        | Semua pengguna diarahkan ke route dashboard.
        |
        | Route dashboard kemudian menentukan tujuan berdasarkan role:
        |
        | admin    -> admin.dashboard
        | operator -> operator.dashboard
        | user     -> profile.edit
        |
        */

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }


    /**
     * Logout pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout dari guard web
        Auth::guard('web')->logout();

        // Hapus session lama
        $request->session()->invalidate();

        // Membuat CSRF token baru
        $request->session()->regenerateToken();

        // Kembali ke halaman utama
        return redirect()->route('home');
    }
}