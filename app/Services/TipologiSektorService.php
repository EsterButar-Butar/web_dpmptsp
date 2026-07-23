<?php

namespace App\Services;

use App\Models\HasilLq;
use App\Models\HasilSsa;
use App\Models\HasilTipologiSektor;

class TipologiSektorService extends BaseAnalysisService
{
    /**
     * ==========================================================
     * Generate Tipologi Sektor
     * ==========================================================
     */
    public function generate(
        int $kabId,
        int $tahun
    ): void {

        $this->calculateTipologi(
            $kabId,
            $tahun
        );

    }

    /**
     * ==========================================================
     * Hitung Tipologi Sektor
     * ==========================================================
     */
    private function calculateTipologi(
        int $kabId,
        int $tahun
    ): void {

        /**
         * --------------------------------------------
         * Ambil seluruh hasil LQ
         * --------------------------------------------
         */

        $hasilLq = HasilLq::where(
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
         * Ambil seluruh hasil SSA
         * --------------------------------------------
         */

        $hasilSsa = HasilSsa::where(
            'kab_id',
            $kabId
        )
        ->where(
            'tahun',
            $tahun
        )
        ->get()
        ->keyBy('sektor_id');

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

        foreach ($hasilLq as $lq) {

            $ssa = $hasilSsa->get(
                $lq->sektor_id
            );

            if (!$ssa) {
                continue;
            }

            /**
             * Kuadran
             */

            $kuadran =

                $this->determineTipologiSektorQuadrant(

                    $lq->nilai_lq,

                    $ssa->cij

                );

            /**
             * Row
             */

            $rows[] = [

                'kab_id' => $kabId,

                'sektor_id' => $lq->sektor_id,

                'tahun' => $tahun,

                'hasil_lq_id' => $lq->id,

                'hasil_ssa_id' => $ssa->id,

                'lq' => $lq->nilai_lq,

                'cij' => $ssa->cij,

                'kuadran' => $kuadran,

                ...$this->timestamp()

            ];

        }

        /**
         * --------------------------------------------
         * Bulk Upsert
         * --------------------------------------------
         */

        if (!empty($rows)) {

            HasilTipologiSektor::upsert(

                $rows,

                [

                    'kab_id',

                    'sektor_id',

                    'tahun'

                ],

                [

                    'hasil_lq_id',

                    'hasil_ssa_id',

                    'lq',

                    'cij',

                    'kuadran',

                    'updated_at'

                ]

            );

        }

    }

}