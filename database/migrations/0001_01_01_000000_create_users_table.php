<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS TABLE
        |--------------------------------------------------------------------------
        */

        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Data dasar pengguna
            $table->string('name');

            $table->string('email')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | EMAIL VERIFICATION
            |--------------------------------------------------------------------------
            |
            | User register manual:
            | NULL -> sebelum verifikasi
            | timestamp -> setelah klik link verifikasi
            |
            | User Google OAuth:
            | dapat langsung diisi saat akun dibuat melalui Google.
            |
            */

            $table->timestamp('email_verified_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | PASSWORD
            |--------------------------------------------------------------------------
            |
            | Nullable karena akun Google OAuth tidak wajib memiliki password lokal.
            |
            */

            $table->string('password')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | ROLE
            |--------------------------------------------------------------------------
            |
            | Role:
            | - user
            | - operator
            | - admin
            |
            | Semua registrasi publik mendapatkan role user.
            |
            */

            $table->string('role', 20)
                ->default('user')
                ->index();


            /*
            |--------------------------------------------------------------------------
            | GOOGLE OAUTH
            |--------------------------------------------------------------------------
            */

            $table->string('google_id')
                ->nullable()
                ->unique();

            $table->text('avatar')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | REMEMBER LOGIN
            |--------------------------------------------------------------------------
            */

            $table->rememberToken();


            /*
            |--------------------------------------------------------------------------
            | CREATED_AT & UPDATED_AT
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | PASSWORD RESET TOKENS
        |--------------------------------------------------------------------------
        */

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')
                ->primary();

            $table->string('token');

            $table->timestamp('created_at')
                ->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | SESSIONS
        |--------------------------------------------------------------------------
        |
        | Digunakan jika:
        |
        | SESSION_DRIVER=database
        |
        */

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')
                ->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->index();

            $table->string('ip_address', 45)
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            $table->longText('payload');

            $table->integer('last_activity')
                ->index();
        });
    }


    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('users');
    }
};