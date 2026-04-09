<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToMasjid;

class Category extends Model
{
    use BelongsToMasjid;

    protected $fillable = ['name', 'masjid_id'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
