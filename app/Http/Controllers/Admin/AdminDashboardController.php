<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            [
                'label' => 'Data Wilayah',
                'value' => $this->countWilayah(),
                'caption_label' => 'Provinsi',
                'caption_value' => $this->countProvinsi(),
                'icon' => 'fa-map',
                'color' => 'green',
            ],
            [
                'label' => 'Kode KBLI',
                'value' => $this->countTable('data_kbli'),
                'caption_label' => 'Kategori Aktif',
                'caption_value' => $this->countKbliAktif(),
                'icon' => 'fa-table-cells-large',
                'color' => 'blue',
            ],
            [
                'label' => 'Kode HS',
                'value' => $this->countHsCode(),
                'caption_label' => 'Subkelompok',
                'caption_value' => $this->countHsSubkelompok(),
                'icon' => 'fa-tags',
                'color' => 'purple',
            ],
            [
                'label' => 'Pengguna',
                'value' => $this->countTable('users'),
                'caption_label' => 'Admin & Operator',
                'caption_value' => $this->countAdminOperator(),
                'icon' => 'fa-users',
                'color' => 'orange',
            ],
        ];

        $quickActions = [
            [
                'label' => 'Tambah Data Wilayah',
                'icon' => 'fa-plus',
                'color' => 'green',
                'url' => $this->routeLink('admin.data-wilayah.index', ['mode' => 'create']),
            ],
            [
                'label' => 'Tambah Kode KBLI',
                'icon' => 'fa-plus',
                'color' => 'blue',
                'url' => $this->routeLink('admin.data-kbli.index', ['mode' => 'create']),
            ],
            [
                'label' => 'Tambah Kode HS',
                'icon' => 'fa-plus',
                'color' => 'purple',
                'url' => $this->routeLink('admin.hs-code.index', ['mode' => 'create']),
            ],
            [
                'label' => 'Tambah Pengguna',
                'icon' => 'fa-plus',
                'color' => 'orange',
                'url' => $this->routeLink('admin.pengguna.index', ['mode' => 'create']),
            ],
        ];

        $activities = $this->latestActivities();

        return view('admin.dashboard', compact(
            'stats',
            'quickActions',
            'activities'
        ));
    }

    private function countTable(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->count();
    }

    private function countWilayah(): int
    {
        if (Schema::hasTable('data_wilayah')) {
            return DB::table('data_wilayah')->count();
        }

        if (Schema::hasTable('kelurahan_desa')) {
            return DB::table('kelurahan_desa')->count();
        }

        return 0;
    }

    private function countProvinsi(): int
    {
        if (Schema::hasTable('provinsi')) {
            return DB::table('provinsi')->count();
        }

        if (! Schema::hasTable('data_wilayah')) {
            return 0;
        }

        $column = $this->firstExistingColumn('data_wilayah', [
            'kode_provinsi',
            'province_code',
            'provinsi_id',
            'nama_provinsi',
            'province_name',
        ]);

        if (! $column) {
            return 0;
        }

        return DB::table('data_wilayah')
            ->whereNotNull($column)
            ->distinct()
            ->count($column);
    }

    private function countKbliAktif(): int
    {
        if (! Schema::hasTable('data_kbli')) {
            return 0;
        }

        $statusColumn = $this->firstExistingColumn('data_kbli', [
            'status',
            'Status',
        ]);

        if (! $statusColumn) {
            return DB::table('data_kbli')->count();
        }

        return DB::table('data_kbli')
            ->whereRaw(
                'LOWER(TRIM(CAST(' . $this->quotedColumn($statusColumn) . ' AS TEXT))) = ?',
                ['aktif']
            )
            ->count();
    }

    private function countHsCode(): int
    {
        $table = $this->hsTable();

        if (! $table) {
            return 0;
        }

        return DB::table($table)->count();
    }

    private function countHsSubkelompok(): int
    {
        $table = $this->hsTable();

        if (! $table) {
            return 0;
        }

        $column = $this->firstExistingColumn($table, [
            'kode_subkelompok',
            'Kode Subkelompok',
        ]);

        if (! $column) {
            return 0;
        }

        return DB::table($table)
            ->whereNotNull($column)
            ->whereRaw('TRIM(CAST(' . $this->quotedColumn($column) . ' AS TEXT)) != ?', [''])
            ->distinct()
            ->count($column);
    }

    private function countAdminOperator(): int
    {
        if (! Schema::hasTable('users')) {
            return 0;
        }

        $roleColumn = $this->firstExistingColumn('users', [
            'role',
            'Role',
        ]);

        if (! $roleColumn) {
            return 0;
        }

        return DB::table('users')
            ->whereIn(DB::raw('LOWER(TRIM(CAST(' . $this->quotedColumn($roleColumn) . ' AS TEXT)))'), [
                'admin',
                'operator',
            ])
            ->count();
    }

    private function routeLink(string $routeName, array $params = []): ?string
    {
        if (! Route::has($routeName)) {
            return null;
        }

        return route($routeName, $params);
    }

    private function latestActivities(): Collection
    {
        $activities = collect();

        $this->appendUserActivities($activities);
        $this->appendWilayahActivities($activities);
        $this->appendKbliActivities($activities);
        $this->appendHsActivities($activities);

        return $activities
            ->filter(fn ($activity) => $activity['time'] !== null)
            ->sortByDesc('time')
            ->take(5)
            ->values();
    }

    private function appendUserActivities(Collection $activities): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $nameColumn = $this->firstExistingColumn('users', ['name']);
        $emailColumn = $this->firstExistingColumn('users', ['email']);
        $roleColumn = $this->firstExistingColumn('users', ['role', 'Role']);
        $createdAtColumn = $this->firstExistingColumn('users', ['created_at']);
        $updatedAtColumn = $this->firstExistingColumn('users', ['updated_at']);

        if (! $createdAtColumn && ! $updatedAtColumn) {
            return;
        }

        $query = DB::table('users');

        $selects = [
            $this->selectAlias($nameColumn, 'name'),
            $this->selectAlias($emailColumn, 'email'),
            $this->selectAlias($roleColumn, 'role'),
            $this->selectAlias($createdAtColumn, 'created_at'),
            $this->selectAlias($updatedAtColumn, 'updated_at'),
        ];

        $query->select($selects);

        $this->orderLatest($query, 'users');

        $rows = $query
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $role = $row->role
                ? ucfirst((string) $row->role)
                : 'User';

            $name = $row->name ?: ($row->email ?: '-');

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Pengguna ' . $name . ' terdaftar sebagai ' . $role . '.',
            ]);
        }
    }

    private function appendWilayahActivities(Collection $activities): void
    {
        if (Schema::hasTable('data_wilayah')) {
            $this->appendDataWilayahActivities($activities);
            return;
        }

        if (Schema::hasTable('kelurahan_desa')) {
            $this->appendKelurahanDesaActivities($activities);
        }
    }

    private function appendDataWilayahActivities(Collection $activities): void
    {
        $table = 'data_wilayah';

        $desaColumn = $this->firstExistingColumn($table, [
            'nama_desa',
            'village_name',
            'desa_kelurahan',
            'nama_kelurahan',
        ]);

        $kecamatanColumn = $this->firstExistingColumn($table, [
            'nama_kecamatan',
            'district_name',
        ]);

        $kabupatenColumn = $this->firstExistingColumn($table, [
            'nama_kabupaten',
            'regency_name',
            'kabupaten_kota',
        ]);

        $createdAtColumn = $this->firstExistingColumn($table, ['created_at']);
        $updatedAtColumn = $this->firstExistingColumn($table, ['updated_at']);

        if (! $desaColumn && ! $kecamatanColumn && ! $kabupatenColumn) {
            return;
        }

        $query = DB::table($table)
            ->select([
                $this->selectAlias($desaColumn, 'nama_desa'),
                $this->selectAlias($kecamatanColumn, 'nama_kecamatan'),
                $this->selectAlias($kabupatenColumn, 'nama_kabupaten'),
                $this->selectAlias($createdAtColumn, 'created_at'),
                $this->selectAlias($updatedAtColumn, 'updated_at'),
            ]);

        $this->orderLatest($query, $table);

        $rows = $query
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $details = array_filter([
                $row->nama_desa ?? null,
                $row->nama_kecamatan ?? null,
                $row->nama_kabupaten ?? null,
            ]);

            $detail = count($details)
                ? implode(' - ', $details)
                : 'Data Wilayah';

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Menambahkan data wilayah ' . $detail . '.',
            ]);
        }
    }

    private function appendKelurahanDesaActivities(Collection $activities): void
    {
        $table = 'kelurahan_desa';

        $nameColumn = $this->firstExistingColumn($table, [
            'nama_desa',
            'nama_kelurahan_desa',
            'nama_kelurahan',
            'name',
        ]);

        $createdAtColumn = $this->firstExistingColumn($table, ['created_at']);
        $updatedAtColumn = $this->firstExistingColumn($table, ['updated_at']);

        if (! $nameColumn) {
            return;
        }

        $query = DB::table($table)
            ->select([
                $this->selectAlias($nameColumn, 'nama_wilayah'),
                $this->selectAlias($createdAtColumn, 'created_at'),
                $this->selectAlias($updatedAtColumn, 'updated_at'),
            ]);

        $this->orderLatest($query, $table);

        $rows = $query
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Menambahkan data wilayah ' . ($row->nama_wilayah ?? '-') . '.',
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
            'kode_kbli',
            'Kode',
            'kode',
        ]);

        $judulColumn = $this->firstExistingColumn($table, [
            'judul_kbli',
            'Judul',
            'judul',
            'nama_kbli',
        ]);

        $createdAtColumn = $this->firstExistingColumn($table, ['created_at']);
        $updatedAtColumn = $this->firstExistingColumn($table, ['updated_at']);

        if (! $kodeColumn && ! $judulColumn) {
            return;
        }

        $query = DB::table($table)
            ->select([
                $this->selectAlias($kodeColumn, 'kode'),
                $this->selectAlias($judulColumn, 'judul'),
                $this->selectAlias($createdAtColumn, 'created_at'),
                $this->selectAlias($updatedAtColumn, 'updated_at'),
            ]);

        $this->orderLatest($query, $table);

        $rows = $query
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $kode = $row->kode
                ? $row->kode . ' - '
                : '';

            $judul = $row->judul ?: 'Data KBLI';

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Menambahkan Kode KBLI ' . $kode . $judul . '.',
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
            'HS Code',
            'kode_hs',
            'Kode HS',
            'kode',
        ]);

        $uraianColumn = $this->firstExistingColumn($table, [
            'uraian_barang',
            'Uraian Barang',
            'uraian',
            'deskripsi',
            'nama',
        ]);

        $createdAtColumn = $this->firstExistingColumn($table, ['created_at']);
        $updatedAtColumn = $this->firstExistingColumn($table, ['updated_at']);

        if (! $kodeColumn && ! $uraianColumn) {
            return;
        }

        $query = DB::table($table)
            ->select([
                $this->selectAlias($kodeColumn, 'kode'),
                $this->selectAlias($uraianColumn, 'uraian'),
                $this->selectAlias($createdAtColumn, 'created_at'),
                $this->selectAlias($updatedAtColumn, 'updated_at'),
            ]);

        $this->orderLatest($query, $table);

        $rows = $query
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $kode = $row->kode
                ? $row->kode . ' - '
                : '';

            $uraian = $row->uraian ?: 'Data HS Code';

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Menambahkan Kode HS ' . $kode . $uraian . '.',
            ]);
        }
    }

    private function hsTable(): ?string
    {
        foreach ([
            'data_hs_code',
            'hs_codes',
            'hs_code',
            'hscode',
        ] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        $existingColumns = Schema::getColumnListing($table);

        foreach ($columns as $targetColumn) {
            foreach ($existingColumns as $existingColumn) {
                if (strtolower($existingColumn) === strtolower($targetColumn)) {
                    return $existingColumn;
                }
            }
        }

        return null;
    }

    private function selectAlias(?string $column, string $alias)
    {
        if (! $column) {
            return DB::raw('NULL as ' . $alias);
        }

        return DB::raw($this->quotedColumn($column) . ' as ' . $alias);
    }

    private function quotedColumn(string $column): string
    {
        return '"' . str_replace('"', '""', $column) . '"';
    }

    private function orderLatest(Builder $query, string $table): void
    {
        if (Schema::hasColumn($table, 'created_at')) {
            $query->orderByDesc('created_at');
            return;
        }

        if (Schema::hasColumn($table, 'updated_at')) {
            $query->orderByDesc('updated_at');
            return;
        }

        if (Schema::hasColumn($table, 'id')) {
            $query->orderByDesc('id');
        }
    }

    private function parseDate($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function formatDate(?Carbon $date): string
    {
        if (! $date) {
            return '-';
        }

        return $date->format('d M Y, H:i');
    }
}