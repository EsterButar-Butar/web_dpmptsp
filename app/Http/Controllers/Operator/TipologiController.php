<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Operator\OperatorDashboardController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TipologiController extends Controller
{
    public function index(Request $request)
    {
        if (! session()->has('tipologi_data_v2')) {
            $dummyData = [
                [
                    'id' => 1,
                    'tingkat_wilayah' => 'Kabupaten/Kota',
                    'daerah_analisis' => 'Medan',
                    'daerah_pembanding' => 'Sumatera Utara',
                    'provinsi' => 'Sumatera Utara',
                    'kabupaten' => 'Medan',
                    'sektor' => 'PERTANIAN, KEHUTANAN, DAN PERIKANAN',
                    'tahun_awal' => '2023',
                    'tahun_akhir' => '2022',

                    'pdrb_sektor_analisis_awal' => 50000000,
                    'pdrb_sektor_analisis_akhir' => 55000000,
                    'total_pdrb_analisis_awal' => 200000000,
                    'total_pdrb_analisis_akhir' => 220000000,

                    'pdrb_sektor_pembanding_awal' => 500000000,
                    'pdrb_sektor_pembanding_akhir' => 530000000,
                    'total_pdrb_pembanding_awal' => 3000000000,
                    'total_pdrb_pembanding_akhir' => 3200000000,

                    'nilai_ss' => 4.0,  // (10.0 - 6.0)
                    'nilai_lq' => 1.505, // (25.0 / 16.61)

                    'tipologi' => 'Maju dan Tumbuh Cepat',
                    'riwayat' => 'Ditambah 22-2-2024',
                ],
            ];
            session(['tipologi_data_v2' => $dummyData]);
        }

        $tipologiData = session('tipologi_data_v2', []);

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = collect($tipologiData)->firstWhere('id', (int) $request->edit) ?? collect($tipologiData)->firstWhere('id', $request->edit);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($tipologiData, $perPage * ($currentPage - 1), $perPage);
        $paginatedData = new LengthAwarePaginator($currentItems, count($tipologiData), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('partials.operator.tipologi.index', [
            'tipologiData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }

    public function calculateTipologiData($item)
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

        // 1. MENGHITUNG NILAI SS
        $gi = (($pdrbSektorKabAkhir - $pdrbSektorKabAwal) / $pdrbSektorKabAwal) * 100;
        $g = (($pdrbSektorProvAkhir - $pdrbSektorProvAwal) / $pdrbSektorProvAwal) * 100;
        $ss = $gi - $g;

        // 2. MENGHITUNG NILAI LQ
        $si = ((($pdrbSektorKabAwal / $pdrbTotalKabAwal) + ($pdrbSektorKabAkhir / $pdrbTotalKabAkhir)) / 2) * 100;
        $s = ((($pdrbSektorProvAwal / $pdrbTotalProvAwal) + ($pdrbSektorProvAkhir / $pdrbTotalProvAkhir)) / 2) * 100;
        $lq = $si / $s;

        // 3. MENENTUKAN TIPOLOGI SEKTOR
        if ($ss > 0 && $lq > 1) {
            $tipologi = 'Maju dan Tumbuh Cepat';
        } elseif ($ss > 0 && $lq <= 1) {
            $tipologi = 'Potensial / Berkembang Cepat';
        } elseif ($ss <= 0 && $lq > 1) {
            $tipologi = 'Berkembang / Maju Tapi Tertekan';
        } else {
            $tipologi = 'Relatif Tertinggal';
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

            'nilai_ss' => round($ss, 4),
            'nilai_lq' => round($lq, 4),
            'tipologi' => $tipologi,
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

        $data = $this->calculateTipologiData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda agar tidak terjadi pembagian dengan nol.');
        }

        $data['id'] = time();
        $data['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');

        $tipologiData = session('tipologi_data_v2', []);
        array_unshift($tipologiData, $data);
        session(['tipologi_data_v2' => $tipologiData]);

        OperatorDashboardController::logActivity('Analisis Tipologi', 'ditambah', "Menambahkan data perhitungan Analisis Tipologi Sektor untuk sektor {$request->sektor}.");

        return back()->with('success', 'Perhitungan Tipologi Sektor berhasil dan data ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $data = $this->calculateTipologiData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
        }

        $tipologiData = session('tipologi_data_v2', []);
        foreach ($tipologiData as $key => $item) {
            if ($item['id'] == $id) {
                $data['id'] = $item['id'];
                $data['riwayat'] = 'Diperbarui '.Carbon::now()->format('d-m-Y');
                $tipologiData[$key] = $data;
                break;
            }
        }

        session(['tipologi_data_v2' => $tipologiData]);

        OperatorDashboardController::logActivity('Analisis Tipologi', 'diperbarui', "Memperbarui data perhitungan Analisis Tipologi Sektor untuk sektor {$request->sektor}.");

        return redirect()->route('operator.tipologi.index')->with('success', 'Data perhitungan Tipologi Sektor berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tipologiData = session('tipologi_data_v2', []);
        $tipologiData = array_filter($tipologiData, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        session(['tipologi_data_v2' => array_values($tipologiData)]);

        OperatorDashboardController::logActivity('Analisis Tipologi', 'dihapus', 'Menghapus sebuah data perhitungan Analisis Tipologi Sektor.');

        return back()->with('success', 'Data perhitungan Tipologi Sektor berhasil dihapus!');
    }
}
