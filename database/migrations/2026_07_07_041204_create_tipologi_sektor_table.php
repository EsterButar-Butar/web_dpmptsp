<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_tipologi_sektor', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('hasil_lq_id');
            $table->unsignedBigInteger('hasil_ssa_id');
            $table->unsignedBigInteger('kab_id');
            $table->unsignedBigInteger('sektor_id');

            $table->integer('tahun');

            $table->decimal('lq', 10, 5);
            $table->decimal('cij', 20, 5);

            $table->string('kuadran', 30);

            $table->timestamps();

            // Foreign Key
            $table->foreign('hasil_lq_id')
                ->references('hasil_lq_id')
                ->on('hasil_lq')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('hasil_ssa_id')
                ->references('hasil_ssa_id')
                ->on('hasil_ssa')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

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
            $table->index('hasil_lq_id');
            $table->index('hasil_ssa_id');
            $table->index('kab_id');
            $table->index('sektor_id');
            $table->index('tahun');
            $table->index('kuadran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_tipologi_sektor');
    }
};