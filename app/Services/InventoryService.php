<?php

namespace App\Services;

use App\Events\InventoryUpdated;
use App\Models\Inventory;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Increment inventory quantity.
     */
    public function increment(Inventory $inventory, int $quantity, string $reason, string $type = 'adjustment', ?User $user = null): Inventory
    {
        return $this->adjust($inventory, $quantity, $reason, $type, $user);
    }

    /**
     * Decrement inventory quantity.
     */
    public function decrement(Inventory $inventory, int $quantity, string $reason, string $type = 'adjustment', ?User $user = null): Inventory
    {
        return $this->adjust($inventory, -$quantity, $reason, $type, $user);
    }

    /**
     * Set absolute inventory quantity.
     */
    public function set(Inventory $inventory, int $quantity, string $reason, string $type = 'correction', ?User $user = null): Inventory
    {
        $diff = $quantity - $inventory->quantity;
        
        if ($diff == 0) {
            return $inventory;
        }

        return $this->adjust($inventory, $diff, $reason, $type, $user);
    }

    /**
     * Core adjustment logic.
     */
    protected function adjust(Inventory $inventory, int $change, string $reason, string $type, ?User $user = null): Inventory
    {
        return DB::transaction(function () use ($inventory, $change, $reason, $type, $user) {
            $inventory->quantity += $change;
            $inventory->save();

            // Record adjustment
            StockAdjustment::create([
                'store_id' => $inventory->store_id,
                'product_id' => $inventory->product_id,
                'user_id' => $user ? $user->id : (Auth::check() ? Auth::id() : null),
                'type' => $type,
                'quantity' => $change,
                'reason' => $reason, 
            ]);

            // Fire event for auto-reordering etc
            InventoryUpdated::dispatch($inventory);

            return $inventory;
        });
    }
}
