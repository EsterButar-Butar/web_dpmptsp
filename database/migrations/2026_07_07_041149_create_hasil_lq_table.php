<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_lq', function (Blueprint $table) {

            $table->id();


            $table->foreignId('kabupaten_id')
                ->constrained('kabupaten')
                ->cascadeOnDelete();


            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnDelete();


            $table->integer('tahun');


            $table->decimal(
                'nilai_lq',
                10,
                5
            );


            $table->string('kategori');


            $table->unique([
                'kabupaten_id',
                'sektor_id',
                'tahun'
            ]);


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('hasil_lq');
    }
};