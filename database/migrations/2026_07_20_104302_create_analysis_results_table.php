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
    Schema::create('analysis_results', function (Blueprint $table) {
        $table->id();
        $table->integer('tahun');
        $table->string('kabupaten_kota');
        $table->string('sektor');
        $table->double('lq');
        $table->double('ssa');
        $table->string('klassen');
        $table->string('tipologi');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
