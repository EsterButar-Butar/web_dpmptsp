<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Operator\OperatorDashboardController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ShiftShareController extends Controller
{
    public function index(Request $request)
    {
        if (! session()->has('ss_data_v2')) {
            $dummyData = [
                [
                    'id' => 1,
                    'tingkat_wilayah' => 'Kabupaten/Kota',
                    'daerah_analisis' => 'Medan',
                    'daerah_pembanding' => 'Sumatera Utara',
                    'provinsi' => 'Sumatera Utara',
                    'kabupaten' => 'Medan',
                    'sektor' => 'PERTANIAN, KEHUTANAN, DAN PERIKANAN',
                    'tahun_awal' => '2021',
                    'tahun_akhir' => '2022',
                    'pdrb_sektor_analisis_awal' => 10000,
                    'pdrb_sektor_analisis_akhir' => 12000,
                    'pdrb_sektor_pembanding_awal' => 50000,
                    'pdrb_sektor_pembanding_akhir' => 55000,
                    'total_pdrb_pembanding_awal' => 200000,
                    'total_pdrb_pembanding_akhir' => 210000,
                    'rij' => 0.20,
                    'rin' => 0.10,
                    'rn' => 0.05,
                    'nij' => 500,
                    'mij' => 500,
                    'cij' => 1500,
                    'dij' => 2500,
                    'status_pertumbuhan' => 'Pertumbuhan Cepat',
                    'status_daya_saing' => 'Daya Saing Baik',
                    'riwayat' => 'Ditambah 22-2-2024',
                ],
                [
                    'id' => 2,
                    'tingkat_wilayah' => 'Kabupaten/Kota',
                    'daerah_analisis' => 'Binjai',
                    'daerah_pembanding' => 'Sumatera Utara',
                    'provinsi' => 'Sumatera Utara',
                    'kabupaten' => 'Binjai',
                    'sektor' => 'INDUSTRI PENGOLAHAN',
                    'tahun_awal' => '2021',
                    'tahun_akhir' => '2022',
                    'pdrb_sektor_analisis_awal' => 15000,
                    'pdrb_sektor_analisis_akhir' => 15500,
                    'pdrb_sektor_pembanding_awal' => 80000,
                    'pdrb_sektor_pembanding_akhir' => 88000,
                    'total_pdrb_pembanding_awal' => 300000,
                    'total_pdrb_pembanding_akhir' => 345000,
                    'rij' => 0.0333,
                    'rin' => 0.10,
                    'rn' => 0.15,
                    'nij' => 2250,
                    'mij' => -750,
                    'cij' => -1750,
                    'dij' => -250,
                    'status_pertumbuhan' => 'Pertumbuhan Lambat',
                    'status_daya_saing' => 'Tidak Dapat Bersaing',
                    'riwayat' => 'Ditambah 22-2-2024',
                ],
            ];
            session(['ss_data_v2' => $dummyData]);
        }

        $ssData = session('ss_data_v2', []);

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = collect($ssData)->firstWhere('id', (int) $request->edit) ?? collect($ssData)->firstWhere('id', $request->edit);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($ssData, $perPage * ($currentPage - 1), $perPage);
        $paginatedData = new LengthAwarePaginator($currentItems, count($ssData), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('partials.operator.ss.index', [
            'ssData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }

    public function calculateSSData($item)
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
            'pdrb_sektor_pembanding_awal' => 'required',
            'pdrb_sektor_pembanding_akhir' => 'required',
            'total_pdrb_pembanding_awal' => 'required',
            'total_pdrb_pembanding_akhir' => 'required',
        ]);

        $data = $this->calculateSSData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
        }

        $data['id'] = time();
        $data['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');

        $ssData = session('ss_data_v2', []);
        array_unshift($ssData, $data);
        session(['ss_data_v2' => $ssData]);

        OperatorDashboardController::logActivity('Analisis SSA', 'ditambah', "Menambahkan data perhitungan Analisis Shift Share untuk sektor {$request->sektor}.");

        return back()->with('success', 'Perhitungan SS berhasil dan data ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $data = $this->calculateSSData($request->all());
        if (! $data) {
            return back()->with('error', 'Terdapat nilai 0 pada data awal. Periksa kembali input nilai Anda.');
        }

        $ssData = session('ss_data_v2', []);
        foreach ($ssData as $key => $item) {
            if ($item['id'] == $id) {
                $data['id'] = $item['id'];
                $data['riwayat'] = 'Diperbarui '.Carbon::now()->format('d-m-Y');
                $ssData[$key] = $data;
                break;
            }
        }

        session(['ss_data_v2' => $ssData]);

        OperatorDashboardController::logActivity('Analisis SSA', 'diperbarui', "Memperbarui data perhitungan Analisis Shift Share untuk sektor {$request->sektor}.");

        return redirect()->route('operator.ss.index')->with('success', 'Data perhitungan SS berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ssData = session('ss_data_v2', []);
        $ssData = array_filter($ssData, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        session(['ss_data_v2' => array_values($ssData)]);

        OperatorDashboardController::logActivity('Analisis SSA', 'dihapus', 'Menghapus sebuah data perhitungan Analisis Shift Share.');

        return back()->with('success', 'Data perhitungan SS berhasil dihapus!');
    }
}
