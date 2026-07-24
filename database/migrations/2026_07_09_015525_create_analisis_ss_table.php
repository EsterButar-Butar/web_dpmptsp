<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel analisis_ss.
     */
    public function up(): void
    {
        Schema::create('analisis_ss', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELASI
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('provinsi_id')
                ->nullable()
                ->constrained('provinsi')
                ->nullOnDelete();

            $table->foreignId('kabupaten_id')
                ->nullable()
                ->constrained('kabupaten')
                ->nullOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA ANALISIS
            |--------------------------------------------------------------------------
            */

            $table->string('tingkat_wilayah', 30);

            $table->string('daerah_analisis', 100);

            $table->string('daerah_pembanding', 100);

            $table->integer('tahun_awal');

            $table->integer('tahun_akhir');

            /*
            |--------------------------------------------------------------------------
            | PDRB DAERAH ANALISIS
            |--------------------------------------------------------------------------
            */

            $table->decimal('pdrb_sektor_analisis_awal', 25, 2);

            $table->decimal('pdrb_sektor_analisis_akhir', 25, 2);

            $table->decimal('total_pdrb_analisis_awal', 25, 2);

            $table->decimal('total_pdrb_analisis_akhir', 25, 2);

            /*
            |--------------------------------------------------------------------------
            | PDRB DAERAH PEMBANDING
            |--------------------------------------------------------------------------
            */

            $table->decimal('pdrb_sektor_pembanding_awal', 25, 2);

            $table->decimal('pdrb_sektor_pembanding_akhir', 25, 2);

            $table->decimal('total_pdrb_pembanding_awal', 25, 2);

            $table->decimal('total_pdrb_pembanding_akhir', 25, 2);

            /*
            |--------------------------------------------------------------------------
            | HASIL PERHITUNGAN SHIFT SHARE
            |--------------------------------------------------------------------------
            */

            $table->decimal('rij', 15, 6);

            $table->decimal('rin', 15, 6);

            $table->decimal('rn', 15, 6);

            $table->decimal('nij', 30, 10);

            $table->decimal('mij', 30, 10);

            $table->decimal('cij', 30, 10);

            $table->decimal('dij', 30, 10);

            /*
            |--------------------------------------------------------------------------
            | HASIL ANALISIS
            |--------------------------------------------------------------------------
            */

            $table->string('status_pertumbuhan', 100);

            $table->string('status_daya_saing', 100);

            /*
            |--------------------------------------------------------------------------
            | UNIQUE
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'user_id',
                'sektor_id',
                'tahun_awal',
                'tahun_akhir'
            ]);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');
            $table->index('provinsi_id');
            $table->index('kabupaten_id');
            $table->index('sektor_id');
            $table->index('tahun_awal');
            $table->index('tahun_akhir');
            $table->index('tingkat_wilayah');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel analisis_ss.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_ss');
    }
};