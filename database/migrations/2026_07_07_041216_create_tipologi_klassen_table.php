<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipologi_klassen', function (Blueprint $table) {

            $table->id();


            $table->foreignId('indikator_provinsi_id')
                ->constrained('indikator_provinsi')
                ->cascadeOnDelete();


            $table->foreignId('indikator_kabupaten_id')
                ->constrained('indikator_kabupaten')
                ->cascadeOnDelete();


            $table->integer('tahun_awal');


            $table->integer('tahun_akhir');


            $table->decimal(
                'rata_pertumbuhan_provinsi',
                10,
                5
            );


            $table->decimal(
                'rata_kontribusi_provinsi',
                10,
                5
            );


            $table->decimal(
                'rata_pertumbuhan_kabupaten',
                10,
                5
            );


            $table->decimal(
                'rata_kontribusi_kabupaten',
                10,
                5
            );


            $table->string('kuadran');


            $table->string(
                'kategori_tipologi_klassen'
            );


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tipologi_klassen');
    }
};