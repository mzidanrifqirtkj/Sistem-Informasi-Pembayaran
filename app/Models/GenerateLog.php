<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenerateLog extends Model
{
    use HasFactory;

    protected $table = 'generate_logs';

    protected $fillable = [
        'type',
        'user_id',
        'user_name',
        'parameters',
        'total_processed',
        'total_success',
        'total_failed',
        'errors'
    ];

    protected $casts = [
        'parameters' => 'array',
        'errors' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('total_failed', 0);
    }

    public function scopeFailed($query)
    {
        return $query->where('total_failed', '>', 0);
    }

    // Accessors
    public function getSuccessRateAttribute()
    {
        if ($this->total_processed == 0) {
            return 0;
        }

        return round(($this->total_success / $this->total_processed) * 100, 2);
    }

    public function getStatusAttribute()
    {
        if ($this->total_failed == 0) {
            return 'success';
        } elseif ($this->total_success == 0) {
            return 'failed';
        } else {
            return 'partial';
        }
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'success' => 'success',
            'failed' => 'danger',
            'partial' => 'warning',
            default => 'secondary'
        };
    }

    public function getFormattedTypeAttribute()
    {
        return match ($this->type) {
            'bulk_tagihan_bulanan' => 'Generate Tagihan Bulanan',
            'bulk_tagihan_terjadwal' => 'Generate Tagihan Terjadwal',
            'import_pembayaran' => 'Import Pembayaran',
            default => $this->type
        };
    }

    public function getDurationAttribute()
    {
        if ($this->created_at && $this->updated_at) {
            return $this->created_at->diffForHumans($this->updated_at, true);
        }
        return null;
    }

    // Static methods
    public static function startLog($type, $parameters)
    {
        return static::create([
            'type' => $type,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'parameters' => $parameters,
            'total_processed' => 0,
            'total_success' => 0,
            'total_failed' => 0
        ]);
    }

    // Instance methods
    public function incrementSuccess()
    {
        $this->increment('total_processed');
        $this->increment('total_success');
    }

    public function incrementFailed($error = null)
    {
        $this->increment('total_processed');
        $this->increment('total_failed');

        if ($error) {
            $errors = $this->errors ?? [];
            $errors[] = [
                'time' => now()->toDateTimeString(),
                'error' => $error
            ];
            $this->errors = $errors;
            $this->save();
        }
    }

    public function finish()
    {
        $this->touch(); // Update updated_at
    }
}
