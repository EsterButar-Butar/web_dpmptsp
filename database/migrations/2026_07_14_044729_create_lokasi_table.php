<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel lokasi.
     */
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | DATA LOKASI
            |--------------------------------------------------------------------------
            */

            $table->string('nama', 255);

            // Koordinat geografis
            $table->decimal('latitude', 10, 8);

            $table->decimal('longitude', 11, 8);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('nama');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel lokasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
};