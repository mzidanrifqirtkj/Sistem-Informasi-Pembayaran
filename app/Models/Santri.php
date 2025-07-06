<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Santri extends Model
{
    use HasFactory;
    protected $guard = 'santri';
    protected $table = 'santris';
    protected $primaryKey = 'id_santri';
    public $timestamps = false;

    protected $fillable = [
        'nama_santri',
        'nis',
        'nik',
        'no_kk',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'no_hp',
        'alamat',
        'golongan_darah',
        'pendidikan_formal',
        'pendidikan_non_formal',
        'foto',
        'foto_kk',
        'tanggal_masuk',
        'is_ustadz',
        'user_id',
        'nama_ayah',
        'no_hp_ayah',
        'pekerjaan_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'alamat_ayah',
        'nama_ibu',
        'no_hp_ibu',
        'pekerjaan_ibu',
        'alamat_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'nama_wali',
        'no_hp_wali',
        'pekerjaan_wali',
        'alamat_wali',
        'tempat_lahir_wali',
        'tanggal_lahir_wali',
        'status',
        'tabungan',
        'status_reason',
        'status_changed_at',
        'status_notes'
    ];

    protected $casts = [
        'monthly_status' => 'array',
        'status_changed_at' => 'date'
    ];

    // Existing relationships...
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function santriTambahanBulanans()
    {
        return $this->hasMany(SantriTambahanBulanan::class, 'santri_id', 'id_santri');
    }

    public function tambahanBulanans()
    {
        return $this->belongsToMany(TambahanBulanan::class, 'santri_tambahan_bulanans', 'santri_id', 'tambahan_bulanan_id')->withPivot(['jumlah'])->withTimestamps();
    }

    public function tagihanBulanan()
    {
        return $this->hasMany(TagihanBulanan::class, 'santri_id', 'id_santri');
    }

    public function tagihanTerjadwal()
    {
        return $this->hasMany(TagihanTerjadwal::class, 'santri_id', 'id_santri');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'nis', 'nis');
    }

    public function penilaianSantri()
    {
        return $this->hasMany(PenilaianSantri::class, 'santri_id', 'id_santri');
    }

    public function qoriKelas()
    {
        return $this->hasMany(QoriKelas::class, 'nis', 'nis');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function biayaSantris()
    {
        return $this->hasMany(BiayaSantri::class, 'santri_id', 'id_santri');
    }

    public function kategoriBiayaJalur()
    {
        return $this->hasManyThrough(
            KategoriBiaya::class,
            DaftarBiaya::class,
            'id_daftar_biaya',
            'id_kategori_biaya',
            'id_santri',
            'daftar_biaya_id'
        )->where('kategori_biayas.status', 'jalur');
    }

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'santri_id', 'id_santri');
    }

    // Existing accessors...
    public function getKelasAktifAttribute()
    {
        $riwayatTerbaru = $this->riwayatKelas()
            ->with('mapelKelas.kelas')
            ->latest()
            ->first();

        return $riwayatTerbaru?->mapelKelas?->kelas;
    }

    public function getNamaKelasAktifAttribute()
    {
        return $this->kelasAktif?->nama_kelas ?? 'Tanpa Kelas';
    }

    public function getKategoriBiayaUtamaAttribute()
    {
        $kategoriUtama = $this->biayaSantris()
            ->with('daftarBiaya.kategoriBiaya')
            ->whereHas('daftarBiaya.kategoriBiaya', function ($q) {
                $q->where('status', 'jalur');
            })
            ->first()?->daftarBiaya?->kategoriBiaya;

        return $kategoriUtama;
    }

    public function getKategoriBiayaUtamaNameAttribute()
    {
        return $this->kategori_biaya_utama?->nama_kategori ?? 'Tanpa Kategori';
    }

    public function getAllKategoriBiayaAttribute()
    {
        return $this->biayaSantris()
            ->with('daftarBiaya.kategoriBiaya')
            ->get()
            ->pluck('daftarBiaya.kategoriBiaya')
            ->filter()
            ->unique('id_kategori_biaya');
    }

    public function getAllKategoriBiayaNameAttribute()
    {
        return $this->all_kategori_biaya
            ->pluck('nama_kategori')
            ->implode(', ') ?: 'Tanpa Kategori';
    }

    // NEW: Total Tunggakan dengan Cache
    public function getTotalTunggakanAttribute()
    {
        $cacheKey = "santri_tunggakan_{$this->id_santri}";

        return Cache::remember($cacheKey, 300, function () {
            try {
                // Load relasi jika belum ada
                if (!$this->relationLoaded('tagihanBulanan')) {
                    $this->load([
                        'tagihanBulanan' => function ($q) {
                            $q->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
                                ->with([
                                    'pembayarans' => function ($q) {
                                        $q->where('is_void', false); },
                                    'paymentAllocations' => function ($q) {
                                        $q->whereHas('pembayaran', function ($q2) {
                                            $q2->where('is_void', false);
                                        });
                                    }
                                ]);
                        }
                    ]);
                }

                if (!$this->relationLoaded('tagihanTerjadwal')) {
                    $this->load([
                        'tagihanTerjadwal' => function ($q) {
                            $q->whereIn('status', ['belum_lunas', 'dibayar_sebagian'])
                                ->with([
                                    'pembayarans' => function ($q) {
                                        $q->where('is_void', false); },
                                    'paymentAllocations' => function ($q) {
                                        $q->whereHas('pembayaran', function ($q2) {
                                            $q2->where('is_void', false);
                                        });
                                    }
                                ]);
                        }
                    ]);
                }

                // Gunakan accessor yang sudah benar di model TagihanBulanan & TagihanTerjadwal
                $tunggakanBulanan = $this->tagihanBulanan->sum('sisa_tagihan');
                $tunggakanTerjadwal = $this->tagihanTerjadwal->sum('sisa_tagihan');

                return $tunggakanBulanan + $tunggakanTerjadwal;

            } catch (\Exception $e) {
                \Log::error('Error calculating total tunggakan for santri', [
                    'santri_id' => $this->id_santri,
                    'error' => $e->getMessage()
                ]);

                // Fail safe: return 0
                return 0;
            }
        });
    }

    // NEW: Clear cache method
    public function clearTunggakanCache()
    {
        $cacheKey = "santri_tunggakan_{$this->id_santri}";
        Cache::forget($cacheKey);
    }

    // Existing scopes...
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonAktif($query)
    {
        return $query->where('status', 'non_aktif');
    }

    // Existing accessors...
    public function getIsAktifAttribute()
    {
        return $this->status === 'aktif';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'aktif'
            ? '<span class="badge badge-success">Aktif</span>'
            : '<span class="badge badge-danger">Non Aktif</span>';
    }

    // Existing methods...
    public function deactivate($reason = null, $notes = null)
    {
        $this->update([
            'status' => 'non_aktif',
            'status_reason' => $reason,
            'status_changed_at' => now(),
            'status_notes' => $notes
        ]);
    }

    public function activate()
    {
        $this->update([
            'status' => 'aktif',
            'status_reason' => null,
            'status_changed_at' => now(),
            'status_notes' => null
        ]);
    }

    public function kategoriBiayaList()
    {
        return $this->hasManyThrough(
            \App\Models\KategoriBiaya::class,
            \App\Models\BiayaSantri::class,
            'santri_id',
            'id_kategori_biaya',
            'id_santri',
            'kategori_biaya_id'
        );
    }

    public function verifyPassword($password)
    {
        return $this->user->password === bcrypt($password);
    }
}
