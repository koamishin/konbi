<?php

namespace App\Models\Scopes;

use App\Services\ContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $storeId = ContextService::getStoreId();
        
        if ($storeId) {
            $builder->where($model->getTable() . '.store_id', $storeId);
        }
    }
}
