<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analisis_ss', function (Blueprint $table) {
            if (Schema::hasColumn('analisis_ss', 'total_pdrb_analisis_awal')) {
                $table->dropColumn('total_pdrb_analisis_awal');
            }
            if (Schema::hasColumn('analisis_ss', 'total_pdrb_analisis_akhir')) {
                $table->dropColumn('total_pdrb_analisis_akhir');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisis_ss', function (Blueprint $table) {
            if (!Schema::hasColumn('analisis_ss', 'total_pdrb_analisis_awal')) {
                $table->decimal('total_pdrb_analisis_awal', 20, 2)->nullable();
            }
            if (!Schema::hasColumn('analisis_ss', 'total_pdrb_analisis_akhir')) {
                $table->decimal('total_pdrb_analisis_akhir', 20, 2)->nullable();
            }
        });
    }
};
