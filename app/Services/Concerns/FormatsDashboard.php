trait FormatsDashboard
{
    protected function rankingItem(
        int $id,
        string $nama,
        float $nilai,
        ?string $kategori=null
    ): array {
        return [
            'id'=>$id,
            'nama'=>$nama,
            'nilai'=>round($nilai,4),
            'kategori'=>$kategori,
        ];
    }

    protected function tab(
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

    protected function chart(
        string $title,
        array $labels,
        array $datasets
    ): array {

        return [
            'title'=>$title,
            'labels'=>$labels,
            'datasets'=>$datasets,
        ];
    }
}