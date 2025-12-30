<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
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
}
