<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OperatorDashboardController extends Controller
{
    public static function logActivity($module, $action, $desc)
    {
        $logs = session('activity_log', []);
        array_unshift($logs, [
            'module' => $module,
            'action' => $action, // ditambah, diperbarui, dihapus, diimpor
            'desc' => $desc,
            'time' => Carbon::now()->format('d M Y H:i'),
        ]);
        // Menyimpan semua log tanpa batasan sesuai permintaan
        session(['activity_log' => $logs]);
    }

    private function getLatestStatus($sessionData)
    {
        if (empty($sessionData)) {
            return ['date' => '-', 'action' => 'Kosong', 'color' => 'bg-slate-100 text-slate-500'];
        }

        $latest = $sessionData[0];
        $riwayat = $latest['riwayat'] ?? '';

        $parts = explode(' ', $riwayat);
        $action = strtolower($parts[0] ?? 'ditambah');

        $color = match ($action) {
            'ditambah' => 'bg-green-100 text-green-700 border-green-200',
            'diperbarui' => 'bg-blue-100 text-blue-700 border-blue-200',
            'diimpor' => 'bg-purple-100 text-purple-700 border-purple-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200'
        };

        return [
            'date' => Carbon::now()->format('d M Y'), // Karena data dummy kadang tanggalnya beda, kita gunakan fallback
            'action' => ucfirst($action),
            'color' => $color,
        ];
    }

    public function index()
    {
        $lqData = session('lq_data_v2', []);
        $ssData = session('ss_data_v2', []);
        $tipologiData = session('tipologi_data_v2', []);
        $klassenData = session('klassen_data_v2', []);

        $countLq = count($lqData);
        $countSs = count($ssData);
        $countTipologi = count($tipologiData);
        $countKlassen = count($klassenData);
        $totalAnalisa = $countLq + $countSs + $countTipologi + $countKlassen;

        $statusLq = $this->getLatestStatus($lqData);
        $statusSs = $this->getLatestStatus($ssData);
        $statusTipologi = $this->getLatestStatus($tipologiData);
        $statusKlassen = $this->getLatestStatus($klassenData);

        $activityLogs = session('activity_log', []);

        return view('partials.operator.dashboard', compact(
            'countLq', 'countSs', 'countTipologi', 'countKlassen', 'totalAnalisa',
            'statusLq', 'statusSs', 'statusTipologi', 'statusKlassen',
            'activityLogs'
        ));
    }

    public function profile()
    {
        return view('partials.operator.profile');
    }

    public function aktivitas(Request $request)
    {
        $activityLogs = session('activity_log', []);

        $collection = collect($activityLogs);

        // Ekstrak tahun unik yang ada di dalam log
        $logYears = $collection->map(function ($log) {
            try {
                return (int) Carbon::parse($log['time'])->format('Y');
            } catch (\Exception $e) {
                return (int) date('Y');
            }
        })->toArray();

        // Buat rentang tahun dasar dari tahun saat ini mundur hingga 2020
        $baseYears = range(date('Y'), 2020);

        // Gabungkan tahun dari log dengan tahun dasar, hapus duplikat, dan urutkan
        $availableYears = collect(array_merge($logYears, $baseYears))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        // Filter variables
        $filterMonth = $request->input('month');
        $filterYear = $request->input('year');

        // Filter logic
        if ($filterMonth || $filterYear) {
            $collection = $collection->filter(function ($log) use ($filterMonth, $filterYear) {
                try {
                    $date = Carbon::parse($log['time']);
                    $matchMonth = $filterMonth ? $date->format('m') == $filterMonth : true;
                    $matchYear = $filterYear ? $date->format('Y') == $filterYear : true;

                    return $matchMonth && $matchYear;
                } catch (\Exception $e) {
                    return true;
                }
            });
        }

        $perPage = 20;
        $page = $request->input('page', 1);

        $currentPageItems = $collection->slice(($page - 1) * $perPage, $perPage)->all();

        $paginatedLogs = new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('partials.operator.aktivitas', compact('paginatedLogs', 'activityLogs', 'filterMonth', 'filterYear', 'availableYears'));
    }

    public function settings()
    {
        return view('partials.operator.settings');
    }

    // Simulasi aksi update
    public function updateProfile(Request $request)
    {
        return back()->with('success', 'Data profil berhasil diperbarui! (Mode Simulasi)');
    }

    public function updatePassword(Request $request)
    {
        return back()->with('success', 'Password akun berhasil diubah! (Mode Simulasi)');
    }

    public function toggle2FA(Request $request)
    {
        return back()->with('success', 'Status Autentikasi Dua Faktor (2FA) berhasil diubah! (Mode Simulasi)');
    }
}
