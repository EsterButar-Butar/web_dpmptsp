<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Operator\OperatorDashboardController;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UploadExcelController extends Controller
{
    // Memproses import data massal dari file Excel untuk LQ
    public function importLq(Request $request)
    {
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Data format tidak valid.']);
        }

        $lqData = session('lq_data_v2', []);
        $successCount = 0;
        
        $lqController = new LQController();

        foreach ($payload as $item) {
            if (! isset($item['Provinsi']) || ! isset($item['Sektor']) || ! isset($item['Tahun']) ||
                ! isset($item['PDRB Sektor Analisis']) || ! isset($item['Total PDRB Analisis']) ||
                ! isset($item['PDRB Sektor Pembanding']) || ! isset($item['Total PDRB Pembanding'])) {
                continue;
            }

            $tingkat = (isset($item['Kabupaten/Kota']) && $item['Kabupaten/Kota'] != '-' && $item['Kabupaten/Kota'] != '') ? 'Kabupaten/Kota' : 'Provinsi';

            $mappedItem = [
                'tingkat_wilayah' => $tingkat,
                'provinsi' => $item['Provinsi'],
                'kabupaten' => $item['Kabupaten/Kota'] ?? '-',
                'sektor' => $item['Sektor'],
                'tahun' => $item['Tahun'],
                'pdrb_sektor_analisis' => $item['PDRB Sektor Analisis'],
                'total_pdrb_analisis' => $item['Total PDRB Analisis'],
                'pdrb_sektor_pembanding' => $item['PDRB Sektor Pembanding'],
                'total_pdrb_pembanding' => $item['Total PDRB Pembanding'],
            ];

            $requestObj = new Request($mappedItem);
            $data = $lqController->calculateLQData($requestObj);
            if (! $data) {
                continue;
            }

            $data['id'] = time().rand(100, 999);
            $data['riwayat'] = 'Diimpor '.Carbon::now()->format('d-m-Y');

            array_unshift($lqData, $data);
            $successCount++;
        }

        session(['lq_data_v2' => $lqData]);

        if ($successCount > 0) {
            OperatorDashboardController::logActivity('Analisis LQ', 'diimpor', "Mengimpor {$successCount} data Analisis LQ secara massal.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format sesuai template.']);
    }

    // Memproses import data massal dari file Excel untuk SS
    public function importSs(Request $request)
    {
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Data format tidak valid.']);
        }

        $ssData = session('ss_data_v2', []);
        $successCount = 0;
        
        $ssController = new ShiftShareController();

        foreach ($payload as $item) {
            if (! isset($item['Provinsi']) || ! isset($item['Sektor']) || ! isset($item['Tahun Awal']) || ! isset($item['Tahun Akhir']) ||
                ! isset($item['PDRB Sektor Analisis Awal']) || ! isset($item['PDRB Sektor Analisis Akhir']) ||
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
                'pdrb_sektor_analisis_awal' => $item['PDRB Sektor Analisis Awal'],
                'pdrb_sektor_analisis_akhir' => $item['PDRB Sektor Analisis Akhir'],
                'pdrb_sektor_pembanding_awal' => $item['PDRB Sektor Pembanding Awal'],
                'pdrb_sektor_pembanding_akhir' => $item['PDRB Sektor Pembanding Akhir'],
                'total_pdrb_pembanding_awal' => $item['Total PDRB Pembanding Awal'],
                'total_pdrb_pembanding_akhir' => $item['Total PDRB Pembanding Akhir'],
            ];

            $data = $ssController->calculateSSData($mappedItem);
            if (! $data) {
                continue;
            }

            $data['id'] = time().rand(100, 999);
            $data['riwayat'] = 'Diimpor '.Carbon::now()->format('d-m-Y');

            array_unshift($ssData, $data);
            $successCount++;
        }

        session(['ss_data_v2' => $ssData]);

        if ($successCount > 0) {
            OperatorDashboardController::logActivity('Analisis SSA', 'diimpor', "Mengimpor {$successCount} data Analisis Shift Share secara massal.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format sesuai template.']);
    }

    // Memproses import data massal dari file Excel untuk Tipologi
    public function importTipologi(Request $request)
    {
        $payload = $request->json()->all();
        if (! $payload || ! is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Data format tidak valid.']);
        }

        $tipologiData = session('tipologi_data_v2', []);
        $successCount = 0;
        
        $tipologiController = new TipologiController();

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

            $data = $tipologiController->calculateTipologiData($mappedItem);
            if (! $data) {
                continue;
            }

            $data['id'] = time().rand(100, 999);
            $data['riwayat'] = 'Diimpor '.Carbon::now()->format('d-m-Y');

            array_unshift($tipologiData, $data);
            $successCount++;
        }

        session(['tipologi_data_v2' => $tipologiData]);

        if ($successCount > 0) {
            OperatorDashboardController::logActivity('Analisis Tipologi', 'diimpor', "Mengimpor {$successCount} data Analisis Tipologi Sektor secara massal.");
            session()->flash('success', "Berhasil mengimpor $successCount data baru!");

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat diimpor. Pastikan format sesuai template.']);
    }

    // Memproses import data massal dari file Excel untuk Klassen
    public function importKlassen(Request $request)
    {
        $data = $request->input('data');

        if (! is_array($data) || empty($data)) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.']);
        }

        $klassenData = session('klassen_data_v2', []);
        $importedCount = 0;
        
        $klassenController = new KlassenController();

        foreach ($data as $item) {
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
                'pdrb_sektor_analisis_awal' => $item['PDRB Sektor Analisis Awal'],
                'pdrb_sektor_analisis_akhir' => $item['PDRB Sektor Analisis Akhir'],
                'total_pdrb_analisis_awal' => $item['Total PDRB Analisis Awal'],
                'total_pdrb_analisis_akhir' => $item['Total PDRB Analisis Akhir'],
                'pdrb_sektor_pembanding_awal' => $item['PDRB Sektor Pembanding Awal'],
                'pdrb_sektor_pembanding_akhir' => $item['PDRB Sektor Pembanding Akhir'],
                'total_pdrb_pembanding_awal' => $item['Total PDRB Pembanding Awal'],
                'total_pdrb_pembanding_akhir' => $item['Total PDRB Pembanding Akhir'],
            ];

            $calculated = $klassenController->calculateKlassenData($mappedItem);
            if ($calculated) {
                $calculated['id'] = time().rand(100, 999);
                $calculated['riwayat'] = 'Diimpor '.Carbon::now()->format('d-m-Y');
                array_unshift($klassenData, $calculated);
                $importedCount++;
            }
        }

        session(['klassen_data_v2' => $klassenData]);

        if ($importedCount > 0) {
            OperatorDashboardController::logActivity('Analisis Klassen', 'diimpor', "Mengimpor {$importedCount} data Analisis Tipologi Klassen secara massal.");
        }

        return response()->json([
            'success' => true,
            'message' => $importedCount.' data berhasil diimpor.',
        ]);
    }
}
