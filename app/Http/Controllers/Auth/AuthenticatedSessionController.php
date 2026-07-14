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

        if ($request->user()->isOperator()) {
            \App\Http\Controllers\Operator\OperatorController::logActivity(
                'Autentikasi',
                'Login',
                'Operator (' . $request->user()->name . ') berhasil login ke sistem.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT SETELAH LOGIN
        |--------------------------------------------------------------------------
        |
        | Pengguna diarahkan ke dashboard masing-masing berdasarkan rolenya.
        |
        */

        $url = match ($request->user()->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'operator' => route('operator.dashboard', absolute: false),
            default => route('profile.show', absolute: false),
        };

        return redirect()->intended($url);
    }


    /**
     * Logout pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user && $user->isOperator()) {
            \App\Http\Controllers\Operator\OperatorController::logActivity(
                'Autentikasi',
                'Logout',
                'Operator (' . $user->name . ') logout dari sistem.'
            );
        }

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