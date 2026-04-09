<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MasjidScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($masjidId = app()->bound('current_masjid_id') ? app('current_masjid_id') : null) {
            $builder->where($model->getTable() . '.masjid_id', $masjidId);
        }
    }
}
