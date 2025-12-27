<?php

use App\Enums\UserRole;
use App\Models\Store;
use App\Models\User;

test('admin can view stores', function () {
    $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
    
    $response = $this->actingAs($user)->get(route('stores.index'));

    $response->assertStatus(200);
});

test('admin can create store', function () {
    $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

    $response = $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'New Store',
        'code' => 'NS-001',
        'email' => 'store@test.com',
    ]);

    $response->assertRedirect(route('stores.index'));
    
    $this->assertDatabaseHas('stores', [
        'name' => 'New Store',
        'code' => 'NS-001',
    ]);
});

test('admin can update store', function () {
    $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
    $store = Store::factory()->create();

    $response = $this->actingAs($user)->put(route('stores.update', $store), [
        'name' => 'Updated Store',
        'code' => $store->code,
    ]);

    $response->assertRedirect(route('stores.index'));
    
    $this->assertDatabaseHas('stores', [
        'id' => $store->id,
        'name' => 'Updated Store',
    ]);
});
