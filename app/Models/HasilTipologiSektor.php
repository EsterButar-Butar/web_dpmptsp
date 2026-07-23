<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTipologiSektor extends Model
{
    protected $table = 'hasil_tipologi_sektor';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Relasi ke Kabupaten
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kab_id', 'kab_id');
    }

    /**
     * Relasi ke Sektor
     */
    public function sektor()
    {
        return $this->belongsTo(Sektor::class, 'sektor_id', 'sektor_id');
    }
}