<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            // Kode resmi kecamatan, contoh 110101
            $table->bigInteger('kec_id')->primary();

            // Harus sama tipe dengan kabupaten.kab_id
            $table->bigInteger('kab_id');

            $table->string('nama_kecamatan', 150);

            $table->timestamps();

            $table->foreign('kab_id')
                ->references('kab_id')
                ->on('kabupaten')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->index('kab_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kecamatan');
    }
};