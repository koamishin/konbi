<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS Routes
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/sales', [POSController::class, 'store'])->name('pos.store');
    Route::get('/pos/products', [POSController::class, 'catalog'])->name('pos.catalog'); // For offline sync

    // Resource Routes
    Route::resource('stores', StoreController::class);
    Route::resource('products', ProductController::class);
    Route::get('inventory/{inventory}/history', [InventoryController::class, 'history'])->name('inventory.history');
    Route::resource('inventory', InventoryController::class);
    
    // Store Context Switcher
    Route::post('/store-selection', [App\Http\Controllers\StoreSelectionController::class, 'update'])->name('store-selection.update');
});

require __DIR__.'/settings.php';
