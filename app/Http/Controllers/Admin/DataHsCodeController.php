<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DataHsCodeController extends Controller
{
    private string $table = 'hs_codes';

    public function index(Request $request)
    {
        $tableExists = Schema::hasTable($this->table);
        $columns = $this->columnMap();

        $hasIdColumn = $columns['id'] !== null;
        $hasStatusColumn = $columns['status'] !== null;

        $columnsReady = $tableExists
            && $columns['hs_code']
            && $columns['uraian_barang'];

        if (! $columnsReady) {
            $dataHsCode = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('admin.hs-code', [
                'tableExists' => $tableExists,
                'columnsReady' => false,
                'hasIdColumn' => $hasIdColumn,
                'hasStatusColumn' => $hasStatusColumn,
                'dataHsCode' => $dataHsCode,
                'stats' => $this->emptyStats(),
                'mode' => $request->query('mode'),
                'editData' => null,
            ]);
        }

        $query = DB::table($this->table)
            ->select([
                $this->selectAlias($columns['id'], 'id'),
                $this->selectAlias($columns['excel_id'], 'excel_id'),

                $this->selectAlias($columns['kode_kategori'], 'kode_kategori'),
                $this->selectAlias($columns['kode_kelompok'], 'kode_kelompok'),
                $this->selectAlias($columns['uraian_kelompok'], 'uraian_kelompok'),

                $this->selectAlias($columns['kode_subkelompok'], 'kode_subkelompok'),
                $this->selectAlias($columns['uraian_subkelompok'], 'uraian_subkelompok'),

                $this->selectAlias($columns['hs_code'], 'hs_code'),
                $this->selectAlias($columns['uraian_barang'], 'uraian_barang'),

                $this->selectAlias($columns['status'], 'status'),
                $this->selectAlias($columns['keterangan'], 'keterangan'),
                $this->selectAlias($columns['created_at'], 'created_at'),
                $this->selectAlias($columns['updated_at'], 'updated_at'),
            ]);

        if ($request->filled('search')) {
            $search = '%' . strtolower(trim($request->search)) . '%';

            $searchColumns = array_filter([
                $columns['kode_kategori'],
                $columns['kode_kelompok'],
                $columns['uraian_kelompok'],
                $columns['kode_subkelompok'],
                $columns['uraian_subkelompok'],
                $columns['hs_code'],
                $columns['uraian_barang'],
                $columns['status'],
                $columns['keterangan'],
            ]);

            $query->where(function ($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $column) {
                    $q->orWhereRaw(
                        'LOWER(CAST(' . $this->quotedColumn($column) . ' AS TEXT)) LIKE ?',
                        [$search]
                    );
                }
            });
        }

        if ($request->filled('status') && $hasStatusColumn) {
            $query->whereRaw(
                'LOWER(TRIM(' . $this->quotedColumn($columns['status']) . ')) = ?',
                [strtolower(trim($request->status))]
            );
        }

        if ($columns['hs_code']) {
            $query->orderByRaw($this->quotedColumn($columns['hs_code']) . ' asc');
        } elseif ($columns['id']) {
            $query->orderBy($columns['id']);
        }

        $dataHsCode = $query
            ->paginate(10)
            ->withQueryString();

        $stats = $this->stats($columns);

        $mode = $request->query('mode');
        $editData = null;

        if ($request->filled('edit') && $hasIdColumn) {
            $editData = $this->findRow($request->edit, $columns);
            $mode = 'edit';
        }

        return view('admin.hs-code', compact(
            'tableExists',
            'columnsReady',
            'hasIdColumn',
            'hasStatusColumn',
            'dataHsCode',
            'stats',
            'mode',
            'editData'
        ));
    }

    public function store(Request $request)
    {
        $columns = $this->columnMap();

        if (! Schema::hasTable($this->table)) {
            return redirect()
                ->route('admin.hs-code.index')
                ->with('error', 'Tabel hs_codes belum tersedia di Supabase.');
        }

        if (! $columns['hs_code'] || ! $columns['uraian_barang']) {
            return redirect()
                ->route('admin.hs-code.index')
                ->with('error', 'Kolom HS Code/Uraian Barang belum sesuai.');
        }

        $data = $request->validate($this->rules($columns), $this->messages());

        $this->ensureUniqueHsCode($data['hs_code'], $columns);

        $insert = $this->payload($data, $columns);

        if ($columns['created_at']) {
            $insert[$columns['created_at']] = now();
        }

        if ($columns['updated_at']) {
            $insert[$columns['updated_at']] = now();
        }

        DB::table($this->table)->insert($insert);

        return redirect()
            ->route('admin.hs-code.index')
            ->with('success', 'Data HS Code berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $columns = $this->columnMap();

        if (! $columns['id']) {
            return redirect()
                ->route('admin.hs-code.index')
                ->with('error', 'Kolom id belum tersedia, data belum bisa diedit.');
        }

        $data = $request->validate($this->rules($columns), $this->messages());

        $this->ensureUniqueHsCode($data['hs_code'], $columns, $id);

        $update = $this->payload($data, $columns);

        if ($columns['updated_at']) {
            $update[$columns['updated_at']] = now();
        }

        DB::table($this->table)
            ->where($columns['id'], $id)
            ->update($update);

        return redirect()
            ->route('admin.hs-code.index')
            ->with('success', 'Data HS Code berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $columns = $this->columnMap();

        if (! $columns['id']) {
            return redirect()
                ->route('admin.hs-code.index')
                ->with('error', 'Kolom id belum tersedia, data belum bisa dihapus.');
        }

        DB::table($this->table)
            ->where($columns['id'], $id)
            ->delete();

        return redirect()
            ->route('admin.hs-code.index')
            ->with('success', 'Data HS Code berhasil dihapus.');
    }

    private function rules(array $columns): array
    {
        $rules = [
            'excel_id' => ['nullable', 'integer'],

            'kode_kategori' => ['nullable', 'string', 'max:80'],
            'kode_kelompok' => ['nullable', 'string', 'max:80'],
            'uraian_kelompok' => ['nullable', 'string'],

            'kode_subkelompok' => ['nullable', 'string', 'max:80'],
            'uraian_subkelompok' => ['nullable', 'string'],

            'hs_code' => ['required', 'string', 'max:80'],
            'uraian_barang' => ['required', 'string'],

            'keterangan' => ['nullable', 'string'],
        ];

        if ($columns['status']) {
            $rules['status'] = ['required', 'in:Aktif,Nonaktif'];
        }

        return $rules;
    }

    private function messages(): array
    {
        return [
            'hs_code.required' => 'HS Code wajib diisi.',
            'uraian_barang.required' => 'Uraian barang wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }

    private function payload(array $data, array $columns): array
    {
        $payload = [];

        if ($columns['excel_id']) {
            $payload[$columns['excel_id']] = $data['excel_id'] ?? null;
        }

        if ($columns['kode_kategori']) {
            $payload[$columns['kode_kategori']] = $data['kode_kategori'] ?? null;
        }

        if ($columns['kode_kelompok']) {
            $payload[$columns['kode_kelompok']] = $data['kode_kelompok'] ?? null;
        }

        if ($columns['uraian_kelompok']) {
            $payload[$columns['uraian_kelompok']] = $data['uraian_kelompok'] ?? null;
        }

        if ($columns['kode_subkelompok']) {
            $payload[$columns['kode_subkelompok']] = $data['kode_subkelompok'] ?? null;
        }

        if ($columns['uraian_subkelompok']) {
            $payload[$columns['uraian_subkelompok']] = $data['uraian_subkelompok'] ?? null;
        }

        if ($columns['hs_code']) {
            $payload[$columns['hs_code']] = $data['hs_code'];
        }

        if ($columns['uraian_barang']) {
            $payload[$columns['uraian_barang']] = $data['uraian_barang'];
        }

        if ($columns['status']) {
            $payload[$columns['status']] = $data['status'] ?? 'Aktif';
        }

        if ($columns['keterangan']) {
            $payload[$columns['keterangan']] = $data['keterangan'] ?? null;
        }

        return $payload;
    }

    private function ensureUniqueHsCode(string $hsCode, array $columns, $ignoreId = null): void
    {
        if (! $columns['hs_code']) {
            return;
        }

        $query = DB::table($this->table)
            ->where($columns['hs_code'], $hsCode);

        if ($ignoreId && $columns['id']) {
            $query->where($columns['id'], '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'hs_code' => 'HS Code sudah terdaftar.',
            ]);
        }
    }

    private function findRow($id, array $columns)
    {
        return DB::table($this->table)
            ->select([
                $this->selectAlias($columns['id'], 'id'),
                $this->selectAlias($columns['excel_id'], 'excel_id'),

                $this->selectAlias($columns['kode_kategori'], 'kode_kategori'),
                $this->selectAlias($columns['kode_kelompok'], 'kode_kelompok'),
                $this->selectAlias($columns['uraian_kelompok'], 'uraian_kelompok'),

                $this->selectAlias($columns['kode_subkelompok'], 'kode_subkelompok'),
                $this->selectAlias($columns['uraian_subkelompok'], 'uraian_subkelompok'),

                $this->selectAlias($columns['hs_code'], 'hs_code'),
                $this->selectAlias($columns['uraian_barang'], 'uraian_barang'),

                $this->selectAlias($columns['status'], 'status'),
                $this->selectAlias($columns['keterangan'], 'keterangan'),
                $this->selectAlias($columns['created_at'], 'created_at'),
                $this->selectAlias($columns['updated_at'], 'updated_at'),
            ])
            ->where($columns['id'], $id)
            ->firstOrFail();
    }

    private function stats(array $columns): array
{
    $total = DB::table($this->table)->count();

    $kategori = 0;
    $kelompok = 0;
    $subkelompok = 0;

    if ($columns['kode_kategori']) {
        $kategori = DB::table($this->table)
            ->whereNotNull($columns['kode_kategori'])
            ->whereRaw('TRIM(CAST(' . $this->quotedColumn($columns['kode_kategori']) . ' AS TEXT)) != ?', [''])
            ->distinct()
            ->count($columns['kode_kategori']);
    }

    if ($columns['kode_kelompok']) {
        $kelompok = DB::table($this->table)
            ->whereNotNull($columns['kode_kelompok'])
            ->whereRaw('TRIM(CAST(' . $this->quotedColumn($columns['kode_kelompok']) . ' AS TEXT)) != ?', [''])
            ->distinct()
            ->count($columns['kode_kelompok']);
    }

    if ($columns['kode_subkelompok']) {
        $subkelompok = DB::table($this->table)
            ->whereNotNull($columns['kode_subkelompok'])
            ->whereRaw('TRIM(CAST(' . $this->quotedColumn($columns['kode_subkelompok']) . ' AS TEXT)) != ?', [''])
            ->distinct()
            ->count($columns['kode_subkelompok']);
    }

    return [
        [
            'label' => 'Total HS Code',
            'value' => $total,
            'color' => 'mint',
            'icon' => 'fa-tags',
        ],
        [
            'label' => 'Kategori',
            'value' => $kategori,
            'color' => 'cream',
            'icon' => 'fa-layer-group',
        ],
        [
            'label' => 'Kelompok',
            'value' => $kelompok,
            'color' => 'blue',
            'icon' => 'fa-table-list',
        ],
        [
            'label' => 'Subkelompok',
            'value' => $subkelompok,
            'color' => 'red',
            'icon' => 'fa-sitemap',
        ],
    ];
}

    private function emptyStats(): array
{
    return [
        [
            'label' => 'Total HS Code',
            'value' => 0,
            'color' => 'mint',
            'icon' => 'fa-tags',
        ],
        [
            'label' => 'Kategori',
            'value' => 0,
            'color' => 'cream',
            'icon' => 'fa-layer-group',
        ],
        [
            'label' => 'Kelompok',
            'value' => 0,
            'color' => 'blue',
            'icon' => 'fa-table-list',
        ],
        [
            'label' => 'Subkelompok',
            'value' => 0,
            'color' => 'red',
            'icon' => 'fa-sitemap',
        ],
    ];
}

    private function columnMap(): array
{
    return [
        'id' => $this->firstExistingColumn(['id']),

        'excel_id' => $this->firstExistingColumn([
            'excel_id',
            'ID',
            'id_excel',
        ]),

        'kode_kategori' => $this->firstExistingColumn([
            'kode_kategori',
            'kategori_kode',
            'Kode Kategori',
        ]),

        'kode_kelompok' => $this->firstExistingColumn([
            'kode_kelompok',
            'kelompok_kode',
            'Kode Kelompok',
        ]),

        'uraian_kelompok' => $this->firstExistingColumn([
            'uraian_kelompok',
            'kelompok_uraian',
            'Uraian Kelompok',
        ]),

        'kode_subkelompok' => $this->firstExistingColumn([
            'kode_subkelompok',
            'subkelompok_kode',
            'Kode Subkelompok',
        ]),

        'uraian_subkelompok' => $this->firstExistingColumn([
            'uraian_subkelompok',
            'subkelompok_uraian',
            'Uraian Subkelompok',
        ]),

        'hs_code' => $this->firstExistingColumn([
            'hs_code',
            'HS Code',
            'kode_hs',
            'Kode HS',
        ]),

        'uraian_barang' => $this->firstExistingColumn([
            'uraian_barang',
            'Uraian Barang',
            'uraian',
            'deskripsi',
        ]),

        'status' => $this->firstExistingColumn([
            'status',
            'Status',
        ]),

        'keterangan' => $this->firstExistingColumn([
            'keterangan',
            'Keterangan',
        ]),

        'created_at' => $this->firstExistingColumn(['created_at']),
        'updated_at' => $this->firstExistingColumn(['updated_at']),
    ];
}

    private function firstExistingColumn(array $columns): ?string
    {
        if (! Schema::hasTable($this->table)) {
            return null;
        }

        $existingColumns = Schema::getColumnListing($this->table);

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
}