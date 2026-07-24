<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel tipologi_klassen.
     */
    public function up(): void
    {
        Schema::create('tipologi_klassen', function (Blueprint $table) {

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

            $table->foreignId('indikator_provinsi_id')
                ->constrained('indikator_provinsi')
                ->cascadeOnDelete();

            $table->foreignId('indikator_kabupaten_id')
                ->constrained('indikator_kabupaten')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA TIPOLOGI KLASSEN
            |--------------------------------------------------------------------------
            */

            $table->integer('tahun');

            $table->decimal('pertumbuhan_kabupaten', 10, 5);

            $table->decimal('kontribusi_kabupaten', 10, 5);

            $table->decimal('pertumbuhan_provinsi', 10, 5);

            $table->decimal('kontribusi_provinsi', 10, 5);

            $table->string('kuadran', 30);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('indikator_provinsi_id');
            $table->index('indikator_kabupaten_id');
            $table->index('tahun');
            $table->index('kuadran');

            /*
            |--------------------------------------------------------------------------
            | MENCEGAH DUPLIKAT
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'indikator_provinsi_id',
                'indikator_kabupaten_id',
                'tahun'
            ]);

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel tipologi_klassen.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipologi_klassen');
    }
};