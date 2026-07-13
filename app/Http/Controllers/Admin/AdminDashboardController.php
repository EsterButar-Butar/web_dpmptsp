<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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
                'caption_label' => 'Pos/Subpos',
                'caption_value' => $this->countHsCode(),
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
        if (Schema::hasTable('kelurahan_desa')) {
            return DB::table('kelurahan_desa')->count();
        }

        if (Schema::hasTable('data_wilayah')) {
            return DB::table('data_wilayah')->count();
        }

        return 0;
    }

    private function countProvinsi(): int
    {
        if (Schema::hasTable('provinsi')) {
            return DB::table('provinsi')->count();
        }

        if (
            Schema::hasTable('data_wilayah')
            && Schema::hasColumn('data_wilayah', 'province_code')
        ) {
            return DB::table('data_wilayah')
                ->distinct()
                ->count('province_code');
        }

        return 0;
    }

    private function countKbliAktif(): int
    {
        if (! Schema::hasTable('data_kbli')) {
            return 0;
        }

        if (! Schema::hasColumn('data_kbli', 'status')) {
            return DB::table('data_kbli')->count();
        }

        return DB::table('data_kbli')
            ->whereRaw('LOWER(TRIM(status)) = ?', ['aktif'])
            ->count();
    }

    private function countHsCode(): int
    {
        $tables = [
            'data_hs_code',
            'hs_code',
            'hs_codes',
            'hscode',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                return DB::table($table)->count();
            }
        }

        return 0;
    }

    private function countAdminOperator(): int
    {
        if (
            ! Schema::hasTable('users')
            || ! Schema::hasColumn('users', 'role')
        ) {
            return 0;
        }

        return DB::table('users')
            ->whereIn(DB::raw('LOWER(TRIM(role))'), [
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

        $query = DB::table('users')
            ->select('name', 'email', 'created_at', 'updated_at');

        if (Schema::hasColumn('users', 'role')) {
            $query->addSelect('role');
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $role = property_exists($row, 'role')
                ? ucfirst($row->role ?? 'user')
                : 'User';

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Pengguna ' . ($row->name ?? $row->email ?? '-') . ' terdaftar sebagai ' . $role . '.',
            ]);
        }
    }

    private function appendWilayahActivities(Collection $activities): void
    {
        if (! Schema::hasTable('kelurahan_desa')) {
            return;
        }

        $nameColumn = $this->firstExistingColumn('kelurahan_desa', [
            'nama_desa',
            'nama_kelurahan_desa',
            'nama_kelurahan',
        ]);

        if (! $nameColumn) {
            return;
        }

        $query = DB::table('kelurahan_desa')
            ->select(
                DB::raw("kelurahan_desa.{$nameColumn} as nama_wilayah"),
                'kelurahan_desa.created_at',
                'kelurahan_desa.updated_at'
            );

        if (
            Schema::hasColumn('kelurahan_desa', 'kecamatan_id')
            && Schema::hasTable('kecamatan')
            && Schema::hasColumn('kecamatan', 'id')
        ) {
            $kecamatanColumn = $this->firstExistingColumn('kecamatan', [
                'nama_kecamatan',
                'name',
            ]);

            if ($kecamatanColumn) {
                $query
                    ->leftJoin('kecamatan', 'kelurahan_desa.kecamatan_id', '=', 'kecamatan.id')
                    ->addSelect(DB::raw("kecamatan.{$kecamatanColumn} as nama_kecamatan"));
            }
        }

        $rows = $query
            ->orderByDesc('kelurahan_desa.created_at')
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $detail = $row->nama_wilayah ?? '-';

            if (property_exists($row, 'nama_kecamatan') && $row->nama_kecamatan) {
                $detail .= ' - ' . $row->nama_kecamatan;
            }

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Menambahkan data wilayah ' . $detail . '.',
            ]);
        }
    }

    private function appendKbliActivities(Collection $activities): void
{
    if (! Schema::hasTable('data_kbli')) {
        return;
    }

    $kodeColumn = $this->firstExistingColumn('data_kbli', [
        'kode_kbli',
        'kode',
        'Kode',
    ]);

    $judulColumn = $this->firstExistingColumn('data_kbli', [
        'judul_kbli',
        'judul',
        'Judul',
        'nama_kbli',
    ]);

    if (! $judulColumn) {
        return;
    }

    $query = DB::table('data_kbli')
        ->select(
            DB::raw($this->quotedColumn($judulColumn) . ' as judul'),
            'created_at',
            'updated_at'
        );

    if ($kodeColumn) {
        $query->addSelect(
            DB::raw($this->quotedColumn($kodeColumn) . ' as kode')
        );
    }

    $rows = $query
        ->orderByDesc('created_at')
        ->limit(3)
        ->get();

    foreach ($rows as $row) {
        $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

        $kode = property_exists($row, 'kode') && $row->kode
            ? $row->kode . ' - '
            : '';

        $activities->push([
            'time' => $time,
            'waktu' => $this->formatDate($time),
            'aktivitas' => 'Menambahkan Kode KBLI ' . $kode . ($row->judul ?? '-') . '.',
        ]);
    }
}

    private function appendHsActivities(Collection $activities): void
    {
        $table = null;

        foreach (['data_hs_code', 'hs_code', 'hs_codes', 'hscode'] as $candidate) {
            if (Schema::hasTable($candidate)) {
                $table = $candidate;
                break;
            }
        }

        if (! $table) {
            return;
        }

        $kodeColumn = $this->firstExistingColumn($table, [
            'kode_hs',
            'hs_code',
            'kode',
        ]);

        $uraianColumn = $this->firstExistingColumn($table, [
            'uraian',
            'deskripsi',
            'nama',
        ]);

        if (! $kodeColumn && ! $uraianColumn) {
            return;
        }

        $query = DB::table($table)
            ->select('created_at', 'updated_at');

        if ($kodeColumn) {
            $query->addSelect(DB::raw("{$kodeColumn} as kode"));
        }

        if ($uraianColumn) {
            $query->addSelect(DB::raw("{$uraianColumn} as uraian"));
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($rows as $row) {
            $time = $this->parseDate($row->created_at ?? $row->updated_at ?? null);

            $kode = property_exists($row, 'kode') && $row->kode
                ? $row->kode . ' - '
                : '';

            $uraian = property_exists($row, 'uraian') && $row->uraian
                ? $row->uraian
                : 'Data HS Code';

            $activities->push([
                'time' => $time,
                'waktu' => $this->formatDate($time),
                'aktivitas' => 'Menambahkan Kode HS ' . $kode . $uraian . '.',
            ]);
        }
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
        } catch (\Throwable $e) {
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