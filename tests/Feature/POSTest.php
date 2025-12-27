<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

test('pos transaction creates sale and deducts inventory', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    $product = Product::factory()->create(['store_id' => $store->id]);
    
    $inventory = Inventory::factory()->create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'quantity' => 100,
        'selling_price' => 10.00,
    ]);

    $response = $this->actingAs($user)->postJson(route('pos.store'), [
        'payment_method' => 'cash',
        'amount_paid' => 20.00,
        'items' => [
            [
                'id' => $product->id,
                'quantity' => 2,
            ]
        ]
    ]);

    $response->assertStatus(200);
    
    $inventory->refresh();
    // 100 - 2 = 98
    expect($inventory->quantity)->toBe(98);
    
    $this->assertDatabaseHas('sales', [
        'store_id' => $store->id,
        'total_amount' => 22.00, // 2 * 10 = 20 + 10% tax = 22
    ]);
    
    $this->assertDatabaseHas('sale_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'total' => 22.00,
    ]);
});

test('pos catalog returns correct products', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    $product = Product::factory()->create(['store_id' => $store->id]);
    Inventory::factory()->create([
        'store_id' => $store->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user)->getJson(route('pos.catalog'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'products',
            'categories'
        ]);
        
    // Check that product is in the list
    $products = $response->json('products');
    expect(collect($products)->pluck('id'))->toContain($product->id);
});
