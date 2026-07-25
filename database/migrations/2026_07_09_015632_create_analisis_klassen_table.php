<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_klassen', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedBigInteger('provinsi_id')->nullable();
            $table->unsignedBigInteger('kabupaten_id')->nullable();
            $table->unsignedBigInteger('sektor_id');

            $table->string('tingkat_wilayah', 30);

            $table->string('daerah_analisis', 100);
            $table->string('daerah_pembanding', 100);

            $table->integer('tahun_awal');
            $table->integer('tahun_akhir');

            $table->decimal('pdrb_sektor_analisis_awal', 25, 2);
            $table->decimal('pdrb_sektor_analisis_akhir', 25, 2);

            $table->decimal('total_pdrb_analisis_awal', 25, 2);
            $table->decimal('total_pdrb_analisis_akhir', 25, 2);

            $table->decimal('pdrb_sektor_pembanding_awal', 25, 2);
            $table->decimal('pdrb_sektor_pembanding_akhir', 25, 2);

            $table->decimal('total_pdrb_pembanding_awal', 25, 2);
            $table->decimal('total_pdrb_pembanding_akhir', 25, 2);

            $table->decimal('ri', 15, 6);
            $table->decimal('r', 15, 6);

            $table->decimal('yi', 15, 6);
            $table->decimal('y', 15, 6);

            $table->string('klasifikasi', 150);

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

            // Unique Constraint
            $table->unique([
                'user_id',
                'sektor_id',
                'tahun_awal',
                'tahun_akhir',
            ]);

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
        Schema::dropIfExists('analisis_klassen');
    }
};