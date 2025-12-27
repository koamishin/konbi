<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Default Store
        $store = Store::create([
            'name' => 'Konbi Main Store',
            'code' => 'STORE-001',
            'address' => '123 Konbi St, Tech City',
            'phone' => '123-456-7890',
            'email' => 'store1@konbi.test',
            'settings' => ['tax_rate' => 0.10],
            'is_active' => true,
        ]);

        // Create Super Admin (Global)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@konbi.test',
            'password' => Hash::make('password'),
            'role' => UserRole::SUPER_ADMIN,
            'store_id' => null,
            'is_active' => true,
        ]);

        // Create Store Manager
        User::create([
            'name' => 'Store Manager',
            'email' => 'manager@konbi.test',
            'password' => Hash::make('password'),
            'role' => UserRole::STORE_MANAGER,
            'store_id' => $store->id,
            'is_active' => true,
        ]);

        // Create Cashier
        User::create([
            'name' => 'Cashier 1',
            'email' => 'cashier@konbi.test',
            'password' => Hash::make('password'),
            'pin_code' => Hash::make('1234'),
            'role' => UserRole::CASHIER,
            'store_id' => $store->id,
            'is_active' => true,
        ]);

        // Seed some categories
        $drinks = Category::create(['name' => 'Drinks', 'slug' => 'drinks']);
        $snacks = Category::create(['name' => 'Snacks', 'slug' => 'snacks']);

        // Seed some products (Global catalog)
        $coke = Product::create([
            'category_id' => $drinks->id,
            'name' => 'Coca Cola 330ml',
            'sku' => 'COKE-330',
            'barcode' => '5449000131805',
            'description' => 'Classic Coke',
            'is_active' => true,
        ]);

        $chips = Product::create([
            'category_id' => $snacks->id,
            'name' => 'Potato Chips',
            'sku' => 'CHIPS-001',
            'barcode' => '123456789',
            'description' => 'Salty Chips',
            'is_active' => true,
        ]);

        // Add Inventory to Store
        Inventory::create([
            'store_id' => $store->id,
            'product_id' => $coke->id,
            'quantity' => 100,
            'cost_price' => 0.50,
            'selling_price' => 1.50,
            'reorder_level' => 20,
        ]);

        Inventory::create([
            'store_id' => $store->id,
            'product_id' => $chips->id,
            'quantity' => 50,
            'cost_price' => 1.00,
            'selling_price' => 2.50,
            'reorder_level' => 10,
        ]);
    }
}
