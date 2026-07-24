<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel hasil_lq.
     */
    public function up(): void
    {
        Schema::create('hasil_lq', function (Blueprint $table) {

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

            $table->foreignId('kab_id')
                ->constrained('kabupaten')
                ->cascadeOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA LQ
            |--------------------------------------------------------------------------
            */

            $table->integer('tahun');

            $table->decimal('nilai_lq', 10, 5);

            $table->string('kategori', 30);

            /*
            |--------------------------------------------------------------------------
            | MENCEGAH DATA DUPLIKAT
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
            $table->index('kategori');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel hasil_lq.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_lq');
    }
};