<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel data_wilayah.
     */
    public function up(): void
    {
        Schema::create('data_wilayah', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | DATA PROVINSI
            |--------------------------------------------------------------------------
            */

            $table->string('nama_provinsi', 255);

            $table->string('kode_provinsi', 20);


            /*
            |--------------------------------------------------------------------------
            | DATA KABUPATEN / KOTA
            |--------------------------------------------------------------------------
            */

            $table->string('nama_kabupaten', 255);

            $table->string('kode_kabupaten', 20);


            /*
            |--------------------------------------------------------------------------
            | DATA KECAMATAN
            |--------------------------------------------------------------------------
            */

            $table->string('nama_kecamatan', 255);

            $table->string('kode_kecamatan', 20);


            /*
            |--------------------------------------------------------------------------
            | DATA DESA / KELURAHAN
            |--------------------------------------------------------------------------
            */

            $table->string('nama_desa', 255);

            $table->string('kode_desa', 30)
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | STATUS DATA
            |--------------------------------------------------------------------------
            */

            $table->string('status', 20)
                ->default('Aktif');

            $table->text('keterangan')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('kode_provinsi');

            $table->index('kode_kabupaten');

            $table->index('kode_kecamatan');

            $table->index('nama_provinsi');

            $table->index('nama_kabupaten');

            $table->index('nama_kecamatan');

            $table->index('nama_desa');

            $table->index('status');
        });
    }


    /**
     * Menghapus tabel data_wilayah.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_wilayah');
    }
};