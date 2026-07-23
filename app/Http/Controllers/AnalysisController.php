<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Provinsi;
use App\Services\DashboardAnalysisService;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct(
        private DashboardAnalysisService $dashboardService
    ) {}

    /**
     * ==========================================================
     * Dashboard Analisis
     * ==========================================================
     */
    public function index(
        Request $request
    ) {

        /**
         * ------------------------------------------
         * Filter
         * ------------------------------------------
         */

        $provinsiId = $request->integer('provinsi');

        $kabId = $request->integer('kabupaten');

        $metode = $request->get(
            'metode',
            'lq'
        );

        $tahun = $request->get(
            'tahun',
            now()->year
        );

        /**
         * ------------------------------------------
         * Dropdown
         * ------------------------------------------
         */

        $provinsi =

            Provinsi::orderBy('nama_provinsi')
                ->get();

        $kabupaten =

            Kabupaten::query()

                ->when(

                    $provinsiId,

                    fn($q)=>

                        $q->where(

                            'provinsi_id',

                            $provinsiId

                        )

                )

                ->orderBy('nama_kabupaten')

                ->get();

        /**
         * ------------------------------------------
         * Dashboard
         * ------------------------------------------
         */

        $dashboard = null;

        if ($kabId) {

            $dashboard =

                $this->dashboardService

                    ->getDashboard(

                        kabId: $kabId,

                        metode: $metode,

                        tahun: $tahun

                    );

        }

        /**
         * ------------------------------------------
         * View
         * ------------------------------------------
         */

        return view(

            'landing.analysis',

            [

                'dashboard'=>$dashboard,

                'provinsi'=>$provinsi,

                'kabupaten'=>$kabupaten,

                'filter'=>[

                    'provinsi'=>$provinsiId,

                    'kabupaten'=>$kabId,

                    'metode'=>$metode,

                    'tahun'=>$tahun

                ]

            ]

        );

    }

}