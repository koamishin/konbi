<?php

use App\Events\InventoryUpdated;
use App\Listeners\CheckStockLevel;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('inventory update event is dispatched on sale', function () {
    Event::fake();

    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    $product = Product::factory()->create(['store_id' => $store->id]);
    $inventory = Inventory::factory()->create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $this->actingAs($user)->postJson(route('pos.store'), [
        'payment_method' => 'cash',
        'amount_paid' => 100,
        'items' => [
            ['id' => $product->id, 'quantity' => 1]
        ]
    ]);

    Event::assertDispatched(InventoryUpdated::class);
});

test('auto reorder creates draft po when stock is low', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    $product = Product::factory()->create(['store_id' => $store->id]);
    
    $inventory = Inventory::factory()->create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 6,
        'reorder_level' => 5,
        'reorder_quantity' => 50,
        'cost_price' => 10,
    ]);

    $this->actingAs($user)->postJson(route('pos.store'), [
        'payment_method' => 'cash',
        'amount_paid' => 100,
        'items' => [
            ['id' => $product->id, 'quantity' => 2]
        ]
    ]);

    $this->assertDatabaseHas('purchase_orders', [
        'store_id' => $store->id,
        'status' => 'draft',
    ]);
});

test('stock movement is logged on sale', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    $product = Product::factory()->create(['store_id' => $store->id]);
    $inventory = Inventory::factory()->create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $this->actingAs($user)->postJson(route('pos.store'), [
        'payment_method' => 'cash',
        'amount_paid' => 100,
        'items' => [
            ['id' => $product->id, 'quantity' => 2]
        ]
    ]);

    $this->assertDatabaseHas('stock_adjustments', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'type' => 'sale',
        'quantity' => -2, // Decrement
        'user_id' => $user->id,
    ]);
});
