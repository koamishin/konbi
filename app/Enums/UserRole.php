<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case STORE_MANAGER = 'store_manager';
    case CASHIER = 'cashier';
    case INVENTORY_MANAGER = 'inventory_manager';
    case ACCOUNTANT = 'accountant';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::STORE_MANAGER => 'Store Manager',
            self::CASHIER => 'Cashier',
            self::INVENTORY_MANAGER => 'Inventory Manager',
            self::ACCOUNTANT => 'Accountant',
            self::VIEWER => 'Viewer',
        };
    }
}
