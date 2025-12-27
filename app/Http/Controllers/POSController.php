<?php

namespace App\Http\Controllers;

use App\Events\InventoryUpdated;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class POSController extends Controller
{
    public function index()
    {
        return Inertia::render('POS/Index');
    }

    public function catalog(Request $request)
    {
        $user = $request->user();
        
        $storeId = $user->store_id;
        
        // Allow Super Admin to operate as first store if not assigned
        if (!$storeId && $user->role === \App\Enums\UserRole::SUPER_ADMIN) {
            $firstStore = \App\Models\Store::first();
            if ($firstStore) {
                $storeId = $firstStore->id;
            }
        }

        if (!$storeId) {
            return response()->json(['error' => 'User not assigned to a store'], 403);
        }

        // Fetch inventories for this store
        $inventories = Inventory::with(['product.category', 'product.brand'])
            ->where('store_id', $storeId)
            ->get()
            ->map(function ($inventory) {
                $product = $inventory->product;
                if (!$product) return null;

                return [
                    'id' => $product->id,
                    'inventory_id' => $inventory->id,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'name' => $product->name,
                    'price' => (float) $inventory->selling_price,
                    'quantity' => $inventory->quantity,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'image_url' => $product->image_url,
                    'requires_age_verification' => $product->requires_age_verification,
                    'tax_rate' => 0.10, // TODO: Get from store settings
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'products' => $inventories,
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'amount_paid' => 'required|numeric',
        ]);

        $user = $request->user();
        $storeId = $user->store_id;

        // Allow Super Admin to operate as first store if not assigned
        if (!$storeId && $user->role === \App\Enums\UserRole::SUPER_ADMIN) {
            $firstStore = \App\Models\Store::first();
            if ($firstStore) {
                $storeId = $firstStore->id;
            }
        }

        if (!$storeId) {
             return response()->json(['error' => 'User not assigned to a store'], 403);
        }

        return DB::transaction(function () use ($request, $user, $storeId, $inventoryService) {
            // Create Sale
            $sale = Sale::create([
                'store_id' => $storeId,
                'transaction_number' => 'TRX-' . strtoupper(uniqid()),
                'cashier_id' => $user->id,
                'sale_date' => now(),
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'change_amount' => 0,
                'status' => 'completed',
            ]);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($request->items as $item) {
                $inventory = Inventory::where('store_id', $storeId)
                    ->where('product_id', $item['id'])
                    ->firstOrFail();

                // Check stock (Allow negative for now to prevent blocking sales)
                // Use Service to decrement stock and log movement
                $inventoryService->decrement(
                    $inventory, 
                    $item['quantity'], 
                    "Sale: {$sale->transaction_number}", 
                    'sale', 
                    $user
                );
                
                $price = $inventory->selling_price;
                $cost = $inventory->cost_price;
                $lineTotal = $price * $item['quantity'];
                $tax = $lineTotal * 0.10; // 10% tax hardcoded for MVP
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'subtotal' => $lineTotal,
                    'tax' => $tax,
                    'total' => $lineTotal + $tax,
                    'cost_price' => $cost,
                ]);

                $subtotal += $lineTotal;
                $taxTotal += $tax;
            }

            $total = $subtotal + $taxTotal;
            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $total,
                'change_amount' => $request->amount_paid - $total,
            ]);

            return response()->json(['success' => true, 'sale' => $sale]);
        });
    }
}
