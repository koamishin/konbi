<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $storeId = $user->store_id;

        $stats = [
            'today_sales' => 0,
            'today_transactions' => 0,
            'low_stock_count' => 0,
            'total_products' => 0,
        ];
        
        if ($storeId) {
            $stats['today_sales'] = Sale::where('store_id', $storeId)
                ->whereDate('sale_date', today())
                ->sum('total_amount');

            $stats['today_transactions'] = Sale::where('store_id', $storeId)
                ->whereDate('sale_date', today())
                ->count();
                
            $stats['low_stock_count'] = Inventory::where('store_id', $storeId)
                ->whereColumn('quantity', '<=', 'reorder_level')
                ->count();

            $stats['total_products'] = Inventory::where('store_id', $storeId)->count();
        } else {
            // Super Admin View (Global Stats)
            $stats['today_sales'] = Sale::whereDate('sale_date', today())->sum('total_amount');
            $stats['today_transactions'] = Sale::whereDate('sale_date', today())->count();
            // $stats['total_stores'] = \App\Models\Store::count(); 
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
