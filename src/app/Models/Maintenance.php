<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maintenance extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'type',
        'status',
        'description',
        'vendor',
        'vendor_phone',
        'cost',
        'started_at',
        'completed_at',
        'estimated_completion',
        'notes',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'started_at' => 'date',
        'completed_at' => 'date',
        'estimated_completion' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(MaintenancePhoto::class);
    }

    public function photosBefore(): HasMany
    {
        return $this->hasMany(MaintenancePhoto::class)->where('type', 'before');
    }

    public function photosProgress(): HasMany
    {
        return $this->hasMany(MaintenancePhoto::class)->where('type', 'progress');
    }

    public function photosAfter(): HasMany
    {
        return $this->hasMany(MaintenancePhoto::class)->where('type', 'after');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'perbaikan' => 'Perbaikan',
            'perawatan' => 'Perawatan',
            'penggantian_part' => 'Penggantian Part',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'in_progress' => 'Dalam Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
