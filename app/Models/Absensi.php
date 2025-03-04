<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensis';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'santri_id',
        'nis',
        'nama_santri',
        'jumlah_hadir',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_alpha',
        'bulan',
        'minggu_per_bulan',
        'tahun_ajar_id',
        'kelas_id',
    ];

    protected $casts = [
        'jumlah_hadir' => 'integer',
        'jumlah_izin' => 'integer',
        'jumlah_sakit' => 'integer',
        'jumlah_alpha' => 'integer',
        'bulan' => 'string',
        'minggu_per_bulan' => 'string',
    ];

    public $timestamps = false;

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id', 'id_tahun_ajar');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function scopeFilterByBulanMinggu($query, $bulan, $minggu)
    {
        return $query->where('bulan', $bulan)
            ->where('minggu_per_bulan', $minggu);
    }



}
