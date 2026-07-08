<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
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

            $table->string('name');

            $table->string('email')
                ->unique();

            $table->timestamp('email_verified_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PASSWORD
            |--------------------------------------------------------------------------
            |
            | Nullable agar mendukung akun yang dibuat melalui Google Login.
            |
            */

            $table->string('password')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | ROLE
            |--------------------------------------------------------------------------
            |
            | Role aplikasi:
            |
            | - admin
            | - operator
            | - user
            |
            | Registrasi publik otomatis menggunakan role user.
            |
            */

            $table->string('role')
                ->default('user')
                ->index();


            /*
            |--------------------------------------------------------------------------
            | GOOGLE AUTHENTICATION
            |--------------------------------------------------------------------------
            */

            $table->string('google_id')
                ->nullable()
                ->unique();

            $table->string('avatar')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | REMEMBER TOKEN
            |--------------------------------------------------------------------------
            */

            $table->rememberToken();


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | PASSWORD RESET TOKENS TABLE
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
        | SESSIONS TABLE
        |--------------------------------------------------------------------------
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        | Hapus tabel pendukung terlebih dahulu.
        */

        Schema::dropIfExists('sessions');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('users');
    }
};