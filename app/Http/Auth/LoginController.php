<?php

namespace App\Http\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW LOGIN PAGE
    |--------------------------------------------------------------------------
    |
    | Menampilkan halaman login.
    |
    */

    public function create(): View
    {
        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESS LOGIN
    |--------------------------------------------------------------------------
    |
    | Memvalidasi email dan password kemudian melakukan autentikasi.
    |
    */

    public function store(
        Request $request
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATION
        |--------------------------------------------------------------------------
        */

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {

            throw ValidationException::withMessages([

                'email' => [
                    'Email atau password yang Anda masukkan salah.',
                ],

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        |
        | Mencegah session fixation setelah login berhasil.
        |
        */

        $request
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT BY ROLE
        |--------------------------------------------------------------------------
        */

        return $this->redirectByRole();
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    |
    | Menghapus autentikasi pengguna dan membersihkan session.
    |
    */

    public function destroy(
        Request $request
    ): RedirectResponse {

        /*
        | Logout user
        */

        Auth::logout();


        /*
        | Invalidate session
        */

        $request
            ->session()
            ->invalidate();


        /*
        | Generate CSRF token baru
        */

        $request
            ->session()
            ->regenerateToken();


        /*
        | Redirect ke halaman utama
        */

        return redirect()
            ->route('home');
    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECT USER BY ROLE
    |--------------------------------------------------------------------------
    |
    | admin    → admin.dashboard
    | operator → operator.dashboard
    | user     → user.dashboard
    |
    */

    private function redirectByRole(): RedirectResponse
    {
        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {

            return redirect()
                ->route('admin.dashboard');

        }


        /*
        |--------------------------------------------------------------------------
        | OPERATOR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'operator') {

            return redirect()
                ->route('operator.dashboard');

        }


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'user') {

            return redirect()
                ->route('user.dashboard');

        }


        /*
        |--------------------------------------------------------------------------
        | UNKNOWN ROLE
        |--------------------------------------------------------------------------
        |
        | Jika role tidak valid, pengguna langsung dikeluarkan
        | dari sistem untuk mencegah akses yang tidak sesuai.
        |
        */

        Auth::logout();


        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Role akun tidak dikenali. Silakan hubungi administrator.',
            ]);
    }
}