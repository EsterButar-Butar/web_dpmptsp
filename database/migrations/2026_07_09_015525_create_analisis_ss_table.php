<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_ss', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('provinsi_id')
                ->nullable()
                ->constrained('provinsi')
                ->nullOnDelete();

            $table->foreignId('kabupaten_id')
                ->nullable()
                ->constrained('kabupaten')
                ->nullOnDelete();

            $table->foreignId('sektor_id')
                ->constrained('sektor')
                ->restrictOnDelete();

            $table->enum('tingkat_wilayah', [
                'Kabupaten/Kota',
                'Provinsi'
            ]);

            $table->string(
                'daerah_analisis',
                100
            );

            $table->string(
                'daerah_pembanding',
                100
            );

            $table->integer('tahun_awal');
            $table->integer('tahun_akhir');

            $table->decimal(
                'pdrb_sektor_analisis_awal',
                20,
                2
            );

            $table->decimal(
                'pdrb_sektor_analisis_akhir',
                20,
                2
            );

            $table->decimal(
                'pdrb_sektor_pembanding_awal',
                20,
                2
            );

            $table->decimal(
                'pdrb_sektor_pembanding_akhir',
                20,
                2
            );

            $table->decimal(
                'total_pdrb_pembanding_awal',
                20,
                2
            );

            $table->decimal(
                'total_pdrb_pembanding_akhir',
                20,
                2
            );

            $table->decimal('rij', 15, 6);
            $table->decimal('rin', 15, 6);
            $table->decimal('rn', 15, 6);

            $table->decimal('nij', 20, 4);
            $table->decimal('mij', 20, 4);
            $table->decimal('cij', 20, 4);
            $table->decimal('dij', 20, 4);

            $table->string(
                'status_pertumbuhan',
                100
            );

            $table->string(
                'status_daya_saing',
                100
            );

            $table->timestamps();

            $table->index([
                'kabupaten_id',
                'sektor_id',
                'tahun_awal',
                'tahun_akhir'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_ss');
    }
};