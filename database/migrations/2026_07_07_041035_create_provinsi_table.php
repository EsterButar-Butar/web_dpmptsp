<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel provinsi.
     */
    public function up(): void
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->timestamps();
            $table->id('provinsi_id');
            $table->string('nama_provinsi', 255);
            $table->index('nama_provinsi');
        });
    }

    /**
     * Menghapus tabel provinsi.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinsi');
    }
};
