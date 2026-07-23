<?php

namespace App\Services\Concerns;

use Illuminate\Support\Collection;

trait BuildsSummary
{

    /**
     * ==========================================================
     * Build Summary Card
     * ==========================================================
     */
    protected function buildSummaryCard(
        string $title,
        mixed $value,
        string $subtitle,
        ?string $icon = null,
        ?string $color = null
    ): array {

        return [

            'title' => $title,

            'value' => $value,

            'subtitle' => $subtitle,

            'icon' => $icon,

            'color' => $color

        ];

    }

    /**
     * ==========================================================
     * Build Category Summary
     * ==========================================================
     */
    protected function buildCategorySummary(
        Collection $rows,
        string $column,
        array $categories,
        array $descriptions = []
    ): array {

        $summary = [];

        foreach ($categories as $category) {

            $summary[] = $this->buildSummaryCard(

                title: $category,

                value: $this->countBy(
                    $rows,
                    $column,
                    $category
                ),

                subtitle: $descriptions[$category] ?? ''

            );

        }

        return $summary;

    }

    /**
     * ==========================================================
     * Build Quadrant Summary
     * ==========================================================
     */
    protected function buildQuadrantSummary(
        Collection $rows
    ): array {

        return $this->buildCategorySummary(

            $rows,

            'kuadran',

            [

                'Kuadran I',

                'Kuadran II',

                'Kuadran III',

                'Kuadran IV'

            ],

            [

                'Kuadran I' =>
                    'Sektor Cepat Maju dan Cepat Tumbuh',

                'Kuadran II' =>
                    'Sektor Potensial',

                'Kuadran III' =>
                    'Sektor Berkembang',

                'Kuadran IV' =>
                    'Sektor Relatif Tertinggal'

            ]

        );

    }

    /**
     * ==========================================================
     * Build Percentage Summary
     * ==========================================================
     */
    protected function buildPercentageSummary(
        Collection $rows,
        string $column,
        array $categories
    ): array {

        $total = max(
            1,
            $rows->count()
        );

        $summary = [];

        foreach ($categories as $category) {

            $jumlah = $this->countBy(
                $rows,
                $column,
                $category
            );

            $summary[$category] = [

                'jumlah' => $jumlah,

                'persentase' => round(

                    ($jumlah / $total) * 100,

                    2

                )

            ];

        }

        return $summary;

    }

}