<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_provinsi', function (Blueprint $table) {

            $table->id();


            $table->foreignId('provinsi_id')
                ->constrained('provinsi')
                ->cascadeOnDelete();


            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnDelete();


            $table->integer('tahun');


            $table->decimal(
                'pertumbuhan',
                10,
                5
            );


            $table->decimal(
                'kontribusi',
                10,
                5
            );


            $table->unique([
                'provinsi_id',
                'sektor_id',
                'tahun'
            ]);


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('indikator_provinsi');
    }
};