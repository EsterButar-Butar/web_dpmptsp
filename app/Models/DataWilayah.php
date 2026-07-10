<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataWilayah extends Model
{
    protected $table = 'data_wilayah';

    protected $fillable = [
        'no_urut',

        'nama_provinsi',
        'kode_provinsi',

        'nama_kabupaten',
        'kode_kabupaten',

        'nama_kecamatan',
        'kode_kecamatan',

        'nama_desa',
        'kode_desa',

        'status',
        'keterangan',
    ];
}