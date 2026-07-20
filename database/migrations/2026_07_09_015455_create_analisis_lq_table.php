<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel analisis_lq.
     */
    public function up(): void
    {
        Schema::create('analisis_lq', function (Blueprint $table) {

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
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('provinsi_id')
                ->nullable()
                ->constrained('provinsi')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('kabupaten_id')
                ->nullable()
                ->constrained('kabupaten')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA ANALISIS
            |--------------------------------------------------------------------------
            */

            $table->string('tingkat_wilayah', 30);

            $table->string('daerah_analisis', 100);

            $table->string('daerah_pembanding', 100);

            $table->integer('tahun');

            /*
            |--------------------------------------------------------------------------
            | NILAI PDRB
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'pdrb_sektor_analisis',
                25,
                2
            );

            $table->decimal(
                'total_pdrb_analisis',
                25,
                2
            );

            $table->decimal(
                'pdrb_sektor_pembanding',
                25,
                2
            );

            $table->decimal(
                'total_pdrb_pembanding',
                25,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | HASIL LQ
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'nilai_lq',
                15,
                6
            );

            $table->string(
                'kategori',
                50
            );

            $table->text('keterangan')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'user_id',
                'sektor_id',
                'tahun'
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
            $table->index('tahun');
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
     * Menghapus tabel analisis_lq.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_lq');
    }
};