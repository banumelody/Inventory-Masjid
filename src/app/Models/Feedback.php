<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToMasjid;

class Feedback extends Model
{
    use BelongsToMasjid;

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'module',
        'type',
        'message',
        'status',
        'admin_notes',
        'masjid_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'bug' => 'Bug / Masalah',
            'suggestion' => 'Saran',
            'question' => 'Pertanyaan',
            'other' => 'Lainnya',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new' => 'Baru',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'yellow',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }
}
