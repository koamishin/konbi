<?php

namespace App\Models\Scopes;

use App\Services\ContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GlobalOrStoreScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Prevent circular dependency when loading User model
        if ($model instanceof \App\Models\User) {
            return;
        }

        $storeId = ContextService::getStoreId();

        if ($storeId) {
            $builder->where(function ($query) use ($storeId) {
                $query->where('store_id', $storeId)
                      ->orWhereNull('store_id');
            });
        }
    }
}
