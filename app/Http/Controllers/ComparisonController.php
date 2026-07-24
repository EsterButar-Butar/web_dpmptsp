<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Provinsi;
use App\Models\Sektor;
use App\Services\ComparisonService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function __construct(
        private ComparisonService $comparisonService
    ) {
    }

    public function index(Request $request)
    {
        $filter = [

            'provinsi'   => $request->provinsi ?? 12,

            'kabupaten'  => $request->kabupaten,

            'sektor'     => $request->sektor,

            'tahun_awal' => $request->tahun_awal ?? 2021,

            'tahun_akhir'=> $request->tahun_akhir ?? 2025,

        ];

        $dashboard = null;

        if (
            $filter['kabupaten'] &&
            $filter['sektor']
        ) {

            $dashboard = $this
                ->comparisonService
                ->getDashboard($filter);

        }

        $selectedKabupaten = Kabupaten::find($filter['kabupaten']);

        $selectedSektor = Sektor::find($filter['sektor']);

        return view(
            'landing.comparison',
            [

                'provinsi' => Provinsi::orderBy(
                    'nama_provinsi'
                )->get(),

                'kabupaten' => Kabupaten::query()
                    ->when(
                        $filter['provinsi'],
                        fn($q)=>$q->where(
                            'provinsi_id',
                            $filter['provinsi']
                        )
                    )
                    ->orderBy('nama_kabupaten')
                    ->get(),

                'sektor' => Sektor::orderBy(
                    'sektor_id'
                )->get(),

                 'selectedKabupaten' => $selectedKabupaten,
                 'selectedSektor' => $selectedSektor,

                'filter'=>$filter,

                'dashboard'=>$dashboard,

            ]
        );
    }
}