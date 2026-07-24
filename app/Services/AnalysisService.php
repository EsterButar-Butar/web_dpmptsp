<?php

namespace App\Services;

use App\Models\Kabupaten;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
    public function __construct(
        protected LqService $lqService,
        protected SsaService $ssaService,
        protected IndikatorService $indikatorService,
        protected TipologiSektorService $tipologiSektorService,
        protected TipologiKlassenService $tipologiKlassenService
    ) {
    }

    /**
     * ==========================================================
     * Generate Analisis
     * Satu Kabupaten
     * Satu Tahun
     * ==========================================================
     */
    public function generateKabupaten(

        int $kabId,

        int $tahun

    ): void {

        DB::transaction(function () use (

            $kabId,

            $tahun,

        ) {

            /**
             * 1
             * Location Quotient
             */

            $this->lqService
                ->generate(
                    $kabId,
                    $tahun
                );

            /**
             * 2
             * Shift Share
             */

            $this->ssaService
                ->generate(
                    $kabId,
                    $tahun
                );

            /**
             * 3
             * Indikator
             */

            $this->indikatorService
                ->generate(
                    $kabId,
                    $tahun
                );

            /**
             * 4
             * Tipologi Sektor
             */

            $this->tipologiSektorService
                ->generate(
                    $kabId,
                    $tahun
                );

            /**
             * 5
             * Tipologi Klassen
             */
            
            $this->tipologiKlassenService
                ->generate(
                    $kabId,
                    $tahun
                );

        });

    }

    /**
     * ==========================================================
     * Generate Seluruh Kabupaten
     * Satu Tahun
     * ==========================================================
     */
    public function generateSemuaKabupaten(

        int $tahun

    ): void {

        Kabupaten::query()

            ->select('kab_id')

            ->orderBy('kab_id')

            ->chunk(

                20,

                function ($kabupaten) use (

                    $tahun

                ) {

                    foreach (

                        $kabupaten

                        as

                        $kab

                    ) {

                        $this->generateKabupaten(

                            $kab->kab_id,

                            $tahun

                        );

                    }

                }

            );

    }

    /**
     * ==========================================================
     * Generate
     * Semua Tahun
     * Satu Kabupaten
     * ==========================================================
     */
    public function generateSemuaTahun(

        int $kabId

    ): void {


        foreach (

        range(2021, 2025)

            as

            $tahun

        ) {

            $this->generateKabupaten(

                $kabId,

                $tahun

            );

        }

    }

    /**
     * ==========================================================
     * Generate Semua
     * ==========================================================
     */
    public function generateSemua(): void
    {

        foreach (

            range(2021, 2025)

            as

            $tahun

        ) {

            $this->generateSemuaKabupaten(

                $tahun

            );

        }

    }

}