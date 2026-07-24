<?php

namespace App\Services\Concerns;

use Illuminate\Support\Collection;

trait BuildsCharts
{

/**
 * ==========================================================
 * Generic Pie Chart
 * ==========================================================
 */

    private function buildPieChart(
        Collection $rows,
        string $column,
        array $labels,
        string $title
    ): array {

        $data = [];

        foreach ($labels as $label) {

            $data[] = $this->countBy(
                $rows,
                $column,
                $label
            );

        }

        $total = array_sum($data);

        return [

            'title' => $title,

            'type' => 'pie',

            'labels' => $labels,

            'datasets' => [

                [

                    'label' => 'Jumlah Sektor',

                    'data' => $data

                ]

            ],

            'summary' => [

                'total' => $total,

                'persentase' =>

                    collect($labels)

                        ->mapWithKeys(function ($label, $index) use ($data, $total) {

                            return [

                                $label =>

                                    $total == 0

                                    ? 0

                                    : round(

                                        ($data[$index] / $total) * 100,

                                        2

                                    )

                            ];

                        })

                        ->toArray()

            ]

        ];

    }

        /**
     * ==========================================================
     * Generic Bar Chart
     * ==========================================================
     */
    private function buildBarChart(
        Collection $rows,
        callable $valueResolver,
        string $title,
        string $label
    ): array {

        $rows =

            $rows

                ->sortByDesc($valueResolver)

                ->values();

        return [

            'title' =>

                $title,

            'type' =>

                'bar',

            'labels' =>

                $rows

                    ->pluck('sektor.nama')

                    ->toArray(),

            'datasets' => [

                [

                    'label' =>

                        $label,

                    'data' =>

                        $rows

                            ->map(function ($row) use ($valueResolver) {

                                return round(

                                    (float) $valueResolver($row),

                                    4

                                );

                            })

                            ->toArray()

                ]

            ]

        ];

    }

        /*
     * ==========================================================
     * Generic Scatter Chart
     * ==========================================================
     */
    private function buildScatterChart(
        Collection $rows,
        callable $xResolver,
        callable $yResolver,
        string $title,
        string $xLabel,
        string $yLabel
    ): array {

        return [

            'title' =>

                $title,

            'type' =>

                'scatter',

            'xLabel' =>

                $xLabel,

            'yLabel' =>

                $yLabel,

            'datasets' => [

                [

                    'label' =>

                        'Sektor',

                    'data' =>

                        $rows

                            ->map(function ($row) use (

                                $xResolver,

                                $yResolver

                            ) {

                                return [

                                    'x' =>

                                        round(

                                            (float) $xResolver($row),

                                            4

                                        ),

                                    'y' =>

                                        round(

                                            (float) $yResolver($row),

                                            4

                                        ),

                                    'label' =>

                                        $row->sektor->nama

                                ];

                            })

                            ->values()

                            ->toArray()

                ]

            ]

        ];

    }
}