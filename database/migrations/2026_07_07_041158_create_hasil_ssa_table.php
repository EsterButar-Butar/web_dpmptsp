<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel hasil_ssa.
     */
    public function up(): void
    {
        Schema::create('hasil_ssa', function (Blueprint $table) {

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
            | DATA SSA
            |--------------------------------------------------------------------------
            */

            $table->integer('tahun');

            $table->decimal('rn', 20, 5);

            $table->decimal('rin', 20, 5);

            $table->decimal('rij', 20, 5);

            $table->decimal('mij', 20, 5);

            $table->decimal('cij', 20, 5);

            $table->decimal('nij', 20, 5)->nullable();

            $table->decimal('dij', 20, 5)->nullable();

            $table->string('kategori_pertumbuhan', 100);

            $table->string('kategori_daya_saing', 100);

            $table->text('periode')->nullable();

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

            $table->index('kab_id');
            $table->index('sektor_id');
            $table->index('tahun');
            $table->index('kategori_pertumbuhan');
            $table->index('kategori_daya_saing');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel hasil_ssa.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_ssa');
    }
};