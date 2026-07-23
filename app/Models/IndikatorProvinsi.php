<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorProvinsi extends Model
{
    protected $table='indikator_provinsi';

    protected $guarded=[];

    public $timestamps=false;

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class,'provinsi_id');
    }

    public function sektor()
    {
        return $this->belongsTo(Sektor::class,'sektor_id');
    }

}