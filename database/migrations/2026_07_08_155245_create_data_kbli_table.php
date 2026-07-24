<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel data_kbli.
     */
    public function up(): void
    {
        Schema::create('data_kbli', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | DATA KBLI
            |--------------------------------------------------------------------------
            */

            // Nomor urut
            $table->integer('no')->nullable();

            // Kode KBLI
            $table->text('kode');

            // Judul KBLI
            $table->text('judul');

            // Cakupan
            $table->text('cakupan')->nullable();

            // Tidak termasuk cakupan
            $table->text('tidak_cakupan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('kode');
            $table->index('no');

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Menghapus tabel data_kbli.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kbli');
    }
};