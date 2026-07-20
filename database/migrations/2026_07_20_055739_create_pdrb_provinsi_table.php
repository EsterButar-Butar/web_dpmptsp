<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdrb_provinsi', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Sesuai dengan header CSV:
            // provinsi_id,sektor_id,tahun,nilai
            $table->string('provinsi_id', 2);

            $table->unsignedSmallInteger('sektor_id');

            $table->year('tahun');

            $table->bigInteger('nilai');

            $table->timestamps();

            // Index
            $table->index('provinsi_id');
            $table->index('sektor_id');
            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdrb_provinsi');
    }
};