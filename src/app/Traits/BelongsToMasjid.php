<?php

namespace App\Traits;

use App\Models\Masjid;
use App\Scopes\MasjidScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToMasjid
{
    protected static function bootBelongsToMasjid(): void
    {
        static::addGlobalScope(new MasjidScope);

        static::creating(function ($model) {
            if (!$model->masjid_id) {
                $masjidId = app()->bound('current_masjid_id') ? app('current_masjid_id') : null;
                if ($masjidId) {
                    $model->masjid_id = $masjidId;
                }
            }
        });
    }

    public function masjid(): BelongsTo
    {
        return $this->belongsTo(Masjid::class);
    }
}
