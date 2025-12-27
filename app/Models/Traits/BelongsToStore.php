<?php

namespace App\Models\Traits;

use App\Models\Scopes\StoreScope;
use App\Models\Store;
use App\Services\ContextService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToStore
{
    protected static function bootBelongsToStore()
    {
        static::addGlobalScope(new StoreScope);

        static::creating(function ($model) {
            $storeId = ContextService::getStoreId();
            if ($storeId && !$model->store_id) {
                $model->store_id = $storeId;
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
