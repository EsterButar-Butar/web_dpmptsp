<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {

        $provinsi = $request->provinsi;
        $kabupaten = $request->kabupaten;
        $metode = $request->get('metode','lq');
        $tahun = $request->get('tahun',2025);

        // dropdown
        $provinsiList = DB::table('provinsi')
            ->orderBy('nama')
            ->get();

        $kabupatenList = DB::table('kabupaten')
            ->orderBy('nama')
            ->get();


        if($kabupaten == null){

            $kabupaten = $kabupatenList->first()->id;

        }   


        if($metode == 'lq'){

            $data = DB::table('hasil_lq')

                ->join('sektor','hasil_lq.sektor_id','=','sektor.id')

                ->where('hasil_lq.kabupaten_id',$kabupaten)

                ->where('hasil_lq.tahun',$tahun)

                ->select(
                    'sektor.nama',
                    'hasil_lq.nilai_lq'
                )

                ->orderByDesc('hasil_lq.nilai_lq')

                ->get();



            $basis = $data->where('nilai_lq','>=',1)->count();

            $nonBasis = $data->where('nilai_lq','<',1)->count();

            $max = $data->first();

            $avg = round($data->avg('nilai_lq'),2);


            $summary = [

                [
                    'title'=>'Sektor Basis',
                    'value'=>$basis,
                    'text'=>'dari '.$data->count().' sektor'
                ],

                [
                    'title'=>'Sektor Non Basis',
                    'value'=>$nonBasis,
                    'text'=>'dari '.$data->count().' sektor'
                ],

                [
                    'title'=>'LQ Tertinggi',
                    'value'=>$max->nilai_lq ?? 0,
                    'text'=>$max->nama ?? '-'
                ],

                [
                    'title'=>'Rata-rata LQ',
                    'value'=>$avg,
                    'text'=>'Semua sektor'
                ],

            ];


            $sektor = $data->pluck('nama');

            $nilai = $data->pluck('nilai_lq');

        }

        elseif($metode=='ssa'){

            $data = DB::table('hasil_ssa')

                ->join('sektor','hasil_ssa.sektor_id','=','sektor.id')

                ->where('hasil_ssa.kabupaten_id',$kabupaten)

                ->where('hasil_ssa.tahun',$tahun)

                ->select(
                    'sektor.nama',
                    'hasil_ssa.cij'
                )

                ->orderByDesc('hasil_ssa.cij')

                ->get();


            $summary = [

                [
                    'title'=>'Berdaya Saing',
                    'value'=>$data->where('cij','>',0)->count(),
                    'text'=>'Cij Positif'
                ],

                [
                    'title'=>'Tidak Berdaya Saing',
                    'value'=>$data->where('cij','<=',0)->count(),
                    'text'=>'Cij Negatif'
                ],

                [
                    'title'=>'Cij Tertinggi',
                    'value'=>round($data->max('cij'),2),
                    'text'=>$data->sortByDesc('cij')->first()->nama ?? '-'
                ],

                [
                    'title'=>'Rata-rata Cij',
                    'value'=>round($data->avg('cij'),2),
                    'text'=>'Semua sektor'
                ],

            ];

            $sektor = $data->pluck('nama');

            $nilai = $data->pluck('cij');

        }

        else{

            $summary=[];

            $sektor=[];

            $nilai=[];

        }


        return view('landing.analysis',compact(

            'summary',
            'metode',
            'tahun',
            'sektor',
            'nilai',
            'provinsiList',
            'kabupatenList',
            'kabupaten'

        ));

    }
}