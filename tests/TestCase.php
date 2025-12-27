<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create a user with a store to bypass the EnsureUserHasStore middleware.
     */
    protected function createUserWithStore(array $userAttributes = [], array $storeAttributes = []): User
    {
        $store = Store::factory()->create($storeAttributes);

        return User::factory()->create(array_merge([
            'store_id' => $store->id,
            'role' => UserRole::STORE_MANAGER,
        ], $userAttributes));
    }

    /**
     * Create a super admin user (exempt from store requirement).
     */
    protected function createSuperAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => UserRole::SUPER_ADMIN,
            'store_id' => null,
        ], $attributes));
    }
}
