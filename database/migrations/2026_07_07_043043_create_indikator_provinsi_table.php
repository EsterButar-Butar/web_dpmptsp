<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel indikator_provinsi.
     */
    public function up(): void
    {
        Schema::create('indikator_provinsi', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('provinsi_id')
                ->constrained('provinsi')
                ->cascadeOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA INDIKATOR
            |--------------------------------------------------------------------------
            */

            $table->integer('tahun');

            $table->decimal('pertumbuhan', 15, 10);

            $table->decimal('kontribusi', 15, 10);

            /*
            |--------------------------------------------------------------------------
            | UNIQUE
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'provinsi_id',
                'sektor_id',
                'tahun'
            ]);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('provinsi_id');
            $table->index('sektor_id');
            $table->index('tahun');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel indikator_provinsi.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_provinsi');
    }
};