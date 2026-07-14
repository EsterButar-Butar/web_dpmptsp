<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipologi extends Model
{
    protected $table = 'analisis_tipologi';
    protected $guarded = [];

    public function sektor()
    {
        return $this->belongsTo(Sektor::class, 'sektor_id');
    }
}
