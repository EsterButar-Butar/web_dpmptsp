<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sektor extends Model
{
    protected $table='sektor';

    protected $primaryKey='sektor_id';

    public $timestamps=false;

    protected $guarded=[];
    
    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama_sektor'] ?? null;
    }

    public function pdrbKabupaten()
    {
        return $this->hasMany(PdrbKabupaten::class,'sektor_id');
    }

    public function pdrbSumut()
    {
        return $this->hasMany(PdrbSumut::class,'sektor_id');
    }

    public function hasilLq()
    {
        return $this->hasMany(HasilLq::class,'sektor_id');
    }

    public function hasilSsa()
    {
        return $this->hasMany(HasilSsa::class,'sektor_id');
    }

    public function tipologiSektor()
    {
        return $this->hasMany(HasilTipologiSektor::class,'sektor_id');
    }

    public function indikatorProvinsi()
    {
        return $this->hasMany(IndikatorProvinsi::class,'sektor_id');
    }

    public function indikatorKabupaten()
    {
        return $this->hasMany(IndikatorKabupaten::class,'sektor_id');
    }

    public function tipologiKlassen()
    {
        return $this->hasMany(HasilTipologiKlassen::class,'sektor_id');
    }

}