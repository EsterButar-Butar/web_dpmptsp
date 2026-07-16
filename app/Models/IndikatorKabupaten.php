<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorKabupaten extends Model
{
    protected $table='indikator_kabupaten';

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

}