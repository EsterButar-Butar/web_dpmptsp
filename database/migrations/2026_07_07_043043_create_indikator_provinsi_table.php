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

            $table->unsignedBigInteger('provinsi_id');
            $table->unsignedBigInteger('sektor_id');

            $table->integer('tahun');

            $table->decimal('pertumbuhan', 15, 10);
            $table->decimal('kontribusi', 15, 10);

            $table->timestamps();

            // Foreign Key
            $table->foreign('provinsi_id')
                ->references('provinsi_id')
                ->on('provinsi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('sektor_id')
                ->references('sektor_id')
                ->on('sektor')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Unique Constraint
            $table->unique([
                'provinsi_id',
                'sektor_id',
                'tahun',
            ]);

            // Index
            $table->index('provinsi_id');
            $table->index('sektor_id');
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_provinsi');
    }
};