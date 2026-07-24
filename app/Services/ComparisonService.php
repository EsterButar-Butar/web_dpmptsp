<?php

namespace App\Services;

use App\Models\HasilLq;
use App\Models\HasilSsa;
use App\Models\HasilTipologiKlassen;
use Illuminate\Support\Collection;

class ComparisonService
{
    public function getDashboard(array $filter): array
    {
        $rows = $this->loadData($filter);

        return [

            'summary' => $this->getSummary($rows),

            'charts' => [
                'growth'       => $this->getGrowthChart($rows),
                'contribution' => $this->getContributionChart($rows),
                'lq'           => $this->getLqChart($rows),
                'ssa'          => $this->getSsaChart($rows),
            ],

            'table' => $this->getTrendTable($rows),

        ];
    }

    private function loadData(array $filter): Collection
    {
        return HasilTipologiKlassen::query()

            ->with([
                'sektor:sektor_id,nama_sektor','indikatorKabupaten','indikatorProvinsi',
            ])

            ->leftJoin(
                'hasil_lq',
                function ($join) {

                    $join

                        ->on(
                            'hasil_tipologi_klassen.kab_id',
                            '=',
                            'hasil_lq.kab_id'
                        )

                        ->on(
                            'hasil_tipologi_klassen.sektor_id',
                            '=',
                            'hasil_lq.sektor_id'
                        )

                        ->on(
                            'hasil_tipologi_klassen.tahun',
                            '=',
                            'hasil_lq.tahun'
                        );

                }
            )

            ->leftJoin(
                'hasil_ssa',
                function ($join) {

                    $join

                        ->on(
                            'hasil_tipologi_klassen.kab_id',
                            '=',
                            'hasil_ssa.kab_id'
                        )

                        ->on(
                            'hasil_tipologi_klassen.sektor_id',
                            '=',
                            'hasil_ssa.sektor_id'
                        )

                        ->on(
                            'hasil_tipologi_klassen.tahun',
                            '=',
                            'hasil_ssa.tahun'
                        );

                }
            )

            ->when(
                $filter['kabupaten'],
                fn($q)=>$q->where(
                    'hasil_tipologi_klassen.kab_id',
                    $filter['kabupaten']
                )
            )

            ->when(
                $filter['sektor'],
                fn($q)=>$q->where(
                    'hasil_tipologi_klassen.sektor_id',
                    $filter['sektor']
                )
            )

            ->whereBetween(
                'hasil_tipologi_klassen.tahun',
                [
                    $filter['tahun_awal'],
                    $filter['tahun_akhir'],
                ]
            )

            ->select([

                'hasil_tipologi_klassen.*',

                'hasil_lq.nilai_lq',

                'hasil_lq.kategori',

                'hasil_ssa.dij',

                'hasil_ssa.kategori_pertumbuhan',

                'hasil_ssa.kategori_daya_saing',

            ])

            ->orderBy('hasil_tipologi_klassen.tahun')

            ->get();
    }

    private function getLqChart(Collection $rows): array
    {
        return [

            'type' => 'line',

            'title' => 'Tren Nilai LQ',

            'labels' => $rows->pluck('tahun')->toArray(),

            'datasets' => [

                [

                    'label' => 'LQ',

                    'borderColor' => '#FFD54F',

                    'backgroundColor' => '#FFD54F',

                    'borderWidth' => 3,

                    'tension' => 0.35,

                    'pointRadius' => 4,

                    'pointHoverRadius' => 6,

                    'fill' => false,

                    'data' => $rows
                        ->pluck('nilai_lq')
                        ->map(fn ($v) => round((float) $v, 2))
                        ->toArray(),

                ],

            ],

        ];
    }

    private function getSsaChart(Collection $rows): array
    {
        return [

            'type' => 'line',

            'title' => 'Tren Nilai SSA (Dij)',

            'labels' => $rows->pluck('tahun')->toArray(),

            'datasets' => [

                [

                    'label' => 'SSA',

                    'borderColor' => '#FFD54F',

                    'backgroundColor' => '#FFD54F',

                    'borderWidth' => 3,

                    'tension' => 0.35,

                    'pointRadius' => 4,

                    'pointHoverRadius' => 6,

                    'fill' => false,

                    'data' => $rows
                        ->pluck('dij')
                        ->map(fn ($v) => round((float) $v, 2))
                        ->toArray(),

                ],

            ],

        ];
    }

    private function getSummary(Collection $rows): array
    {
        $last = $rows->last();
        $contributionAvg = $rows->avg(fn ($row) =>
            ((float) ($row->indikatorKabupaten?->kontribusi ?? 0)) * 100
        );

        $contributionMax = $rows
            ->sortByDesc(fn ($row) =>
                ((float) ($row->indikatorKabupaten?->kontribusi ?? 0))
            )
            ->first();
        $growthAvg = $rows->avg(fn ($row) =>
            ((float) ($row->indikatorKabupaten?->pertumbuhan ?? 0)) * 100
        );

        $growthMax = $rows->sortByDesc(fn ($row) =>
            (float) ($row->indikatorKabupaten?->pertumbuhan ?? 0)
        )->first();

        $previous = $rows->count() > 1
            ? $rows[$rows->count() - 2]
            : null;

        $persentasePerubahanLq = 0;

        if (
            $previous &&
            $previous->nilai_lq &&
            $previous->nilai_lq != 0
        ) {

            $persentasePerubahanLq =
                (
                    ($last->nilai_lq - $previous->nilai_lq)
                    /
                    $previous->nilai_lq
                ) * 100;

        }
        $first = $rows->first();
        $last = $rows->last();

        $rank = [
            'Kuadran I'   => 4,
            'Kuadran II'  => 3,
            'Kuadran III' => 2,
            'Kuadran IV'  => 1,
        ];

        $movement = "Tetap sejak {$first->tahun}";

        // Cari perubahan terakhir
        for ($i = $rows->count() - 1; $i > 0; $i--) {

            $current = $rows[$i];
            $before  = $rows[$i - 1];

            if ($current->kuadran !== $before->kuadran) {

                $currentRank = $rank[$current->kuadran] ?? 0;
                $beforeRank  = $rank[$before->kuadran] ?? 0;

                $selisih = $currentRank - $beforeRank;

                $movement = match (true) {
                    $selisih > 0 => "Naik {$selisih} tingkat pada {$current->tahun}",
                    $selisih < 0 => "Turun " . abs($selisih) . " tingkat pada {$current->tahun}",
                    default      => "Tetap sejak {$current->tahun}",
                };

                break;
            }
        }
        return [

            'growth' => [

                'average' => round($growthAvg, 2),

                'highest' => [

                    'tahun' => $growthMax->tahun,

                    'nilai' => round(
                        ((float) $growthMax->indikatorKabupaten->pertumbuhan) * 100,
                        2
                    )

                ]

            ],

            'contribution' => [

                'average' => round($contributionAvg, 2),

                'highest' => [

                    'tahun' => $contributionMax->tahun,

                    'nilai' => round(
                        ((float) $contributionMax->indikatorKabupaten->kontribusi) * 100,
                        2
                    )

                ]

            ],

            'lq' => [

                'nilai' => round((float) ($last->nilai_lq ?? 0), 2),

                'tahun' => $last->tahun,

                'status' => $last->kategori,

                'change' => round($persentasePerubahanLq, 2),

            ],

            'tipologi' => [

                'kuadran' => $last->kuadran,

                'kategori' => match ($last->kuadran) {

                    'Kuadran I' => 'Sektor Unggulan',

                    'Kuadran II' => 'Sektor Berkembang',

                    'Kuadran III' => 'Sektor Potensial',

                    'Kuadran IV' => 'Sektor Terbelakang',

                    default => '-',

                },

                'movement' => $movement,

            ],

        ];
    }

    private function getGrowthChart(Collection $rows): array
    {
        return [

            'type' => 'line',

            'labels' => $rows->pluck('tahun')->toArray(),

            'datasets' => [

                [

                    'label' => 'Pertumbuhan (%)',

                    'borderColor' => '#FFD54F',

                    'backgroundColor' => '#FFD54F',

                    'borderWidth' => 3,

                    'tension' => 0.35,

                    'pointRadius' => 4,

                    'pointHoverRadius' => 6,

                    'fill' => false,

                    'data' => $rows
                        ->map(fn ($row) =>
                            round(
                                (float) ($row->indikatorKabupaten?->pertumbuhan ?? 0),
                                2
                            )
                        )
                        ->toArray(),

                ],

            ],

        ];
    }

    private function getContributionChart(Collection $rows): array
    {
        return [

            'type' => 'line',

            'labels' => $rows->pluck('tahun')->toArray(),

            'datasets' => [

                [

                    'label' => 'Kontribusi (%)',

                    'borderColor' => '#FFD54F',

                    'backgroundColor' => '#FFD54F',

                    'borderWidth' => 3,

                    'tension' => 0.35,

                    'pointRadius' => 4,

                    'pointHoverRadius' => 6,

                    'fill' => false,

                    'data' => $rows
                        ->map(fn ($row) =>
                            round(
                                ((float) ($row->indikatorKabupaten?->kontribusi ?? 0)) * 100,
                                2
                            )
                        )
                        ->toArray(),

                ],

            ],

        ];
    } 

    private function getIndicatorChart(Collection $rows): array
    {
        return [

            'type' => 'line',

            'title' => 'Perbandingan Indikator',

            'labels' =>

                $rows->pluck('tahun')->toArray(),

            'datasets' => [

                [

                    'label'=>'LQ',

                    'data'=>$rows
                        ->pluck('nilai_lq')
                        ->map(fn($v)=>(float)$v)
                        ->toArray(),

                ],

                [

                    'label'=>'SSA',

                    'data'=>$rows
                        ->pluck('dij')
                        ->map(fn($v)=>(float)$v)
                        ->toArray(),

                ],

                [

                    'label'=>'Kontribusi',

                    'data'=>
                    $rows

                        ->map(fn($row)=>

                            (float)

                            ($row->indikatorKabupaten?->kontribusi ?? 0) * 100

                        )

                        ->toArray()

                ],

            ],

        ];
    }

    private function getTrendTable(Collection $rows): array
    {
        return $rows
            ->map(fn ($row) => [

                'tahun' => $row->tahun,

                'growth' => round(
                    ((float) ($row->indikatorKabupaten?->pertumbuhan ?? 0)) * 100,
                    2
                ),

                'contribution' => round(
                    ((float) ($row->indikatorKabupaten?->kontribusi ?? 0)) * 100,
                    2
                ),

                'lq' => round(
                    (float) $row->nilai_lq,
                    3
                ),

                'ssa' => round(
                    (float) $row->dij,
                    2
                ),

                'status_lq' => $row->kategori,

                'kuadran' => $row->kuadran,

                'kategori' => match ($row->kuadran) {
                    'Kuadran I'   => 'Sektor Unggulan',
                    'Kuadran II'  => 'Sektor Berkembang',
                    'Kuadran III' => 'Sektor Potensial',
                    'Kuadran IV'  => 'Sektor Relatif Tertinggal',
                    default       => '-',
                },

            ])
            ->toArray();
    }
}