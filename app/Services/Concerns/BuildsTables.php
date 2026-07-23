<?php

namespace App\Services\Concerns;

use Illuminate\Support\Collection;

trait BuildsTables
{

    /**
     * ==========================================================
     * Generic Table Builder
     * ==========================================================
     */
    protected function buildTable(
        Collection $rows,
        callable $mapResolver,
        ?callable $sortResolver = null
    ): array {

        if ($sortResolver) {
            $rows = $rows
                ->sortBy($sortResolver)
                ->values();
        }

        return $rows
            ->map($mapResolver)
            ->values()
            ->toArray();
    }

    /**
     * ==========================================================
     * Generic Quadrant Table Builder
     * ==========================================================
     */
    // protected function buildQuadrantTable(
    //     Collection $rows,
    //     callable $mapResolver,
    //     ?callable $sortResolver = null
    // ): array {

    //     return $this->buildTable(

    //         rows: $rows,

    //         mapResolver: $mapResolver,

    //         sortResolver: $sortResolver

    //     );

    // }

}