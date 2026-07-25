<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelurahan_desa', function (Blueprint $table) {
            // Kode resmi desa/kelurahan, contoh 1101012001
            $table->bigInteger('desa_id')->primary();

            // Harus sama tipe dengan kecamatan.kec_id
            $table->bigInteger('kec_id');

            $table->string('nama_kelurahan_desa', 150);

            $table->string('jenis', 20)->nullable();

            $table->timestamps();

            $table->foreign('kec_id')
                ->references('kec_id')
                ->on('kecamatan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->index('kec_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelurahan_desa');
    }
};