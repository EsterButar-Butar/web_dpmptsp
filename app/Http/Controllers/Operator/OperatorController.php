<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    public static function logActivity($module, $action, $desc)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'desc' => $desc
        ]);
    }

    private function getLatestStatus($modelClass)
    {
        $latest = $modelClass::latest('updated_at')->first();
        if (!$latest) {
            return ['date' => '-', 'action' => 'Kosong', 'color' => 'bg-slate-100 text-slate-500 border-slate-200'];
        }

        $action = ($latest->created_at == $latest->updated_at) ? 'ditambah' : 'diperbarui';

        $color = match ($action) {
            'ditambah' => 'bg-green-100 text-green-700 border-green-200',
            'diperbarui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'diimpor' => 'bg-purple-100 text-purple-700 border-purple-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200'
        };

        return [
            'date' => $latest->updated_at ? $latest->updated_at->format('d M Y') : '-',
            'action' => ucfirst($action),
            'color' => $color,
        ];
    }

    public function index()
    {
        $countLq = \App\Models\LQ::count();
        $countSs = \App\Models\ShiftShare::count();
        $countTipologi = \App\Models\Tipologi::count();
        $countKlassen = \App\Models\Klassen::count();
        $totalAnalisa = $countLq + $countSs + $countTipologi + $countKlassen;

        $statusLq = $this->getLatestStatus(\App\Models\LQ::class);
        $statusSs = $this->getLatestStatus(\App\Models\ShiftShare::class);
        $statusTipologi = $this->getLatestStatus(\App\Models\Tipologi::class);
        $statusKlassen = $this->getLatestStatus(\App\Models\Klassen::class);

        $activityLogs = ActivityLog::latest()->take(10)->get();

        return view('operator.dashboard', compact(
            'countLq', 'countSs', 'countTipologi', 'countKlassen', 'totalAnalisa',
            'statusLq', 'statusSs', 'statusTipologi', 'statusKlassen',
            'activityLogs'
        ));
    }

    public function profile()
    {
        return view('operator.profile');
    }

    public function aktivitas(Request $request)
    {
        $activityLogs = ActivityLog::latest()->get();
        $collection = collect($activityLogs);

        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');

        $filteredLogs = $collection->filter(function ($log) use ($search, $month, $year) {
            $matchSearch = true;
            $matchMonth = true;
            $matchYear = true;

            // Pencarian text
            if ($search) {
                $searchLower = strtolower($search);
                $matchSearch = str_contains(strtolower($log->module), $searchLower) ||
                               str_contains(strtolower($log->action), $searchLower) ||
                               str_contains(strtolower($log->desc), $searchLower);
            }

            // Filter bulan
            if ($month) {
                $matchMonth = $log->created_at->format('m') === str_pad($month, 2, '0', STR_PAD_LEFT);
            }

            // Filter tahun
            if ($year) {
                $matchYear = $log->created_at->format('Y') == $year;
            }

            return $matchSearch && $matchMonth && $matchYear;
        });

        // Pagination
        $perPage = 10;
        $page = $request->get('page', 1);
        
        $paginatedLogs = new LengthAwarePaginator(
            $filteredLogs->forPage($page, $perPage),
            $filteredLogs->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $baseYears = range(date('Y'), 2020);
        $availableYears = collect(array_merge($activityLogs->pluck('created_at')->map(fn($d) => (int)$d->format('Y'))->toArray(), $baseYears))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return view('operator.aktivitas', compact('paginatedLogs', 'search', 'month', 'year', 'availableYears'));
    }

    public function settings()
    {
        return view('operator.settings');
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

