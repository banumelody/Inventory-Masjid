<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getModelNameAttribute(): string
    {
        if (!$this->model_type) {
            return '-';
        }
        
        $map = [
            'App\Models\Item' => 'Barang',
            'App\Models\Loan' => 'Peminjaman',
            'App\Models\Category' => 'Kategori',
            'App\Models\Location' => 'Lokasi',
            'App\Models\User' => 'Pengguna',
            'App\Models\StockMovement' => 'Mutasi Stok',
            'App\Models\Maintenance' => 'Maintenance',
            'App\Models\Backup' => 'Backup',
        ];

        return $map[$this->model_type] ?? class_basename($this->model_type);
    }

    public function getActionLabelAttribute(): string
    {
        $map = [
            'create' => 'Tambah',
            'update' => 'Update',
            'delete' => 'Hapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'import' => 'Import',
            'export' => 'Export',
            'return' => 'Pengembalian',
            'restore' => 'Restore',
        ];

        return $map[$this->action] ?? ucfirst($this->action);
    }

    public function getActionColorAttribute(): string
    {
        $map = [
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'login' => 'purple',
            'logout' => 'gray',
            'import' => 'yellow',
            'export' => 'indigo',
            'return' => 'teal',
        ];

        return $map[$this->action] ?? 'gray';
    }

    /**
     * Get the related model instance
     */
    public function subject()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        return $this->model_type::find($this->model_id);
    }

    /**
     * Static helper to log activity
     */
    public static function log(
        string $action,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
