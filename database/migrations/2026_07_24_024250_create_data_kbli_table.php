<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_kbli', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('struktur', 20);
            $table->unsignedSmallInteger('level');
            $table->string('kode', 5)->unique();
            $table->string('kode_induk', 5)->nullable();
            $table->string('kategori_kode', 1)->nullable();
            $table->string('golongan_pokok_kode', 2)->nullable();
            $table->string('golongan_kode', 3)->nullable();
            $table->string('subgolongan_kode', 4)->nullable();
            $table->string('kelompok_kode', 5)->nullable();
            $table->text('judul');
            $table->text('cakupan')->nullable();
            $table->text('tidak_cakupan')->nullable();
            $table->string('no_asli', 20)->nullable();
            $table->string('kode_asli', 10)->nullable();
            $table->text('catatan')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index('kode_induk');
            $table->index('struktur');
            $table->index('level');
            $table->index('kategori_kode');
            $table->index('golongan_pokok_kode');
            $table->index('golongan_kode');
            $table->index('subgolongan_kode');
            $table->index('kelompok_kode');
        });

        DB::statement("
            ALTER TABLE public.data_kbli
            ADD CONSTRAINT data_kbli_level_check
            CHECK (level BETWEEN 1 AND 5)
        ");

        DB::statement("
            ALTER TABLE public.data_kbli
            ADD CONSTRAINT data_kbli_structure_check
            CHECK (
                (struktur = 'Kategori' AND level = 1 AND kode ~ '^[A-V]$')
                OR
                (struktur = 'Golongan Pokok' AND level = 2 AND kode ~ '^[0-9]{2}$')
                OR
                (struktur = 'Golongan' AND level = 3 AND kode ~ '^[0-9]{3}$')
                OR
                (struktur = 'Subgolongan' AND level = 4 AND kode ~ '^[0-9]{4}$')
                OR
                (struktur = 'Kelompok' AND level = 5 AND kode ~ '^[0-9]{5}$')
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kbli');
    }
};