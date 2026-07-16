<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalysisService;

class GenerateAnalysisCommand extends Command
{
    /**
     * Signature
     */
    protected $signature = '

        analysis:generate

        {--kabupaten= : ID Kabupaten}

        {--tahun= : Tahun}

        {--all : Generate seluruh data}

    ';

    /**
     * Description
     */
    protected $description =
        'Generate hasil analisis ekonomi';

    public function __construct(

        protected AnalysisService $analysisService

    ) {

        parent::__construct();

    }

    public function handle(): int
    {

        $kabupaten =

            $this->option('kabupaten');

        $tahun =

            $this->option('tahun');

        $all =

            $this->option('all');

        $this->info('');

        $this->info('===================================');

        $this->info(' Generate Analisis');

        $this->info('===================================');

        /**
         * -----------------------------------------
         * Semua
         * -----------------------------------------
         */

        if ($all) {

            $this->analysisService
                ->generateSemua();

            $this->info('');

            $this->info('Selesai.');

            return self::SUCCESS;

        }

        /**
         * -----------------------------------------
         * Kabupaten + Tahun
         * -----------------------------------------
         */

        if ($kabupaten && $tahun) {

            $this->analysisService
                ->generateKabupaten(

                    (int) $kabupaten,

                    (int) $tahun

                );

            $this->info('');

            $this->info('Selesai.');

            return self::SUCCESS;

        }

        /**
         * -----------------------------------------
         * Kabupaten
         * -----------------------------------------
         */

        if ($kabupaten) {

            $this->analysisService
                ->generateSemuaTahun(

                    (int) $kabupaten

                );

            $this->info('');

            $this->info('Selesai.');

            return self::SUCCESS;

        }

        /**
         * -----------------------------------------
         * Tahun
         * -----------------------------------------
         */

        if ($tahun) {

            $this->analysisService
                ->generateSemuaKabupaten(

                    (int) $tahun

                );

            $this->info('');

            $this->info('Selesai.');

            return self::SUCCESS;

        }

        $this->error(

            'Gunakan --all atau --kabupaten atau --tahun.'

        );

        return self::FAILURE;

    }

}