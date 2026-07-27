<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lq extends Model
{
    protected $table = 'analisis_lq';
    protected $guarded = [];

    public function sektor()
    {
        return $this->belongsTo(Sektor::class, 'sektor_id');
    }
}
