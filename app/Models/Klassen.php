<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klassen extends Model
{
    protected $table = 'analisis_klassen';
    protected $guarded = [];

    public function sektor()
    {
        return $this->belongsTo(Sektor::class, 'sektor_id');
    }
}
