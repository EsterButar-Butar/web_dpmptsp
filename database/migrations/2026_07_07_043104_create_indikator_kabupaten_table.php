<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel indikator_kabupaten.
     */
    public function up(): void
    {
        Schema::create('indikator_kabupaten', function (Blueprint $table) {

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

            $table->foreignId('kab_id')
                ->constrained('kabupaten')
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

            $table->decimal('pertumbuhan', 10, 5);

            $table->decimal('kontribusi', 10, 5);

            /*
            |--------------------------------------------------------------------------
            | UNIQUE
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'kab_id',
                'sektor_id',
                'tahun'
            ]);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('kab_id');
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
     * Menghapus tabel indikator_kabupaten.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_kabupaten');
    }
};