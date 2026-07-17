<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Tipologi;
use App\Models\Sektor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TipologiController extends Controller
{
    private function mapDbToView($items)
    {
        return $items->map(function ($item) {
            return [
                'id' => $item->id,
                'tingkat_wilayah' => $item->tingkat_wilayah,
                'daerah_analisis' => $item->daerah_analisis,
                'daerah_pembanding' => $item->daerah_pembanding,
                'provinsi' => $item->daerah_pembanding,
                'kabupaten' => $item->daerah_analisis,
                'sektor' => $item->sektor->nama_sektor ?? '-',
                'tahun_awal' => $item->tahun_awal,
                'tahun_akhir' => $item->tahun_akhir,
                'pdrb_sektor_analisis_awal' => $item->pdrb_sektor_analisis_awal,
                'pdrb_sektor_analisis_akhir' => $item->pdrb_sektor_analisis_akhir,
                'total_pdrb_analisis_awal' => $item->total_pdrb_analisis_awal,
                'total_pdrb_analisis_akhir' => $item->total_pdrb_analisis_akhir,
                'pdrb_sektor_pembanding_awal' => $item->pdrb_sektor_pembanding_awal,
                'pdrb_sektor_pembanding_akhir' => $item->pdrb_sektor_pembanding_akhir,
                'total_pdrb_pembanding_awal' => $item->total_pdrb_pembanding_awal,
                'total_pdrb_pembanding_akhir' => $item->total_pdrb_pembanding_akhir,
                'nilai_ss' => $item->nilai_ss,
                'nilai_lq' => $item->nilai_lq,
                'tipologi' => $item->tipologi,
                'riwayat' => 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $query = Tipologi::with('sektor');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('daerah_analisis', 'like', "%{$search}%")
                  ->orWhere('daerah_pembanding', 'like', "%{$search}%")
                  ->orWhereHas('sektor', function ($qSektor) use ($search) {
                      $qSektor->where('nama_sektor', 'like', "%{$search}%");
                  });
            });
        }

        $rawDbData = $query->orderBy('created_at', 'desc')->orderBy('id', 'asc')->get();
        $tipologiData = collect($this->mapDbToView($rawDbData));

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = $tipologiData->firstWhere('id', $request->edit);
        }

        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedData = (new LengthAwarePaginator(
            $tipologiData->forPage($page, $perPage),
            $tipologiData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        ))->onEachSide(1);

        return view('operator.tipologi.index', [
            'tipologiData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }

    // Menghitung Tipologi Sektor berdasarkan pertumbuhan dan kontribusi.
    private function calculateTipologiData($item)
    {
        $pdrbSektorKabAwal = $this->parseNumber($item['pdrb_sektor_analisis_awal'] ?? 0);
        $pdrbSektorKabAkhir = $this->parseNumber($item['pdrb_sektor_analisis_akhir'] ?? 0);
        $pdrbTotalKabAwal = $this->parseNumber($item['total_pdrb_analisis_awal'] ?? 0);
        $pdrbTotalKabAkhir = $this->parseNumber($item['total_pdrb_analisis_akhir'] ?? 0);

        $pdrbSektorProvAwal = $this->parseNumber($item['pdrb_sektor_pembanding_awal'] ?? 0);
        $pdrbSektorProvAkhir = $this->parseNumber($item['pdrb_sektor_pembanding_akhir'] ?? 0);
        $pdrbTotalProvAwal = $this->parseNumber($item['total_pdrb_pembanding_awal'] ?? 0);
        $pdrbTotalProvAkhir = $this->parseNumber($item['total_pdrb_pembanding_akhir'] ?? 0);

        if ($pdrbSektorKabAwal == 0 || $pdrbSektorProvAwal == 0 || $pdrbTotalKabAkhir == 0 || $pdrbTotalProvAkhir == 0 || $pdrbTotalKabAwal == 0 || $pdrbTotalProvAwal == 0) {
            return null; // Prevent Division by Zero
        }

        // 1. MENGHITUNG NILAI TOTAL SHIFT SHARE (dij)
        $rij = ($pdrbSektorKabAkhir - $pdrbSektorKabAwal) / $pdrbSektorKabAwal;
        $rin = ($pdrbSektorProvAkhir - $pdrbSektorProvAwal) / $pdrbSektorProvAwal;
        $rn = ($pdrbTotalProvAkhir - $pdrbTotalProvAwal) / $pdrbTotalProvAwal;

        $nij = $pdrbSektorKabAwal * $rn;
        $mij = $pdrbSektorKabAwal * ($rin - $rn);
        $cij = $pdrbSektorKabAwal * ($rij - $rn);
        $dij = $nij + $mij + $cij;

        // 2. MENGHITUNG NILAI LQ (Menggunakan rata-rata tahun awal dan akhir)
        $lqAwal = ($pdrbSektorKabAwal / $pdrbTotalKabAwal) / ($pdrbSektorProvAwal / $pdrbTotalProvAwal);
        $lqAkhir = ($pdrbSektorKabAkhir / $pdrbTotalKabAkhir) / ($pdrbSektorProvAkhir / $pdrbTotalProvAkhir);
        $lq = ($lqAwal + $lqAkhir) / 2;

        // 3. MENENTUKAN TIPOLOGI SEKTOR
        if ($dij > 0 && $lq > 1) {
            $tipologi = 'Sektor Cepat Maju dan Cepat Tumbuh'; // Kuadran I
        } elseif ($dij <= 0 && $lq > 1) {
            $tipologi = 'Sektor Potensial'; // Kuadran II
        } elseif ($dij > 0 && $lq <= 1) {
            $tipologi = 'Sektor Berkembang'; // Kuadran III
        } else {
            $tipologi = 'Sektor Relatif Tertinggal'; // Kuadran IV
        }

        $daerah_analisis = ($item['tingkat_wilayah'] ?? 'Kabupaten/Kota') === 'Provinsi' ? $item['provinsi'] : $item['kabupaten'];
        $daerah_pembanding = ($item['tingkat_wilayah'] ?? 'Kabupaten/Kota') === 'Provinsi' ? 'Nasional' : $item['provinsi'];

        return [
            'tingkat_wilayah' => $item['tingkat_wilayah'] ?? 'Kabupaten/Kota',
            'provinsi' => $item['provinsi'],
            'kabupaten' => ($item['tingkat_wilayah'] ?? 'Kabupaten/Kota') === 'Provinsi' ? '-' : ($item['kabupaten'] ?? '-'),
            'daerah_analisis' => $daerah_analisis,
            'daerah_pembanding' => $daerah_pembanding,
            'sektor' => $item['sektor'],
            'tahun_awal' => $item['tahun_awal'],
            'tahun_akhir' => $item['tahun_akhir'],

            'pdrb_sektor_analisis_awal' => $pdrbSektorKabAwal,
            'pdrb_sektor_analisis_akhir' => $pdrbSektorKabAkhir,
            'total_pdrb_analisis_awal' => $pdrbTotalKabAwal,
            'total_pdrb_analisis_akhir' => $pdrbTotalKabAkhir,

            'pdrb_sektor_pembanding_awal' => $pdrbSektorProvAwal,
            'pdrb_sektor_pembanding_akhir' => $pdrbSektorProvAkhir,
            'total_pdrb_pembanding_awal' => $pdrbTotalProvAwal,
            'total_pdrb_pembanding_akhir' => $pdrbTotalProvAkhir,

            'nilai_ss' => round($dij, 2),
            'nilai_lq' => round($lq, 4),
            'tipologi' => $tipologi,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat_wilayah' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'nullable|string',
            'sektor' => 'required|string',
            'tahun_awal' => 'required|string',
            'tahun_akhir' => 'required|string',
            'pdrb_sektor_analisis_awal' => 'required',
            'pdrb_sektor_analisis_akhir' => 'required',
            'total_pdrb_analisis_awal' => 'required',
            'total_pdrb_analisis_akhir' => 'required',
            'pdrb_sektor_pembanding_awal' => 'required',
            'pdrb_sektor_pembanding_akhir' => 'required',
            'total_pdrb_pembanding_awal' => 'required',
            'total_pdrb_pembanding_akhir' => 'required',
        ]);

        $data = $this->calculateTipologiData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda agar tidak terjadi pembagian dengan nol.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        Tipologi::create([
            'user_id' => Auth::id() ?? 1,
            'sektor_id' => $sektorModel->sektor_id,
            'tingkat_wilayah' => $data['tingkat_wilayah'],
            'daerah_analisis' => $data['daerah_analisis'],
            'daerah_pembanding' => $data['daerah_pembanding'],
            'tahun_awal' => $data['tahun_awal'],
            'tahun_akhir' => $data['tahun_akhir'],
            'pdrb_sektor_analisis_awal' => $data['pdrb_sektor_analisis_awal'],
            'pdrb_sektor_analisis_akhir' => $data['pdrb_sektor_analisis_akhir'],
            'total_pdrb_analisis_awal' => $data['total_pdrb_analisis_awal'],
            'total_pdrb_analisis_akhir' => $data['total_pdrb_analisis_akhir'],
            'pdrb_sektor_pembanding_awal' => $data['pdrb_sektor_pembanding_awal'],
            'pdrb_sektor_pembanding_akhir' => $data['pdrb_sektor_pembanding_akhir'],
            'total_pdrb_pembanding_awal' => $data['total_pdrb_pembanding_awal'],
            'total_pdrb_pembanding_akhir' => $data['total_pdrb_pembanding_akhir'],
            'nilai_ss' => $data['nilai_ss'],
            'nilai_lq' => $data['nilai_lq'],
            'tipologi' => $data['tipologi']
        ]);

        OperatorController::logActivity('Analisis Tipologi', 'ditambah', "Menambahkan data perhitungan Analisis Tipologi Sektor untuk sektor {$data['sektor']}.");

        return back()->with('success', 'Perhitungan Tipologi Sektor berhasil disimpan secara permanen!');
    }

    public function update(Request $request, $id)
    {
        $tipologi = Tipologi::find($id);
        if (!$tipologi) {
            return redirect()->route('operator.tipologi.index')->with('error', 'Data tidak ditemukan!');
        }

        $data = $this->calculateTipologiData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        $tipologi->update([
            'sektor_id' => $sektorModel->sektor_id,
            'tingkat_wilayah' => $data['tingkat_wilayah'],
            'daerah_analisis' => $data['daerah_analisis'],
            'daerah_pembanding' => $data['daerah_pembanding'],
            'tahun_awal' => $data['tahun_awal'],
            'tahun_akhir' => $data['tahun_akhir'],
            'pdrb_sektor_analisis_awal' => $data['pdrb_sektor_analisis_awal'],
            'pdrb_sektor_analisis_akhir' => $data['pdrb_sektor_analisis_akhir'],
            'total_pdrb_analisis_awal' => $data['total_pdrb_analisis_awal'],
            'total_pdrb_analisis_akhir' => $data['total_pdrb_analisis_akhir'],
            'pdrb_sektor_pembanding_awal' => $data['pdrb_sektor_pembanding_awal'],
            'pdrb_sektor_pembanding_akhir' => $data['pdrb_sektor_pembanding_akhir'],
            'total_pdrb_pembanding_awal' => $data['total_pdrb_pembanding_awal'],
            'total_pdrb_pembanding_akhir' => $data['total_pdrb_pembanding_akhir'],
            'nilai_ss' => $data['nilai_ss'],
            'nilai_lq' => $data['nilai_lq'],
            'tipologi' => $data['tipologi']
        ]);

        OperatorController::logActivity('Analisis Tipologi', 'diperbarui', "Memperbarui data perhitungan Analisis Tipologi Sektor untuk sektor {$data['sektor']}.");

        return redirect()->route('operator.tipologi.index')->with('success', 'Data perhitungan Tipologi Sektor berhasil diperbarui secara permanen!');
    }

    public function destroy($id)
    {
        $tipologi = Tipologi::find($id);
        if ($tipologi) {
            $daerah = $tipologi->daerah_analisis;
            $tipologi->delete();
            OperatorController::logActivity('Tipologi Sektor', 'dihapus', "Menghapus data Tipologi daerah {$daerah}.");
        }

        return back()->with('success', 'Data Tipologi berhasil dihapus secara permanen!');
    }

    public function empty()
    {
        Tipologi::truncate();
        OperatorController::logActivity('Analisis Tipologi Sektor', 'dihapus', "Menghapus semua data perhitungan Tipologi Sektor");
        return back()->with('success', 'Semua data perhitungan Tipologi Sektor berhasil dihapus secara permanen!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!empty($ids)) {
            $count = count($ids);
            Tipologi::whereIn('id', $ids)->delete();
            OperatorController::logActivity('Analisis Tipologi Sektor', 'dihapus', "Menghapus {$count} data perhitungan Tipologi Sektor secara massal");
            return back()->with('success', "{$count} data perhitungan Tipologi Sektor berhasil dihapus secara massal!");
        }
        return back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    public function import(Request $request)
    {
        set_time_limit(300);
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Format data tidak valid.']);
        }

        $successCount = 0;

        // Preload all sectors in memory
        $sektorsCache = Sektor::all()->pluck('sektor_id', 'nama_sektor')->mapWithKeys(fn($id, $name) => [strtolower(trim($name)) => $id])->toArray();

        \Illuminate\Support\Facades\DB::transaction(function () use ($payload, &$successCount, &$sektorsCache) {
            foreach ($payload as $item) {
                $hasProvinsi = isset($item['Provinsi']) || isset($item['Kode Provinsi']) || isset($item['Kode_Provinsi']) || isset($item['Kode Wilayah']) || isset($item['Kode_Wilayah']);
                if (!$hasProvinsi || ! isset($item['Sektor']) || ! isset($item['Tahun Awal']) || ! isset($item['Tahun Akhir']) ||
                    ! isset($item['PDRB Sektor Analisis Awal']) || ! isset($item['PDRB Sektor Analisis Akhir']) ||
                    ! isset($item['Total PDRB Analisis Awal']) || ! isset($item['Total PDRB Analisis Akhir']) ||
                    ! isset($item['PDRB Sektor Pembanding Awal']) || ! isset($item['PDRB Sektor Pembanding Akhir']) ||
                    ! isset($item['Total PDRB Pembanding Awal']) || ! isset($item['Total PDRB Pembanding Akhir'])) {
                    continue;
                }

                $resolved = $this->resolveRegionNames($item);
                $provinsi = $resolved['provinsi'];
                $kabupaten = $resolved['kabupaten'];

                $tingkat = ($kabupaten != '-' && $kabupaten != '') ? 'Kabupaten/Kota' : 'Provinsi';
                
                $sektorName = $this->resolveSektorName($item);
                $sektorKey = strtolower(trim($sektorName));
                
                if (isset($sektorsCache[$sektorKey])) {
                    $sektorId = $sektorsCache[$sektorKey];
                } else {
                    $sektorModel = Sektor::create(['nama_sektor' => $sektorName]);
                    $sektorsCache[$sektorKey] = $sektorModel->sektor_id;
                    $sektorId = $sektorModel->sektor_id;
                }

                $mappedItem = [
                    'tingkat_wilayah' => $tingkat,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'sektor' => $sektorName,
                    'tahun_awal' => $item['Tahun Awal'],
                    'tahun_akhir' => $item['Tahun Akhir'],
                    'pdrb_sektor_analisis_awal' => $item['PDRB Sektor Analisis Awal'] ?? 0,
                    'pdrb_sektor_analisis_akhir' => $item['PDRB Sektor Analisis Akhir'] ?? 0,
                    'total_pdrb_analisis_awal' => $item['Total PDRB Analisis Awal'] ?? 0,
                    'total_pdrb_analisis_akhir' => $item['Total PDRB Analisis Akhir'] ?? 0,
                    'pdrb_sektor_pembanding_awal' => $item['PDRB Sektor Pembanding Awal'] ?? 0,
                    'pdrb_sektor_pembanding_akhir' => $item['PDRB Sektor Pembanding Akhir'] ?? 0,
                    'total_pdrb_pembanding_awal' => $item['Total PDRB Pembanding Awal'] ?? 0,
                    'total_pdrb_pembanding_akhir' => $item['Total PDRB Pembanding Akhir'] ?? 0,
                ];

                $newData = $this->calculateTipologiData($mappedItem);

                if ($newData) {
                    Tipologi::create([
                        'user_id' => Auth::id() ?? 1,
                        'sektor_id' => $sektorId,
                        'tingkat_wilayah' => $newData['tingkat_wilayah'],
                        'daerah_analisis' => $newData['daerah_analisis'],
                        'daerah_pembanding' => $newData['daerah_pembanding'],
                        'tahun_awal' => $newData['tahun_awal'],
                        'tahun_akhir' => $newData['tahun_akhir'],
                        'pdrb_sektor_analisis_awal' => $newData['pdrb_sektor_analisis_awal'],
                        'pdrb_sektor_analisis_akhir' => $newData['pdrb_sektor_analisis_akhir'],
                        'total_pdrb_analisis_awal' => $newData['total_pdrb_analisis_awal'],
                        'total_pdrb_analisis_akhir' => $newData['total_pdrb_analisis_akhir'],
                        'pdrb_sektor_pembanding_awal' => $newData['pdrb_sektor_pembanding_awal'],
                        'pdrb_sektor_pembanding_akhir' => $newData['pdrb_sektor_pembanding_akhir'],
                        'total_pdrb_pembanding_awal' => $newData['total_pdrb_pembanding_awal'],
                        'total_pdrb_pembanding_akhir' => $newData['total_pdrb_pembanding_akhir'],
                        'nilai_ss' => $newData['nilai_ss'],
                        'nilai_lq' => $newData['nilai_lq'],
                        'tipologi' => $newData['tipologi']
                    ]);
                    $successCount++;
                }
            }
        });

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis Tipologi', 'diimpor', "Mengimpor {$successCount} data Analisis Tipologi Sektor secara massal dari Template Master.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format kolom sesuai dengan Template Master.']);
    }
}

