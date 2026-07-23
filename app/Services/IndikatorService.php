<?php

namespace App\Services;

use App\Models\IndikatorKabupaten;
use App\Models\IndikatorProvinsi;

class IndikatorService extends BaseAnalysisService
{
    /**
     * ==========================================================
     * Generate indikator
     * ==========================================================
     */
    public function generate(
        int $kabId,
        int $tahun
    ): void {

        $this->generateProvinsi(
            $kabId,
            $tahun
        );

        $this->generateKabupaten(
            $kabId,
            $tahun
        );

    }

    /**
     * ==========================================================
     * INDIKATOR PROVINSI
     * ==========================================================
     */
    private function generateProvinsi(
        int $kabId,
        int $tahun
    ): void {

        $tahunLalu = $tahun - 1;

        $provinsiId = $this->getProvinsiId($kabId);

        /**
         * --------------------------------------------
         * Total PDRB Provinsi
         * --------------------------------------------
         */

        $totalProvinsi = $this->getTotalProvinsi(
            $provinsiId,
            $tahun
        );

        if ($totalProvinsi <= 0) {
            return;
        }

        /**
         * --------------------------------------------
         * Data Provinsi
         * --------------------------------------------
         */

        $provinsiSekarang = $this->getPdrbProvinsiByTahun(
            $provinsiId,
            $tahun
        )->keyBy('sektor_id');

        $provinsiLalu = $this->getPdrbProvinsiByTahun(
            $provinsiId,
            $tahunLalu
        )->keyBy('sektor_id');

        /**
         * --------------------------------------------
         * Row Upsert
         * --------------------------------------------
         */

        $rows = [];

        foreach ($provinsiSekarang as $sektorId => $sektor) {

            $sektorLalu = $provinsiLalu->get($sektorId);

            if (
                !$sektorLalu ||
                $this->hasNullValue(
                    $sektor->nilai,
                    $sektorLalu->nilai,
                    $totalProvinsi
                )
            ) {
                continue;
            }

            /**
             * Pertumbuhan
             */

            if ($this->hasNullValue(
                $sektor->nilai,
                $sektorLalu->nilai,
                $totalProvinsi
            )) {
                continue;
            }

            $pertumbuhan = $this->calculateGrowth(
                $sektor->nilai,
                $sektorLalu->nilai
            );

            /**
             * Kontribusi
             */

            $kontribusi = $this->calculateContribution(
                $sektor->nilai,
                $totalProvinsi
            );

            /**
             * Build Row
             */

            $this->buildIndicatorRow(

                $rows,

                [

                    'provinsi_id' => $provinsiId,

                    'sektor_id' => $sektorId,

                    'tahun' => $tahun

                ],

                [

                    'pertumbuhan' => round($pertumbuhan, 6),

                    'kontribusi' => round($kontribusi, 6)

                ]

            );

        }

        /**
         * --------------------------------------------
         * Bulk Upsert
         * --------------------------------------------
         */

        if (!empty($rows)) {

            IndikatorProvinsi::upsert(

                $rows,

                [

                    'provinsi_id',

                    'sektor_id',

                    'tahun'

                ],

                [

                    'pertumbuhan',

                    'kontribusi',

                    'updated_at'

                ]

            );

        }

    }

    /**
     * ==========================================================
     * INDIKATOR KABUPATEN
     * ==========================================================
     */
    private function generateKabupaten(
        int $kabId,
        int $tahun
    ): void {

        $tahunLalu = $tahun - 1;

        /**
         * --------------------------------------------
         * Total Kabupaten
         * --------------------------------------------
         */

        $totalKabupaten = $this->getTotalKabupaten(
            $kabId,
            $tahun
        );

        if ($totalKabupaten <= 0) {
            return;
        }

        /**
         * --------------------------------------------
         * Data Kabupaten
         * --------------------------------------------
         */

        $kabupatenSekarang = $this->getPdrbKabupatenByTahun(
            $kabId,
            $tahun
        )->keyBy('sektor_id');

        $kabupatenLalu = $this->getPdrbKabupatenByTahun(
            $kabId,
            $tahunLalu
        )->keyBy('sektor_id');

        /**
         * --------------------------------------------
         * Row Upsert
         * --------------------------------------------
         */

        $rows = [];

        foreach ($kabupatenSekarang as $sektorId => $sektor) {

            $sektorLalu = $kabupatenLalu->get($sektorId);

            if (
                !$sektorLalu ||
                $this->hasNullValue(
                    $sektor->nilai,
                    $sektorLalu->nilai,
                    $totalKabupaten
                )
            ) {
                continue;
            }

            /**
             * Pertumbuhan
             */

            if ($this->hasNullValue(
                $sektor->nilai,
                $sektorLalu->nilai,
                $totalKabupaten
            )) {
                continue;
            }

            $pertumbuhan = $this->calculateGrowth(
                $sektor->nilai,
                $sektorLalu->nilai
            );

            /**
             * Kontribusi
             */

            $kontribusi = $this->calculateContribution(
                $sektor->nilai,
                $totalKabupaten
            );

            /**
             * Build Row
             */

            $this->buildIndicatorRow(

                $rows,

                [

                    'kab_id' => $kabId,

                    'sektor_id' => $sektorId,

                    'tahun' => $tahun

                ],

                [

                    'pertumbuhan' => round($pertumbuhan, 6),

                    'kontribusi' => round($kontribusi, 6)

                ]

            );

        }

        /**
         * --------------------------------------------
         * Bulk Upsert
         * --------------------------------------------
         */

        if (!empty($rows)) {

            IndikatorKabupaten::upsert(

                $rows,

                [

                    'kab_id',

                    'sektor_id',

                    'tahun'

                ],

                [

                    'pertumbuhan',

                    'kontribusi',

                    'updated_at'

                ]

            );

        }

    }

}