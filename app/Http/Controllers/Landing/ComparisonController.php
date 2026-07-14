<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        $filter = [
            'provinsi' => $request->provinsi ?? 'Sumatera Utara',
            'kabupaten' => $request->kabupaten ?? 'Deli Serdang',
            'sektor' => $request->sektor ?? 'Industri Pengolahan',
            'tahun_awal' => $request->tahun_awal ?? 2021,
            'tahun_akhir' => $request->tahun_akhir ?? 2025,
        ];


        $summary = [
            'growth' => 9.29,
            'contribution' => 8.53,
            'lq' => 2.59,
            'status' => 'Kuadran I',
            'kategori' => 'Sektor Unggulan'
        ];


        $trend = [
            [
                'tahun'=>2021,
                'growth'=>4.29,
                'contribution'=>5.23,
                'lq'=>1.54,
                'ssa'=>1.12,
                'kuadran'=>'I',
                'status'=>'Basis'
            ],
            [
                'tahun'=>2022,
                'growth'=>8.53,
                'contribution'=>6.21,
                'lq'=>1.89,
                'ssa'=>1.44,
                'kuadran'=>'II',
                'status'=>'Basis'
            ],
            [
                'tahun'=>2023,
                'growth'=>9.29,
                'contribution'=>7.32,
                'lq'=>2.12,
                'ssa'=>1.67,
                'kuadran'=>'III',
                'status'=>'Basis'
            ],
            [
                'tahun'=>2024,
                'growth'=>9.38,
                'contribution'=>8.01,
                'lq'=>2.33,
                'ssa'=>1.81,
                'kuadran'=>'III',
                'status'=>'Basis'
            ],
            [
                'tahun'=>2025,
                'growth'=>11.21,
                'contribution'=>8.53,
                'lq'=>2.59,
                'ssa'=>2.13,
                'kuadran'=>'I',
                'status'=>'Basis'
            ],
        ];


        return view(
            'landing.comparison',
            compact(
                'filter',
                'summary',
                'trend'
            )
        );
    }
}