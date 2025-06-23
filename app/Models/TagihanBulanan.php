<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TagihanBulanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihan_bulanans';
    protected $primaryKey = 'id_tagihan_bulanan';

    protected $fillable = [
        'santri_id',
        'bulan',
        'bulan_urutan',
        'tahun',
        'due_date',
        'nominal',
        'rincian',
        'status'
    ];

    // Cast attributes
    protected $casts = [
        'rincian' => 'array',
        'nominal' => 'decimal:0',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Month mapping
    public static $bulanMapping = [
        'Jan' => 1,
        'Feb' => 2,
        'Mar' => 3,
        'Apr' => 4,
        'May' => 5,
        'Jun' => 6,
        'Jul' => 7,
        'Aug' => 8,
        'Sep' => 9,
        'Oct' => 10,
        'Nov' => 11,
        'Dec' => 12
    ];

    // Boot method untuk auto-set values
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set bulan_urutan otomatis
            if (isset(self::$bulanMapping[$model->bulan])) {
                $model->bulan_urutan = self::$bulanMapping[$model->bulan];
            }

            // Set due_date otomatis (tanggal 1 bulan berikutnya)
            if (!$model->due_date && $model->tahun && $model->bulan_urutan) {
                $date = Carbon::create($model->tahun, $model->bulan_urutan, 1);
                $model->due_date = $date->addMonth()->format('Y-m-d');
            }

            // Set default status
            if (!$model->status) {
                $model->status = 'belum_lunas';
            }
        });
    }

    // Relationships
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id_santri');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_bulanan_id', 'id_tagihan_bulanan');
    }

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'tagihan_bulanan_id', 'id_tagihan_bulanan');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    public function scopeBySantri($query, $santriId)
    {
        return $query->where('santri_id', $santriId);
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', ['belum_lunas', 'dibayar_sebagian']);
    }

    public function scopeOrderByMonth($query)
    {
        return $query->orderBy('tahun')->orderBy('bulan_urutan');
    }

    // Accessors & Mutators
    public function getTotalPembayaranAttribute()
    {
        $totalLangsung = $this->pembayarans()
            ->where('is_void', false)
            ->sum('nominal_pembayaran');

        $totalAlokasi = $this->paymentAllocations()
            ->whereHas('pembayaran', function ($q) {
                $q->where('is_void', false);
            })
            ->sum('allocated_amount');

        return $totalLangsung + $totalAlokasi;
    }

    public function getSisaTagihanAttribute()
    {
        return max(0, $this->nominal - $this->total_pembayaran);
    }

    public function getIsLunasAttribute()
    {
        return $this->status === 'lunas';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'lunas' => 'success',
            'dibayar_sebagian' => 'warning',
            'belum_lunas' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            'lunas' => '✅',
            'dibayar_sebagian' => '⚠️',
            'belum_lunas' => '❌',
            default => '❓'
        };
    }

    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('d M Y') : '-';
    }

    // Methods
    public function calculateNominal()
    {
        return collect($this->rincian)->sum('nominal');
    }

    public function updateStatus()
    {
        // CLEAR cache relationships dulu
        $this->unsetRelation('pembayarans');
        $this->unsetRelation('paymentAllocations');

        // Refresh model untuk data terbaru
        $this->refresh();

        // Hitung total pembayaran yang tidak void dengan query fresh
        $totalPembayaranLangsung = $this->pembayarans()
            ->where('is_void', false)
            ->sum('nominal_pembayaran');

        // Hitung total dari payment allocations yang tidak void
        $totalPembayaranAlokasi = $this->paymentAllocations()
            ->whereHas('pembayaran', function ($q) {
                $q->where('is_void', false);
            })
            ->sum('allocated_amount');

        $totalPembayaran = $totalPembayaranLangsung + $totalPembayaranAlokasi;

        if ($totalPembayaran >= $this->nominal) {
            $this->status = 'lunas';
        } elseif ($totalPembayaran > 0) {
            $this->status = 'dibayar_sebagian';
        } else {
            $this->status = 'belum_lunas';
        }

        $this->save();
        return $this->status;
    }

    public function canEdit()
    {
        // Tidak bisa edit jika sudah ada pembayaran
        return $this->pembayarans->count() === 0 &&
            $this->paymentAllocations->count() === 0;
    }

    public function canDelete()
    {
        // Tidak bisa hapus jika sudah ada pembayaran
        return $this->canEdit();
    }
}
