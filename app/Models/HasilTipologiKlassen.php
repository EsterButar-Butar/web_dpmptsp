<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTipologiKlassen extends Model
{
    protected $table='hasil_tipologi_klassen';

    protected $guarded=[];

    public $timestamps=false;

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class,'kab_id');
    }

    public function sektor()
    {
        return $this->belongsTo(Sektor::class,'sektor_id');
    }

<<<<<<< HEAD
    public function indikatorProvinsi()
    {
        return $this->belongsTo(
            IndikatorProvinsi::class,
            'indikator_provinsi_id'
        );
    }

=======
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29
    public function indikatorKabupaten()
    {
        return $this->belongsTo(
            IndikatorKabupaten::class,
<<<<<<< HEAD
            'indikator_kabupaten_id'
=======
            'indikator_kabupaten_id',
            'id'
        );
    }

    public function indikatorProvinsi()
    {
        return $this->belongsTo(
            IndikatorProvinsi::class,
            'indikator_provinsi_id',
            'id'
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29
        );
    }

}