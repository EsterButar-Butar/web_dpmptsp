<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftShare extends Model
{
    protected $table = 'analisis_ss';
    protected $guarded = [];

    public function sektor()
    {
        return $this->belongsTo(Sektor::class, 'sektor_id');
    }
}
