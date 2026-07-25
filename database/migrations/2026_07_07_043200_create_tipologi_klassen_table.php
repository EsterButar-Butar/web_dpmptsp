<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_tipologi_klassen', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('indikator_provinsi_id');
            $table->unsignedBigInteger('indikator_kabupaten_id');

            $table->unsignedBigInteger('kab_id');
            $table->unsignedBigInteger('sektor_id');

            $table->integer('tahun');

            $table->string('pertumbuhan_kabupaten');
            $table->string('kontribusi_kabupaten');

            $table->string('pertumbuhan_provinsi');
            $table->string('kontribusi_provinsi');

            $table->string('kuadran', 30);

            $table->timestamps();

            // Foreign Key
            $table->foreign('indikator_provinsi_id')
                ->references('id')
                ->on('indikator_provinsi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('indikator_kabupaten_id')
                ->references('id')
                ->on('indikator_kabupaten')
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
                'indikator_provinsi_id',
                'indikator_kabupaten_id',
                'tahun',
            ]);

            // Index
            $table->index('indikator_provinsi_id');
            $table->index('indikator_kabupaten_id');
            $table->index('kab_id');
            $table->index('sektor_id');
            $table->index('tahun');
            $table->index('kuadran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_tipologi_klassen');
    }
};