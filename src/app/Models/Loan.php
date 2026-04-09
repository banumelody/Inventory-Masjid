<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToMasjid;

class Loan extends Model
{
    use BelongsToMasjid;

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
        'return_qr_key',
        'masjid_id',
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

    /**
     * Check if loan has QR code for return
     */
    public function hasReturnQrCode(): bool
    {
        return !empty($this->return_qr_key);
    }

    /**
     * Generate QR code key for return
     */
    public function generateReturnQrKey(): string
    {
        $this->return_qr_key = bin2hex(random_bytes(12));
        $this->save();
        return $this->return_qr_key;
    }

    /**
     * Get return QR code URL
     */
    public function getReturnQrUrlAttribute(): ?string
    {
        if (!$this->hasReturnQrCode()) {
            return null;
        }
        return url('/loans/return-scan/' . $this->return_qr_key);
    }
}
