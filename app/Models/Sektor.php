<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sektor extends Model
{
    protected $table = 'sektor';
    protected $primaryKey = 'sektor_id';
    protected $guarded = [];
    
    public const UPDATED_AT = null;

    public function getIdAttribute()
    {
        return $this->sektor_id;
    }
}
