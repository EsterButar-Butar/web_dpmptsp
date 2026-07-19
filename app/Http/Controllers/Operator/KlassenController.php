<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Klassen;
use App\Models\Sektor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class KlassenController extends Controller
{
    private function mapDbToView($items)
    {
        return $items->map(function ($item) {
            $ri = (float) $item->ri;
            $r = (float) $item->r;
            $yi = (float) $item->yi;
            $y = (float) $item->y;

            if ($yi > $y && $ri > $r) {
                $kuadran = 'Kuadran I';
            } elseif ($yi > $y && $ri < $r) {
                $kuadran = 'Kuadran II';
            } elseif ($yi < $y && $ri > $r) {
                $kuadran = 'Kuadran III';
            } else {
                $kuadran = 'Kuadran IV';
            }

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
                'ri' => number_format((float) $item->ri, 3, '.', ''),
                'r' => number_format((float) $item->r, 3, '.', ''),
                'yi' => number_format((float) $item->yi, 3, '.', ''),
                'y' => number_format((float) $item->y, 3, '.', ''),
                'kuadran' => $kuadran,
                'klasifikasi' => $item->klasifikasi,
                'riwayat' => 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $query = Klassen::with('sektor');

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
        $klassenData = collect($this->mapDbToView($rawDbData));

        $editData = null;
        if ($request->has('edit')) {
            $editData = $klassenData->firstWhere('id', $request->edit);
        }

        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedData = (new LengthAwarePaginator(
            $klassenData->forPage($page, $perPage),
            $klassenData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        ))->onEachSide(1);

        return view('operator.klassen.index', [
            'klassenData' => $paginatedData,
            'editData' => $editData,
        ]);
    }

    // Menghitung Tipologi Klassen berdasarkan laju pertumbuhan dan kontribusi sektor.
    private function calculateKlassenData($item)
    {
        // Parse numbers safely
        $pdrbSektorKabAwal = $this->parseNumber($item['pdrb_sektor_analisis_awal'] ?? 0);
        $pdrbSektorKabAkhir = $this->parseNumber($item['pdrb_sektor_analisis_akhir'] ?? 0);

        $pdrbTotalKabAwal = $this->parseNumber($item['total_pdrb_analisis_awal'] ?? 0);
        $pdrbTotalKabAkhir = $this->parseNumber($item['total_pdrb_analisis_akhir'] ?? 0);

        $pdrbSektorProvAwal = $this->parseNumber($item['pdrb_sektor_pembanding_awal'] ?? 0);
        $pdrbSektorProvAkhir = $this->parseNumber($item['pdrb_sektor_pembanding_akhir'] ?? 0);

        $pdrbTotalProvAwal = $this->parseNumber($item['total_pdrb_pembanding_awal'] ?? 0);
        $pdrbTotalProvAkhir = $this->parseNumber($item['total_pdrb_pembanding_akhir'] ?? 0);

        // Prevent division by zero for Total PDRB only (which shouldn't be 0 unless the whole city has 0 economy)
        if ($pdrbTotalKabAwal <= 0 || $pdrbTotalProvAwal <= 0 || $pdrbTotalKabAkhir <= 0 || $pdrbTotalProvAkhir <= 0) {
            return false;
        }

        $tahunAwal = (int) ($item['tahun_awal'] ?? 0);
        $tahunAkhir = (int) ($item['tahun_akhir'] ?? 0);
        $n = abs($tahunAkhir - $tahunAwal);
        if ($n == 0) $n = 1; // Mencegah pembagian dengan nol jika tahun sama

        // 1. MENGHITUNG LAJU PERTUMBUHAN SEKTOR (Estimasi Rata-rata Tahunan Aritmatika)
        if ($pdrbSektorKabAwal > 0) {
            $ri = ((($pdrbSektorKabAkhir - $pdrbSektorKabAwal) / $pdrbSektorKabAwal) * 100) / $n;
        } else {
            $ri = $pdrbSektorKabAkhir > 0 ? (100 / $n) : 0;
        }

        if ($pdrbSektorProvAwal > 0) {
            $r = ((($pdrbSektorProvAkhir - $pdrbSektorProvAwal) / $pdrbSektorProvAwal) * 100) / $n;
        } else {
            $r = $pdrbSektorProvAkhir > 0 ? (100 / $n) : 0;
        }

        // 2. MENGHITUNG KONTRIBUSI SEKTOR
        $yi = ((($pdrbSektorKabAwal / $pdrbTotalKabAwal) + ($pdrbSektorKabAkhir / $pdrbTotalKabAkhir)) / 2) * 100;
        $y = ((($pdrbSektorProvAwal / $pdrbTotalProvAwal) + ($pdrbSektorProvAkhir / $pdrbTotalProvAkhir)) / 2) * 100;

        // 3. KLASIFIKASI TIPOLOGI KLASSEN
        if ($yi > $y && $ri > $r) {
            $kuadran = 'Kuadran I';
            $klasifikasi = 'Sektor Maju, tumbuh pesat';
        } elseif ($yi > $y && $ri < $r) {
            $kuadran = 'Kuadran II';
            $klasifikasi = 'Sektor Maju dan Tertekan';
        } elseif ($yi < $y && $ri > $r) {
            $kuadran = 'Kuadran III';
            $klasifikasi = 'Sektor Potensial';
        } else {
            $kuadran = 'Kuadran IV';
            $klasifikasi = 'Sektor Relatif Tertinggal';
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

            'ri' => round($ri, 2),
            'r' => round($r, 2),
            'yi' => round($yi, 2),
            'y' => round($y, 2),
            'kuadran' => $kuadran,
            'klasifikasi' => $klasifikasi,
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

        $data = $this->calculateKlassenData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada input awal yang menyebabkan pembagian nol. Periksa kembali input Anda.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        Klassen::create([
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
            'ri' => $data['ri'],
            'r' => $data['r'],
            'yi' => $data['yi'],
            'y' => $data['y'],
            'klasifikasi' => $data['klasifikasi']
        ]);

        OperatorController::logActivity('Analisis Klassen', 'ditambah', "Menambahkan data perhitungan Analisis Tipologi Klassen untuk sektor {$data['sektor']}.");

        return back()->with('success', 'Analisis Klassen berhasil disimpan secara permanen!');
    }

    public function update(Request $request, $id)
    {
        $klassen = Klassen::find($id);
        if (!$klassen) {
            return redirect()->route('operator.klassen.index')->with('error', 'Data tidak ditemukan!');
        }

        $data = $this->calculateKlassenData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada input awal. Periksa kembali nilai Anda.');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        $klassen->update([
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
            'ri' => $data['ri'],
            'r' => $data['r'],
            'yi' => $data['yi'],
            'y' => $data['y'],
            'klasifikasi' => $data['klasifikasi']
        ]);

        OperatorController::logActivity('Analisis Klassen', 'diperbarui', "Memperbarui data perhitungan Analisis Tipologi Klassen untuk sektor {$data['sektor']}.");

        return redirect()->route('operator.klassen.index')->with('success', 'Data perhitungan Klassen berhasil diperbarui secara permanen!');
    }

    public function destroy($id)
    {
        $klassen = Klassen::find($id);
        if ($klassen) {
            $daerah = $klassen->daerah_analisis;
            $klassen->delete();
            OperatorController::logActivity('Tipologi Klassen', 'dihapus', "Menghapus data Tipologi Klassen daerah {$daerah}.");
        }

        return back()->with('success', 'Data Tipologi Klassen berhasil dihapus secara permanen!');
    }

    public function empty()
    {
        Klassen::truncate();
        OperatorController::logActivity('Analisis Tipologi Klassen', 'dihapus', "Menghapus semua data perhitungan Tipologi Klassen");
        return back()->with('success', 'Semua data perhitungan Tipologi Klassen berhasil dihapus secara permanen!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!empty($ids)) {
            $count = count($ids);
            Klassen::whereIn('id', $ids)->delete();
            OperatorController::logActivity('Analisis Tipologi Klassen', 'dihapus', "Menghapus {$count} data perhitungan Tipologi Klassen secara massal");
            return back()->with('success', "{$count} data perhitungan Tipologi Klassen berhasil dihapus secara massal!");
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
            foreach ($payload as $rawItem) {
                $item = $this->normalizeKeys($rawItem);

                $hasProvinsi = isset($item['provinsi']) || isset($item['kodeprovinsi']) || isset($item['kodewilayah']);
                if (!$hasProvinsi || ! isset($item['sektor']) || ! isset($item['tahunawal']) || ! isset($item['tahunakhir']) ||
                    ! isset($item['pdrbsektoranalisisawal']) || ! isset($item['pdrbsektoranalisisakhir']) ||
                    ! isset($item['totalpdrbanalisisawal']) || ! isset($item['totalpdrbanalisisakhir']) ||
                    ! isset($item['pdrbsektorpembandingawal']) || ! isset($item['pdrbsektorpembandingakhir']) ||
                    ! isset($item['totalpdrbpembandingawal']) || ! isset($item['totalpdrbpembandingakhir'])) {
                    continue;
                }

                $resolved = $this->resolveRegionNames($rawItem);
                $provinsi = $resolved['provinsi'];
                $kabupaten = $resolved['kabupaten'];

                $tingkat = ($kabupaten != '-' && $kabupaten != '') ? 'Kabupaten/Kota' : 'Provinsi';
                
                $sektorName = $this->resolveSektorName($rawItem);
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
                    'tahun_awal' => $item['tahunawal'],
                    'tahun_akhir' => $item['tahunakhir'],
                    'pdrb_sektor_analisis_awal' => $item['pdrbsektoranalisisawal'] ?? 0,
                    'pdrb_sektor_analisis_akhir' => $item['pdrbsektoranalisisakhir'] ?? 0,
                    'total_pdrb_analisis_awal' => $item['totalpdrbanalisisawal'] ?? 0,
                    'total_pdrb_analisis_akhir' => $item['totalpdrbanalisisakhir'] ?? 0,
                    'pdrb_sektor_pembanding_awal' => $item['pdrbsektorpembandingawal'] ?? 0,
                    'pdrb_sektor_pembanding_akhir' => $item['pdrbsektorpembandingakhir'] ?? 0,
                    'total_pdrb_pembanding_awal' => $item['totalpdrbpembandingawal'] ?? 0,
                    'total_pdrb_pembanding_akhir' => $item['totalpdrbpembandingakhir'] ?? 0,
                ];

                $newData = $this->calculateKlassenData($mappedItem);

                if ($newData) {
                    Klassen::create([
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
                        'ri' => $newData['ri'],
                        'r' => $newData['r'],
                        'yi' => $newData['yi'],
                        'y' => $newData['y'],
                        'klasifikasi' => $newData['klasifikasi']
                    ]);
                    $successCount++;
                }
            }
        });

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis Klassen', 'diimpor', "Mengimpor {$successCount} data Analisis Tipologi Klassen secara massal dari Template Master.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");
            return response()->json([
                'success' => true,
                'message' => $successCount.' data berhasil diimpor.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format kolom sesuai dengan Template Master.']);
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

        $sektorModel = Sektor::where('nama_sektor', $sektorName)->first();
        if (!$sektorModel) {
            return response()->json(['success' => false, 'message' => 'Sektor tidak ditemukan di database.']);
        }

        // Cari data LQ tahun awal
        $lqAwal = \App\Models\Lq::where('daerah_analisis', $daerahAnalisis)
            ->where('sektor_id', $sektorModel->sektor_id)
            ->where('tahun', $tahunAwal)
            ->first();

        // Cari data LQ tahun akhir
        $lqAkhir = \App\Models\Lq::where('daerah_analisis', $daerahAnalisis)
            ->where('sektor_id', $sektorModel->sektor_id)
            ->where('tahun', $tahunAkhir)
            ->first();

        if (!$lqAwal || !$lqAkhir) {
            return response()->json(['success' => false, 'message' => "Data PDRB untuk daerah {$daerahAnalisis} sektor {$sektorName} pada tahun {$tahunAwal} atau {$tahunAkhir} tidak ditemukan di database LQ. Silakan hitung LQ terlebih dahulu."]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pdrb_sektor_analisis_awal' => $lqAwal->pdrb_sektor_analisis,
                'pdrb_sektor_analisis_akhir' => $lqAkhir->pdrb_sektor_analisis,
                'total_pdrb_analisis_awal' => $lqAwal->total_pdrb_analisis,
                'total_pdrb_analisis_akhir' => $lqAkhir->total_pdrb_analisis,
                'pdrb_sektor_pembanding_awal' => $lqAwal->pdrb_sektor_pembanding,
                'pdrb_sektor_pembanding_akhir' => $lqAkhir->pdrb_sektor_pembanding,
                'total_pdrb_pembanding_awal' => $lqAwal->total_pdrb_pembanding,
                'total_pdrb_pembanding_akhir' => $lqAkhir->total_pdrb_pembanding,
            ],
            'message' => 'Berhasil menarik data dari database!'
        ]);
    }
}

