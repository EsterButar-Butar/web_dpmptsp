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

            $table->integer('tahun');

            $table->decimal('pdrb_sektor_analisis', 25, 2);
            $table->decimal('total_pdrb_analisis', 25, 2);

            $table->decimal('pdrb_sektor_pembanding', 25, 2);
            $table->decimal('total_pdrb_pembanding', 25, 2);

            $table->decimal('nilai_lq', 15, 6);

            $table->string('kategori', 50);

            $table->text('keterangan')->nullable();

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

            // Unique
            $table->unique([
                'user_id',
                'sektor_id',
                'tahun',
            ]);

            // Index
            $table->index('user_id');
            $table->index('provinsi_id');
            $table->index('kabupaten_id');
            $table->index('sektor_id');
            $table->index('tahun');
            $table->index('tingkat_wilayah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_lq');
    }
};