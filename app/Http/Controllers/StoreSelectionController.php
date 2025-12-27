<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreSelectionController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'store_id' => 'nullable|exists:stores,id',
        ]);

        $storeId = $request->input('store_id');

        if ($storeId) {
            Session::put('active_store_id', $storeId);
        } else {
            Session::forget('active_store_id');
        }

        return back();
    }
}
