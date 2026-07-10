<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Operator\OperatorDashboardController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class KlassenController extends Controller
{
    public function index(Request $request)
    {
        if (! session()->has('klassen_data_v2')) {
            $dummyData = [
                $this->calculateKlassenData([
                    'tingkat_wilayah' => 'Kabupaten/Kota',
                    'provinsi' => 'Sumatera Utara',
                    'kabupaten' => 'Medan',
                    'sektor' => 'PERTANIAN, KEHUTANAN, DAN PERIKANAN',
                    'tahun_awal' => '2021', 'tahun_akhir' => '2022',
                    'pdrb_sektor_analisis_awal' => 50000000, 'pdrb_sektor_analisis_akhir' => 60000000,
                    'total_pdrb_analisis_awal' => 200000000, 'total_pdrb_analisis_akhir' => 220000000,
                    'pdrb_sektor_pembanding_awal' => 500000000, 'pdrb_sektor_pembanding_akhir' => 530000000,
                    'total_pdrb_pembanding_awal' => 3000000000, 'total_pdrb_pembanding_akhir' => 3200000000,
                ]),
                $this->calculateKlassenData([
                    'tingkat_wilayah' => 'Provinsi',
                    'provinsi' => 'Sumatera Utara',
                    'kabupaten' => '-',
                    'sektor' => 'INDUSTRI PENGOLAHAN',
                    'tahun_awal' => '2021', 'tahun_akhir' => '2022',
                    'pdrb_sektor_analisis_awal' => 150000, 'pdrb_sektor_analisis_akhir' => 155000,
                    'total_pdrb_analisis_awal' => 800000, 'total_pdrb_analisis_akhir' => 880000,
                    'pdrb_sektor_pembanding_awal' => 3000000, 'pdrb_sektor_pembanding_akhir' => 3450000,
                    'total_pdrb_pembanding_awal' => 25000000, 'total_pdrb_pembanding_akhir' => 28000000,
                ]),
            ];
            $dummyData[0]['id'] = time();
            $dummyData[0]['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');
            $dummyData[1]['id'] = time() - 100;
            $dummyData[1]['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');

            session(['klassen_data_v2' => $dummyData]);
        }

        $klassenData = session('klassen_data_v2', []);

        // Paksa update tulisan "Data Dummy" di session lama jika ada
        $sessionUpdated = false;
        foreach ($klassenData as &$item) {
            if (isset($item['riwayat']) && $item['riwayat'] === 'Data Dummy') {
                $item['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');
                $sessionUpdated = true;
            }
        }
        if ($sessionUpdated) {
            session(['klassen_data_v2' => $klassenData]);
        }

        // Sort by ID descending (newest first)
        usort($klassenData, function ($a, $b) {
            return $b['id'] <=> $a['id'];
        });

        // Pagination manually
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($klassenData, ($currentPage - 1) * $perPage, $perPage);
        $paginatedData = new LengthAwarePaginator($currentItems, count($klassenData), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $editData = null;
        if ($request->has('edit')) {
            foreach ($klassenData as $item) {
                if ($item['id'] == $request->edit) {
                    $editData = $item;
                    break;
                }
            }
        }

        return view('partials.operator.klassen.index', [
            'klassenData' => $paginatedData,
            'editData' => $editData,
        ]);
    }

    public function calculateKlassenData($item)
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

        // 1. MENGHITUNG LAJU PERTUMBUHAN SEKTOR
        $ri = (($pdrbSektorKabAkhir - $pdrbSektorKabAwal) / $pdrbSektorKabAwal) * 100;
        $r = (($pdrbSektorProvAkhir - $pdrbSektorProvAwal) / $pdrbSektorProvAwal) * 100;

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

        OperatorDashboardController::logActivity('Analisis Klassen', 'ditambah', "Menambahkan data perhitungan Analisis Tipologi Klassen untuk sektor {$request->sektor}.");

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

        OperatorDashboardController::logActivity('Analisis Klassen', 'diperbarui', "Memperbarui data perhitungan Analisis Tipologi Klassen untuk sektor {$request->sektor}.");

        return redirect()->route('operator.klassen.index')->with('success', 'Data perhitungan Klassen berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $klassenData = session('klassen_data_v2', []);
        $klassenData = array_filter($klassenData, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        session(['klassen_data_v2' => array_values($klassenData)]);

        OperatorDashboardController::logActivity('Analisis Klassen', 'dihapus', 'Menghapus sebuah data perhitungan Analisis Tipologi Klassen.');

        return back()->with('success', 'Data berhasil dihapus dari sesi.');
    }
}
