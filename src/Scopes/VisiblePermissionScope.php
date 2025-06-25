<?php

namespace LechugaNegra\AccessManager\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisiblePermissionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('hidden', false);
    }
}
