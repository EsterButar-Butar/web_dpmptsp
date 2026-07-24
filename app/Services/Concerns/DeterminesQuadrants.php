<?php

namespace App\Services\Concerns;

trait DeterminesQuadrants
{
    /**
     * ==========================================================
     * Tipologi Sektor
     * ==========================================================
     *
     * Kuadran I   : LQ >= 1 dan Cij >= 0
     * Kuadran II  : LQ >= 1 dan Cij < 0
     * Kuadran III : LQ < 1 dan Cij >= 0
     * Kuadran IV  : LQ < 1 dan Cij < 0
     *
     * @param float $lq
     * @param float $cij
     *
     * @return string
     */
    protected function determineTipologiSektorQuadrant(
        float $lq,
        float $cij
    ): string {

        if ($lq >= 1 && $cij >= 0) {
            return 'Kuadran I';
        }

        if ($lq >= 1 && $cij < 0) {
            return 'Kuadran II';
        }

        if ($lq < 1 && $cij >= 0) {
            return 'Kuadran III';
        }

        return 'Kuadran IV';

    }

    /**
     * ==========================================================
     * Tipologi Klassen
     * ==========================================================
     *
     * Kuadran I
     * Pertumbuhan Kabupaten >= Pertumbuhan Provinsi
     * Kontribusi Kabupaten >= Kontribusi Provinsi
     *
     * Kuadran II
     * Pertumbuhan Kabupaten < Pertumbuhan Provinsi
     * Kontribusi Kabupaten >= Kontribusi Provinsi
     *
     * Kuadran III
     * Pertumbuhan Kabupaten >= Pertumbuhan Provinsi
     * Kontribusi Kabupaten < Kontribusi Provinsi
     *
     * Kuadran IV
     * Pertumbuhan Kabupaten < Pertumbuhan Provinsi
     * Kontribusi Kabupaten < Kontribusi Provinsi
     *
     * @return string
     */
    protected function determineTipologiKlassenQuadrant(
        float $pertumbuhanKabupaten,
        float $pertumbuhanProvinsi,
        float $kontribusiKabupaten,
        float $kontribusiProvinsi
    ): string {

        $growth =

            $pertumbuhanKabupaten >=
            $pertumbuhanProvinsi;

        $contribution =

            $kontribusiKabupaten >=
            $kontribusiProvinsi;

        if ($growth && $contribution) {
            return 'Kuadran I';
        }

        if (!$growth && $contribution) {
            return 'Kuadran II';
        }

        if ($growth && !$contribution) {
            return 'Kuadran III';
        }

        return 'Kuadran IV';

    }

}