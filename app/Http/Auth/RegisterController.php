<?php

namespace App\Http\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW REGISTER PAGE
    |--------------------------------------------------------------------------
    |
    | Menampilkan halaman registrasi pengguna.
    |
    */

    public function create(): View
    {
        return view('auth.register');
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESS REGISTRATION
    |--------------------------------------------------------------------------
    |
    | Memvalidasi data, membuat akun, mengirim event Registered,
    | kemudian login otomatis.
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

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],


            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],


            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | CREATE USER
        |--------------------------------------------------------------------------
        |
        | Semua pengguna yang mendaftar melalui halaman publik
        | otomatis mendapatkan role "user".
        |
        | Role admin dan operator tidak boleh dibuat melalui
        | form registrasi publik.
        |
        */

        $user = User::create([

            'name' =>
                $validated['name'],


            'email' =>
                $validated['email'],


            'password' =>
                Hash::make(
                    $validated['password']
                ),


            'role' =>
                'user',

        ]);


        /*
        |--------------------------------------------------------------------------
        | REGISTERED EVENT
        |--------------------------------------------------------------------------
        |
        | Event ini diperlukan untuk mekanisme verifikasi email.
        |
        */

        event(
            new Registered($user)
        );


        /*
        |--------------------------------------------------------------------------
        | AUTO LOGIN
        |--------------------------------------------------------------------------
        |
        | Pengguna langsung login setelah registrasi berhasil.
        |
        */

        Auth::login($user);


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        $request
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        |
        | Pengguna diarahkan melalui route dashboard utama.
        |
        | Karena dashboard user menggunakan middleware:
        |
        | auth
        | verified
        | role:user
        |
        | maka jika email belum diverifikasi, Laravel akan
        | mengarahkan pengguna ke verification.notice.
        |
        */

        return redirect()
            ->route('dashboard');
    }
}