<?php

namespace App\Services;

use App\Models\HasilTipologiKlassen;
use App\Models\IndikatorKabupaten;
use App\Models\IndikatorProvinsi;

class TipologiKlassenService extends BaseAnalysisService
{
    /**
     * ==========================================================
     * Generate Tipologi Klassen
     * ==========================================================
     */
    public function generate(
        int $kabId,
        int $tahun
    ): void {

        $this->calculateKlassen(
            $kabId,
            $tahun
        );

    }

    /**
     * ==========================================================
     * Hitung Tipologi Klassen
     * ==========================================================
     */
    private function calculateKlassen(
        int $kabId,
        int $tahun
    ): void {

        /**
         * --------------------------------------------
         * Provinsi
         * --------------------------------------------
         */

        $provinsiId =

            $this->getProvinsiId(
                $kabId
            );

        /**
         * --------------------------------------------
         * Ambil indikator kabupaten
         * --------------------------------------------
         */

        $indikatorKabupaten =

            IndikatorKabupaten::

                where(
                    'kab_id',
                    $kabId
                )

                ->where(
                    'tahun',
                    $tahun
                )

                ->get();

        /**
         * --------------------------------------------
         * Ambil indikator provinsi
         * --------------------------------------------
         */

        $indikatorProvinsi =

            IndikatorProvinsi::

                where(
                    'provinsi_id',
                    $provinsiId
                )

                ->where(
                    'tahun',
                    $tahun
                )

                ->get()

                ->keyBy(
                    'sektor_id'
                );

        /**
         * --------------------------------------------
         * Row Upsert
         * --------------------------------------------
         */

        $rows = [];

        /**
         * --------------------------------------------
         * Loop seluruh sektor
         * --------------------------------------------
         */

        foreach (

            $indikatorKabupaten

            as

            $kabupaten

        ) {

            $provinsi =

                $indikatorProvinsi->get(

                    $kabupaten->sektor_id

                );

            if (!$provinsi) {

                continue;

            }

            /**
             * Kuadran
             */

            $kuadran =

                $this->determineTipologiKlassenQuadrant(

                    $kabupaten->pertumbuhan,

                    $provinsi->pertumbuhan,

                    $kabupaten->kontribusi,

                    $provinsi->kontribusi

                );

            /**
             * Row
             */

            $rows[] = [

                'kab_id' =>

                    $kabId,

                'sektor_id' =>

                    $kabupaten->sektor_id,

                'tahun' =>

                    $tahun,

                'indikator_provinsi_id' =>

                    $provinsi->id,

                'indikator_kabupaten_id' =>

                    $kabupaten->id,

                'pertumbuhan_kabupaten' =>

                    round(
                        $kabupaten->pertumbuhan,
                        6
                    ),

                'pertumbuhan_provinsi' =>

                    round(
                        $provinsi->pertumbuhan,
                        6
                    ),

                'kontribusi_kabupaten' =>

                    round(
                        $kabupaten->kontribusi,
                        6
                    ),

                'kontribusi_provinsi' =>

                    round(
                        $provinsi->kontribusi,
                        6
                    ),

                'kuadran' =>

                    $kuadran,

                ...$this->timestamp()

            ];

        }

        /**
         * --------------------------------------------
         * Bulk Upsert
         * --------------------------------------------
         */

        if (!empty($rows)) {

            HasilTipologiKlassen::upsert(

                $rows,

                [

                    'kab_id',

                    'sektor_id',

                    'tahun'

                ],

                [

                    'indikator_provinsi_id',

                    'indikator_kabupaten_id',

                    'pertumbuhan_kabupaten',

                    'pertumbuhan_provinsi',

                    'kontribusi_kabupaten',

                    'kontribusi_provinsi',

                    'kuadran',

                    'updated_at'

                ]

            );

        }

    }

}
