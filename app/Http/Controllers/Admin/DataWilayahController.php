<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataWilayah;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class DataWilayahController extends Controller
{
    public function index(Request $request)
    {
        $tableExists = Schema::hasTable('data_wilayah');

        if (! $tableExists) {
            $dataWilayah = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('admin.data-wilayah', [
                'tableExists' => false,
                'dataWilayah' => $dataWilayah,
                'stats' => $this->emptyStats(),
                'kabupatenOptions' => collect(),
                'kecamatanOptions' => collect(),
                'desaOptions' => collect(),
                'wilayahOptions' => collect(),
                'mode' => $request->query('mode'),
                'editData' => null,
            ]);
        }

        $query = DataWilayah::query();

        if ($request->filled('search')) {
            $search = '%' . strtolower(trim($request->search)) . '%';

            $query->where(function ($subQuery) use ($search) {
                $columns = [
                    'nama_provinsi',
                    'kode_provinsi',
                    'nama_kabupaten',
                    'kode_kabupaten',
                    'nama_kecamatan',
                    'kode_kecamatan',
                    'nama_desa',
                    'kode_desa',
                    'status',
                    'keterangan',
                ];

                foreach ($columns as $column) {
                    $subQuery->orWhereRaw(
                        "LOWER(CAST({$column} AS TEXT)) LIKE ?",
                        [$search]
                    );
                }
            });
        }

        if ($request->filled('kode_kabupaten')) {
            $query->where(
                'kode_kabupaten',
                trim($request->kode_kabupaten)
            );
        }

        if ($request->filled('kode_kecamatan')) {
            $query->where(
                'kode_kecamatan',
                trim($request->kode_kecamatan)
            );
        }

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(TRIM(status)) = ?', [
                strtolower(trim($request->status)),
            ]);
        }

        $dataWilayah = $query
            ->orderBy('nama_provinsi')
            ->orderBy('nama_kabupaten')
            ->orderBy('nama_kecamatan')
            ->orderBy('nama_desa')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            [
                'label' => 'Total Wilayah',
                'value' => DataWilayah::count(),
                'color' => 'green',
                'icon' => 'fa-map-location-dot',
            ],
            [
                'label' => 'Kab/Kota',
                'value' => DataWilayah::query()
                    ->whereNotNull('kode_kabupaten')
                    ->where('kode_kabupaten', '<>', '')
                    ->distinct()
                    ->count('kode_kabupaten'),
                'color' => 'yellow',
                'icon' => 'fa-city',
            ],
            [
                'label' => 'Kecamatan',
                'value' => DataWilayah::query()
                    ->whereNotNull('kode_kecamatan')
                    ->where('kode_kecamatan', '<>', '')
                    ->distinct()
                    ->count('kode_kecamatan'),
                'color' => 'blue',
                'icon' => 'fa-map',
            ],
            [
                'label' => 'Desa/Kelurahan',
                'value' => DataWilayah::query()
                    ->whereNotNull('kode_desa')
                    ->where('kode_desa', '<>', '')
                    ->distinct()
                    ->count('kode_desa'),
                'color' => 'red',
                'icon' => 'fa-location-dot',
            ],
        ];

        $kabupatenOptions = DataWilayah::query()
            ->select('kode_kabupaten', 'nama_kabupaten')
            ->whereNotNull('kode_kabupaten')
            ->where('kode_kabupaten', '<>', '')
            ->distinct()
            ->orderBy('nama_kabupaten')
            ->get();

        $kecamatanOptions = DataWilayah::query()
            ->select(
                'kode_kabupaten',
                'kode_kecamatan',
                'nama_kecamatan'
            )
            ->when(
                $request->filled('kode_kabupaten'),
                function ($optionQuery) use ($request) {
                    $optionQuery->where(
                        'kode_kabupaten',
                        trim($request->kode_kabupaten)
                    );
                }
            )
            ->whereNotNull('kode_kecamatan')
            ->where('kode_kecamatan', '<>', '')
            ->distinct()
            ->orderBy('nama_kecamatan')
            ->get();

        $desaOptions = DataWilayah::query()
            ->select(
                'kode_kecamatan',
                'kode_desa',
                'nama_desa'
            )
            ->when(
                $request->filled('kode_kecamatan'),
                function ($optionQuery) use ($request) {
                    $optionQuery->where(
                        'kode_kecamatan',
                        trim($request->kode_kecamatan)
                    );
                }
            )
            ->whereNotNull('kode_desa')
            ->where('kode_desa', '<>', '')
            ->distinct()
            ->orderBy('nama_desa')
            ->get();

        $wilayahOptions = DataWilayah::query()
            ->select(
                'kode_provinsi',
                'nama_provinsi',
                'kode_kabupaten',
                'nama_kabupaten',
                'kode_kecamatan',
                'nama_kecamatan'
            )
            ->whereNotNull('kode_kabupaten')
            ->whereNotNull('kode_kecamatan')
            ->distinct()
            ->orderBy('nama_provinsi')
            ->orderBy('nama_kabupaten')
            ->orderBy('nama_kecamatan')
            ->get();

        $mode = $request->query('mode');
        $editData = null;

        if ($request->filled('edit')) {
            $editData = DataWilayah::findOrFail($request->edit);
            $mode = 'edit';
        }

        return view('admin.data-wilayah', compact(
            'tableExists',
            'dataWilayah',
            'stats',
            'kabupatenOptions',
            'kecamatanOptions',
            'desaOptions',
            'wilayahOptions',
            'mode',
            'editData'
        ));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('data_wilayah')) {
            return redirect()
                ->route('admin.data-wilayah.index')
                ->with(
                    'error',
                    'Tabel data_wilayah belum tersedia di Supabase.'
                );
        }

        $data = $request->validate(
            $this->rules(),
            $this->messages()
        );

        $data['no_urut'] =
            (DataWilayah::query()->max('no_urut') ?? 0) + 1;

        DataWilayah::create($data);

        return redirect()
            ->route('admin.data-wilayah.index')
            ->with(
                'success',
                'Data wilayah berhasil ditambahkan.'
            );
    }

    public function update(
        Request $request,
        DataWilayah $dataWilayah
    ) {
        $data = $request->validate(
            $this->rules($dataWilayah->id),
            $this->messages()
        );

        $dataWilayah->update($data);

        return redirect()
            ->route('admin.data-wilayah.index')
            ->with(
                'success',
                'Data wilayah berhasil diperbarui.'
            );
    }

    public function destroy(DataWilayah $dataWilayah)
    {
        $dataWilayah->delete();

        return redirect()
            ->route('admin.data-wilayah.index')
            ->with(
                'success',
                'Data wilayah berhasil dihapus.'
            );
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'nama_provinsi' => [
                'required',
                'string',
                'max:255',
            ],
            'kode_provinsi' => [
                'required',
                'string',
                'max:20',
            ],
            'nama_kabupaten' => [
                'required',
                'string',
                'max:255',
            ],
            'kode_kabupaten' => [
                'required',
                'string',
                'max:20',
            ],
            'nama_kecamatan' => [
                'required',
                'string',
                'max:255',
            ],
            'kode_kecamatan' => [
                'required',
                'string',
                'max:30',
            ],
            'nama_desa' => [
                'required',
                'string',
                'max:255',
            ],
            'kode_desa' => [
                'required',
                'string',
                'max:40',
                Rule::unique(
                    'data_wilayah',
                    'kode_desa'
                )->ignore($ignoreId),
            ],
            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Nonaktif',
                ]),
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'nama_provinsi.required' =>
                'Nama provinsi wajib diisi.',
            'kode_provinsi.required' =>
                'Kode provinsi wajib diisi.',
            'nama_kabupaten.required' =>
                'Nama kabupaten/kota wajib diisi.',
            'kode_kabupaten.required' =>
                'Kode kabupaten/kota wajib diisi.',
            'nama_kecamatan.required' =>
                'Nama kecamatan wajib diisi.',
            'kode_kecamatan.required' =>
                'Kode kecamatan wajib diisi.',
            'nama_desa.required' =>
                'Nama desa/kelurahan wajib diisi.',
            'kode_desa.required' =>
                'Kode desa/kelurahan wajib diisi.',
            'kode_desa.unique' =>
                'Kode desa/kelurahan ini sudah ada.',
            'status.required' =>
                'Status wajib dipilih.',
            'status.in' =>
                'Status yang dipilih tidak valid.',
        ];
    }

    private function emptyStats(): array
    {
        return [
            [
                'label' => 'Total Wilayah',
                'value' => 0,
                'color' => 'green',
                'icon' => 'fa-map-location-dot',
            ],
            [
                'label' => 'Kab/Kota',
                'value' => 0,
                'color' => 'yellow',
                'icon' => 'fa-city',
            ],
            [
                'label' => 'Kecamatan',
                'value' => 0,
                'color' => 'blue',
                'icon' => 'fa-map',
            ],
            [
                'label' => 'Desa/Kelurahan',
                'value' => 0,
                'color' => 'red',
                'icon' => 'fa-location-dot',
            ],
        ];
    }
}