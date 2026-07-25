<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel kabupaten.
     */
    public function up(): void
    {
        Schema::create('kabupaten', function (Blueprint $table) {

            // Primary Key
            $table->id('kab_id');

            // Foreign Key ke tabel provinsi
            $table->unsignedBigInteger('provinsi_id');

            // Data kabupaten
            $table->string('nama_kabupaten', 255);

            // Timestamp
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('provinsi_id')
                ->references('provinsi_id')
                ->on('provinsi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Index
            $table->index('provinsi_id');
            $table->index('nama_kabupaten');
        });
    }

    /**
     * Menghapus tabel kabupaten.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupaten');
    }
};