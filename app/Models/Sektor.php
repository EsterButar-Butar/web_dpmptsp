<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sektor extends Model
{
<<<<<<< HEAD
    /**
     * Nama tabel yang digunakan.
     */
    protected $table = 'sektor';
=======
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
    
    public const UPDATED_AT = null;
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'sektor_id';

    /**
     * Menonaktifkan timestamp karena tabel tidak memiliki
     * kolom created_at dan updated_at.
     */
    public $timestamps = false;

    /**
     * Semua atribut dapat diisi secara mass assignment.
     */
    protected $guarded = [];

    /**
     * Accessor agar atribut id mengembalikan nilai sektor_id.
     * Berguna untuk kompatibilitas dengan package atau komponen
     * yang mengharapkan primary key bernama "id".
     */
    public function getIdAttribute()
    {
        return $this->sektor_id;
    }

    /**
     * Relasi ke tabel PDRB Kabupaten.
     */
    public function pdrbKabupaten()
    {
        return $this->hasMany(PdrbKabupaten::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel PDRB Sumatera Utara.
     */
    public function pdrbSumut()
    {
        return $this->hasMany(PdrbSumut::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel Hasil LQ.
     */
    public function hasilLq()
    {
        return $this->hasMany(HasilLq::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel Hasil SSA.
     */
    public function hasilSsa()
    {
        return $this->hasMany(HasilSsa::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel Tipologi Sektor.
     */
    public function tipologiSektor()
    {
        return $this->hasMany(HasilTipologiSektor::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel Indikator Provinsi.
     */
    public function indikatorProvinsi()
    {
        return $this->hasMany(IndikatorProvinsi::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel Indikator Kabupaten.
     */
    public function indikatorKabupaten()
    {
        return $this->hasMany(IndikatorKabupaten::class, 'sektor_id');
    }

    /**
     * Relasi ke tabel Tipologi Klassen.
     */
    public function tipologiKlassen()
    {
        return $this->hasMany(HasilTipologiKlassen::class, 'sektor_id');
    }
}