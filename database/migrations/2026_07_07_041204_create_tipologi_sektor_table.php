<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipologi_sektor', function (Blueprint $table) {

            $table->id();


            $table->foreignId('hasil_lq_id')
                ->constrained('hasil_lq')
                ->cascadeOnDelete();


            $table->foreignId('hasil_ssa_id')
                ->constrained('hasil_ssa')
                ->cascadeOnDelete();


            $table->integer('tahun_awal');


            $table->integer('tahun_akhir');


            $table->string(
                'kategori_tipologi_sektor'
            );


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tipologi_sektor');
    }
};