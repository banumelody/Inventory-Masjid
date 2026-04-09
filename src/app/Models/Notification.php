<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'masjid_id',
        'type',
        'title',
        'message',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function masjid(): BelongsTo
    {
        return $this->belongsTo(Masjid::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Create a notification for a specific user
     */
    public static function notify(int $userId, string $type, string $title, string $message, ?string $link = null, ?int $masjidId = null): self
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'masjid_id' => $masjidId ?? (app()->bound('current_masjid_id') ? app('current_masjid_id') : null),
        ]);
    }

    /**
     * Notify all admins of a masjid
     */
    public static function notifyMasjidAdmins(int $masjidId, string $type, string $title, string $message, ?string $link = null): void
    {
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $admins = User::where('masjid_id', $masjidId)
            ->where('role_id', $adminRoleId)
            ->pluck('id');

        foreach ($admins as $userId) {
            static::notify($userId, $type, $title, $message, $link, $masjidId);
        }
    }
}
