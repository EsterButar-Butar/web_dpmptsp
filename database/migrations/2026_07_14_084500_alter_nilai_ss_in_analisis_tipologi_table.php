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
        Schema::table('analisis_tipologi', function (Blueprint $table) {
            $table->decimal('nilai_ss', 25, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisis_tipologi', function (Blueprint $table) {
            $table->decimal('nilai_ss', 15, 6)->change();
        });
    }
};
