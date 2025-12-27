<?php

namespace App\Listeners;

use App\Events\InventoryUpdated;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckStockLevel
{
    /**
     * Handle the event.
     */
    public function handle(InventoryUpdated $event): void
    {
        $inventory = $event->inventory;

        // 1. Check if stock is low
        if ($inventory->quantity <= $inventory->reorder_level) {
            $storeId = $inventory->store_id;
            
            // Ensure a supplier exists (Fallback)
            $supplier = Supplier::firstOrCreate(
                ['store_id' => $storeId, 'name' => 'System Auto Supplier'],
                ['email' => 'auto@system.local', 'is_active' => true]
            );

            // Find or Create a Draft PO for "Auto Reorder"
            $po = PurchaseOrder::firstOrCreate(
                [
                    'store_id' => $storeId,
                    'status' => 'draft',
                    'supplier_id' => $supplier->id, // Group by supplier in real app, here simplified
                ],
                [
                    'po_number' => 'PO-AUTO-' . strtoupper(uniqid()),
                    'order_date' => now(),
                    'notes' => 'Auto-generated from low stock alerts',
                ]
            );

            // Add or Update Item in PO
            $existingItem = $po->items()->where('product_id', $inventory->product_id)->first();

            if (!$existingItem) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $inventory->product_id,
                    'quantity_ordered' => $inventory->reorder_quantity,
                    'unit_cost' => $inventory->cost_price,
                    'total_cost' => $inventory->cost_price * $inventory->reorder_quantity,
                ]);
                
                // Update PO Total
                $po->increment('total_amount', $inventory->cost_price * $inventory->reorder_quantity);
            }
            
            Log::info("Auto-reorder trigger for Product ID: {$inventory->product_id} at Store: {$storeId}");
        }
    }
}
