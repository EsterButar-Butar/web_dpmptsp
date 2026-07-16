<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTipologiSektor extends Model
{
    protected $table='hasil_tipologi_sektor';

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

    public function lq()
    {
        return $this->belongsTo(HasilLq::class,'hasil_lq_id');
    }

    public function ssa()
    {
        return $this->belongsTo(HasilSsa::class,'hasil_ssa_id');
    }

}