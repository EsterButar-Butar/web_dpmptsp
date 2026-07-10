<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataWilayah extends Model
{
    protected $table = 'data_wilayah';

    protected $fillable = [
        'no_urut',

        'province_code',
        'province_name',

        'regency_code',
        'regency_name',

        'district_code',
        'district_name',

        'village_code',
        'village_name',

        'status',
        'keterangan',
    ];
}