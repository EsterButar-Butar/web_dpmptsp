<?php

namespace App\Services;

use App\Models\Kabupaten;
use App\Models\PdrbKabupaten;
use App\Models\PdrbSumut;
use App\Models\HasilLq;
use Illuminate\Support\Facades\DB;

class LqService extends BaseAnalysisService
{
    /**
     * Generate hasil LQ
     */
    public function generate(
        int $kabId,
        int $tahun
    ): void {

        DB::transaction(function () use ($kabId, $tahun) {

            $this->calculateLq(
                $kabId,
                $tahun
            );

        });

    }

    /**
     * Hitung seluruh LQ
     */
    private function calculateLq(
        int $kabId,
        int $tahun
    ): void {

        $provinsiId =

            $this->getProvinsiId($kabId);

        $totalKabupaten =

            $this->getTotalKabupaten(
                $kabId,
                $tahun
            );

        $totalProvinsi =

            $this->getTotalProvinsi(
                $provinsiId,
                $tahun
            );

        if (
            $totalKabupaten <= 0 ||
            $totalProvinsi <= 0
        ) {
            return;
        }

        /**
         * Seluruh sektor kabupaten
         */

        $dataKabupaten =

            $this->getPdrbKabupaten(
                $kabId,
                $tahun
            );

        /**
         * Seluruh sektor provinsi
         * hanya 1 query
         */

        $dataProvinsi =

            $this->getPdrbProvinsiByTahun(
                $provinsiId,
                $tahun
            )->keyBy('sektor_id');

        /**
         * Row untuk upsert
         */

        $rows = [];

        foreach ($dataKabupaten as $item) {

            $provinsi =

                $dataProvinsi->get(
                    $item->sektor_id
                );

            if (!$provinsi) {
                continue;
            }

            $persenKabupaten =

                $item->nilai /
                $totalKabupaten;

            $persenProvinsi =

                $provinsi->nilai /
                $totalProvinsi;

            if ($persenProvinsi == 0) {
                continue;
            }

            $nilaiLq =

                round(
                    $persenKabupaten /
                    $persenProvinsi,
                    4
                );

            $kategori =

                $nilaiLq >= 1
                    ? 'Basis'
                    : 'Non Basis';

            $rows[] = [

                'kab_id' => $kabId,

                'sektor_id' => $item->sektor_id,

                'tahun' => $tahun,

                'nilai_lq' => $nilaiLq,

                'kategori' => $kategori,

                'created_at' => now(),

                'updated_at' => now()

            ];

        }

        /**
         * Tidak ada data
         */

        if (empty($rows)) {
            return;
        }

        /**
         * Bulk Upsert
         */

        HasilLq::upsert(

            $rows,

            [
                'kab_id',
                'sektor_id',
                'tahun'
            ],

            [
                'nilai_lq',
                'kategori',
                'updated_at'
            ]

        );

    }

}