<?php

namespace App\Services\Concerns;

use Illuminate\Support\Collection;

trait BuildsRanking
{

    private function rankingItem(
        int $id,
        string $nama,
        float $nilai,
        ?string $kategori = null
    ): array {
        return [
            'id'        => $id,
            'nama'      => $nama,
            'nilai'     => round($nilai, 4),
            'kategori'  => $kategori,
        ];
    }

    protected function buildRanking(
        Collection $rows,
        callable $sortResolver,
        callable $mapResolver,
        int $limit = 5
    ): array {


        $sorted =

            $rows

                ->sortByDesc($sortResolver)

                ->values();

        /**
         * ------------------------------------------
         * Top
         * ------------------------------------------
         */

        $top =

            $sorted

                ->take($limit)

                ->map($mapResolver)

                ->values()

                ->toArray();

        /**
         * ------------------------------------------
         * Bottom
         * ------------------------------------------
         */

        $bottom =

            $sorted

                ->reverse()

                ->take($limit)

                ->values()

                ->map($mapResolver)

                ->values()

                ->toArray();

        return [

            'top' => $top,

            'bottom' => $bottom

        ];

    }

}