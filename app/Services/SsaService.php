<?php

namespace App\Services;

use App\Models\HasilSsa;
use Illuminate\Support\Facades\DB;

class SsaService extends BaseAnalysisService
{
    /**
     * Generate hasil SSA
     */
    public function generate(
        int $kabId,
        int $tahun
    ): void {

        DB::transaction(function () use ($kabId, $tahun) {

            $this->calculateSsa(
                $kabId,
                $tahun
            );

        });

    }

    /**
     * Hitung seluruh SSA
     */
    private function calculateSsa(
        int $kabId,
        int $tahun
    ): void {

        $tahunLalu = $tahun - 1;

        $provinsiId = $this->getProvinsiId($kabId);

        /**
         * ============================================
         * TOTAL PROVINSI
         * ============================================
         */

        $totalProvinsiSekarang = $this->getTotalProvinsi(
            $provinsiId,
            $tahun
        );

        $totalProvinsiLalu = $this->getTotalProvinsi(
            $provinsiId,
            $tahunLalu
        );

        if ($totalProvinsiLalu <= 0) {
            return;
        }

        /**
         * ============================================
         * Rn
         * ============================================
         */

        $rn = $this->calculateGrowth(
            $totalProvinsiSekarang,
            $totalProvinsiLalu
        );

        /**
         * ============================================
         * DATA KABUPATEN
         * ============================================
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
         * ============================================
         * DATA PROVINSI
         * ============================================
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
         * ============================================
         * ROW UPSERT
         * ============================================
         */

        $rows = [];

        /**
         * ============================================
         * LOOP SELURUH SEKTOR
         * ============================================
         */

        foreach ($kabupatenSekarang as $sektorId => $kabSekarang) {

            $kabLalu = $kabupatenLalu->get($sektorId);

            $provSekarang = $provinsiSekarang->get($sektorId);

            $provLalu = $provinsiLalu->get($sektorId);

            if (
                !$kabLalu ||
                !$provSekarang ||
                !$provLalu
            ) {
                continue;
            }

            if ($this->hasNullValue(
                $kabSekarang->nilai,
                $kabLalu->nilai,
                $provSekarang->nilai,
                $provLalu->nilai
            )) {
                continue;
            }

            /**
             * ============================================
             * Growth
             * ============================================
             */

            $rij = $this->calculateGrowth(
                $kabSekarang->nilai,
                $kabLalu->nilai
            );

            $rin = $this->calculateGrowth(
                $provSekarang->nilai,
                $provLalu->nilai
            );

            /**
             * ============================================
             * Yij
             * Mengikuti PDF BKPM
             * Menggunakan nilai tahun sebelumnya
             * ============================================
             */

            $yij = $kabLalu->nilai;

            /**
             * ============================================
             * SSA
             * Mengikuti rumus PDF BKPM
             * ============================================
             */

            $nij = $yij * $rn;

            $mij = $yij * ($rin - $rn);

            // Mengikuti PDF BKPM
            $cij = $yij * ($rij - $rn);

            $dij = $nij + $mij + $cij;

            /**
             * ============================================
             * Kategori
             * ============================================
             */

            $kategoriPertumbuhan =

                $dij >= 0

                ? 'Pertumbuhan Cepat'

                : 'Pertumbuhan Lambat';

            $kategoriDayaSaing =

                $cij >= 0

                ? 'Daya Saing Baik'

                : 'Tidak Dapat Bersaing';

            /**
             * ============================================
             * ROW
             * ============================================
             */

            $rows[] = [

                'kab_id' => $kabId,

                'sektor_id' => $sektorId,

                'tahun' => $tahun,

                'rn' => round($rn,6),

                'rin' => round($rin,6),

                'rij' => round($rij,6),

                'nij' => round($nij,4),

                'mij' => round($mij,4),

                'cij' => round($cij,4),

                'dij' => round($dij,4),

                'kategori_pertumbuhan' => $kategoriPertumbuhan,

                'kategori_daya_saing' => $kategoriDayaSaing,

                ...$this->timestamp()

            ];

        }

        /**
         * ============================================
         * BULK UPSERT
         * ============================================
         */

        if (!empty($rows)) {

            HasilSsa::upsert(

                $rows,

                [
                    'kab_id',
                    'sektor_id',
                    'tahun'
                ],

                [
                    'rn',
                    'rin',
                    'rij',
                    'nij',
                    'mij',
                    'cij',
                    'dij',
                    'kategori_pertumbuhan',
                    'kategori_daya_saing',
                    'updated_at'
                ]

            );

        }

    }

}

/*
|--------------------------------------------------------------------------
| Catatan
|--------------------------------------------------------------------------
| Rumus Cij mengikuti Buku Panduan Potensi Unggulan dan Peluang Investasi
| BKPM/DPMPTSP:
|
|     Cij = Yij × (Rij - Rn)
|
| Beberapa literatur akademik (mis. Soepono, Tarigan) menggunakan:
|
|     Cij = Yij × (Rij - Rin)
|
| Implementasi ini sengaja mengikuti pedoman BKPM agar hasil analisis
| konsisten dengan dokumen acuan proyek.
|--------------------------------------------------------------------------
*/