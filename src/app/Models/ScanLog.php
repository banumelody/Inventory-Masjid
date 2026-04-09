<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanLog extends Model
{
    use \App\Traits\BelongsToMasjid;

    protected $fillable = [
        'item_id',
        'user_id',
        'scanned_at',
        'purpose',
        'notes',
        'ip_address',
        'masjid_id',
    ];

    /**
     * Available scan purposes
     */
    public const PURPOSES = [
        'audit' => 'Audit Inventaris',
        'check' => 'Pengecekan Barang',
        'maintenance' => 'Cek Maintenance',
        'other' => 'Lainnya',
    ];

    /**
     * Get purpose label
     */
    public function getPurposeLabelAttribute(): string
    {
        return self::PURPOSES[$this->purpose] ?? $this->purpose ?? '-';
    }

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
