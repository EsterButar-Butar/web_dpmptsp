<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kabupaten', function (Blueprint $table) {
            $table->string('kode_kabupaten', 20)
                ->nullable()
                ->unique()
                ->after('provinsi_id');

            $table->string('status', 20)
                ->default('Aktif')
                ->after('nama_kabupaten');
        });
    }

    public function down(): void
    {
        Schema::table('kabupaten', function (Blueprint $table) {
            $table->dropUnique(['kode_kabupaten']);
            $table->dropColumn([
                'kode_kabupaten',
                'status'
            ]);
        });
    }
};