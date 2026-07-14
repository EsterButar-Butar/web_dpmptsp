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
                'ri' => $item->ri,
                'r' => $item->r,
                'yi' => $item->yi,
                'y' => $item->y,
                'klasifikasi' => $item->klasifikasi,
                'riwayat' => 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $rawDbData = Klassen::with('sektor')->latest()->get();
        $klassenData = collect($this->mapDbToView($rawDbData));

        $editData = null;
        if ($request->has('edit')) {
            $editData = $klassenData->firstWhere('id', $request->edit);
        }

        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedData = new LengthAwarePaginator(
            $klassenData->forPage($page, $perPage),
            $klassenData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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

        // Prevent division by zero
        if ($pdrbSektorKabAwal <= 0 || $pdrbSektorProvAwal <= 0 || $pdrbTotalKabAwal <= 0 || $pdrbTotalProvAwal <= 0 || $pdrbTotalKabAkhir <= 0 || $pdrbTotalProvAkhir <= 0) {
            return false;
        }

        $tahunAwal = (int) ($item['tahun_awal'] ?? 0);
        $tahunAkhir = (int) ($item['tahun_akhir'] ?? 0);
        $n = abs($tahunAkhir - $tahunAwal);
        if ($n == 0) $n = 1; // Mencegah pembagian dengan nol jika tahun sama

        // 1. MENGHITUNG LAJU PERTUMBUHAN SEKTOR (Estimasi Rata-rata Tahunan Aritmatika)
        $ri = ((($pdrbSektorKabAkhir - $pdrbSektorKabAwal) / $pdrbSektorKabAwal) * 100) / $n;
        $r = ((($pdrbSektorProvAkhir - $pdrbSektorProvAwal) / $pdrbSektorProvAwal) * 100) / $n;

        // 2. MENGHITUNG KONTRIBUSI SEKTOR
        $yi = ((($pdrbSektorKabAwal / $pdrbTotalKabAwal) + ($pdrbSektorKabAkhir / $pdrbTotalKabAkhir)) / 2) * 100;
        $y = ((($pdrbSektorProvAwal / $pdrbTotalProvAwal) + ($pdrbSektorProvAkhir / $pdrbTotalProvAkhir)) / 2) * 100;

        // 3. KLASIFIKASI TIPOLOGI KLASSEN
        if ($yi > $y && $ri > $r) {
            $kuadran = 'Kuadran I';
            $klasifikasi = 'Sektor maju dan tumbuh cepat';
        } elseif ($yi > $y && $ri < $r) {
            $kuadran = 'Kuadran II';
            $klasifikasi = 'Sektor maju tetapi tertekan';
        } elseif ($yi < $y && $ri > $r) {
            $kuadran = 'Kuadran III';
            $klasifikasi = 'Sektor berkembang cepat';
        } else {
            $kuadran = 'Kuadran IV';
            $klasifikasi = 'Sektor relatif tertinggal';
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

    // Menyimpan hasil perhitungan Klassen ke session.
    public function hitung(Request $request)
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

        $data['id'] = time();
        $data['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');

        $klassenData = session('klassen_data_v2', []);
        array_unshift($klassenData, $data);
        session(['klassen_data_v2' => $klassenData]);

        OperatorController::logActivity('Analisis Klassen', 'ditambah', "Menambahkan data perhitungan Analisis Tipologi Klassen untuk sektor {$request->sektor}.");

        return back()->with('success', 'Analisis Klassen berhasil dihitung dan data ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $data = $this->calculateKlassenData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada input awal. Periksa kembali nilai Anda.');
        }

        $klassenData = session('klassen_data_v2', []);
        foreach ($klassenData as $key => $item) {
            if ($item['id'] == $id) {
                $data['id'] = $item['id'];
                $data['riwayat'] = 'Diperbarui '.Carbon::now()->format('d-m-Y');
                $klassenData[$key] = $data;
                break;
            }
        }

        session(['klassen_data_v2' => $klassenData]);

        OperatorController::logActivity('Analisis Klassen', 'diperbarui', "Memperbarui data perhitungan Analisis Tipologi Klassen untuk sektor {$request->sektor}.");

        return redirect()->route('operator.klassen.index')->with('success', 'Data perhitungan Klassen berhasil diperbarui!');
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

    // Memproses import data massal dari file Excel.
    public function import(Request $request)
    {
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Format data tidak valid.']);
        }

        $successCount = 0;

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

            $newData = $this->calculateKlassenData($mappedItem);

            if ($newData) {
                $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $newData['sektor']]);
                
                Klassen::create([
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
                    'ri' => $newData['ri'],
                    'r' => $newData['r'],
                    'yi' => $newData['yi'],
                    'y' => $newData['y'],
                    'klasifikasi' => $newData['klasifikasi']
                ]);
                $successCount++;
            }
        }

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis Klassen', 'diimpor', "Mengimpor {$successCount} data Analisis Tipologi Klassen secara massal dari Template Master.");
        }

        return response()->json([
            'success' => true,
            'message' => $successCount.' data berhasil diimpor.',
        ]);
    }
}

