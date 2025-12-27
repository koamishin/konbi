<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        // Automatically scoped to store by BelongsToStore trait if user has store_id
        $inventory = Inventory::with(['product'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Inventory/Index', [
            'inventory' => $inventory
        ]);
    }

    public function history(Inventory $inventory)
    {
        $history = StockAdjustment::where('store_id', $inventory->store_id)
            ->where('product_id', $inventory->product_id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('Inventory/History', [
            'inventory' => $inventory->load('product'),
            'history' => $history,
        ]);
    }

    public function edit(Inventory $inventory)
    {
        return Inertia::render('Inventory/Edit', [
            'inventory' => $inventory->load('product'),
        ]);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory, InventoryService $inventoryService)
    {
        $data = $request->validated();
        
        // Handle Quantity Change via Service (Logs movement)
        if (isset($data['quantity']) && $data['quantity'] != $inventory->quantity) {
            $inventoryService->set(
                $inventory, 
                $data['quantity'], 
                'Manual Adjustment', 
                'adjustment',
                $request->user()
            );
        }

        // Update other fields
        $otherFields = collect($data)->except('quantity')->toArray();
        if (!empty($otherFields)) {
            $inventory->update($otherFields);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory updated successfully.');
    }
}
