<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeForTable($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    public function scopeForRecord($query, $tableName, $recordId)
    {
        return $query->where('table_name', $tableName)
            ->where('record_id', $recordId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Accessors
    public function getActionColorAttribute()
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            default => 'secondary'
        };
    }

    public function getActionIconAttribute()
    {
        return match ($this->action) {
            'created' => 'fas fa-plus-circle',
            'updated' => 'fas fa-edit',
            'deleted' => 'fas fa-trash',
            default => 'fas fa-question-circle'
        };
    }

    public function getFormattedActionAttribute()
    {
        return match ($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            default => $this->action
        };
    }

    public function getChangedFieldsAttribute()
    {
        if ($this->action === 'created') {
            return array_keys($this->new_values ?? []);
        }

        if ($this->action === 'updated') {
            $old = $this->old_values ?? [];
            $new = $this->new_values ?? [];

            $changed = [];
            foreach ($new as $key => $value) {
                if (!isset($old[$key]) || $old[$key] != $value) {
                    $changed[] = $key;
                }
            }

            return $changed;
        }

        return [];
    }

    // Static methods
    public static function logAction($tableName, $recordId, $action, $oldValues = null, $newValues = null)
    {
        return static::create([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
