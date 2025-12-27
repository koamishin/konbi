<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ContextService
{
    public static function getStoreId()
    {
        if (!Auth::check()) {
            return null;
        }

        // 1. Check Session
        if (Session::has('active_store_id')) {
            return Session::get('active_store_id');
        }

        // 2. Check User's Default Store
        if (Auth::user()->store_id) {
            return Auth::user()->store_id;
        }

        return null;
    }

    public static function getActiveStore()
    {
        $id = self::getStoreId();
        if ($id) {
            return Store::find($id);
        }
        
        return null;
    }
}
