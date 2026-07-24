<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function show(Request $request): View
    {
        return view('user.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password sebelum menghapus akun
        $request->validateWithBag('userDeletion', [
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = $request->user();

        // Logout pengguna
        Auth::logout();

        // Hapus akun
        $user->delete();

        // Hapus session lama
        $request->session()->invalidate();

        // Buat ulang CSRF token
        $request->session()->regenerateToken();

        // Kembali ke halaman beranda
        return redirect('/');
    }
}