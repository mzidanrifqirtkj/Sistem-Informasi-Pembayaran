<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    use HasFactory;

    protected $table = 'absensi_harian';

    protected $primaryKey = 'id';
    protected $fillable = ['tanggal', 'tahun_ajar_id'];


    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class);
    }

    public function absensiSetiapMapel()
    {
        return $this->hasMany(AbsensiSetiapMapel::class, 'absensi_harian_id');
    }
}
