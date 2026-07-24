<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $provinsi      = $request->get('provinsi');
        $kabupaten     = $request->get('kabupaten');
        $metode        = $request->get('metode', 'lq');
        $sektorDipilih = $request->get('sektor');

        $tahun         = $request->get('tahun', 2025);
        $tahunAwal     = $request->get('tahun_awal', $tahun);
        $tahunAkhir    = $request->get('tahun_akhir', $tahun);

        /*
        |--------------------------------------------------------------------------
        | DROPDOWN PROVINSI
        |--------------------------------------------------------------------------
        */

        $provinsiList = DB::table('provinsi')
            ->orderBy('nama_provinsi')
            ->get();

        // Default provinsi pertama
        if (!$provinsi && $provinsiList->isNotEmpty()) {
            $provinsi = $provinsiList->first()->provinsi_id;
        }

        /*
        |--------------------------------------------------------------------------
        | DROPDOWN KABUPATEN
        |--------------------------------------------------------------------------
        */

        $kabupatenList = DB::table('kabupaten')
            ->where('provinsi_id', $provinsi)
            ->orderBy('nama_kabupaten')
            ->get();

        // Default kabupaten pertama sesuai provinsi
        if (!$kabupaten && $kabupatenList->isNotEmpty()) {
            $kabupaten = $kabupatenList->first()->kab_id;
        }

        /*
        |--------------------------------------------------------------------------
        | DROPDOWN SEKTOR
        |--------------------------------------------------------------------------
        */

        $sektorList = DB::table('sektor')
            ->orderBy('nama_sektor')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DEFAULT DATA
        |--------------------------------------------------------------------------
        */

        $summary = [];
        $sektor  = collect();
        $nilai   = collect();

        /*
        |--------------------------------------------------------------------------
        | LOCATION QUOTIENT
        |--------------------------------------------------------------------------
        */

        if ($metode == 'lq') {

            $query = DB::table('hasil_lq')
                ->join('sektor', 'hasil_lq.sektor_id', '=', 'sektor.sektor_id')
                ->where('hasil_lq.kab_id', $kabupaten);

            if ($sektorDipilih) {
                $query->where('hasil_lq.sektor_id', $sektorDipilih);
            }

            if ($tahunAwal && $tahunAkhir) {
                $query->whereBetween('hasil_lq.tahun', [$tahunAwal, $tahunAkhir]);
            } else {
                $query->where('hasil_lq.tahun', $tahun);
            }

            $data = $query
                ->select(
                    'sektor.nama_sektor',
                    'hasil_lq.nilai_lq',
                    'hasil_lq.tahun'
                )
                ->orderByDesc('hasil_lq.nilai_lq')
                ->get();

            $basis    = $data->where('nilai_lq', '>=', 1)->count();
            $nonBasis = $data->where('nilai_lq', '<', 1)->count();
            $max      = $data->sortByDesc('nilai_lq')->first();
            $avg      = round($data->avg('nilai_lq'), 2);

            $summary = [

                [
                    'title' => 'Sektor Basis',
                    'value' => $basis,
                    'text'  => 'dari ' . $data->count() . ' sektor'
                ],

                [
                    'title' => 'Sektor Non Basis',
                    'value' => $nonBasis,
                    'text'  => 'dari ' . $data->count() . ' sektor'
                ],

                [
                    'title' => 'LQ Tertinggi',
                    'value' => $max->nilai_lq ?? 0,
                    'text'  => $max->nama_sektor ?? '-'
                ],

                [
                    'title' => 'Rata-rata LQ',
                    'value' => $avg,
                    'text'  => 'Semua sektor'
                ],

            ];

            $sektor = $data->pluck('nama_sektor');
            $nilai  = $data->pluck('nilai_lq');
        }

        /*
        |--------------------------------------------------------------------------
        | SHIFT SHARE ANALYSIS
        |--------------------------------------------------------------------------
        */

        elseif ($metode == 'ssa') {

            $query = DB::table('hasil_ssa')
                ->join('sektor', 'hasil_ssa.sektor_id', '=', 'sektor.sektor_id')
                ->where('hasil_ssa.kab_id', $kabupaten);

            if ($sektorDipilih) {
                $query->where('hasil_ssa.sektor_id', $sektorDipilih);
            }

            if ($tahunAwal && $tahunAkhir) {
                $query->whereBetween('hasil_ssa.tahun', [$tahunAwal, $tahunAkhir]);
            } else {
                $query->where('hasil_ssa.tahun', $tahun);
            }

            $data = $query
                ->select(
                    'sektor.nama_sektor',
                    'hasil_ssa.cij',
                    'hasil_ssa.tahun'
                )
                ->orderByDesc('hasil_ssa.cij')
                ->get();

            $summary = [

                [
                    'title' => 'Berdaya Saing',
                    'value' => $data->where('cij', '>', 0)->count(),
                    'text'  => 'Cij Positif'
                ],

                [
                    'title' => 'Tidak Berdaya Saing',
                    'value' => $data->where('cij', '<=', 0)->count(),
                    'text'  => 'Cij Negatif'
                ],

                [
                    'title' => 'Cij Tertinggi',
                    'value' => round($data->max('cij'), 2),
                    'text'  => optional($data->sortByDesc('cij')->first())->nama_sektor ?? '-'
                ],

                [
                    'title' => 'Rata-rata Cij',
                    'value' => round($data->avg('cij'), 2),
                    'text'  => 'Semua sektor'
                ],

            ];

            $sektor = $data->pluck('nama_sektor');
            $nilai  = $data->pluck('cij');
        }

        /*
        |--------------------------------------------------------------------------
        | DEBUG (aktifkan jika diperlukan)
        |--------------------------------------------------------------------------
        */

        /*
        dd([
            'provinsi'         => $provinsi,
            'kabupaten'        => $kabupaten,
            'jumlah_kabupaten' => $kabupatenList->count(),
        ]);
        */

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view('landing.analysis', compact(

            'summary',

            'metode',

            'tahun',

            'tahunAwal',

            'tahunAkhir',

            'sektor',

            'nilai',

            'provinsi',

            'kabupaten',

            'provinsiList',

            'kabupatenList',

            'sektorList',

            'sektorDipilih'

        ));
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX : Kabupaten berdasarkan Provinsi
    |--------------------------------------------------------------------------
    */

    public function getKabupaten($provinsi)
    {
        return DB::table('kabupaten')
            ->where('provinsi_id', $provinsi)
            ->orderBy('nama_kabupaten')
            ->get();
    }
}