<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel kabupaten.
     */
    public function up(): void
    {
        Schema::create('kabupaten', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id('kab_id');

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('provinsi_id')
                ->constrained('provinsi', 'prov_id')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA KABUPATEN
            |--------------------------------------------------------------------------
            */

            $table->string('nama_kabupaten', 255);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('provinsi_id');
            $table->index('nama_kabupaten');
        });
    }

    /**
     * Menghapus tabel kabupaten.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupaten');
    }
};