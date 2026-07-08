<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {

        $metode = $request->get('metode', 'lq');

        $tahun = $request->get('tahun', 2025);


        if($metode == 'lq'){

            $summary = [

                [
                    'title'=>'Sektor Basis',
                    'value'=>5,
                    'text'=>'dari 17 sektor'
                ],

                [
                    'title'=>'Sektor Non Basis',
                    'value'=>12,
                    'text'=>'dari 17 sektor'
                ],

                [
                    'title'=>'LQ Tertinggi',
                    'value'=>2.59,
                    'text'=>'Transportasi'
                ],

                [
                    'title'=>'Rata-rata LQ',
                    'value'=>0.96,
                    'text'=>'Semua sektor'
                ],

            ];


            $chart = [
                2.59,
                1.71,
                1.28,
                1.15,
                0.98
            ];

        }


        elseif($metode == 'ssa'){

            $summary = [

                [
                    'title'=>'Pertumbuhan Cepat',
                    'value'=>5,
                    'text'=>'Sektor'
                ],

                [
                    'title'=>'Pertumbuhan Lambat',
                    'value'=>12,
                    'text'=>'Sektor'
                ],

                [
                    'title'=>'Daya Saing Baik',
                    'value'=>5,
                    'text'=>'Cij positif'
                ],

                [
                    'title'=>'Tidak Berdaya Saing',
                    'value'=>12,
                    'text'=>'Cij negatif'
                ],

            ];


            $chart=[
                80,
                75,
                55,
                40,
                30
            ];

        }


        else{

            $summary=[];

            $chart=[];

        }



        return view('landing.analysis',[

            'metode'=>$metode,

            'tahun'=>$tahun,

            'summary'=>$summary,


            'sektor'=>[

                'Pertanian',

                'Industri',

                'Konstruksi',

                'Perdagangan',

                'Transportasi'

            ],


            'nilai'=>$chart

        ]);

    }
}