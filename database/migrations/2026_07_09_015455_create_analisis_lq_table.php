<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_lq', function (Blueprint $table) {
            $table->id();

            // User/operator yang menjalankan analisis
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Referensi master
            $table->foreignId('provinsi_id')
                ->nullable()
                ->constrained('provinsi')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('kabupaten_id')
                ->nullable()
                ->constrained('kabupaten')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('tingkat_wilayah', [
                'Kabupaten/Kota',
                'Provinsi'
            ]);

            $table->string('daerah_analisis', 100);
            $table->string('daerah_pembanding', 100);

            $table->integer('tahun');

            $table->decimal(
                'pdrb_sektor_analisis',
                20,
                2
            );

            $table->decimal(
                'total_pdrb_analisis',
                20,
                2
            );

            $table->decimal(
                'pdrb_sektor_pembanding',
                20,
                2
            );

            $table->decimal(
                'total_pdrb_pembanding',
                20,
                2
            );

            $table->decimal(
                'nilai_lq',
                15,
                6
            );

            $table->string('kategori', 50);

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            $table->index([
                'kabupaten_id',
                'sektor_id',
                'tahun'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_lq');
    }
};