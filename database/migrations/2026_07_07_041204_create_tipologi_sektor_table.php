<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel tipologi_sektor.
     */
    public function up(): void
    {
        Schema::create('tipologi_sektor', function (Blueprint $table) {

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

            $table->foreignId('hasil_lq_id')
                ->constrained('hasil_lq')
                ->cascadeOnDelete();

            $table->foreignId('hasil_ssa_id')
                ->constrained('hasil_ssa')
                ->cascadeOnDelete();

            $table->foreignId('kab_id')
                ->constrained('kabupaten')
                ->cascadeOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATA TIPOLOGI SEKTOR
            |--------------------------------------------------------------------------
            */

            $table->integer('tahun');

            $table->decimal('lq', 10, 5);

            $table->decimal('cij', 20, 5);

            $table->string('kuadran', 30);

            /*
            |--------------------------------------------------------------------------
            | MENCEGAH DUPLIKAT
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

            $table->index('hasil_lq_id');
            $table->index('hasil_ssa_id');
            $table->index('kab_id');
            $table->index('sektor_id');
            $table->index('tahun');
            $table->index('kuadran');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel tipologi_sektor.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipologi_sektor');
    }
};