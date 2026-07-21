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

    public function index(Request $request)
    {
        $tableExists = Schema::hasTable($this->table);
        $columns = $this->columnMap();

        $hasIdColumn = $columns['id'] !== null;
        $hasStatusColumn = $columns['status'] !== null;

        $columnsReady = $tableExists
            && $columns['kode']
            && $columns['judul'];

        if (! $columnsReady) {
            $dataKbli = new LengthAwarePaginator(
                [],
                0,
                10,
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('admin.data-kbli', [
                'tableExists' => $tableExists,
                'columnsReady' => false,
                'hasIdColumn' => $hasIdColumn,
                'hasStatusColumn' => $hasStatusColumn,
                'dataKbli' => $dataKbli,
                'stats' => $this->emptyStats(),
                'mode' => $request->query('mode'),
                'editData' => null,
            ]);
        }

        $query = DB::table($this->table)
            ->select([
                $this->selectAlias($columns['id'], 'id'),
                $this->selectAlias($columns['no'], 'no_urut'),
                $this->selectAlias($columns['kode'], 'kode_kbli'),
                $this->selectAlias($columns['judul'], 'judul_kbli'),
                $this->selectAlias($columns['cakupan'], 'cakupan'),
                $this->selectAlias(
                    $columns['tidak_cakupan'],
                    'tidak_cakupan'
                ),
                $this->selectAlias($columns['status'], 'status'),
                $this->selectAlias(
                    $columns['created_at'],
                    'created_at'
                ),
                $this->selectAlias(
                    $columns['updated_at'],
                    'updated_at'
                ),
            ]);

        if ($request->filled('search')) {
            $search = '%' . strtolower(
                trim((string) $request->search)
            ) . '%';

            $searchColumns = array_filter([
                $columns['kode'],
                $columns['judul'],
                $columns['cakupan'],
                $columns['tidak_cakupan'],
                $columns['status'],
            ]);

            $query->where(
                function ($subQuery) use (
                    $searchColumns,
                    $search
                ) {
                    foreach ($searchColumns as $column) {
                        $subQuery->orWhereRaw(
                            'LOWER(CAST('
                            . $this->quotedColumn($column)
                            . ' AS TEXT)) LIKE ?',
                            [$search]
                        );
                    }
                }
            );
        }

        if (
            $request->filled('status')
            && $hasStatusColumn
        ) {
            $query->whereRaw(
                'LOWER(TRIM(CAST('
                . $this->quotedColumn($columns['status'])
                . ' AS TEXT))) = ?',
                [
                    strtolower(
                        trim((string) $request->status)
                    ),
                ]
            );
        }

        if ($columns['id']) {
            $query->orderBy($columns['id'], 'asc');
        } elseif ($columns['kode']) {
            $query->orderBy($columns['kode'], 'asc');
        }

        $dataKbli = $query
            ->paginate(10)
            ->withQueryString();

        $stats = $this->stats($columns);

        $mode = $request->query('mode');
        $editData = null;

        if (
            $request->filled('edit')
            && $hasIdColumn
        ) {
            $editData = $this->findRow(
                $request->edit,
                $columns
            );

            $mode = 'edit';
        }

        return view('admin.data-kbli', compact(
            'tableExists',
            'columnsReady',
            'hasIdColumn',
            'hasStatusColumn',
            'dataKbli',
            'stats',
            'mode',
            'editData'
        ));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable($this->table)) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with(
                    'error',
                    'Tabel data_kbli belum tersedia di Supabase.'
                );
        }

        $columns = $this->columnMap();

        if (! $columns['kode'] || ! $columns['judul']) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with(
                    'error',
                    'Kolom Kode dan Judul belum sesuai.'
                );
        }

        $data = $request->validate(
            $this->rules($columns),
            $this->messages()
        );

        $this->ensureUniqueKode(
            $data['kode_kbli'],
            $columns
        );

        $insert = $this->payload($data, $columns);

        if ($columns['created_at']) {
            $insert[$columns['created_at']] = now();
        }

        if ($columns['updated_at']) {
            $insert[$columns['updated_at']] = now();
        }

        DB::table($this->table)->insert($insert);

        return redirect()
            ->route('admin.data-kbli.index')
            ->with(
                'success',
                'Data KBLI berhasil ditambahkan.'
            );
    }

    public function update(Request $request, $id)
    {
        $columns = $this->columnMap();

        if (! $columns['id']) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with(
                    'error',
                    'Kolom id belum tersedia.'
                );
        }

        $data = $request->validate(
            $this->rules($columns),
            $this->messages()
        );

        $this->ensureUniqueKode(
            $data['kode_kbli'],
            $columns,
            $id
        );

        $update = $this->payload($data, $columns);

        if ($columns['updated_at']) {
            $update[$columns['updated_at']] = now();
        }

        DB::table($this->table)
            ->where($columns['id'], $id)
            ->update($update);

        return redirect()
            ->route('admin.data-kbli.index')
            ->with(
                'success',
                'Data KBLI berhasil diperbarui.'
            );
    }

    public function destroy($id)
    {
        $columns = $this->columnMap();

        if (! $columns['id']) {
            return redirect()
                ->route('admin.data-kbli.index')
                ->with(
                    'error',
                    'Kolom id belum tersedia.'
                );
        }

        DB::table($this->table)
            ->where($columns['id'], $id)
            ->delete();

        return redirect()
            ->route('admin.data-kbli.index')
            ->with(
                'success',
                'Data KBLI berhasil dihapus.'
            );
    }

    private function rules(array $columns): array
    {
        $rules = [
            'kode_kbli' => [
                'required',
                'string',
                'max:30',
            ],
            'judul_kbli' => [
                'required',
                'string',
                'max:500',
            ],
            'cakupan' => [
                'nullable',
                'string',
            ],
            'tidak_cakupan' => [
                'nullable',
                'string',
            ],
        ];

        if ($columns['status']) {
            $rules['status'] = [
                'required',
                'in:Aktif,Nonaktif',
            ];
        }

        return $rules;
    }

    private function messages(): array
    {
        return [
            'kode_kbli.required' =>
                'Kode KBLI wajib diisi.',
            'judul_kbli.required' =>
                'Judul KBLI wajib diisi.',
            'status.required' =>
                'Status wajib dipilih.',
            'status.in' =>
                'Status yang dipilih tidak valid.',
        ];
    }

    private function payload(
        array $data,
        array $columns
    ): array {
        $payload = [];

        if ($columns['kode']) {
            $payload[$columns['kode']] =
                trim($data['kode_kbli']);
        }

        if ($columns['judul']) {
            $payload[$columns['judul']] =
                trim($data['judul_kbli']);
        }

        if ($columns['cakupan']) {
            $payload[$columns['cakupan']] =
                $data['cakupan'] ?? null;
        }

        if ($columns['tidak_cakupan']) {
            $payload[$columns['tidak_cakupan']] =
                $data['tidak_cakupan'] ?? null;
        }

        if ($columns['status']) {
            $payload[$columns['status']] =
                $data['status'] ?? 'Aktif';
        }

        return $payload;
    }

    private function ensureUniqueKode(
        string $kode,
        array $columns,
        $ignoreId = null
    ): void {
        if (! $columns['kode']) {
            return;
        }

        $query = DB::table($this->table)
            ->whereRaw(
                'LOWER(TRIM(CAST('
                . $this->quotedColumn($columns['kode'])
                . ' AS TEXT))) = ?',
                [strtolower(trim($kode))]
            );

        if (
            $ignoreId !== null
            && $columns['id']
        ) {
            $query->where(
                $columns['id'],
                '!=',
                $ignoreId
            );
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'kode_kbli' =>
                    'Kode KBLI sudah terdaftar.',
            ]);
        }
    }

    private function findRow(
        $id,
        array $columns
    ) {
        return DB::table($this->table)
            ->select([
                $this->selectAlias($columns['id'], 'id'),
                $this->selectAlias($columns['no'], 'no_urut'),
                $this->selectAlias($columns['kode'], 'kode_kbli'),
                $this->selectAlias($columns['judul'], 'judul_kbli'),
                $this->selectAlias($columns['cakupan'], 'cakupan'),
                $this->selectAlias(
                    $columns['tidak_cakupan'],
                    'tidak_cakupan'
                ),
                $this->selectAlias($columns['status'], 'status'),
                $this->selectAlias(
                    $columns['created_at'],
                    'created_at'
                ),
                $this->selectAlias(
                    $columns['updated_at'],
                    'updated_at'
                ),
            ])
            ->where($columns['id'], $id)
            ->firstOrFail();
    }

    private function stats(array $columns): array
    {
        $total = DB::table($this->table)->count();

        $aktif = $total;
        $nonaktif = 0;
        $cakupanTerisi = 0;

        if ($columns['status']) {
            $aktif = DB::table($this->table)
                ->whereRaw(
                    'LOWER(TRIM(CAST('
                    . $this->quotedColumn($columns['status'])
                    . ' AS TEXT))) = ?',
                    ['aktif']
                )
                ->count();

            $nonaktif = DB::table($this->table)
                ->whereRaw(
                    'LOWER(TRIM(CAST('
                    . $this->quotedColumn($columns['status'])
                    . ' AS TEXT))) = ?',
                    ['nonaktif']
                )
                ->count();
        }

        if ($columns['cakupan']) {
            $cakupanTerisi = DB::table($this->table)
                ->whereNotNull($columns['cakupan'])
                ->whereRaw(
                    'TRIM(CAST('
                    . $this->quotedColumn($columns['cakupan'])
                    . ' AS TEXT)) != ?',
                    ['']
                )
                ->count();
        }

        return [
            [
                'label' => 'Total KBLI',
                'value' => $total,
                'color' => 'green',
                'icon' => 'fa-table-cells-large',
            ],
            [
                'label' => 'Aktif',
                'value' => $aktif,
                'color' => 'yellow',
                'icon' => 'fa-circle-check',
            ],
            [
                'label' => 'Nonaktif',
                'value' => $nonaktif,
                'color' => 'blue',
                'icon' => 'fa-circle-xmark',
            ],
            [
                'label' => 'Cakupan Terisi',
                'value' => $cakupanTerisi,
                'color' => 'red',
                'icon' => 'fa-file-lines',
            ],
        ];
    }

    private function emptyStats(): array
    {
        return [
            [
                'label' => 'Total KBLI',
                'value' => 0,
                'color' => 'green',
                'icon' => 'fa-table-cells-large',
            ],
            [
                'label' => 'Aktif',
                'value' => 0,
                'color' => 'yellow',
                'icon' => 'fa-circle-check',
            ],
            [
                'label' => 'Nonaktif',
                'value' => 0,
                'color' => 'blue',
                'icon' => 'fa-circle-xmark',
            ],
            [
                'label' => 'Cakupan Terisi',
                'value' => 0,
                'color' => 'red',
                'icon' => 'fa-file-lines',
            ],
        ];
    }

    private function columnMap(): array
    {
        return [
            'id' => $this->firstExistingColumn([
                'id',
            ]),
            'no' => $this->firstExistingColumn([
                'no_urut',
                'No',
                'no',
            ]),
            'kode' => $this->firstExistingColumn([
                'kode_kbli',
                'Kode',
                'kode',
            ]),
            'judul' => $this->firstExistingColumn([
                'judul_kbli',
                'Judul',
                'judul',
                'nama_kbli',
            ]),
            'cakupan' => $this->firstExistingColumn([
                'Cakupan',
                'cakupan',
            ]),
            'tidak_cakupan' => $this->firstExistingColumn([
                'Tidak Cakupan',
                'tidak_cakupan',
                'Tidak_Cakupan',
            ]),
            'status' => $this->firstExistingColumn([
                'status',
                'Status',
            ]),
            'created_at' => $this->firstExistingColumn([
                'created_at',
            ]),
            'updated_at' => $this->firstExistingColumn([
                'updated_at',
            ]),
        ];
    }

    private function firstExistingColumn(
        array $columns
    ): ?string {
        if (! Schema::hasTable($this->table)) {
            return null;
        }

        $existingColumns =
            Schema::getColumnListing($this->table);

        foreach ($columns as $targetColumn) {
            foreach ($existingColumns as $existingColumn) {
                if (
                    strtolower($existingColumn)
                    === strtolower($targetColumn)
                ) {
                    return $existingColumn;
                }
            }
        }

        return null;
    }

    private function selectAlias(
        ?string $column,
        string $alias
    ) {
        if (! $column) {
            return DB::raw(
                'NULL AS "' . $alias . '"'
            );
        }

        return DB::raw(
            $this->quotedColumn($column)
            . ' AS "'
            . $alias
            . '"'
        );
    }

    private function quotedColumn(
        string $column
    ): string {
        return '"'
            . str_replace('"', '""', $column)
            . '"';
    }
}