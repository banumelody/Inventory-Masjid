<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'location_id',
        'quantity',
        'unit',
        'condition',
        'note',
        'photo_path',
        'qr_code_key',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(Loan::class)->whereNull('returned_at');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function activeMaintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class)->whereIn('status', ['pending', 'in_progress']);
    }

    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'baik' => 'Baik',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'rusak' => 'Rusak',
            default => $this->condition,
        };
    }

    public function getBorrowedQuantityAttribute(): int
    {
        return $this->activeLoans()->sum('quantity');
    }

    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->borrowed_quantity;
    }

    public function hasPhoto(): bool
    {
        return !empty($this->photo_path);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->hasPhoto()) {
            return null;
        }
        return asset('storage/' . $this->photo_path);
    }

    public function scanLogs(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }

    public function hasQrCode(): bool
    {
        return !empty($this->qr_code_key);
    }

    public function generateQrCodeKey(): string
    {
        $this->qr_code_key = bin2hex(random_bytes(12));
        $this->save();
        return $this->qr_code_key;
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        if (!$this->hasQrCode()) {
            return null;
        }
        return url('/i/' . $this->qr_code_key);
    }

    public function hasActiveMaintenance(): bool
    {
        return $this->activeMaintenances()->exists();
    }

    public function getActiveMaintenanceCountAttribute(): int
    {
        return $this->activeMaintenances()->count();
    }
}
