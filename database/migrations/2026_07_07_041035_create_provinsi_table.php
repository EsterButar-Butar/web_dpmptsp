<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provinsi', function (Blueprint $table) {
            $table->string('kode_provinsi', 20)
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('status', 20)
                ->default('Aktif')
                ->after('nama_provinsi');
        });
    }

    public function down(): void
    {
        Schema::table('provinsi', function (Blueprint $table) {
            $table->dropUnique(['kode_provinsi']);
            $table->dropColumn([
                'kode_provinsi',
                'status'
            ]);
        });
    }
};