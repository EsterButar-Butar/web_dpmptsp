<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_kbki', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('struktur', 24);
            $table->unsignedSmallInteger('level');
            $table->unsignedSmallInteger('jumlah_digit');
            $table->string('kode', 10)->unique();
            $table->string('kode_induk', 10)->nullable();
            $table->string('seksi_kode', 1)->nullable();
            $table->string('divisi_kode', 2)->nullable();
            $table->string('kelompok_kode', 3)->nullable();
            $table->string('kelas_kode', 4)->nullable();
            $table->string('subkelas_kode', 5)->nullable();
            $table->string('kelompok_komoditas_kode', 7)->nullable();
            $table->string('komoditas_kode', 10)->nullable();
            $table->text('judul');
            $table->integer('halaman')->nullable();
            $table->string('sumber_sheet', 50)->nullable();
            $table->integer('baris_asli')->nullable();
            $table->string('kode_asli', 10)->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 10)->default('Aktif');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index('kode_induk');
            $table->index('struktur');
            $table->index('level');
            $table->index('seksi_kode');
            $table->index('divisi_kode');
            $table->index('kelompok_kode');
            $table->index('kelas_kode');
            $table->index('subkelas_kode');
            $table->index('kelompok_komoditas_kode');
            $table->index('komoditas_kode');
            $table->index('status');
        });

        DB::statement("
            ALTER TABLE public.data_kbki
            ADD CONSTRAINT data_kbki_level_check
            CHECK (level BETWEEN 1 AND 7)
        ");

        DB::statement("
            ALTER TABLE public.data_kbki
            ADD CONSTRAINT data_kbki_digit_check
            CHECK (jumlah_digit IN (1, 2, 3, 4, 5, 7, 10))
        ");

        DB::statement("
            ALTER TABLE public.data_kbki
            ADD CONSTRAINT data_kbki_status_check
            CHECK (status IN ('Aktif', 'Nonaktif'))
        ");

        DB::statement("
            ALTER TABLE public.data_kbki
            ADD CONSTRAINT data_kbki_structure_check
            CHECK (
                (struktur = 'Seksi' AND level = 1 AND jumlah_digit = 1 AND kode ~ '^[0-9]{1}$')
                OR
                (struktur = 'Divisi' AND level = 2 AND jumlah_digit = 2 AND kode ~ '^[0-9]{2}$')
                OR
                (struktur = 'Kelompok' AND level = 3 AND jumlah_digit = 3 AND kode ~ '^[0-9]{3}$')
                OR
                (struktur = 'Kelas' AND level = 4 AND jumlah_digit = 4 AND kode ~ '^[0-9]{4}$')
                OR
                (struktur = 'Subkelas' AND level = 5 AND jumlah_digit = 5 AND kode ~ '^[0-9]{5}$')
                OR
                (struktur = 'Kelompok Komoditas' AND level = 6 AND jumlah_digit = 7 AND kode ~ '^[0-9]{7}$')
                OR
                (struktur = 'Komoditas' AND level = 7 AND jumlah_digit = 10 AND kode ~ '^[0-9]{10}$')
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kbki');
    }
};