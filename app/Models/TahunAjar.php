<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjar extends Model
{
    protected $table = 'tahun_ajars';
    protected $primaryKey = 'id_tahun_ajar';
    public $timestamps = false;

    protected $fillable = [
        'tahun_ajar',
    ];
}
