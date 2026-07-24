<?php

namespace App\Services\Concerns;

use Illuminate\Support\Collection;

trait BuildsTabs
{
    private function makeTab(
        string $key,
        string $title,
        Collection $rows
    ): array {

        return [
            'key'=>$key,
            'title'=>$title,
            'rows'=>$rows->values()->toArray(),
        ];
    }
    protected function buildTabs(
        Collection $rows,
        string $column,
        array $categories,
        callable $sortResolver,
        callable $mapResolver
    ): array {

        $tabs = [];

        foreach ($categories as $category) {

            $key = str($category)
                ->lower()
                ->replace(' ', '_')
                ->replace('-', '_')
                ->toString();

            $tabs[] = $this->makeTab(
                $key,
                $category,
                $rows
                    ->where($column, $category)
                    ->sortByDesc($sortResolver)
                    ->map($mapResolver)
            );

        }

        return $tabs;
    }

    protected function buildQuadrantTable(
        Collection $rows,
        callable $sortResolver,
        callable $mapResolver
    ): array {

        return [

            $this->makeTab(
                'kuadran_i',
                'Kuadran I',
                $rows
                    ->where('kuadran', 'Kuadran I')
                    ->sortBy($sortResolver)
                    ->map($mapResolver)
            ),

            $this->makeTab(
                'kuadran_ii',
                'Kuadran II',
                $rows
                    ->where('kuadran', 'Kuadran II')
                    ->sortBy($sortResolver)
                    ->map($mapResolver)
            ),

            $this->makeTab(
                'kuadran_iii',
                'Kuadran III',
                $rows
                    ->where('kuadran', 'Kuadran III')
                    ->sortBy($sortResolver)
                    ->map($mapResolver)
            ),

            $this->makeTab(
                'kuadran_iv',
                'Kuadran IV',
                $rows
                    ->where('kuadran', 'Kuadran IV')
                    ->sortBy($sortResolver)
                    ->map($mapResolver)
            ),

        ];
    }

}