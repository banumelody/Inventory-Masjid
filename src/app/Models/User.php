<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements CanResetPassword
{
    use CanResetPasswordTrait, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_superadmin',
        'masjid_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_superadmin' => 'boolean',
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
        return $this->isAdmin();
    }

    public function canDeleteItems(): bool
    {
        return $this->isAdmin();
    }

    public function canManageBackups(): bool
    {
        return $this->isAdmin();
    }

    public function canEditItems(): bool
    {
        return $this->isAdmin() || $this->isOperator();
    }

    public function canManageLoans(): bool
    {
        return $this->isAdmin() || $this->isOperator();
    }

    public function canManageStock(): bool
    {
        return $this->isAdmin() || $this->isOperator();
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
