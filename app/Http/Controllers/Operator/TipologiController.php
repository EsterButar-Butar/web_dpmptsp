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
<<<<<<< HEAD
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
=======
                'tahun' => $item->tahun_akhir, // Hanya menggunakan tahun akhir
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
                'nilai_ss' => $item->nilai_ss,
                'nilai_lq' => $item->nilai_lq,
                'tipologi' => $item->tipologi,
                'riwayat' => 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
<<<<<<< HEAD
        $rawDbData = Tipologi::with('sektor')->latest()->get();
=======
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
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
        $tipologiData = collect($this->mapDbToView($rawDbData));

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = $tipologiData->firstWhere('id', $request->edit);
        }

        $perPage = 10;
        $page = $request->get('page', 1);
<<<<<<< HEAD
        $paginatedData = new LengthAwarePaginator(
=======
        $paginatedData = (new LengthAwarePaginator(
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
            $tipologiData->forPage($page, $perPage),
            $tipologiData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
<<<<<<< HEAD
        );
=======
        ))->onEachSide(1);
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349

        return view('operator.tipologi.index', [
            'tipologiData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }

<<<<<<< HEAD
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
            $tipologi = 'Maju dan Tumbuh Cepat';
        } elseif ($dij > 0 && $lq <= 1) {
            $tipologi = 'Potensial / Berkembang Cepat';
        } elseif ($dij <= 0 && $lq > 1) {
            $tipologi = 'Berkembang / Maju Tapi Tertekan';
        } else {
            $tipologi = 'Relatif Tertinggal';
=======
    // Fungsi bantu untuk parsing string angka dari Excel
    protected function parseExcelNumber($val)
    {
        if (is_numeric($val)) return (float) $val;
        
        $val = trim((string) $val);
        $commaCount = substr_count($val, ',');
        $dotCount = substr_count($val, '.');
        
        if ($dotCount > 0 && $commaCount > 0) {
            $lastComma = strrpos($val, ',');
            $lastDot = strrpos($val, '.');
            if ($lastComma > $lastDot) {
                // Format Indonesia: 1.234.567,89
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                // Format US: 1,234,567.89
                $val = str_replace(',', '', $val);
            }
        } elseif ($dotCount > 1) {
            // Format Indonesia: 1.234.567
            $val = str_replace('.', '', $val);
        } elseif ($commaCount > 1) {
            // Format US: 1,234,567
            $val = str_replace(',', '', $val);
        } elseif ($commaCount == 1) {
            // Format Indonesia: 1,23 (koma sebagai desimal)
            $val = str_replace(',', '.', $val);
        }
        
        $val = preg_replace('/[^0-9\.\-]/', '', $val);
        return (float) $val;
    }

    // Menghitung Tipologi Sektor berdasarkan LQ dan SS.
    private function calculateTipologiData($item)
    {
        $dij = $this->parseExcelNumber($item['nilai_ss'] ?? 0);
        $lq = $this->parseExcelNumber($item['nilai_lq'] ?? 0);

        // 3. MENENTUKAN TIPOLOGI SEKTOR
        if ($lq >= 1 && $dij > 0) {
            $tipologi = 'I'; // Kuadran I: Sektor Cepat Maju dan Cepat Tumbuh
        } elseif ($lq < 1 && $dij > 0) {
            $tipologi = 'II'; // Kuadran II: Sektor Potensial
        } elseif ($lq >= 1 && $dij <= 0) {
            $tipologi = 'III'; // Kuadran III: Sektor Maju tetapi Tertekan
        } else {
            $tipologi = 'IV'; // Kuadran IV: Sektor Relatif Tertinggal
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
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
<<<<<<< HEAD
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

            'nilai_ss' => round($dij, 4),
            'nilai_lq' => round($lq, 4),
=======
            'tahun_awal' => (int) $item['tahun'] - 1,
            'tahun_akhir' => (int) $item['tahun'],

            // Kolom PDRB diset 0 karena tidak digunakan lagi
            'pdrb_sektor_analisis_awal' => 0,
            'pdrb_sektor_analisis_akhir' => 0,
            'total_pdrb_analisis_awal' => 0,
            'total_pdrb_analisis_akhir' => 0,
            'pdrb_sektor_pembanding_awal' => 0,
            'pdrb_sektor_pembanding_akhir' => 0,
            'total_pdrb_pembanding_awal' => 0,
            'total_pdrb_pembanding_akhir' => 0,

            'nilai_ss' => $dij,
            'nilai_lq' => $lq,
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
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
<<<<<<< HEAD
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
=======
            'tahun' => 'required|string',
            'nilai_ss' => 'required',
            'nilai_lq' => 'required',
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
        ]);

        $data = $this->calculateTipologiData($request->all());
        if (! $data) {
<<<<<<< HEAD
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda agar tidak terjadi pembagian dengan nol.');
=======
            return back()->with('error', 'Terjadi kesalahan perhitungan.');
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        Tipologi::create([
            'user_id' => Auth::id() ?? 1,
<<<<<<< HEAD
            'sektor_id' => $sektorModel->id,
=======
            'sektor_id' => $sektorModel->sektor_id,
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
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
<<<<<<< HEAD
=======
        $request->validate([
            'tingkat_wilayah' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'nullable|string',
            'sektor' => 'required|string',
            'tahun' => 'required|string',
            'nilai_ss' => 'required',
            'nilai_lq' => 'required',
        ]);

>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
        $tipologi = Tipologi::find($id);
        if (!$tipologi) {
            return redirect()->route('operator.tipologi.index')->with('error', 'Data tidak ditemukan!');
        }

        $data = $this->calculateTipologiData($request->all());
        if (! $data) {
<<<<<<< HEAD
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
=======
            return back()->with('error', 'Terjadi kesalahan perhitungan.');
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $data['sektor']]);

        $tipologi->update([
<<<<<<< HEAD
            'sektor_id' => $sektorModel->id,
=======
            'sektor_id' => $sektorModel->sektor_id,
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
            'tingkat_wilayah' => $data['tingkat_wilayah'],
            'daerah_analisis' => $data['daerah_analisis'],
            'daerah_pembanding' => $data['daerah_pembanding'],
            'tahun_awal' => $data['tahun_awal'],
            'tahun_akhir' => $data['tahun_akhir'],
<<<<<<< HEAD
            'pdrb_sektor_analisis_awal' => $data['pdrb_sektor_analisis_awal'],
            'pdrb_sektor_analisis_akhir' => $data['pdrb_sektor_analisis_akhir'],
            'total_pdrb_analisis_awal' => $data['total_pdrb_analisis_awal'],
            'total_pdrb_analisis_akhir' => $data['total_pdrb_analisis_akhir'],
            'pdrb_sektor_pembanding_awal' => $data['pdrb_sektor_pembanding_awal'],
            'pdrb_sektor_pembanding_akhir' => $data['pdrb_sektor_pembanding_akhir'],
            'total_pdrb_pembanding_awal' => $data['total_pdrb_pembanding_awal'],
            'total_pdrb_pembanding_akhir' => $data['total_pdrb_pembanding_akhir'],
=======
            'pdrb_sektor_analisis_awal' => 0,
            'pdrb_sektor_analisis_akhir' => 0,
            'total_pdrb_analisis_awal' => 0,
            'total_pdrb_analisis_akhir' => 0,
            'pdrb_sektor_pembanding_awal' => 0,
            'pdrb_sektor_pembanding_akhir' => 0,
            'total_pdrb_pembanding_awal' => 0,
            'total_pdrb_pembanding_akhir' => 0,
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
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

<<<<<<< HEAD
    // Memproses import data massal dari file Excel.
    public function import(Request $request)
    {
=======
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
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Format data tidak valid.']);
        }

        $successCount = 0;

<<<<<<< HEAD
        foreach ($payload as $item) {
            if (! isset($item['Provinsi']) || ! isset($item['Sektor']) || ! isset($item['Tahun Awal']) || ! isset($item['Tahun Akhir']) ||
                ! isset($item['PDRB Sektor Analisis Awal']) || ! isset($item['PDRB Sektor Analisis Akhir']) ||
                ! isset($item['Total PDRB Analisis Awal']) || ! isset($item['Total PDRB Analisis Akhir']) ||
                ! isset($item['PDRB Sektor Pembanding Awal']) || ! isset($item['PDRB Sektor Pembanding Akhir']) ||
                ! isset($item['Total PDRB Pembanding Awal']) || ! isset($item['Total PDRB Pembanding Akhir'])) {
                continue;
            }

            $tingkat = (isset($item['Kabupaten/Kota']) && $item['Kabupaten/Kota'] != '-' && $item['Kabupaten/Kota'] != '') ? 'Kabupaten/Kota' : 'Provinsi';
            
            $mappedItem = [
                'tingkat_wilayah' => $tingkat,
                'provinsi' => $item['Provinsi'],
                'kabupaten' => $item['Kabupaten/Kota'] ?? '-',
                'sektor' => $item['Sektor'],
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
                $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $newData['sektor']]);
                
                Tipologi::create([
                    'user_id' => Auth::id() ?? 1,
                    'sektor_id' => $sektorModel->id,
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
=======
        // Preload all sectors in memory
        $sektorsCache = Sektor::all()->pluck('sektor_id', 'nama_sektor')->mapWithKeys(fn($id, $name) => [strtolower(trim($name)) => $id])->toArray();

        \Illuminate\Support\Facades\DB::transaction(function () use ($payload, &$successCount, &$sektorsCache) {
            foreach ($payload as $rawItem) {
                // Normalize keys (lowercase and remove spaces)
                $item = [];
                foreach ($rawItem as $key => $val) {
                    $normalizedKey = str_replace([' ', '_'], '', strtolower(trim($key)));
                    $item[$normalizedKey] = $val;
                }

                $hasProvinsi = isset($item['provinsi']) || isset($item['kodeprovinsi']) || isset($item['kodewilayah']);
                
                // Also accept 'sektor' and 'tahun', 'nilailq', 'nilaiss'
                if (!$hasProvinsi || !isset($item['sektor']) || !isset($item['tahun']) || !isset($item['nilailq']) || !isset($item['nilaiss'])) {
                    continue;
                }

                $resolved = $this->resolveRegionNames($rawItem); // Use rawItem for resolving because it expects the original keys
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
                    'tahun' => $item['tahun'],
                    'nilai_lq' => $item['nilailq'],
                    'nilai_ss' => $item['nilaiss'],
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
                        'pdrb_sektor_analisis_awal' => 0,
                        'pdrb_sektor_analisis_akhir' => 0,
                        'total_pdrb_analisis_awal' => 0,
                        'total_pdrb_analisis_akhir' => 0,
                        'pdrb_sektor_pembanding_awal' => 0,
                        'pdrb_sektor_pembanding_akhir' => 0,
                        'total_pdrb_pembanding_awal' => 0,
                        'total_pdrb_pembanding_akhir' => 0,
                        'nilai_ss' => $newData['nilai_ss'],
                        'nilai_lq' => $newData['nilai_lq'],
                        'tipologi' => $newData['tipologi']
                    ]);
                    $successCount++;
                }
            }
        });
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis Tipologi', 'diimpor', "Mengimpor {$successCount} data Analisis Tipologi Sektor secara massal dari Template Master.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format kolom sesuai dengan Template Master.']);
    }
<<<<<<< HEAD
=======

    public function syncFromDatabase(Request $request)
    {
        $request->validate([
            'daerah_analisis' => 'required|string',
            'tahun_awal' => 'required|numeric',
            'tahun_akhir' => 'required|numeric'
        ]);

        $daerah = $request->daerah_analisis;
        $startYear = $request->tahun_awal;
        $endYear = $request->tahun_akhir;

        $ssData = \App\Models\ShiftShare::where('daerah_analisis', $daerah)
            ->whereBetween('tahun_akhir', [$startYear, $endYear])
            ->get();
            
        $lqData = \App\Models\LQ::where('daerah_analisis', $daerah)
            ->whereBetween('tahun', [$startYear, $endYear])
            ->get();

        if ($ssData->isEmpty() || $lqData->isEmpty()) {
            return back()->with('error', 'Data LQ atau Shift Share untuk daerah dan tahun tersebut tidak ditemukan di database.');
        }

        $successCount = 0;
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($ssData, $lqData, &$successCount) {
            foreach ($ssData as $ss) {
                // Find matching LQ
                $lq = $lqData->first(function($item) use ($ss) {
                    return $item->sektor_id == $ss->sektor_id && $item->tahun == $ss->tahun_akhir;
                });
                
                if ($lq) {
                    $item = [
                        'tingkat_wilayah' => $ss->tingkat_wilayah,
                        'provinsi' => $ss->daerah_pembanding === 'Nasional' ? $ss->daerah_analisis : $ss->daerah_pembanding, // Aproksimasi
                        'kabupaten' => $ss->tingkat_wilayah === 'Kabupaten/Kota' ? $ss->daerah_analisis : '-',
                        'sektor' => $ss->sektor->nama_sektor,
                        'tahun' => $ss->tahun_akhir,
                        'nilai_ss' => $ss->dij,
                        'nilai_lq' => $lq->nilai_lq
                    ];
                    
                    $newData = $this->calculateTipologiData($item);
                    if ($newData) {
                        // Cek apakah sudah ada untuk menghindari duplikat
                        $existing = Tipologi::where('daerah_analisis', $newData['daerah_analisis'])
                            ->where('sektor_id', $ss->sektor_id)
                            ->where('tahun_akhir', $newData['tahun_akhir'])
                            ->first();
                            
                        if (!$existing) {
                            Tipologi::create([
                                'user_id' => Auth::id() ?? 1,
                                'sektor_id' => $ss->sektor_id,
                                'tingkat_wilayah' => $newData['tingkat_wilayah'],
                                'daerah_analisis' => $newData['daerah_analisis'],
                                'daerah_pembanding' => $newData['daerah_pembanding'],
                                'tahun_awal' => $newData['tahun_awal'],
                                'tahun_akhir' => $newData['tahun_akhir'],
                                'pdrb_sektor_analisis_awal' => 0,
                                'pdrb_sektor_analisis_akhir' => 0,
                                'total_pdrb_analisis_awal' => 0,
                                'total_pdrb_analisis_akhir' => 0,
                                'pdrb_sektor_pembanding_awal' => 0,
                                'pdrb_sektor_pembanding_akhir' => 0,
                                'total_pdrb_pembanding_awal' => 0,
                                'total_pdrb_pembanding_akhir' => 0,
                                'nilai_ss' => $newData['nilai_ss'],
                                'nilai_lq' => $newData['nilai_lq'],
                                'tipologi' => $newData['tipologi']
                            ]);
                            $successCount++;
                        }
                    }
                }
            }
        });

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis Tipologi', 'disinkronisasi', "Menarik {$successCount} data Tipologi secara otomatis dari modul LQ & SS.");
            return back()->with('success', "Berhasil menyinkronkan $successCount data Tipologi baru dari database LQ & SS!");
        }

        return back()->with('success', 'Sinkronisasi selesai. Tidak ada data baru yang ditambahkan (semua sudah tersinkron atau data tidak cocok).');
    }
>>>>>>> 4c77b612a43bfdb13f29af11e303045e71caf349
}

