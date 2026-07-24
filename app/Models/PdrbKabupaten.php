<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdrbKabupaten extends Model
{
    protected $table='pdrb_kabupaten';

    protected $primaryKey='pdrb_kab_id';

    public $timestamps=false;

    protected $guarded=[];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class,'kab_id');
    }

    public function sektor()
    {
        return $this->belongsTo(Sektor::class,'sektor_id');
    }

}