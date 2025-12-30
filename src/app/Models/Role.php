<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'display_name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->name === 'operator';
    }

    public function isViewer(): bool
    {
        return $this->name === 'viewer';
    }
}
