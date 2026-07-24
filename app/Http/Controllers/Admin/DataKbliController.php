<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DataKbliController extends Controller
{
    private string $table = 'data_kbli';

    private array $levels = [
        'Kategori' => 1,
        'Golongan Pokok' => 2,
        'Golongan' => 3,
        'Subgolongan' => 4,
        'Kelompok' => 5,
    ];

    public function index(Request $request)
    {
        $tableExists = Schema::hasTable($this->table);
        $columnsReady = $this->tableReady();
        $mode = $request->query('mode');
        $editData = null;
        $perPageOptions = [10, 22, 50];
        $perPage = (int) $request->query('per_page', 22);

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 22;
        }

        if (! $columnsReady) {
            $paginator = new LengthAwarePaginator(
                [],
                0,
                $perPage,
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('admin.data-kbli', [
                'tableExists' => $tableExists,
                'columnsReady' => false,
                'dataKbli' => collect(),
                'paginator' => $paginator,
                'stats' => $this->emptyStats(),
                'categories' => collect(),
                'parentOptions' => collect(),
                'mode' => $mode,
                'editData' => null,
                'perPageOptions' => $perPageOptions,
                'hierarchyMode' => true,
                'totalData' => 0,
            ]);
        }

        if ($request->boolean('export')) {
            return $this->exportCsv($request);
        }

        $categories = DB::table($this->table)
            ->where('struktur', 'Kategori')
            ->orderBy('kode')
            ->get(['kode', 'judul']);

        $parentOptions = DB::table($this->table)
            ->whereBetween('level', [1, 4])
            ->orderBy('kategori_kode')
            ->orderByRaw("CASE WHEN level = 1 THEN '' ELSE kode END")
            ->orderBy('level')
            ->get([
                'id',
                'struktur',
                'level',
                'kode',
                'kode_induk',
                'kategori_kode',
                'judul',
            ]);

        $totalData = DB::table($this->table)->count();
        $hierarchyMode = ! $request->filled('search') && ! $request->filled('struktur');

        if ($hierarchyMode) {
            $categoryQuery = DB::table($this->table)
                ->where('struktur', 'Kategori');

            if ($request->filled('kategori')) {
                $categoryQuery->where(
                    'kode',
                    strtoupper(trim((string) $request->query('kategori')))
                );
            }

            $paginator = $categoryQuery
                ->orderBy('kode')
                ->paginate($perPage)
                ->withQueryString();

            $categoryCodes = collect($paginator->items())
                ->pluck('kode')
                ->filter()
                ->values();

            $dataKbli = $categoryCodes->isEmpty()
                ? collect()
                : $this->hierarchyRowsQuery()
                    ->whereIn('k.kategori_kode', $categoryCodes)
                    ->orderBy('k.kategori_kode')
                    ->orderByRaw("CASE WHEN k.level = 1 THEN '' ELSE k.kode END")
                    ->orderBy('k.level')
                    ->get();
        } else {
            $paginator = $this->filteredQuery($request)
                ->select($this->selectColumns())
                ->selectSub(function ($subQuery) {
                    $subQuery
                        ->from($this->table . ' as child')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('child.kode_induk', 'k.kode');
                }, 'child_count')
                ->orderBy('k.kategori_kode')
                ->orderByRaw("CASE WHEN k.level = 1 THEN '' ELSE k.kode END")
                ->orderBy('k.level')
                ->paginate($perPage)
                ->withQueryString();

            $dataKbli = collect($paginator->items());
        }

        $stats = $this->stats();

        if ($request->filled('edit')) {
            $editData = $this->findRow($request->query('edit'));
            $mode = 'edit';
        }

        return view('admin.data-kbli', compact(
            'tableExists',
            'columnsReady',
            'dataKbli',
            'paginator',
            'stats',
            'categories',
            'parentOptions',
            'mode',
            'editData',
            'perPageOptions',
            'hierarchyMode',
            'totalData'
        ));
    }

    public function store(Request $request)
    {
        if (! $this->tableReady()) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with('error', 'Struktur tabel data_kbli belum sesuai dengan dataset KBLI 2025.');
        }

        $data = $this->validatedData($request);
        $payload = $this->buildPayload($data);

        if (Schema::hasColumn($this->table, 'created_at')) {
            $payload['created_at'] = now();
        }

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        DB::table($this->table)->insert($payload);

        return redirect()
            ->route('admin.data-kbli.index')
            ->with('success', 'Data KBLI berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (! $this->tableReady()) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with('error', 'Struktur tabel data_kbli belum sesuai dengan dataset KBLI 2025.');
        }

        $current = $this->findRow($id);
        $data = $this->validatedData($request, $current);
        $payload = $this->buildPayload($data);

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        DB::table($this->table)
            ->where('id', $id)
            ->update($payload);

        return redirect()
            ->route('admin.data-kbli.index')
            ->with('success', 'Data KBLI berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (! $this->tableReady()) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with('error', 'Struktur tabel data_kbli belum sesuai dengan dataset KBLI 2025.');
        }

        $row = $this->findRow($id);

        $hasChildren = DB::table($this->table)
            ->where('kode_induk', $row->kode)
            ->exists();

        if ($hasChildren) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with('error', 'Data tidak dapat dihapus karena masih memiliki data turunan.');
        }

        DB::table($this->table)
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('admin.data-kbli.index')
            ->with('success', 'Data KBLI berhasil dihapus.');
    }

    private function hierarchyRowsQuery()
    {
        return DB::table($this->table . ' as k')
            ->select($this->selectColumns())
            ->selectSub(function ($subQuery) {
                $subQuery
                    ->from($this->table . ' as child')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('child.kode_induk', 'k.kode');
            }, 'child_count');
    }

    private function selectColumns(): array
    {
        return [
            'k.id',
            'k.struktur',
            'k.level',
            'k.kode',
            'k.kode_induk',
            'k.kategori_kode',
            'k.golongan_pokok_kode',
            'k.golongan_kode',
            'k.subgolongan_kode',
            'k.kelompok_kode',
            'k.judul',
            'k.cakupan',
            'k.tidak_cakupan',
            'k.no_asli',
            'k.kode_asli',
            'k.catatan',
            'k.created_at',
            'k.updated_at',
        ];
    }

    private function filteredQuery(Request $request)
    {
        $query = DB::table($this->table . ' as k');

        if ($request->filled('search')) {
            $search = '%' . mb_strtolower(trim((string) $request->query('search'))) . '%';

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->whereRaw('LOWER(CAST(k.kode AS TEXT)) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(CAST(k.judul AS TEXT)) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(CAST(k.cakupan AS TEXT)) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(CAST(k.tidak_cakupan AS TEXT)) LIKE ?', [$search]);
            });
        }

        if ($request->filled('struktur')) {
            $query->where('k.struktur', $request->query('struktur'));
        }

        if ($request->filled('kategori')) {
            $query->where(
                'k.kategori_kode',
                strtoupper(trim((string) $request->query('kategori')))
            );
        }

        return $query;
    }

    private function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)
            ->select([
                'k.id',
                'k.struktur',
                'k.level',
                'k.kode',
                'k.kode_induk',
                'k.kategori_kode',
                'k.golongan_pokok_kode',
                'k.golongan_kode',
                'k.subgolongan_kode',
                'k.kelompok_kode',
                'k.judul',
                'k.cakupan',
                'k.tidak_cakupan',
                'k.no_asli',
                'k.kode_asli',
                'k.catatan',
            ])
            ->orderBy('k.kategori_kode')
            ->orderByRaw("CASE WHEN k.level = 1 THEN '' ELSE k.kode END")
            ->orderBy('k.level');

        $filename = 'data-kbli-2025-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'id',
                'struktur',
                'level',
                'kode',
                'kode_induk',
                'kategori_kode',
                'golongan_pokok_kode',
                'golongan_kode',
                'subgolongan_kode',
                'kelompok_kode',
                'judul',
                'cakupan',
                'tidak_cakupan',
                'no_asli',
                'kode_asli',
                'catatan',
            ]);

            foreach ($query->cursor() as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->struktur,
                    $row->level,
                    $row->kode,
                    $row->kode_induk,
                    $row->kategori_kode,
                    $row->golongan_pokok_kode,
                    $row->golongan_kode,
                    $row->subgolongan_kode,
                    $row->kelompok_kode,
                    $row->judul,
                    $row->cakupan,
                    $row->tidak_cakupan,
                    $row->no_asli,
                    $row->kode_asli,
                    $row->catatan,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function validatedData(Request $request, $current = null): array
    {
        $data = $request->validate([
            'struktur' => ['required', 'string', 'in:' . implode(',', array_keys($this->levels))],
            'kode_induk' => ['nullable', 'string', 'max:5'],
            'kode' => ['required', 'string', 'max:5'],
            'judul' => ['required', 'string', 'max:500'],
            'cakupan' => ['nullable', 'string'],
            'tidak_cakupan' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
        ], [
            'struktur.required' => 'Level struktur wajib dipilih.',
            'struktur.in' => 'Level struktur tidak valid.',
            'kode.required' => 'Kode KBLI wajib diisi.',
            'judul.required' => 'Judul KBLI wajib diisi.',
        ]);

        $data['struktur'] = trim($data['struktur']);
        $data['level'] = $this->levels[$data['struktur']];
        $data['kode'] = strtoupper(trim($data['kode']));
        $data['kode_induk'] = isset($data['kode_induk']) && trim($data['kode_induk']) !== ''
            ? strtoupper(trim($data['kode_induk']))
            : null;
        $data['judul'] = trim($data['judul']);
        $data['cakupan'] = $this->nullableText($data['cakupan'] ?? null);
        $data['tidak_cakupan'] = $this->nullableText($data['tidak_cakupan'] ?? null);
        $data['catatan'] = $this->nullableText($data['catatan'] ?? null);

        $errors = [];
        $expectedLength = $data['level'];

        if ($data['level'] === 1) {
            if (! preg_match('/^[A-V]$/', $data['kode'])) {
                $errors['kode'] = 'Kode kategori harus berupa satu huruf A sampai V.';
            }

            $data['kode_induk'] = null;
        } else {
            if (! preg_match('/^\d{' . $expectedLength . '}$/', $data['kode'])) {
                $errors['kode'] = 'Kode ' . $data['struktur'] . ' harus terdiri dari ' . $expectedLength . ' digit.';
            }

            if (! $data['kode_induk']) {
                $errors['kode_induk'] = 'Data induk wajib dipilih.';
            }
        }

        $parent = null;

        if ($data['kode_induk']) {
            $parent = DB::table($this->table)
                ->where('kode', $data['kode_induk'])
                ->first();

            if (! $parent) {
                $errors['kode_induk'] = 'Data induk tidak ditemukan.';
            } elseif ((int) $parent->level !== $data['level'] - 1) {
                $errors['kode_induk'] = 'Level data induk tidak sesuai dengan struktur yang dipilih.';
            } elseif ($data['level'] >= 3 && ! str_starts_with($data['kode'], $parent->kode)) {
                $errors['kode'] = 'Kode harus diawali dengan kode induk ' . $parent->kode . '.';
            }
        }

        $duplicateQuery = DB::table($this->table)
            ->whereRaw('LOWER(TRIM(CAST(kode AS TEXT))) = ?', [mb_strtolower($data['kode'])]);

        if ($current) {
            $duplicateQuery->where('id', '!=', $current->id);
        }

        if ($duplicateQuery->exists()) {
            $errors['kode'] = 'Kode KBLI sudah terdaftar.';
        }

        if ($current) {
            $hasChildren = DB::table($this->table)
                ->where('kode_induk', $current->kode)
                ->exists();

            $structureChanged = $data['struktur'] !== $current->struktur;
            $codeChanged = $data['kode'] !== $current->kode;
            $parentChanged = ($data['kode_induk'] ?? null) !== ($current->kode_induk ?? null);

            if ($hasChildren && ($structureChanged || $codeChanged || $parentChanged)) {
                $errors['struktur'] = 'Struktur, kode, dan induk tidak dapat diubah karena data ini memiliki turunan.';
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $data['parent'] = $parent;

        return $data;
    }

    private function buildPayload(array $data): array
    {
        $level = $data['level'];
        $kode = $data['kode'];
        $parent = $data['parent'] ?? null;

        $kategoriKode = $level === 1
            ? $kode
            : ($parent->kategori_kode ?: ($parent->struktur === 'Kategori' ? $parent->kode : null));

        return [
            'struktur' => $data['struktur'],
            'level' => $level,
            'kode' => $kode,
            'kode_induk' => $level === 1 ? null : $data['kode_induk'],
            'kategori_kode' => $kategoriKode,
            'golongan_pokok_kode' => $level >= 2 ? substr($kode, 0, 2) : null,
            'golongan_kode' => $level >= 3 ? substr($kode, 0, 3) : null,
            'subgolongan_kode' => $level >= 4 ? substr($kode, 0, 4) : null,
            'kelompok_kode' => $level === 5 ? $kode : null,
            'judul' => $data['judul'],
            'cakupan' => $data['cakupan'],
            'tidak_cakupan' => $data['tidak_cakupan'],
            'catatan' => $data['catatan'],
        ];
    }

    private function findRow($id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->firstOrFail();
    }

    private function stats(): array
    {
        $counts = DB::table($this->table)
            ->select('struktur', DB::raw('COUNT(*) AS total'))
            ->groupBy('struktur')
            ->pluck('total', 'struktur');

        $total = (int) $counts->sum();

        return [
            [
                'label' => 'Kategori',
                'value' => (int) ($counts['Kategori'] ?? 0),
                'description' => 'Huruf A–V',
                'icon' => 'fa-layer-group',
                'tone' => 'green',
            ],
            [
                'label' => 'Golongan Pokok',
                'value' => (int) ($counts['Golongan Pokok'] ?? 0),
                'description' => '2 digit',
                'icon' => 'fa-building',
                'tone' => 'orange',
            ],
            [
                'label' => 'Golongan',
                'value' => (int) ($counts['Golongan'] ?? 0),
                'description' => '3 digit',
                'icon' => 'fa-map',
                'tone' => 'blue',
            ],
            [
                'label' => 'Subgolongan',
                'value' => (int) ($counts['Subgolongan'] ?? 0),
                'description' => '4 digit',
                'icon' => 'fa-location-dot',
                'tone' => 'red',
            ],
            [
                'label' => 'Kelompok',
                'value' => (int) ($counts['Kelompok'] ?? 0),
                'description' => '5 digit',
                'icon' => 'fa-sitemap',
                'tone' => 'violet',
            ],
            [
                'label' => 'Total Data',
                'value' => $total,
                'description' => 'Seluruh struktur',
                'icon' => 'fa-database',
                'tone' => 'teal',
            ],
        ];
    }

    private function emptyStats(): array
    {
        return [
            ['label' => 'Kategori', 'value' => 0, 'description' => 'Huruf A–V', 'icon' => 'fa-layer-group', 'tone' => 'green'],
            ['label' => 'Golongan Pokok', 'value' => 0, 'description' => '2 digit', 'icon' => 'fa-building', 'tone' => 'orange'],
            ['label' => 'Golongan', 'value' => 0, 'description' => '3 digit', 'icon' => 'fa-map', 'tone' => 'blue'],
            ['label' => 'Subgolongan', 'value' => 0, 'description' => '4 digit', 'icon' => 'fa-location-dot', 'tone' => 'red'],
            ['label' => 'Kelompok', 'value' => 0, 'description' => '5 digit', 'icon' => 'fa-sitemap', 'tone' => 'violet'],
            ['label' => 'Total Data', 'value' => 0, 'description' => 'Seluruh struktur', 'icon' => 'fa-database', 'tone' => 'teal'],
        ];
    }

    private function tableReady(): bool
    {
        return Schema::hasTable($this->table) && Schema::hasColumns($this->table, [
            'id',
            'struktur',
            'level',
            'kode',
            'kode_induk',
            'kategori_kode',
            'golongan_pokok_kode',
            'golongan_kode',
            'subgolongan_kode',
            'kelompok_kode',
            'judul',
            'cakupan',
            'tidak_cakupan',
            'no_asli',
            'kode_asli',
            'catatan',
        ]);
    }

    private function nullableText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}