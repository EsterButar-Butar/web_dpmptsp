<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ShiftShare;
use App\Models\Sektor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SsController extends Controller
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
                'pdrb_sektor_pembanding_awal' => $item->pdrb_sektor_pembanding_awal,
                'pdrb_sektor_pembanding_akhir' => $item->pdrb_sektor_pembanding_akhir,
                'total_pdrb_pembanding_awal' => $item->total_pdrb_pembanding_awal,
                'total_pdrb_pembanding_akhir' => $item->total_pdrb_pembanding_akhir,
                'rij' => number_format((float) $item->rij, 3, '.', ''),
                'rin' => number_format((float) $item->rin, 3, '.', ''),
                'rn' => number_format((float) $item->rn, 3, '.', ''),
                'nij' => $item->nij,
                'mij' => $item->mij,
                'cij' => $item->cij,
                'dij' => $item->dij,
                'status_pertumbuhan' => $item->status_pertumbuhan,
                'status_daya_saing' => $item->status_daya_saing,
                'riwayat' => 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $rawDbData = ShiftShare::with('sektor')->orderBy('created_at', 'desc')->orderBy('id', 'asc')->get();
        $ssData = collect($this->mapDbToView($rawDbData));

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = $ssData->firstWhere('id', $request->edit);
        }

        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedData = (new LengthAwarePaginator(
            $ssData->forPage($page, $perPage),
            $ssData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        ))->onEachSide(1);

        return view('operator.ss.index', [
            'ssData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }

    // Menghitung analisis Shift Share berdasarkan data input.
    private function calculateSSData($item)
    {
        $pdrbAwal = $this->parseNumber($item['pdrb_sektor_analisis_awal']);
        $pdrbAkhir = $this->parseNumber($item['pdrb_sektor_analisis_akhir']);
        $pdbAwal = $this->parseNumber($item['pdrb_sektor_pembanding_awal']);
        $pdbAkhir = $this->parseNumber($item['pdrb_sektor_pembanding_akhir']);
        $nasionalAwal = $this->parseNumber($item['total_pdrb_pembanding_awal']);
        $nasionalAkhir = $this->parseNumber($item['total_pdrb_pembanding_akhir']);

        if ($pdrbAwal == 0 || $pdbAwal == 0 || $nasionalAwal == 0) {
            return null; // Prevent Division by Zero
        }

        // Rates
        $rij = ($pdrbAkhir - $pdrbAwal) / $pdrbAwal;
        $rin = ($pdbAkhir - $pdbAwal) / $pdbAwal;
        $rn = ($nasionalAkhir - $nasionalAwal) / $nasionalAwal;

        // Components
        $nij = $pdrbAwal * $rn;
        $mij = $pdrbAwal * ($rin - $rn);
        $cij = $pdrbAwal * ($rij - $rn); // Using rn as per guidebook
        $dij = $nij + $mij + $cij;

        // Status
        $statusPertumbuhan = $mij > 0 ? 'Pertumbuhan Cepat' : 'Pertumbuhan Lambat';
        $statusDayaSaing = $cij > 0 ? 'Daya Saing Baik' : 'Tidak Dapat Bersaing';

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
            'pdrb_sektor_analisis_awal' => $pdrbAwal,
            'pdrb_sektor_analisis_akhir' => $pdrbAkhir,
            'pdrb_sektor_pembanding_awal' => $pdbAwal,
            'pdrb_sektor_pembanding_akhir' => $pdbAkhir,
            'total_pdrb_pembanding_awal' => $nasionalAwal,
            'total_pdrb_pembanding_akhir' => $nasionalAkhir,
            'rij' => round($rij, 4),
            'rin' => round($rin, 4),
            'rn' => round($rn, 4),
            'nij' => round($nij, 2),
            'mij' => round($mij, 2),
            'cij' => round($cij, 2),
            'dij' => round($dij, 2),
            'status_pertumbuhan' => $statusPertumbuhan,
            'status_daya_saing' => $statusDayaSaing,
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
            'pdrb_sektor_pembanding_awal' => 'required',
            'pdrb_sektor_pembanding_akhir' => 'required',
            'total_pdrb_pembanding_awal' => 'required',
            'total_pdrb_pembanding_akhir' => 'required',
        ]);

        $data = $this->calculateSSData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        ShiftShare::create([
            'user_id' => Auth::id() ?? 1,
            'sektor_id' => $sektorModel->sektor_id,
            'tingkat_wilayah' => $data['tingkat_wilayah'],
            'daerah_analisis' => $data['daerah_analisis'],
            'daerah_pembanding' => $data['daerah_pembanding'],
            'tahun_awal' => $data['tahun_awal'],
            'tahun_akhir' => $data['tahun_akhir'],
            'pdrb_sektor_analisis_awal' => $data['pdrb_sektor_analisis_awal'],
            'pdrb_sektor_analisis_akhir' => $data['pdrb_sektor_analisis_akhir'],
            'pdrb_sektor_pembanding_awal' => $data['pdrb_sektor_pembanding_awal'],
            'pdrb_sektor_pembanding_akhir' => $data['pdrb_sektor_pembanding_akhir'],
            'total_pdrb_pembanding_awal' => $data['total_pdrb_pembanding_awal'],
            'total_pdrb_pembanding_akhir' => $data['total_pdrb_pembanding_akhir'],
            'rij' => $data['rij'],
            'rin' => $data['rin'],
            'rn' => $data['rn'],
            'nij' => $data['nij'],
            'mij' => $data['mij'],
            'cij' => $data['cij'],
            'dij' => $data['dij'],
            'status_pertumbuhan' => $data['status_pertumbuhan'],
            'status_daya_saing' => $data['status_daya_saing']
        ]);

        OperatorController::logActivity('Analisis SSA', 'ditambah', "Menambahkan data perhitungan Analisis Shift Share untuk sektor {$data['sektor']}.");

        return back()->with('success', 'Perhitungan SS berhasil disimpan secara permanen!');
    }

    public function update(Request $request, $id)
    {
        $ss = ShiftShare::find($id);
        if (!$ss) {
            return redirect()->route('operator.ss.index')->with('error', 'Data tidak ditemukan!');
        }

        $data = $this->calculateSSData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        $ss->update([
            'sektor_id' => $sektorModel->sektor_id,
            'tingkat_wilayah' => $data['tingkat_wilayah'],
            'daerah_analisis' => $data['daerah_analisis'],
            'daerah_pembanding' => $data['daerah_pembanding'],
            'tahun_awal' => $data['tahun_awal'],
            'tahun_akhir' => $data['tahun_akhir'],
            'pdrb_sektor_analisis_awal' => $data['pdrb_sektor_analisis_awal'],
            'pdrb_sektor_analisis_akhir' => $data['pdrb_sektor_analisis_akhir'],
            'pdrb_sektor_pembanding_awal' => $data['pdrb_sektor_pembanding_awal'],
            'pdrb_sektor_pembanding_akhir' => $data['pdrb_sektor_pembanding_akhir'],
            'total_pdrb_pembanding_awal' => $data['total_pdrb_pembanding_awal'],
            'total_pdrb_pembanding_akhir' => $data['total_pdrb_pembanding_akhir'],
            'rij' => $data['rij'],
            'rin' => $data['rin'],
            'rn' => $data['rn'],
            'nij' => $data['nij'],
            'mij' => $data['mij'],
            'cij' => $data['cij'],
            'dij' => $data['dij'],
            'status_pertumbuhan' => $data['status_pertumbuhan'],
            'status_daya_saing' => $data['status_daya_saing']
        ]);

        OperatorController::logActivity('Analisis SSA', 'diperbarui', "Memperbarui data perhitungan Analisis Shift Share untuk sektor {$data['sektor']}.");

        return redirect()->route('operator.ss.index')->with('success', 'Data perhitungan SS berhasil diperbarui secara permanen!');
    }

    public function destroy($id)
    {
        $ss = ShiftShare::find($id);
        if ($ss) {
            $daerah = $ss->daerah_analisis;
            $ss->delete();
            OperatorController::logActivity('Analisis SS', 'dihapus', "Menghapus data perhitungan SS daerah {$daerah}.");
        }

        return back()->with('success', 'Data Shift Share berhasil dihapus secara permanen!');
    }

    public function empty()
    {
        ShiftShare::truncate();
        OperatorController::logActivity('Analisis Shift Share', 'dihapus', "Menghapus semua data perhitungan Shift Share");
        return back()->with('success', 'Semua data perhitungan Shift Share berhasil dihapus secara permanen!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!empty($ids)) {
            $count = count($ids);
            ShiftShare::whereIn('id', $ids)->delete();
            OperatorController::logActivity('Analisis Shift Share', 'dihapus', "Menghapus {$count} data perhitungan Shift Share secara massal");
            return back()->with('success', "{$count} data perhitungan Shift Share berhasil dihapus secara massal!");
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
                    'pdrb_sektor_pembanding_awal' => $item['PDRB Sektor Pembanding Awal'] ?? 0,
                    'pdrb_sektor_pembanding_akhir' => $item['PDRB Sektor Pembanding Akhir'] ?? 0,
                    'total_pdrb_pembanding_awal' => $item['Total PDRB Pembanding Awal'] ?? 0,
                    'total_pdrb_pembanding_akhir' => $item['Total PDRB Pembanding Akhir'] ?? 0,
                ];

                $newData = $this->calculateSSData($mappedItem);

                if ($newData) {
                    ShiftShare::create([
                        'user_id' => Auth::id() ?? 1,
                        'sektor_id' => $sektorId,
                        'tingkat_wilayah' => $newData['tingkat_wilayah'],
                        'daerah_analisis' => $newData['daerah_analisis'],
                        'daerah_pembanding' => $newData['daerah_pembanding'],
                        'tahun_awal' => $newData['tahun_awal'],
                        'tahun_akhir' => $newData['tahun_akhir'],
                        'pdrb_sektor_analisis_awal' => $newData['pdrb_sektor_analisis_awal'],
                        'pdrb_sektor_analisis_akhir' => $newData['pdrb_sektor_analisis_akhir'],
                        'pdrb_sektor_pembanding_awal' => $newData['pdrb_sektor_pembanding_awal'],
                        'pdrb_sektor_pembanding_akhir' => $newData['pdrb_sektor_pembanding_akhir'],
                        'total_pdrb_pembanding_awal' => $newData['total_pdrb_pembanding_awal'],
                        'total_pdrb_pembanding_akhir' => $newData['total_pdrb_pembanding_akhir'],
                        'rij' => $newData['rij'],
                        'rin' => $newData['rin'],
                        'rn' => $newData['rn'],
                        'nij' => $newData['nij'],
                        'mij' => $newData['mij'],
                        'cij' => $newData['cij'],
                        'dij' => $newData['dij'],
                        'status_pertumbuhan' => $newData['status_pertumbuhan'],
                        'status_daya_saing' => $newData['status_daya_saing']
                    ]);
                    $successCount++;
                }
            }
        });

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis SSA', 'diimpor', "Mengimpor {$successCount} data Analisis Shift Share secara massal dari Template Master.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format kolom sesuai dengan Template Master.']);
    }
}

