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
            $dataWilayah = new LengthAwarePaginator(
                [],
                0,
                10,
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            $stats = [
                [
                    'label' => 'Total Wilayah',
                    'value' => 0,
                    'color' => 'mint',
                    'icon' => 'fa-map-location-dot',
                ],
                [
                    'label' => 'Kab/Kota',
                    'value' => 0,
                    'color' => 'cream',
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

            return view('admin.data-wilayah', [
                'tableExists' => false,
                'dataWilayah' => $dataWilayah,
                'stats' => $stats,
                'regencyOptions' => collect(),
                'districtOptions' => collect(),
                'villageOptions' => collect(),
                'mode' => $request->query('mode'),
                'editData' => null,
            ]);
        }

        $query = DataWilayah::query();

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(province_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(province_code) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(regency_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(regency_code) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(district_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(district_code) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(village_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(village_code) LIKE ?', ["%{$search}%"]);

                $q->orWhereRaw('LOWER(COALESCE(keterangan, \'\')) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('regency_code')) {
            $query->where('regency_code', $request->regency_code);
        }

        if ($request->filled('district_code')) {
            $query->where('district_code', $request->district_code);
        }

        if ($request->filled('village_code')) {
            $query->where('village_code', $request->village_code);
        }

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(TRIM(status)) = ?', [
                strtolower(trim($request->status)),
            ]);
        }

        $dataWilayah = $query
            ->orderBy('province_name')
            ->orderBy('regency_name')
            ->orderBy('district_name')
            ->orderBy('village_name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            [
                'label' => 'Total Wilayah',
                'value' => DataWilayah::count(),
                'color' => 'mint',
                'icon' => 'fa-map-location-dot',
            ],
            [
                'label' => 'Kab/Kota',
                'value' => DataWilayah::whereNotNull('regency_code')
                    ->distinct()
                    ->count('regency_code'),
                'color' => 'cream',
                'icon' => 'fa-city',
            ],
            [
                'label' => 'Kecamatan',
                'value' => DataWilayah::whereNotNull('district_code')
                    ->distinct()
                    ->count('district_code'),
                'color' => 'blue',
                'icon' => 'fa-map',
            ],
            [
                'label' => 'Desa/Kelurahan',
                'value' => DataWilayah::whereNotNull('village_code')
                    ->distinct()
                    ->count('village_code'),
                'color' => 'red',
                'icon' => 'fa-location-dot',
            ],
        ];

        $regencyOptions = DataWilayah::query()
            ->select('regency_code', 'regency_name')
            ->whereNotNull('regency_code')
            ->distinct()
            ->orderBy('regency_name')
            ->get();

        $districtOptions = DataWilayah::query()
            ->select('district_code', 'district_name')
            ->when($request->filled('regency_code'), function ($q) use ($request) {
                $q->where('regency_code', $request->regency_code);
            })
            ->whereNotNull('district_code')
            ->distinct()
            ->orderBy('district_name')
            ->get();

        $villageOptions = DataWilayah::query()
            ->select('village_code', 'village_name')
            ->when($request->filled('district_code'), function ($q) use ($request) {
                $q->where('district_code', $request->district_code);
            })
            ->whereNotNull('village_code')
            ->distinct()
            ->orderBy('village_name')
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
            'regencyOptions',
            'districtOptions',
            'villageOptions',
            'mode',
            'editData'
        ));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('data_wilayah')) {
            return redirect()
                ->route('admin.data-wilayah.index')
                ->with('error', 'Tabel data_wilayah belum tersedia di Supabase.');
        }

        $data = $request->validate($this->rules(), $this->messages());

        $data['no_urut'] = DataWilayah::max('no_urut') + 1;

        DataWilayah::create($data);

        return redirect()
            ->route('admin.data-wilayah.index')
            ->with('success', 'Data wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, DataWilayah $dataWilayah)
    {
        if (! Schema::hasTable('data_wilayah')) {
            return redirect()
                ->route('admin.data-wilayah.index')
                ->with('error', 'Tabel data_wilayah belum tersedia di Supabase.');
        }

        $data = $request->validate(
            $this->rules($dataWilayah->id),
            $this->messages()
        );

        $dataWilayah->update($data);

        return redirect()
            ->route('admin.data-wilayah.index')
            ->with('success', 'Data wilayah berhasil diperbarui.');
    }

    public function destroy(DataWilayah $dataWilayah)
    {
        $dataWilayah->delete();

        return redirect()
            ->route('admin.data-wilayah.index')
            ->with('success', 'Data wilayah berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'province_code' => [
                'required',
                'string',
                'max:20',
            ],
            'province_name' => [
                'required',
                'string',
                'max:255',
            ],
            'regency_code' => [
                'required',
                'string',
                'max:20',
            ],
            'regency_name' => [
                'required',
                'string',
                'max:255',
            ],
            'district_code' => [
                'required',
                'string',
                'max:20',
            ],
            'district_name' => [
                'required',
                'string',
                'max:255',
            ],
            'village_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('data_wilayah', 'village_code')->ignore($ignoreId),
            ],
            'village_name' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                'in:Aktif,Nonaktif',
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
            'province_code.required' => 'Kode provinsi wajib diisi.',
            'province_name.required' => 'Nama provinsi wajib diisi.',
            'regency_code.required' => 'Kabupaten/Kota wajib dipilih.',
            'regency_name.required' => 'Nama Kabupaten/Kota wajib diisi.',
            'district_code.required' => 'Kecamatan wajib dipilih.',
            'district_name.required' => 'Nama kecamatan wajib diisi.',
            'village_code.required' => 'Desa/Kelurahan wajib dipilih.',
            'village_code.unique' => 'Desa/Kelurahan ini sudah ada di data wilayah.',
            'village_name.required' => 'Nama desa/kelurahan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}