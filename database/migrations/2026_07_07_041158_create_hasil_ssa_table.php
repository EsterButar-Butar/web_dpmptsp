<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_ssa', function (Blueprint $table) {

            $table->id();


            $table->foreignId('kabupaten_id')
                ->constrained('kabupaten')
                ->cascadeOnDelete();


            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnDelete();


            $table->integer('tahun_awal');


            $table->integer('tahun_akhir');


            $table->decimal('rn', 20, 5);


            $table->decimal('rin', 20, 5);


            $table->decimal('rij', 20, 5);


            $table->decimal('mij', 20, 5);


            $table->decimal('cij', 20, 5);


            $table->string('kategori_pertumbuhan');


            $table->string('kategori_daya_saing');


            $table->unique([
                'kabupaten_id',
                'sektor_id',
                'tahun_awal',
                'tahun_akhir'
            ]);


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('hasil_ssa');
    }
};