<?php

// Controller untuk mengelola analisis Shift Share (SS) bagi Operator
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
                'provinsi' => $item->tingkat_wilayah === 'Provinsi' ? $item->daerah_analisis : $item->daerah_pembanding,
                'kabupaten' => $item->tingkat_wilayah === 'Provinsi' ? '' : $item->daerah_analisis,
                'sektor' => $item->sektor->nama_sektor ?? '-',
                'tahun_awal' => $item->tahun_awal,
                'tahun_akhir' => $item->tahun_akhir,
                'pdrb_sektor_analisis_awal' => $item->pdrb_sektor_analisis_awal,
                'pdrb_sektor_analisis_akhir' => $item->pdrb_sektor_analisis_akhir,
                'pdrb_sektor_pembanding_awal' => $item->pdrb_sektor_pembanding_awal,
                'pdrb_sektor_pembanding_akhir' => $item->pdrb_sektor_pembanding_akhir,
                'total_pdrb_pembanding_awal' => $item->total_pdrb_pembanding_awal,
                'total_pdrb_pembanding_akhir' => $item->total_pdrb_pembanding_akhir,
                'rij' => number_format((float) $item->rij, 2, '.', ''),
                'rin' => number_format((float) $item->rin, 2, '.', ''),
                'rn' => number_format((float) $item->rn, 2, '.', ''),
                'nij' => $item->nij,
                'mij' => $item->mij,
                'cij' => $item->cij,
                'dij' => $item->dij,
                'status_pertumbuhan' => $item->status_pertumbuhan,
                'status_daya_saing' => $item->status_daya_saing,
                'riwayat' => $item->created_at->timestamp === $item->updated_at->timestamp
                    ? 'Ditambah ' . $item->created_at->format('d-m-Y')
                    : 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $query = ShiftShare::with('sektor');

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

    // Menghitung analisis Shift Share secara dinamis (Year-on-Year / n-1) untuk satu transisi (2 tahun).
    private function calculateSSData($prev, $curr, $tingkatWilayah, $provinsi, $kabupaten, $sektor)
    {
        $xijAwal = $this->parseNumber($prev['pdrb_sektor_analisis']);
        $xijAkhir = $this->parseNumber($curr['pdrb_sektor_analisis']);
        $xiAwal = $this->parseNumber($prev['pdrb_sektor_pembanding']);
        $xiAkhir = $this->parseNumber($curr['pdrb_sektor_pembanding']);
        $pdrbTotalPembandingAwal = $this->parseNumber($prev['total_pdrb_pembanding']);
        $pdrbTotalPembandingAkhir = $this->parseNumber($curr['total_pdrb_pembanding']);

        // rn (Kinerja Perekonomian Nasional/Provinsi)
        $rn = 0;
        if ($pdrbTotalPembandingAwal > 0) {
            $rn = ($pdrbTotalPembandingAkhir - $pdrbTotalPembandingAwal) / $pdrbTotalPembandingAwal;
        }
        $nij = $xijAwal * $rn;

        // rin (Kinerja Pertumbuhan Proporsional)
        $rin = 0;
        if ($xiAwal > 0) {
            $rin = ($xiAkhir - $xiAwal) / $xiAwal;
        } else {
            $rin = $xiAkhir > 0 ? 1 : 0;
        }
        $mij = $xijAwal * ($rin - $rn);

        // ri (Kinerja Pertumbuhan Pangsa Wilayah)
        $ri = 0;
        if ($xijAwal > 0) {
            $ri = ($xijAkhir - $xijAwal) / $xijAwal;
        } else {
            $ri = $xijAkhir > 0 ? 1 : 0;
        }
        $cij = $xijAwal * ($ri - $rin);

        $dij = $nij + $mij + $cij;

        // Status
        $statusPertumbuhan = $mij > 0 ? 'Pertumbuhan Cepat' : 'Pertumbuhan Lambat';
        $statusDayaSaing = $cij > 0 ? 'Daya Saing Baik' : 'Tidak Dapat Bersaing';

        $daerah_analisis = ($tingkatWilayah === 'Provinsi') ? $provinsi : $kabupaten;
        $daerah_pembanding = ($tingkatWilayah === 'Provinsi') ? 'Nasional' : $provinsi;

        return [
            'tingkat_wilayah' => $tingkatWilayah,
            'provinsi' => $provinsi,
            'kabupaten' => $tingkatWilayah === 'Provinsi' ? '-' : ($kabupaten ?? '-'),
            'daerah_analisis' => $daerah_analisis,
            'daerah_pembanding' => $daerah_pembanding,
            'sektor' => $sektor,

            'tahun_awal' => $prev['tahun'],
            'tahun_akhir' => $curr['tahun'],

            'pdrb_sektor_analisis_awal' => $xijAwal,
            'pdrb_sektor_analisis_akhir' => $xijAkhir,

            'pdrb_sektor_pembanding_awal' => $xiAwal,
            'pdrb_sektor_pembanding_akhir' => $xiAkhir,

            'total_pdrb_pembanding_awal' => $pdrbTotalPembandingAwal,
            'total_pdrb_pembanding_akhir' => $pdrbTotalPembandingAkhir,

            'rij' => round($ri, 4),
            'rin' => round($rin, 4),
            'rn' => round($rn, 4),

            'nij' => round($nij, 2),
            'mij' => round($mij, 2),
            'cij' => round($cij, 2),
            'dij' => round($nij, 2) + round($mij, 2) + round($cij, 2),

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
            'tahun' => 'required|array',
            'pdrb_sektor_analisis' => 'required|array',
            'pdrb_sektor_pembanding' => 'required|array',
            'total_pdrb_pembanding' => 'required|array',
        ]);

        $yearsData = [];
        for ($i = 0; $i < count($request->tahun); $i++) {
            $yearsData[] = [
                'tahun' => $request->tahun[$i],
                'pdrb_sektor_analisis' => $request->pdrb_sektor_analisis[$i],
                'pdrb_sektor_pembanding' => $request->pdrb_sektor_pembanding[$i],
                'total_pdrb_pembanding' => $request->total_pdrb_pembanding[$i],
            ];
        }

        // Sort data by year ascending
        usort($yearsData, function ($a, $b) {
            return (int) $a['tahun'] <=> (int) $b['tahun'];
        });

        if (count($yearsData) < 2) {
            return back()->with('error', 'Terjadi kesalahan perhitungan atau jumlah tahun kurang dari 2.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $request->sektor]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($yearsData, $request, $sektorModel) {
            for ($i = 1; $i < count($yearsData); $i++) {
                $prev = $yearsData[$i - 1];
                $curr = $yearsData[$i];

                $data = $this->calculateSSData($prev, $curr, $request->tingkat_wilayah, $request->provinsi, $request->kabupaten, $request->sektor);

                if ($data) {
                    ShiftShare::updateOrCreate([
                        'user_id' => Auth::id() ?? 1,
                        'sektor_id' => $sektorModel->sektor_id,
                        'tahun_awal' => $data['tahun_awal'],
                        'tahun_akhir' => $data['tahun_akhir'],
                    ], [
                        'tingkat_wilayah' => $data['tingkat_wilayah'],
                        'daerah_analisis' => $data['daerah_analisis'],
                        'daerah_pembanding' => $data['daerah_pembanding'],
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
                }
            }
        });

        OperatorController::logActivity('Analisis SSA', 'ditambah', "Menambahkan data perhitungan Analisis Shift Share untuk sektor {$request->sektor}.");

        return back()->with('success', 'Perhitungan SS berhasil disimpan secara permanen!');
    }

    public function update(Request $request, $id)
    {
        $ss = ShiftShare::find($id);
        if (!$ss) {
            return redirect()->route('operator.ss.index')->with('error', 'Data tidak ditemukan!');
        }

        $request->validate([
            'tingkat_wilayah' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'nullable|string',
            'sektor' => 'required|string',
            'tahun' => 'required|array',
            'pdrb_sektor_analisis' => 'required|array',
            'pdrb_sektor_pembanding' => 'required|array',
            'total_pdrb_pembanding' => 'required|array',
        ]);

        $yearsData = [];
        for ($i = 0; $i < count($request->tahun); $i++) {
            $yearsData[] = [
                'tahun' => $request->tahun[$i],
                'pdrb_sektor_analisis' => $request->pdrb_sektor_analisis[$i],
                'pdrb_sektor_pembanding' => $request->pdrb_sektor_pembanding[$i],
                'total_pdrb_pembanding' => $request->total_pdrb_pembanding[$i],
            ];
        }

        // Sort data by year ascending
        usort($yearsData, function ($a, $b) {
            return (int) $a['tahun'] <=> (int) $b['tahun'];
        });

        if (count($yearsData) < 2) {
            return back()->with('error', 'Terjadi kesalahan perhitungan atau jumlah tahun kurang dari 2.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $request->sektor]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($yearsData, $request, $sektorModel, $ss) {
            // Update the main record with the first transition
            $prev = $yearsData[0];
            $curr = $yearsData[1];
            $data = $this->calculateSSData($prev, $curr, $request->tingkat_wilayah, $request->provinsi, $request->kabupaten, $request->sektor);

            if ($data) {
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
            }

            // Create/update any subsequent transitions as separate records
            for ($i = 2; $i < count($yearsData); $i++) {
                $prevSub = $yearsData[$i - 1];
                $currSub = $yearsData[$i];
                $subData = $this->calculateSSData($prevSub, $currSub, $request->tingkat_wilayah, $request->provinsi, $request->kabupaten, $request->sektor);

                if ($subData) {
                    ShiftShare::updateOrCreate([
                        'user_id' => Auth::id() ?? 1,
                        'sektor_id' => $sektorModel->sektor_id,
                        'tahun_awal' => $subData['tahun_awal'],
                        'tahun_akhir' => $subData['tahun_akhir'],
                    ], [
                        'tingkat_wilayah' => $subData['tingkat_wilayah'],
                        'daerah_analisis' => $subData['daerah_analisis'],
                        'daerah_pembanding' => $subData['daerah_pembanding'],
                        'pdrb_sektor_analisis_awal' => $subData['pdrb_sektor_analisis_awal'],
                        'pdrb_sektor_analisis_akhir' => $subData['pdrb_sektor_analisis_akhir'],
                        'pdrb_sektor_pembanding_awal' => $subData['pdrb_sektor_pembanding_awal'],
                        'pdrb_sektor_pembanding_akhir' => $subData['pdrb_sektor_pembanding_akhir'],
                        'total_pdrb_pembanding_awal' => $subData['total_pdrb_pembanding_awal'],
                        'total_pdrb_pembanding_akhir' => $subData['total_pdrb_pembanding_akhir'],
                        'rij' => $subData['rij'],
                        'rin' => $subData['rin'],
                        'rn' => $subData['rn'],
                        'nij' => $subData['nij'],
                        'mij' => $subData['mij'],
                        'cij' => $subData['cij'],
                        'dij' => $subData['dij'],
                        'status_pertumbuhan' => $subData['status_pertumbuhan'],
                        'status_daya_saing' => $subData['status_daya_saing']
                    ]);
                }
            }
        });

        OperatorController::logActivity('Analisis SSA', 'diperbarui', "Memperbarui data perhitungan Analisis Shift Share untuk sektor {$request->sektor}.");

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
        if (!$payload || !is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Format data tidak valid.']);
        }

        $successCount = 0;
        $sektorsCache = Sektor::all()->pluck('sektor_id', 'nama_sektor')->mapWithKeys(fn($id, $name) => [strtolower(trim($name)) => $id])->toArray();

        // Group rows by Region and Sector for Relational calculation
        $groupedData = [];

        foreach ($payload as $rawItem) {
            $item = $this->normalizeKeys($rawItem);
            $hasProvinsi = isset($item['provinsi']) || isset($item['kodeprovinsi']) || isset($item['kodewilayah']);
            if (
                !$hasProvinsi || !isset($item['sektor']) || !isset($item['tahun']) ||
                !isset($item['pdrbsektor']) ||
                !isset($item['pdrbsektorpembanding']) || !isset($item['totalpdrbpembanding'])
            ) {
                continue;
            }

            $resolved = $this->resolveRegionNames($rawItem);
            $provinsi = $resolved['provinsi'];
            $kabupaten = $resolved['kabupaten'];
            $tingkat = ($kabupaten != '-' && $kabupaten != '') ? 'Kabupaten/Kota' : 'Provinsi';

            $sektorName = $this->resolveSektorName($rawItem);
            $groupKey = $tingkat . '_' . $provinsi . '_' . $kabupaten . '_' . $sektorName;

            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'tingkat_wilayah' => $tingkat,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'sektor' => $sektorName,
                    'years' => []
                ];
            }

            $groupedData[$groupKey]['years'][] = [
                'tahun' => $item['tahun'],
                'pdrb_sektor_analisis' => $item['pdrbsektor'],
                'pdrb_sektor_pembanding' => $item['pdrbsektorpembanding'],
                'total_pdrb_pembanding' => $item['totalpdrbpembanding'],
            ];
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($groupedData, &$successCount, &$sektorsCache) {
            foreach ($groupedData as $group) {
                // Minimum 2 years for YoY
                if (count($group['years']) < 2)
                    continue;

                // Sort by year
                usort($group['years'], function ($a, $b) {
                    return (int) $a['tahun'] <=> (int) $b['tahun'];
                });

                $sektorKey = strtolower(trim($group['sektor']));
                if (isset($sektorsCache[$sektorKey])) {
                    $sektorId = $sektorsCache[$sektorKey];
                } else {
                    $sektorModel = Sektor::create(['nama_sektor' => $group['sektor']]);
                    $sektorsCache[$sektorKey] = $sektorModel->sektor_id;
                    $sektorId = $sektorModel->sektor_id;
                }

                for ($i = 1; $i < count($group['years']); $i++) {
                    $prev = $group['years'][$i - 1];
                    $curr = $group['years'][$i];

                    $newData = $this->calculateSSData($prev, $curr, $group['tingkat_wilayah'], $group['provinsi'], $group['kabupaten'], $group['sektor']);

                    if ($newData) {
                        ShiftShare::updateOrCreate([
                            'user_id' => Auth::id() ?? 1,
                            'sektor_id' => $sektorId,
                            'tahun_awal' => $newData['tahun_awal'],
                            'tahun_akhir' => $newData['tahun_akhir'],
                        ], [
                            'tingkat_wilayah' => $newData['tingkat_wilayah'],
                            'daerah_analisis' => $newData['daerah_analisis'],
                            'daerah_pembanding' => $newData['daerah_pembanding'],
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
            }
        });

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis SSA', 'diimpor', "Mengimpor {$successCount} Sektor Analisis Shift Share secara massal menggunakan metode Year-on-Year.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");
            return response()->json([
                'success' => true,
                'message' => $successCount . ' sektor berhasil dihitung.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan template menggunakan format tahun berurutan minimal 2 tahun per sektor.']);
    }

    public function syncFromDatabase(Request $request)
    {
        $request->validate([
            'tingkat_wilayah' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'nullable|string',
            'sektor' => 'required|string',
            'tahun_awal' => 'required|numeric',
            'tahun_akhir' => 'required|numeric',
        ]);

        $daerahAnalisis = $request->tingkat_wilayah === 'Provinsi' ? $request->provinsi : $request->kabupaten;
        $sektorName = $request->sektor;
        $tahunAwal = $request->tahun_awal;
        $tahunAkhir = $request->tahun_akhir;

        if ($tahunAkhir <= $tahunAwal) {
            return response()->json(['success' => false, 'message' => 'Tahun akhir harus lebih besar dari tahun awal.']);
        }

        $sektorModel = Sektor::where('nama_sektor', $sektorName)->first();
        if (!$sektorModel) {
            return response()->json(['success' => false, 'message' => 'Sektor tidak ditemukan di database.']);
        }

        // Cari data LQ untuk semua tahun dalam rentang
        $lqData = \App\Models\Lq::where('daerah_analisis', $daerahAnalisis)
            ->where('sektor_id', $sektorModel->sektor_id)
            ->whereBetween('tahun', [$tahunAwal, $tahunAkhir])
            ->orderBy('tahun', 'asc')
            ->get();

        if ($lqData->count() < 2) {
            return response()->json(['success' => false, 'message' => "Data PDRB untuk daerah {$daerahAnalisis} sektor {$sektorName} dalam rentang {$tahunAwal} - {$tahunAkhir} kurang dari 2 tahun di database LQ. Sinkronisasi dibatalkan."]);
        }

        // Return the array of yearly data
        $yearsData = $lqData->map(function ($item) {
            return [
                'tahun' => $item->tahun,
                'pdrb_sektor_analisis' => $item->pdrb_sektor_analisis,
                'pdrb_sektor_pembanding' => $item->pdrb_sektor_pembanding,
                'total_pdrb_pembanding' => $item->total_pdrb_pembanding,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => $yearsData,
            'message' => 'Berhasil menarik data tahunan dari database!'
        ]);
    }

    public function syncAllFromDatabase(Request $request)
    {
        $request->validate([
            'tingkat_wilayah' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'nullable|string',
            'tahun_awal' => 'required|numeric',
            'tahun_akhir' => 'required|numeric',
        ]);

        $daerahAnalisis = $request->tingkat_wilayah === 'Provinsi' ? $request->provinsi : $request->kabupaten;
        $tahunAwal = $request->tahun_awal;
        $tahunAkhir = $request->tahun_akhir;

        if ($tahunAkhir <= $tahunAwal) {
            return response()->json(['success' => false, 'message' => 'Tahun akhir harus lebih besar dari tahun awal.']);
        }

        // Ambil semua data LQ untuk daerah tersebut pada rentang tahun yang dipilih
        $lqData = \App\Models\Lq::with('sektor')
            ->where('daerah_analisis', $daerahAnalisis)
            ->whereBetween('tahun', [$tahunAwal, $tahunAkhir])
            ->orderBy('tahun', 'asc')
            ->get();

        if ($lqData->isEmpty()) {
            return response()->json(['success' => false, 'message' => "Tidak ada data PDRB ditemukan untuk daerah {$daerahAnalisis} dalam rentang {$tahunAwal} - {$tahunAkhir} di database LQ."]);
        }

        // Kelompokkan berdasarkan sektor
        $groupedBySektor = $lqData->groupBy('sektor_id');
        $successCount = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($groupedBySektor, $request, &$successCount) {
            foreach ($groupedBySektor as $sektorId => $items) {
                // Pastikan minimal ada 2 tahun data
                if ($items->count() < 2) continue;

                $sektorName = $items->first()->sektor->nama_sektor;

                // Format data array tahunan untuk calculateSSData
                $yearsData = $items->map(function ($item) {
                    return [
                        'tahun' => $item->tahun,
                        'pdrb_sektor_analisis' => $item->pdrb_sektor_analisis,
                        'total_pdrb_analisis' => $item->total_pdrb_analisis,
                        'pdrb_sektor_pembanding' => $item->pdrb_sektor_pembanding,
                        'total_pdrb_pembanding' => $item->total_pdrb_pembanding,
                    ];
                })->toArray();

                // Sort by year
                usort($yearsData, function ($a, $b) {
                    return (int) $a['tahun'] <=> (int) $b['tahun'];
                });

                for ($i = 1; $i < count($yearsData); $i++) {
                    $prev = $yearsData[$i - 1];
                    $curr = $yearsData[$i];

                    $newData = $this->calculateSSData($prev, $curr, $request->tingkat_wilayah, $request->provinsi, $request->kabupaten, $sektorName);

                    if ($newData) {
                        ShiftShare::updateOrCreate([
                            'user_id' => Auth::id() ?? 1,
                            'sektor_id' => $sektorId,
                            'tahun_awal' => $newData['tahun_awal'],
                            'tahun_akhir' => $newData['tahun_akhir'],
                        ], [
                            'tingkat_wilayah' => $newData['tingkat_wilayah'],
                            'daerah_analisis' => $newData['daerah_analisis'],
                            'daerah_pembanding' => $newData['daerah_pembanding'],
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
            }
        });

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis SSA', 'diimpor', "Menarik {$successCount} Sektor Analisis Shift Share dari database secara massal.");
            session()->flash('success', "Berhasil menarik dan menghitung {$successCount} sektor dari database!");
            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses {$successCount} sektor."
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Data ditemukan tapi tidak cukup lengkap (minimal 2 tahun berurutan) untuk dihitung.']);
    }

    public function downloadPdf(Request $request)
    {
        $query = ShiftShare::with('sektor');

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
        $ssData = $this->mapDbToView($rawDbData);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('operator.ss.pdf', [
            'ssData' => $ssData,
            'search' => $request->search ?? null,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-analisis-ss-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadExcel(Request $request)
    {
        $query = ShiftShare::with('sektor');

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
        $ssData = $this->mapDbToView($rawDbData);

        $html = view('operator.ss.excel', [
            'ssData' => $ssData,
            'search' => $request->search ?? null,
        ])->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="laporan-analisis-ss-' . now()->format('Y-m-d') . '.xls"')
            ->header('Cache-Control', 'max-age=0');
    }
}