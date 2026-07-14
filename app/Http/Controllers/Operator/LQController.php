<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\LQ;
use App\Models\Sektor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class LqController extends Controller
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
                'tahun' => $item->tahun,
                'pdrb_sektor_analisis' => $item->pdrb_sektor_analisis,
                'total_pdrb_analisis' => $item->total_pdrb_analisis,
                'pdrb_sektor_pembanding' => $item->pdrb_sektor_pembanding,
                'total_pdrb_pembanding' => $item->total_pdrb_pembanding,
                'nilai_lq' => $item->nilai_lq,
                'keterangan' => $item->keterangan,
                'kategori' => $item->kategori,
                'riwayat' => 'Diperbarui ' . $item->updated_at->format('d-m-Y'),
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $rawDbData = LQ::with('sektor')->latest()->get();
        $lqData = collect($this->mapDbToView($rawDbData));

        $editItem = null;
        if ($request->has('edit')) {
            $editItem = $lqData->firstWhere('id', $request->edit);
        }

        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedData = new LengthAwarePaginator(
            $lqData->forPage($page, $perPage),
            $lqData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('operator.lq.index', [
            'lqData' => $paginatedData,
            'editItem' => $editItem,
        ]);
    }



    private function calculateLQData(Request $request)
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

    public function store(Request $request)
    {
        $newData = $this->calculateLQData($request);

        if (! $newData) {
            return back()->with('error', 'Semua form PDRB wajib diisi dengan angka valid!');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $newData['sektor']]);

        LQ::create([
            'user_id' => Auth::id() ?? 1,
            'sektor_id' => $sektorModel->id,
            'tingkat_wilayah' => $newData['tingkat_wilayah'],
            'daerah_analisis' => $newData['daerah_analisis'],
            'daerah_pembanding' => $newData['daerah_pembanding'],
            'tahun' => $newData['tahun'],
            'pdrb_sektor_analisis' => $newData['pdrb_sektor_analisis'],
            'total_pdrb_analisis' => $newData['total_pdrb_analisis'],
            'pdrb_sektor_pembanding' => $newData['pdrb_sektor_pembanding'],
            'total_pdrb_pembanding' => $newData['total_pdrb_pembanding'],
            'nilai_lq' => $newData['nilai_lq'],
            'kategori' => $newData['kategori'],
            'keterangan' => $newData['keterangan']
        ]);

        OperatorController::logActivity('Analisis LQ', 'ditambah', "Menambah data perhitungan LQ untuk daerah {$newData['daerah_analisis']} tahun {$newData['tahun']}");

        return redirect()->route('operator.lq.index')->with('success', 'Data perhitungan LQ berhasil disimpan secara permanen!');
    }

    public function update(Request $request, $id)
    {
        $lq = LQ::find($id);
        if (!$lq) {
            return redirect()->route('operator.lq.index')->with('error', 'Data tidak ditemukan!');
        }

        $updatedData = $this->calculateLQData($request);

        if (! $updatedData) {
            return back()->with('error', 'Semua form PDRB wajib diisi dengan angka valid!');
        }

        $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $updatedData['sektor']]);

        $lq->update([
            'sektor_id' => $sektorModel->id,
            'tingkat_wilayah' => $updatedData['tingkat_wilayah'],
            'daerah_analisis' => $updatedData['daerah_analisis'],
            'daerah_pembanding' => $updatedData['daerah_pembanding'],
            'tahun' => $updatedData['tahun'],
            'pdrb_sektor_analisis' => $updatedData['pdrb_sektor_analisis'],
            'total_pdrb_analisis' => $updatedData['total_pdrb_analisis'],
            'pdrb_sektor_pembanding' => $updatedData['pdrb_sektor_pembanding'],
            'total_pdrb_pembanding' => $updatedData['total_pdrb_pembanding'],
            'nilai_lq' => $updatedData['nilai_lq'],
            'kategori' => $updatedData['kategori'],
            'keterangan' => $updatedData['keterangan']
        ]);

        OperatorController::logActivity('Analisis LQ', 'diubah', "Mengubah data perhitungan LQ daerah {$updatedData['daerah_analisis']}");

        return redirect()->route('operator.lq.index')->with('success', 'Data perhitungan LQ berhasil diperbarui secara permanen!');
    }

    public function destroy($id)
    {
        $lq = LQ::find($id);
        
        if ($lq) {
            $daerah = $lq->daerah_analisis;
            $lq->delete();
            OperatorController::logActivity('Analisis LQ', 'dihapus', "Menghapus data perhitungan LQ daerah {$daerah}");
        }

        return back()->with('success', 'Data perhitungan LQ berhasil dihapus secara permanen!');
    }

    public function import(Request $request)
    {
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Data format tidak valid.']);
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
            
            $sektorModel = Sektor::firstOrCreate(['nama_sektor' => $item['Sektor']]);

            // 1. Data Tahun Awal
            $mappedItemAwal = [
                'tingkat_wilayah' => $tingkat,
                'provinsi' => $item['Provinsi'],
                'kabupaten' => $item['Kabupaten/Kota'] ?? '-',
                'sektor' => $item['Sektor'],
                'tahun' => $item['Tahun Awal'],
                'pdrb_sektor_analisis' => $item['PDRB Sektor Analisis Awal'] ?? 0,
                'total_pdrb_analisis' => $item['Total PDRB Analisis Awal'] ?? 0,
                'pdrb_sektor_pembanding' => $item['PDRB Sektor Pembanding Awal'] ?? 0,
                'total_pdrb_pembanding' => $item['Total PDRB Pembanding Awal'] ?? 0,
            ];
            $requestObjAwal = new Request($mappedItemAwal);
            $newDataAwal = $this->calculateLQData($requestObjAwal);
            
            if ($newDataAwal) {
                LQ::create([
                    'user_id' => Auth::id() ?? 1,
                    'sektor_id' => $sektorModel->id,
                    'tingkat_wilayah' => $newDataAwal['tingkat_wilayah'],
                    'daerah_analisis' => $newDataAwal['daerah_analisis'],
                    'daerah_pembanding' => $newDataAwal['daerah_pembanding'],
                    'tahun' => $newDataAwal['tahun'],
                    'pdrb_sektor_analisis' => $newDataAwal['pdrb_sektor_analisis'],
                    'total_pdrb_analisis' => $newDataAwal['total_pdrb_analisis'],
                    'pdrb_sektor_pembanding' => $newDataAwal['pdrb_sektor_pembanding'],
                    'total_pdrb_pembanding' => $newDataAwal['total_pdrb_pembanding'],
                    'nilai_lq' => $newDataAwal['nilai_lq'],
                    'kategori' => $newDataAwal['kategori'],
                    'keterangan' => $newDataAwal['keterangan']
                ]);
                $successCount++;
            }

            // 2. Data Tahun Akhir
            $mappedItemAkhir = [
                'tingkat_wilayah' => $tingkat,
                'provinsi' => $item['Provinsi'],
                'kabupaten' => $item['Kabupaten/Kota'] ?? '-',
                'sektor' => $item['Sektor'],
                'tahun' => $item['Tahun Akhir'],
                'pdrb_sektor_analisis' => $item['PDRB Sektor Analisis Akhir'] ?? 0,
                'total_pdrb_analisis' => $item['Total PDRB Analisis Akhir'] ?? 0,
                'pdrb_sektor_pembanding' => $item['PDRB Sektor Pembanding Akhir'] ?? 0,
                'total_pdrb_pembanding' => $item['Total PDRB Pembanding Akhir'] ?? 0,
            ];
            $requestObjAkhir = new Request($mappedItemAkhir);
            $newDataAkhir = $this->calculateLQData($requestObjAkhir);
            
            if ($newDataAkhir) {
                LQ::create([
                    'user_id' => Auth::id() ?? 1,
                    'sektor_id' => $sektorModel->id,
                    'tingkat_wilayah' => $newDataAkhir['tingkat_wilayah'],
                    'daerah_analisis' => $newDataAkhir['daerah_analisis'],
                    'daerah_pembanding' => $newDataAkhir['daerah_pembanding'],
                    'tahun' => $newDataAkhir['tahun'],
                    'pdrb_sektor_analisis' => $newDataAkhir['pdrb_sektor_analisis'],
                    'total_pdrb_analisis' => $newDataAkhir['total_pdrb_analisis'],
                    'pdrb_sektor_pembanding' => $newDataAkhir['pdrb_sektor_pembanding'],
                    'total_pdrb_pembanding' => $newDataAkhir['total_pdrb_pembanding'],
                    'nilai_lq' => $newDataAkhir['nilai_lq'],
                    'kategori' => $newDataAkhir['kategori'],
                    'keterangan' => $newDataAkhir['keterangan']
                ]);
                $successCount++;
            }
        }

        if ($successCount > 0) {
            OperatorController::logActivity('Analisis LQ', 'diimpor', "Mengimpor {$successCount} rekaman Analisis LQ secara massal dari template master.");
            session()->flash('success', "Berhasil mengimpor $successCount data (karena format Awal & Akhir dipecah)!");
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format kolom sesuai dengan Template Master.']);
    }
}

