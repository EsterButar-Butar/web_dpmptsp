<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Operator\OperatorDashboardController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LQController extends Controller
{
    public function index(Request $request)
    {
        if (! session()->has('lq_data_v2')) {
            $dummyData = [
                [
                    'id' => 1,
                    'tingkat_wilayah' => 'Kabupaten/Kota',
                    'daerah_analisis' => 'Medan',
                    'daerah_pembanding' => 'Sumatera Utara',
                    'provinsi' => 'Sumatera Utara',
                    'kabupaten' => 'Medan',
                    'sektor' => 'PERTANIAN, KEHUTANAN, DAN PERIKANAN',
                    'tahun' => '2021',
                    'pdrb_sektor_analisis' => 15000,
                    'total_pdrb_analisis' => 50000,
                    'pdrb_sektor_pembanding' => 20000,
                    'total_pdrb_pembanding' => 100000,
                    'nilai_lq' => 1.5,
                    'keterangan' => 'Menunjukkan bahwa indikator dengan LQ > 1, yaitu sektor unggulan (surplus). Peranannya di daerah lebih dominan dibanding rata-rata nasional, sehingga berpotensi besar untuk diekspor.',
                    'kategori' => 'BASIS',
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
                    'tahun' => '2021',
                    'pdrb_sektor_analisis' => 8000,
                    'total_pdrb_analisis' => 50000,
                    'pdrb_sektor_pembanding' => 20000,
                    'total_pdrb_pembanding' => 100000,
                    'nilai_lq' => 0.8,
                    'keterangan' => 'Menunjukkan bahwa indikator dengan LQ < 1, yaitu sektor non-unggulan (defisit). Belum mampu memenuhi kebutuhan daerah karena peranannya lebih rendah dari nasional, sehingga memerlukan impor.',
                    'kategori' => 'NON-BASIS',
                    'riwayat' => 'Ditambah 22-2-2024',
                ],
            ];
            session(['lq_data_v2' => $dummyData]);
        }

        $lqData = session('lq_data_v2', []);

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = collect($lqData)->firstWhere('id', (int) $request->edit) ?? collect($lqData)->firstWhere('id', $request->edit);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($lqData, $perPage * ($currentPage - 1), $perPage);
        $paginatedData = new LengthAwarePaginator($currentItems, count($lqData), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('partials.operator.lq.index', [
            'lqData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }

    public function calculateLQData(Request $request)
    {
        $request->validate([
            'tingkat_wilayah' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'nullable|string',
            'sektor' => 'required|string',
            'tahun' => 'required|numeric',
            'pdrb_sektor_analisis' => 'required',
            'total_pdrb_analisis' => 'required',
            'pdrb_sektor_pembanding' => 'required',
            'total_pdrb_pembanding' => 'required',
        ]);

        $daerah_analisis = $request->tingkat_wilayah === 'Provinsi' ? $request->provinsi : $request->kabupaten;
        $daerah_pembanding = $request->tingkat_wilayah === 'Provinsi' ? 'Nasional' : $request->provinsi;

        $xij = $this->parseNumber($request->pdrb_sektor_analisis);
        $rvj = $this->parseNumber($request->total_pdrb_analisis);
        $xi = $this->parseNumber($request->pdrb_sektor_pembanding);
        $rvi = $this->parseNumber($request->total_pdrb_pembanding);

        if ($rvj == 0 || $rvi == 0 || ($xi / $rvi) == 0) {
            return null;
        }

        $lq = round(($xij / $rvj) / ($xi / $rvi), 2);

        if ($lq > 1) {
            $kategori = 'BASIS';
            $keterangan = 'Menunjukkan bahwa indikator dengan LQ > 1, yaitu sektor unggulan (surplus). Peranannya di daerah lebih dominan dibanding rata-rata referensi, sehingga berpotensi besar untuk diekspor.';
        } elseif ($lq < 1) {
            $kategori = 'NON-BASIS';
            $keterangan = 'Menunjukkan bahwa indikator dengan LQ < 1, yaitu sektor non-unggulan (defisit). Belum mampu memenuhi kebutuhan daerah karena peranannya lebih rendah dari referensi, sehingga memerlukan impor.';
        } else {
            $kategori = 'SEIMBANG';
            $keterangan = 'Menunjukkan bahwa indikator dengan LQ = 1, yaitu sektor berimbang. Produktivitas setara dengan referensi. Mampu memenuhi kebutuhan daerah sendiri namun belum layak diekspor.';
        }

        return [
            'tingkat_wilayah' => $request->tingkat_wilayah,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->tingkat_wilayah === 'Provinsi' ? '-' : ($request->kabupaten ?? '-'),
            'daerah_analisis' => $daerah_analisis,
            'daerah_pembanding' => $daerah_pembanding,
            'sektor' => $request->sektor,
            'tahun' => $request->tahun,
            'pdrb_sektor_analisis' => $xij,
            'total_pdrb_analisis' => $rvj,
            'pdrb_sektor_pembanding' => $xi,
            'total_pdrb_pembanding' => $rvi,
            'nilai_lq' => $lq,
            'keterangan' => $keterangan,
            'kategori' => $kategori,
        ];
    }

    public function hitung(Request $request)
    {
        $data = $this->calculateLQData($request);
        if (! $data) {
            return back()->with('error', 'Terdapat pembagian dengan nol. Periksa kembali input nilai Anda.');
        }

        $data['id'] = time();
        $data['riwayat'] = 'Ditambah '.Carbon::now()->format('d-m-Y');

        $lqData = session('lq_data_v2', []);
        array_unshift($lqData, $data);
        session(['lq_data_v2' => $lqData]);

        OperatorDashboardController::logActivity('Analisis LQ', 'ditambah', "Menambahkan data perhitungan Analisis LQ untuk sektor {$request->sektor}.");

        return back()->with('success', 'Perhitungan LQ berhasil dan data ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $data = $this->calculateLQData($request);
        if (! $data) {
            return back()->with('error', 'Terdapat pembagian dengan nol. Periksa kembali input nilai Anda.');
        }

        $lqData = session('lq_data_v2', []);
        foreach ($lqData as $key => $item) {
            if ($item['id'] == $id) {
                $data['id'] = $item['id'];
                $data['riwayat'] = 'Diperbarui '.Carbon::now()->format('d-m-Y');
                $lqData[$key] = $data;
                break;
            }
        }

        session(['lq_data_v2' => $lqData]);

        OperatorDashboardController::logActivity('Analisis LQ', 'diperbarui', "Memperbarui data perhitungan Analisis LQ untuk sektor {$request->sektor}.");

        return redirect()->route('operator.lq.index')->with('success', 'Data perhitungan LQ berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $lqData = session('lq_data_v2', []);
        $lqData = array_filter($lqData, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        session(['lq_data_v2' => array_values($lqData)]);

        OperatorDashboardController::logActivity('Analisis LQ', 'dihapus', 'Menghapus sebuah data perhitungan Analisis LQ.');

        return back()->with('success', 'Data perhitungan LQ berhasil dihapus!');
    }
}
