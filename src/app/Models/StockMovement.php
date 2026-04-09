<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToMasjid;

class StockMovement extends Model
{
    use BelongsToMasjid;

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'reason',
        'moved_at',
        'notes',
        'masjid_id',
    ];

    protected $casts = [
        'moved_at' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === 'in' ? 'green' : 'red';
    }
}
