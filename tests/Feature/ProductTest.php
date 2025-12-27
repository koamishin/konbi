<?php

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

test('can list products', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);
    Product::factory()->create();

    $response = $this->actingAs($user)->get(route('products.index'));

    $response->assertStatus(200);
});

test('can create global product', function () {
    // Only Super Admin can do this effectively now as they bypass store check
    $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Global Product',
        'sku' => 'GP-001',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('products.index'));
    
    $this->assertDatabaseHas('products', [
        'name' => 'Global Product',
        'store_id' => null,
    ]);
});

test('store manager creates product scoped to store', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Store Product',
        'sku' => 'SP-001',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('products.index'));
    
    $this->assertDatabaseHas('products', [
        'name' => 'Store Product',
        'store_id' => $store->id,
    ]);
});

test('store manager sees global and own products but not others', function () {
    $store1 = Store::factory()->create();
    $store2 = Store::factory()->create();

    $globalProduct = Product::factory()->create(['store_id' => null, 'name' => 'Global']);
    $store1Product = Product::factory()->create(['store_id' => $store1->id, 'name' => 'Store 1']);
    $store2Product = Product::factory()->create(['store_id' => $store2->id, 'name' => 'Store 2']);

    $user1 = User::factory()->create(['store_id' => $store1->id]);

    // We need to inspect the data passed to the view, or just query via model acting as user
    // Since we are testing the Controller/Model scope integration:
    
    $this->actingAs($user1);
    
    // Test the scope directly
    $visibleProducts = Product::all();
    
    expect($visibleProducts->contains($globalProduct))->toBeTrue();
    expect($visibleProducts->contains($store1Product))->toBeTrue();
    expect($visibleProducts->contains($store2Product))->toBeFalse();
});
