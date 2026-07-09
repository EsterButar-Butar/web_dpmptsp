<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'import_histories',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->string(
                    'jenis_data',
                    100
                );

                $table->string(
                    'nama_file',
                    255
                );

                $table->integer(
                    'jumlah_baris'
                )->default(0);

                $table->integer(
                    'berhasil'
                )->default(0);

                $table->integer(
                    'gagal'
                )->default(0);

                $table->enum('status', [
                    'processing',
                    'success',
                    'failed'
                ]);

                $table->text(
                    'error_message'
                )->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'import_histories'
        );
    }
};