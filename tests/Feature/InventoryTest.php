<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

test('can update inventory', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    $product = Product::factory()->create(['store_id' => $store->id]);
    
    $inventory = Inventory::factory()->create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $response = $this->actingAs($user)->put(route('inventory.update', $inventory), [
        'quantity' => 20,
        'reorder_level' => 5,
        'reorder_quantity' => 50,
        'cost_price' => 10,
        'selling_price' => 15,
    ]);

    $response->assertRedirect(route('inventory.index'));
    
    $inventory->refresh();
    expect($inventory->quantity)->toBe(20);
});

test('cannot update other store inventory', function () {
    $store1 = Store::factory()->create();
    $store2 = Store::factory()->create();
    
    $user1 = User::factory()->create(['store_id' => $store1->id]);
    
    $product = Product::factory()->create(['store_id' => $store2->id]);
    $inventory2 = Inventory::factory()->create([
        'store_id' => $store2->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $response = $this->actingAs($user1)->put(route('inventory.update', $inventory2), [
        'quantity' => 20,
        // ... other required fields
        'reorder_level' => 5,
        'reorder_quantity' => 50,
        'cost_price' => 10,
        'selling_price' => 15,
    ]);

    // Should be 404 because scope hides it, or 403. 
    // Laravel Route Model Binding with global scope usually returns 404 if not found in scope.
    $response->assertStatus(404);
});
