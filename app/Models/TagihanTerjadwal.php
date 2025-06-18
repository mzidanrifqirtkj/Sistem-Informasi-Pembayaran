<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagihanTerjadwal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tagihan_terjadwals';
    protected $primaryKey = 'id_tagihan_terjadwal';
    public $timestamps = true;
    protected $fillable = [
        'santri_id',        // FK ke Santri (BARU/DIKEMBALIKAN)
        'daftar_biaya_id',  // FK ke DaftarBiaya (BARU)
        'biaya_santri_id',  // FK ke BiayaSantri (BISA NULL jika tagihan tidak berasal dari alokasi)
        'tahun_ajar_id',    // FK ke TahunAjar
        'nominal',
        'status',
        'tahun',
        'rincian',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'rincian' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];


    // public function calculateNominal()
    // {
    //     return collect($this->rincian)->sum('nominal');
    // }

    // Relasi langsung ke Santri (BARU/DIKEMBALIKAN)
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }
    // Relasi langsung ke DaftarBiaya (BARU)
    public function daftarBiaya()
    {
        return $this->belongsTo(DaftarBiaya::class, 'daftar_biaya_id', 'id_daftar_biaya');
    }

    public function biayaSantri()
    {
        return $this->belongsTo(BiayaSantri::class, 'biaya_santri_id', 'id_biaya_santri');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id', 'id_tahun_ajar');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_terjadwal_id', 'id_tagihan_terjadwal');
    }

    // Accessor untuk total nominal tagihan (jika rincian digunakan untuk penyesuaian)
    public function calculateNominal(): int
    {
        // Jika nominal sudah ada di kolom 'nominal', gunakan itu.
        // Jika ingin selalu menghitung dari 'rincian', ganti logika ini.
        return $this->nominal ?? collect($this->rincian)->sum('nominal');
    }

    public function getTotalPembayaranAttribute(): float
    {
        return $this->pembayarans->sum('nominal_pembayaran');
    }

    // Accessor untuk sisa tagihan
    public function getSisaTagihanAttribute(): float
    {
        $totalTagihan = $this->nominal;
        $totalDibayar = $this->total_pembayaran;
        return max(0, $totalTagihan - $totalDibayar);
    }

    // Accessor untuk status pembayaran dalam bentuk teks yang mudah dibaca
    public function getStatusPembayaranTextAttribute(): string
    {
        if ($this->sisa_tagihan <= 0) {
            return 'Lunas';
        } elseif ($this->sisa_tagihan > 0 && $this->sisa_tagihan < $this->nominal) {
            return 'Dibayar Sebagian';
        } else {
            return 'Belum Lunas';
        }
    }

    // Method untuk menghitung status berdasarkan pembayaran
    public function calculateStatus(): string
    {
        $totalPembayaran = $this->total_pembayaran;
        $nominalTagihan = $this->nominal;

        if ($totalPembayaran == 0) {
            return 'belum_lunas';
        } elseif ($totalPembayaran >= $nominalTagihan) {
            return 'lunas';
        } else {
            return 'dibayar_sebagian';
        }
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan santri
    public function scopeBySantri($query, $santriId)
    {
        return $query->where('santri_id', $santriId);
    }

    // Scope untuk filter berdasarkan jenis biaya
    public function scopeByJenisBiaya($query, $jenisBiayaId)
    {
        return $query->whereHas('daftarBiaya.kategoriBiaya', function ($q) use ($jenisBiayaId) {
            $q->where('id_kategori_biaya', $jenisBiayaId);
        });
    }

    // Scope untuk search berdasarkan nama santri
    public function scopeSearchSantri($query, $searchTerm)
    {
        return $query->whereHas('santri', function ($q) use ($searchTerm) {
            $q->where('nama_santri', 'like', "%{$searchTerm}%")
                ->orWhere('nis', 'like', "%{$searchTerm}%");
        });
    }
}
