<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_ssa', function (Blueprint $table) {

            $table->id('hasil_ssa_id');

            $table->unsignedBigInteger('kab_id');
            $table->unsignedBigInteger('sektor_id');

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

            $table->timestamps();

            // Foreign Key
            $table->foreign('kab_id')
                ->references('kab_id')
                ->on('kabupaten')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('sektor_id')
                ->references('sektor_id')
                ->on('sektor')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Unique Constraint
            $table->unique([
                'kab_id',
                'sektor_id',
                'tahun',
            ]);

            // Index
            $table->index('kab_id');
            $table->index('sektor_id');
            $table->index('tahun');
            $table->index('kategori_pertumbuhan');
            $table->index('kategori_daya_saing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_ssa');
    }
};