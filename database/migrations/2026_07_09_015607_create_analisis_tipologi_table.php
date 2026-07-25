<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_tipologi', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedBigInteger('provinsi_id')->nullable();
            $table->unsignedBigInteger('kabupaten_id')->nullable();
            $table->unsignedBigInteger('sektor_id');

            $table->enum('tingkat_wilayah', [
                'Kabupaten/Kota',
                'Provinsi',
            ]);

            $table->string('daerah_analisis', 100);
            $table->string('daerah_pembanding', 100);

            $table->integer('tahun_awal');
            $table->integer('tahun_akhir');

            $table->decimal('pdrb_sektor_analisis_awal', 20, 2);
            $table->decimal('pdrb_sektor_analisis_akhir', 20, 2);

            $table->decimal('total_pdrb_analisis_awal', 20, 2);
            $table->decimal('total_pdrb_analisis_akhir', 20, 2);

            $table->decimal('pdrb_sektor_pembanding_awal', 20, 2);
            $table->decimal('pdrb_sektor_pembanding_akhir', 20, 2);

            $table->decimal('total_pdrb_pembanding_awal', 20, 2);
            $table->decimal('total_pdrb_pembanding_akhir', 20, 2);

            $table->decimal('nilai_ss', 15, 6);
            $table->decimal('nilai_lq', 15, 6);

            $table->string('tipologi', 100);

            $table->timestamps();

            // Foreign Key
            $table->foreign('provinsi_id')
                ->references('provinsi_id')
                ->on('provinsi')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('kabupaten_id')
                ->references('kab_id')
                ->on('kabupaten')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('sektor_id')
                ->references('sektor_id')
                ->on('sektor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Index
            $table->index('user_id');
            $table->index('provinsi_id');
            $table->index('kabupaten_id');
            $table->index('sektor_id');
            $table->index('tahun_awal');
            $table->index('tahun_akhir');
            $table->index('tingkat_wilayah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_tipologi');
    }
};