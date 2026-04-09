<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MaintenancePhoto extends Model
{
    use \App\Traits\BelongsToMasjid;

    protected $fillable = [
        'maintenance_id',
        'filename',
        'original_name',
        'type',
        'caption',
        'uploaded_by',
        'masjid_id',
    ];

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url('maintenance-photos/' . $this->filename);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'before' => 'Sebelum',
            'progress' => 'Proses',
            'after' => 'Sesudah',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'before' => 'red',
            'progress' => 'yellow',
            'after' => 'green',
            default => 'gray',
        };
    }

    public static function getTypes(): array
    {
        return [
            'before' => 'Sebelum',
            'progress' => 'Proses',
            'after' => 'Sesudah',
        ];
    }
}
