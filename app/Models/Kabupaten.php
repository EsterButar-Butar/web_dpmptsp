<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';

    protected $primaryKey = 'kab_id';

    public $timestamps = false;

    protected $guarded = [];
}