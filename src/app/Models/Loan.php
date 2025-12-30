<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'item_id',
        'borrower_name',
        'borrower_phone',
        'quantity',
        'borrowed_at',
        'due_at',
        'returned_at',
        'returned_condition',
        'notes',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_at' => 'date',
        'returned_at' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    public function isOverdue(): bool
    {
        if ($this->isReturned() || !$this->due_at) {
            return false;
        }
        return $this->due_at->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->isReturned()) {
            return 'Sudah Kembali';
        }
        if ($this->isOverdue()) {
            return 'Terlambat';
        }
        return 'Dipinjam';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->isReturned()) {
            return 'green';
        }
        if ($this->isOverdue()) {
            return 'red';
        }
        return 'yellow';
    }
}
