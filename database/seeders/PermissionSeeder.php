<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Dashboard
            'dashboard-view',
            
            // Warehouses
            'warehouse-view',
            'warehouse-create',
            'warehouse-edit',
            'warehouse-delete',
            
            // Categories
            'category-view',
            'category-create',
            'category-edit',
            'category-delete',
            
            // Products
            'product-view',
            'product-create',
            'product-edit',
            'product-delete',
            
            // Suppliers
            'supplier-view',
            'supplier-create',
            'supplier-edit',
            'supplier-delete',
            
            // Customers
            'customer-view',
            'customer-create',
            'customer-edit',
            'customer-delete',
            'customer-increase-credit',
            
            // Purchases
            'purchase-view',
            'purchase-create',
            'purchase-edit',
            'purchase-delete',
            'purchase-receive',
            
            // Sales
            'sale-view',
            'sale-create',
            'sale-edit',
            'sale-delete',
            'sale-validate',
            'sale-convert',
            'sale-payment',
            
            // Stock Transfers
            'transfer-view',
            'transfer-create',
            'transfer-edit',
            'transfer-delete',
            'transfer-send',
            'transfer-receive',
            
            // POS
            'pos-access',
            
            // Reports
            'report-view',
            
            // Users & Roles (pour l'admin)
            'user-view',
            'user-create',
            'user-edit',
            'user-delete',
            'role-view',
            'role-create',
            'role-edit',
            'role-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles
        
        // Super Admin - Toutes les permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Presque toutes les permissions sauf gestion des utilisateurs
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'dashboard-view',
            'warehouse-view', 'warehouse-create', 'warehouse-edit',
            'category-view', 'category-create', 'category-edit',
            'product-view', 'product-create', 'product-edit',
            'supplier-view', 'supplier-create', 'supplier-edit',
            'customer-view', 'customer-create', 'customer-edit', 'customer-increase-credit',
            'purchase-view', 'purchase-create', 'purchase-edit', 'purchase-receive',
            'sale-view', 'sale-create', 'sale-edit', 'sale-validate', 'sale-convert', 'sale-payment',
            'transfer-view', 'transfer-create', 'transfer-edit', 'transfer-send', 'transfer-receive',
            'pos-access',
            'report-view',
        ]);

        // Manager - Gestion quotidienne
        $manager = Role::create(['name' => 'Manager']);
        $manager->givePermissionTo([
            'dashboard-view',
            'warehouse-view',
            'category-view',
            'product-view', 'product-create', 'product-edit',
            'supplier-view', 'supplier-create', 'supplier-edit',
            'customer-view', 'customer-create', 'customer-edit',
            'purchase-view', 'purchase-create', 'purchase-edit', 'purchase-receive',
            'sale-view', 'sale-create', 'sale-edit', 'sale-validate', 'sale-payment',
            'transfer-view', 'transfer-create', 'transfer-send', 'transfer-receive',
            'report-view',
        ]);

        // Vendeur - Ventes et POS
        $vendeur = Role::create(['name' => 'Vendeur']);
        $vendeur->givePermissionTo([
            'dashboard-view',
            'product-view',
            'customer-view', 'customer-create',
            'sale-view', 'sale-create', 'sale-payment',
            'pos-access',
        ]);

        // Magasinier - Gestion du stock
        $magasinier = Role::create(['name' => 'Magasinier']);
        $magasinier->givePermissionTo([
            'dashboard-view',
            'warehouse-view',
            'product-view',
            'purchase-view', 'purchase-receive',
            'transfer-view', 'transfer-create', 'transfer-send', 'transfer-receive',
            'report-view',
        ]);
    }
}