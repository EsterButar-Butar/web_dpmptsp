<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTipologiSektor extends Model
{
<<<<<<< HEAD
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

=======
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
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29
}