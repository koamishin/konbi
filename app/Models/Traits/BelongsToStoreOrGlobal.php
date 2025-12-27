<?php

namespace App\Models\Traits;

use App\Models\Scopes\GlobalOrStoreScope;
use App\Models\Store;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToStoreOrGlobal
{
    protected static function bootBelongsToStoreOrGlobal()
    {
        static::addGlobalScope(new GlobalOrStoreScope);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    
    public function scopeGlobal($query)
    {
        return $query->whereNull('store_id');
    }
    
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}
