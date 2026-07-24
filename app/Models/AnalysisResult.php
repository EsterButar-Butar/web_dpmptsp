<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisResult extends Model
{
    protected $table = 'analysis_results';

    public $timestamps = false;

    protected $fillable = [
        'tahun',
        'kabupaten_kota',
        'sektor',
        'lq',
        'ssa',
        'klassen',
        'tipologi'
    ];
}