<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel data_hs_code.
     */
    public function up(): void
    {
        Schema::create('data_hs_code', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | DATA HS CODE
            |--------------------------------------------------------------------------
            */

            $table->string('kategori_kode', 100);

            $table->string('kelompok_kode', 50);

            $table->text('kelompok_uraian');

            $table->string('subkelompok_kode', 50);

            $table->text('subkelompok_uraian');

            $table->string('hs_code', 20);

            $table->text('uraian_barang');

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('kategori_kode');
            $table->index('kelompok_kode');
            $table->index('subkelompok_kode');
            $table->index('hs_code');

            /*
            |--------------------------------------------------------------------------
            | MENCEGAH DUPLIKAT HS CODE
            |--------------------------------------------------------------------------
            */

            $table->unique('hs_code');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel data_hs_code.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_hs_code');
    }
};