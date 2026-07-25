<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('data_kbli')) {
            DB::statement("
                ALTER TABLE public.data_kbli
                ADD CONSTRAINT data_kbli_parent_fk
                FOREIGN KEY (kode_induk)
                REFERENCES public.data_kbli(kode)
                ON UPDATE CASCADE
                ON DELETE RESTRICT
                DEFERRABLE INITIALLY DEFERRED
            ");
        }

        if (Schema::hasTable('data_kbki')) {
            DB::statement("
                ALTER TABLE public.data_kbki
                ADD CONSTRAINT data_kbki_parent_fk
                FOREIGN KEY (kode_induk)
                REFERENCES public.data_kbki(kode)
                ON UPDATE CASCADE
                ON DELETE RESTRICT
                DEFERRABLE INITIALLY DEFERRED
            ");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('data_kbli')) {
            DB::statement("
                ALTER TABLE public.data_kbli
                DROP CONSTRAINT IF EXISTS data_kbli_parent_fk
            ");
        }

        if (Schema::hasTable('data_kbki')) {
            DB::statement("
                ALTER TABLE public.data_kbki
                DROP CONSTRAINT IF EXISTS data_kbki_parent_fk
            ");
        }
    }
};