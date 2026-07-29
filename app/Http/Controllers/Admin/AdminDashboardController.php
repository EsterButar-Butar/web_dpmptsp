<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Throwable;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard previously made dozens of schema-inspection and database calls.
     * This is expensive with the remote PostgreSQL connection, so the complete
     * snapshot is fetched in two queries and cached briefly.
     */
    public function index()
    {
        $dashboardData = Cache::remember(
            'admin.dashboard.snapshot.v1',
            now()->addMinutes(5),
            fn (): array => $this->dashboardData(),
        );

        // The view uses Collection helpers such as take(). This also keeps
        // snapshots written by older requests compatible with the view.
        $dashboardData['activities'] = collect($dashboardData['activities'] ?? []);

        return view('admin.dashboard', $dashboardData);
    }

    private function dashboardData(): array
    {
        $counts = $this->counts();
        $wilayah = $counts['provinsi'] + $counts['kabupaten'] + $counts['kecamatan'] + $counts['kelurahan_desa'];
        $stats = [
            $this->stat('total', 'Total Data', $wilayah + $counts['data_kbli'] + $counts['data_kbki'] + $counts['data_hs_code'] + $counts['users'], 'fa-clipboard-list', 'green'),
            $this->stat('wilayah', 'Data Wilayah', $wilayah, 'fa-location-dot', 'purple', 'admin.data-wilayah.index'),
            $this->stat('kbli', 'Kode KBLI', $counts['data_kbli'], 'fa-table-cells-large', 'blue', 'admin.data-kbli.index'),
            $this->stat('kbki', 'Kode KBKI', $counts['data_kbki'], 'fa-boxes-stacked', 'purple', 'admin.data-kbki.index'),
            $this->stat('hs', 'Kode HS', $counts['data_hs_code'], 'fa-link', 'teal', 'admin.hs-code.index'),
            $this->stat('pengguna', 'Pengguna', $counts['users'], 'fa-users', 'cyan', 'admin.pengguna.index'),
        ];

        $activities = $this->activities();
        $latest = collect($activities)->keyBy('category');
        $summaryRows = [
            $this->summary('Data Wilayah', $latest->get('wilayah')['title'] ?? '-', $wilayah, 'admin.data-wilayah.index'),
            $this->summary('Kode KBLI', $latest->get('kbli')['title'] ?? '-', $counts['data_kbli'], 'admin.data-kbli.index'),
            $this->summary('Kode KBKI', $latest->get('kbki')['title'] ?? '-', $counts['data_kbki'], 'admin.data-kbki.index'),
            $this->summary('Kode HS', $latest->get('hs')['title'] ?? '-', $counts['data_hs_code'], 'admin.hs-code.index'),
            $this->summary('Pengguna', $latest->get('pengguna')['title'] ?? '-', $counts['users'], 'admin.pengguna.index'),
        ];

        return compact('stats', 'summaryRows', 'activities');
    }

    private function counts(): array
    {
        $tables = ['provinsi', 'kabupaten', 'kecamatan', 'kelurahan_desa', 'data_kbli', 'data_kbki', 'data_hs_code', 'users'];
        $selects = collect($tables)->map(fn (string $table) => sprintf('(SELECT COUNT(*) FROM "%s") AS "%s"', $table, $table))->implode(', ');

        try {
            return (array) DB::selectOne("SELECT {$selects}");
        } catch (Throwable $exception) {
            report($exception);

            return array_fill_keys($tables, 0);
        }
    }

    private function activities(): array
    {
        $sources = [
            ['pengguna', 'Pengguna', 'users', 'id', "COALESCE(NULLIF(name, ''), email, 'Pengguna')", "COALESCE(NULLIF(name, ''), email, 'Pengguna') || ' terdaftar sebagai ' || INITCAP(COALESCE(role, 'user')) || '.'", 'fa-user', 'orange'],
            ['wilayah', 'Wilayah', 'provinsi', 'provinsi_id', 'nama_provinsi', "'Provinsi ' || nama_provinsi || ' ditambahkan.'", 'fa-location-dot', 'green'],
            ['wilayah', 'Wilayah', 'kabupaten', 'kab_id', 'nama_kabupaten', "'Kabupaten/Kota ' || nama_kabupaten || ' ditambahkan.'", 'fa-location-dot', 'green'],
            ['wilayah', 'Wilayah', 'kecamatan', 'kec_id', 'nama_kecamatan', "'Kecamatan ' || nama_kecamatan || ' ditambahkan.'", 'fa-location-dot', 'green'],
            ['wilayah', 'Wilayah', 'kelurahan_desa', 'desa_id', 'nama_kelurahan_desa', "'Kelurahan/Desa ' || nama_kelurahan_desa || ' ditambahkan.'", 'fa-location-dot', 'green'],
            ['kbli', 'KBLI', 'data_kbli', 'id', "TRIM(kode || ' - ' || COALESCE(judul, 'Data KBLI'))", "'Kode KBLI ' || TRIM(kode || ' - ' || COALESCE(judul, 'Data KBLI')) || ' ditambahkan.'", 'fa-table-cells-large', 'blue'],
            ['kbki', 'KBKI', 'data_kbki', 'id', "TRIM(kode || ' - ' || COALESCE(judul, 'Data KBKI'))", "'Kode KBKI ' || TRIM(kode || ' - ' || COALESCE(judul, 'Data KBKI')) || ' ditambahkan.'", 'fa-boxes-stacked', 'purple'],
            ['hs', 'HS Code', 'data_hs_code', 'id', "TRIM(hs_code || ' - ' || COALESCE(uraian_barang, 'Data HS Code'))", "'Kode HS ' || TRIM(hs_code || ' - ' || COALESCE(uraian_barang, 'Data HS Code')) || ' ditambahkan.'", 'fa-link', 'purple'],
        ];

        $queries = collect($sources)->map(function (array $source): string {
            [$category, $label, $table, $id, $title, $activity, $icon, $color] = $source;
            return sprintf("(SELECT '%s' category, '%s' category_label, %s title, %s aktivitas, '%s' icon, '%s' color, created_at activity_time FROM \"%s\" ORDER BY \"%s\" DESC LIMIT 3)", $category, $label, $title, $activity, $icon, $color, $table, $id);
        })->implode(' UNION ALL ');

        try {
            return collect(DB::select("SELECT * FROM ({$queries}) AS dashboard_activities ORDER BY activity_time DESC NULLS LAST LIMIT 5"))
                ->map(function (object $row): array {
                    $time = $row->activity_time ? Carbon::parse($row->activity_time) : null;
                    return ['category' => $row->category, 'category_label' => $row->category_label, 'title' => $row->title, 'aktivitas' => $row->aktivitas, 'icon' => $row->icon, 'color' => $row->color, 'time' => $time, 'waktu' => $time?->translatedFormat('d F Y, H:i') ?? '-'];
                })->all();
        } catch (Throwable $exception) {
            report($exception);
            return [];
        }
    }

    private function stat(string $key, string $label, int $value, string $icon, string $color, ?string $route = null): array
    {
        return compact('key', 'label', 'value', 'icon', 'color') + ['url' => $route ? $this->routeLink($route) : null];
    }

    private function summary(string $label, string $last, int $total, string $route): array
    {
        return ['label' => $label, 'data_terakhir' => $last, 'total' => $total, 'url' => $this->routeLink($route)];
    }

    private function routeLink(string $route): ?string
    {
        return Route::has($route) ? route($route) : null;
    }
}
