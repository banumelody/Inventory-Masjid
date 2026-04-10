<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToMasjid;

class Category extends Model
{
    use BelongsToMasjid, SoftDeletes;

    protected $fillable = ['name', 'masjid_id'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
