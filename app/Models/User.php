<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;


    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    |
    | Field yang boleh diisi melalui create() atau update().
    |
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'email_verified_at',
    ];


    /*
    |--------------------------------------------------------------------------
    | HIDDEN ATTRIBUTES
    |--------------------------------------------------------------------------
    |
    | Field yang tidak ditampilkan ketika model dikonversi
    | menjadi array atau JSON.
    |
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE CASTING
    |--------------------------------------------------------------------------
    |
    | email_verified_at diubah menjadi objek datetime.
    |
    | password menggunakan cast "hashed" sehingga Laravel
    | menangani hashing password secara otomatis.
    |
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE CHECKER
    |--------------------------------------------------------------------------
    |
    | Pemeriksaan role secara umum.
    |
    | Contoh:
    |
    | auth()->user()->hasRole('admin');
    |
    */

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN CHECKER
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    /*
    |--------------------------------------------------------------------------
    | OPERATOR CHECKER
    |--------------------------------------------------------------------------
    */

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }


    /*
    |--------------------------------------------------------------------------
    | USER CHECKER
    |--------------------------------------------------------------------------
    */

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}