<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $wilayah = $this->countWilayah();
        $kbli = $this->countTable('data_kbli');
        $kbki = $this->countTable('data_kbki');
        $hsCode = $this->countHsCode();
        $pengguna = $this->countTable('users');

        $stats = [
            [
                'key' => 'total',
                'label' => 'Total Data',
                'value' => $wilayah + $kbli + $kbki + $hsCode + $pengguna,
                'icon' => 'fa-clipboard-list',
                'color' => 'green',
                'url' => null,
            ],
            [
                'key' => 'wilayah',
                'label' => 'Data Wilayah',
                'value' => $wilayah,
                'icon' => 'fa-location-dot',
                'color' => 'purple',
                'url' => $this->routeLink('admin.data-wilayah.index'),
            ],
            [
                'key' => 'kbli',
                'label' => 'Kode KBLI',
                'value' => $kbli,
                'icon' => 'fa-table-cells-large',
                'color' => 'blue',
                'url' => $this->routeLink('admin.data-kbli.index'),
            ],
            [
                'key' => 'kbki',
                'label' => 'Kode KBKI',
                'value' => $kbki,
                'icon' => 'fa-boxes-stacked',
                'color' => 'purple',
                'url' => $this->routeLink('admin.data-kbki.index'),
            ],
            [
                'key' => 'hs',
                'label' => 'Kode HS',
                'value' => $hsCode,
                'icon' => 'fa-link',
                'color' => 'teal',
                'url' => $this->routeLink('admin.hs-code.index'),
            ],
            [
                'key' => 'pengguna',
                'label' => 'Pengguna',
                'value' => $pengguna,
                'icon' => 'fa-users',
                'color' => 'cyan',
                'url' => $this->routeLink('admin.pengguna.index'),
            ],
        ];

        $activityData = $this->buildActivities();

        $summaryRows = [
            [
                'label' => 'Data Wilayah',
                'data_terakhir' => $activityData['latestByCategory']['wilayah']['title'] ?? '-',
                'total' => $wilayah,
                'url' => $this->routeLink('admin.data-wilayah.index'),
            ],
            [
                'label' => 'Kode KBLI',
                'data_terakhir' => $activityData['latestByCategory']['kbli']['title'] ?? '-',
                'total' => $kbli,
                'url' => $this->routeLink('admin.data-kbli.index'),
            ],
            [
                'label' => 'Kode KBKI',
                'data_terakhir' => $activityData['latestByCategory']['kbki']['title'] ?? '-',
                'total' => $kbki,
                'url' => $this->routeLink('admin.data-kbki.index'),
            ],
            [
                'label' => 'Kode HS',
                'data_terakhir' => $activityData['latestByCategory']['hs']['title'] ?? '-',
                'total' => $hsCode,
                'url' => $this->routeLink('admin.hs-code.index'),
            ],
            [
                'label' => 'Pengguna',
                'data_terakhir' => $activityData['latestByCategory']['pengguna']['title'] ?? '-',
                'total' => $pengguna,
                'url' => $this->routeLink('admin.pengguna.index'),
            ],
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'summaryRows' => $summaryRows,
            'activities' => $activityData['recent'],
        ]);
    }

    private function countTable(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        try {
            return DB::table($table)->count();
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    private function countWilayah(): int
    {
        foreach ([
            'data_wilayah',
            'kelurahan_desa',
            'desa_kelurahan',
        ] as $table) {
            if (Schema::hasTable($table)) {
                return $this->countTable($table);
            }
        }

        return 0;
    }

    private function countHsCode(): int
    {
        $table = $this->hsTable();

        return $table ? $this->countTable($table) : 0;
    }

    private function hsTable(): ?string
    {
        foreach ([
            'hs_codes',
            'data_hs_code',
            'hs_code',
            'hscode',
        ] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function routeLink(string $routeName): ?string
    {
        return Route::has($routeName)
            ? route($routeName)
            : null;
    }

    private function buildActivities(): array
    {
        $activities = collect();

        $this->appendUserActivities($activities);
        $this->appendWilayahActivities($activities);
        $this->appendKbliActivities($activities);
        $this->appendKbkiActivities($activities);
        $this->appendHsActivities($activities);

        $activities = $activities
            ->filter(fn (array $activity) => $activity['time'] !== null)
            ->sortByDesc('time')
            ->values();

        $latestByCategory = [];

        foreach ([
            'wilayah',
            'kbli',
            'kbki',
            'hs',
            'pengguna',
        ] as $category) {
            $latestByCategory[$category] = $activities
                ->firstWhere('category', $category);
        }

        return [
            'recent' => $activities->take(5)->values(),
            'latestByCategory' => $latestByCategory,
        ];
    }

    private function appendUserActivities(Collection $activities): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $nameColumn = $this->firstExistingColumn('users', [
            'name',
            'nama',
        ]);

        $emailColumn = $this->firstExistingColumn('users', [
            'email',
        ]);

        $roleColumn = $this->firstExistingColumn('users', [
            'role',
        ]);

        $timeColumn = $this->timeColumn('users');

        if (! $timeColumn) {
            return;
        }

        $rows = DB::table('users')
            ->select([
                $this->selectAlias($nameColumn, 'name'),
                $this->selectAlias($emailColumn, 'email'),
                $this->selectAlias($roleColumn, 'role'),
                $this->selectAlias($timeColumn, 'activity_time'),
            ])
            ->orderByDesc($timeColumn)
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $name = $row->name ?: ($row->email ?: 'Pengguna');
            $role = ucfirst(strtolower((string) ($row->role ?: 'user')));
            $time = $this->parseDate($row->activity_time ?? null);

            $activities->push([
                'category' => 'pengguna',
                'category_label' => 'Pengguna',
                'title' => $name,
                'aktivitas' => $name . ' terdaftar sebagai ' . $role . '.',
                'icon' => 'fa-user',
                'color' => 'orange',
                'time' => $time,
                'waktu' => $this->formatDate($time),
            ]);
        }
    }

    private function appendWilayahActivities(Collection $activities): void
    {
        $table = null;

        foreach ([
            'data_wilayah',
            'kelurahan_desa',
            'desa_kelurahan',
        ] as $candidate) {
            if (Schema::hasTable($candidate)) {
                $table = $candidate;
                break;
            }
        }

        if (! $table) {
            return;
        }

        $nameColumn = $this->firstExistingColumn($table, [
            'nama_desa',
            'nama_kelurahan',
            'nama_kelurahan_desa',
            'village_name',
            'nama_kecamatan',
            'nama_kabupaten',
            'name',
        ]);

        $timeColumn = $this->timeColumn($table);

        if (! $nameColumn || ! $timeColumn) {
            return;
        }

        $rows = DB::table($table)
            ->select([
                $this->selectAlias($nameColumn, 'nama'),
                $this->selectAlias($timeColumn, 'activity_time'),
            ])
            ->orderByDesc($timeColumn)
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $title = $row->nama ?: 'Data Wilayah';
            $time = $this->parseDate($row->activity_time ?? null);

            $activities->push([
                'category' => 'wilayah',
                'category_label' => 'Wilayah',
                'title' => $title,
                'aktivitas' => 'Data wilayah ' . $title . ' ditambahkan.',
                'icon' => 'fa-location-dot',
                'color' => 'green',
                'time' => $time,
                'waktu' => $this->formatDate($time),
            ]);
        }
    }

    private function appendKbliActivities(Collection $activities): void
    {
        $table = 'data_kbli';

        if (! Schema::hasTable($table)) {
            return;
        }

        $kodeColumn = $this->firstExistingColumn($table, [
            'kode',
            'kode_kbli',
            'Kode',
        ]);

        $judulColumn = $this->firstExistingColumn($table, [
            'judul',
            'judul_kbli',
            'nama_kbli',
            'Judul',
        ]);

        $timeColumn = $this->timeColumn($table);

        if ((! $kodeColumn && ! $judulColumn) || ! $timeColumn) {
            return;
        }

        $rows = DB::table($table)
            ->select([
                $this->selectAlias($kodeColumn, 'kode'),
                $this->selectAlias($judulColumn, 'judul'),
                $this->selectAlias($timeColumn, 'activity_time'),
            ])
            ->orderByDesc($timeColumn)
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $title = trim(
                ($row->kode ? $row->kode . ' - ' : '') .
                ($row->judul ?: 'Data KBLI')
            );

            $time = $this->parseDate($row->activity_time ?? null);

            $activities->push([
                'category' => 'kbli',
                'category_label' => 'KBLI',
                'title' => $title,
                'aktivitas' => 'Kode KBLI ' . $title . ' ditambahkan.',
                'icon' => 'fa-table-cells-large',
                'color' => 'blue',
                'time' => $time,
                'waktu' => $this->formatDate($time),
            ]);
        }
    }

    private function appendKbkiActivities(Collection $activities): void
    {
        $table = 'data_kbki';

        if (! Schema::hasTable($table)) {
            return;
        }

        $kodeColumn = $this->firstExistingColumn($table, [
            'kode',
            'kode_kbki',
            'Kode',
        ]);

        $judulColumn = $this->firstExistingColumn($table, [
            'judul',
            'judul_kbki',
            'nama_kbki',
            'Judul',
        ]);

        $timeColumn = $this->timeColumn($table);

        if ((! $kodeColumn && ! $judulColumn) || ! $timeColumn) {
            return;
        }

        $rows = DB::table($table)
            ->select([
                $this->selectAlias($kodeColumn, 'kode'),
                $this->selectAlias($judulColumn, 'judul'),
                $this->selectAlias($timeColumn, 'activity_time'),
            ])
            ->orderByDesc($timeColumn)
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $title = trim(
                ($row->kode ? $row->kode . ' - ' : '') .
                ($row->judul ?: 'Data KBKI')
            );

            $time = $this->parseDate($row->activity_time ?? null);

            $activities->push([
                'category' => 'kbki',
                'category_label' => 'KBKI',
                'title' => $title,
                'aktivitas' => 'Kode KBKI ' . $title . ' ditambahkan.',
                'icon' => 'fa-boxes-stacked',
                'color' => 'purple',
                'time' => $time,
                'waktu' => $this->formatDate($time),
            ]);
        }
    }

    private function appendHsActivities(Collection $activities): void
    {
        $table = $this->hsTable();

        if (! $table) {
            return;
        }

        $kodeColumn = $this->firstExistingColumn($table, [
            'hs_code',
            'kode_hs',
            'HS Code',
            'kode',
        ]);

        $uraianColumn = $this->firstExistingColumn($table, [
            'uraian_barang',
            'Uraian Barang',
            'uraian',
            'deskripsi',
        ]);

        $timeColumn = $this->timeColumn($table);

        if ((! $kodeColumn && ! $uraianColumn) || ! $timeColumn) {
            return;
        }

        $rows = DB::table($table)
            ->select([
                $this->selectAlias($kodeColumn, 'kode'),
                $this->selectAlias($uraianColumn, 'uraian'),
                $this->selectAlias($timeColumn, 'activity_time'),
            ])
            ->orderByDesc($timeColumn)
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $title = trim(
                ($row->kode ? $row->kode . ' - ' : '') .
                ($row->uraian ?: 'Data HS Code')
            );

            $time = $this->parseDate($row->activity_time ?? null);

            $activities->push([
                'category' => 'hs',
                'category_label' => 'HS Code',
                'title' => $title,
                'aktivitas' => 'Kode HS ' . $title . ' ditambahkan.',
                'icon' => 'fa-link',
                'color' => 'purple',
                'time' => $time,
                'waktu' => $this->formatDate($time),
            ]);
        }
    }

    private function firstExistingColumn(
        string $table,
        array $columns
    ): ?string {
        if (! Schema::hasTable($table)) {
            return null;
        }

        $existingColumns = Schema::getColumnListing($table);

        foreach ($columns as $candidate) {
            foreach ($existingColumns as $existingColumn) {
                if (
                    strtolower($candidate) ===
                    strtolower($existingColumn)
                ) {
                    return $existingColumn;
                }
            }
        }

        return null;
    }

    private function timeColumn(string $table): ?string
    {
        return $this->firstExistingColumn($table, [
            'updated_at',
            'created_at',
        ]);
    }

    private function selectAlias(
        ?string $column,
        string $alias
    ) {
        if (! $column) {
            return DB::raw('NULL AS "' . $alias . '"');
        }

        return DB::raw(
            $this->quotedColumn($column) .
            ' AS "' .
            $alias .
            '"'
        );
    }

    private function quotedColumn(string $column): string
    {
        return '"' . str_replace('"', '""', $column) . '"';
    }

    private function parseDate($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable $exception) {
            return null;
        }
    }

    private function formatDate(?Carbon $date): string
    {
        return $date
            ? $date->translatedFormat('d F Y, H:i')
            : '-';
    }
}