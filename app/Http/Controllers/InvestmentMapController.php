<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\AnalysisResult;
use Illuminate\Http\Request;

class InvestmentMapController extends Controller
{
    /**
     * Menampilkan halaman peta investasi
     */
    public function index()
    {
        $lokasi = Lokasi::orderBy('nama')->get();
        $analysis = AnalysisResult::all();

        return view('landing.map', compact('lokasi', 'analysis'));
    }

    /**
     * Mengambil hasil analisis suatu kabupaten/kota
     */
    public function analysis($nama)
    {
        $analysis = AnalysisResult::where('kabupaten_kota', $nama)
            ->where('tahun', 2025)
            ->orderByDesc('lq')
            ->orderByDesc('ssa')
            ->first();

        if (!$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'success'   => true,
            'kabupaten' => $analysis->kabupaten_kota,
            'sektor'    => $analysis->sektor,
            'lq'        => $analysis->lq,
            'ssa'       => $analysis->ssa,
            'klassen'   => $analysis->klassen,
            'tipologi'  => $analysis->tipologi,
            'tahun'     => $analysis->tahun,
        ]);
    }
}