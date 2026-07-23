<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilLq extends Model
{
    protected $table='hasil_lq';

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

    public function tipologi()
    {
        return $this->hasOne(HasilTipologiSektor::class,'hasil_lq_id');
    }

}