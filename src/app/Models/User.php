<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements CanResetPassword
{
    use CanResetPasswordTrait, HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_superadmin',
        'masjid_id',
        'dashboard_widgets',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_superadmin' => 'boolean',
        'dashboard_widgets' => 'array',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function masjid(): BelongsTo
    {
        return $this->belongsTo(Masjid::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    public const DEFAULT_WIDGETS = [
        'stats_overview' => true,
        'charts' => true,
        'most_borrowed' => true,
        'condition_summary' => true,
        'overdue_loans' => true,
        'recent_items' => true,
        'recent_movements' => true,
        'items_by_category' => true,
        'items_by_location' => true,
        'recent_scans' => true,
        'quick_actions' => true,
    ];

    public function getWidgetPreferences(): array
    {
        return array_merge(self::DEFAULT_WIDGETS, $this->dashboard_widgets ?? []);
    }

    public function isWidgetEnabled(string $widget): bool
    {
        $prefs = $this->getWidgetPreferences();
        return $prefs[$widget] ?? true;
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role->name === 'operator';
    }

    public function isViewer(): bool
    {
        return $this->role->name === 'viewer';
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canDeleteItems(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canManageBackups(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canEditItems(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isOperator();
    }

    public function canManageLoans(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isOperator();
    }

    public function canManageStock(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isOperator();
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
