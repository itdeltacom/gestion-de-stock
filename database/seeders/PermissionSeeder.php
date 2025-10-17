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

        // Définir toutes les permissions
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

            // Delivery Notes
            'delivery-note-view',
            'delivery-note-create',
            'delivery-note-edit',
            'delivery-note-delete',

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

        // Créer uniquement les permissions qui n'existent pas
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Récupérer ou créer les rôles et assigner les permissions

        // Super Admin - TOUJOURS toutes les permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin - Presque toutes les permissions sauf gestion des utilisateurs
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $adminPermissions = [
            'dashboard-view',
            'warehouse-view',
            'warehouse-create',
            'warehouse-edit',
            'category-view',
            'category-create',
            'category-edit',
            'product-view',
            'product-create',
            'product-edit',
            'supplier-view',
            'supplier-create',
            'supplier-edit',
            'customer-view',
            'customer-create',
            'customer-edit',
            'customer-increase-credit',
            'purchase-view',
            'purchase-create',
            'purchase-edit',
            'purchase-receive',
            'sale-view',
            'sale-create',
            'sale-edit',
            'sale-validate',
            'sale-convert',
            'sale-payment',
            'transfer-view',
            'transfer-create',
            'transfer-edit',
            'transfer-send',
            'transfer-receive',
            'delivery-note-view',
            'delivery-note-create',
            'delivery-note-edit',
            'delivery-note-delete',
            'pos-access',
            'report-view',
        ];
        $admin->syncPermissions($adminPermissions);

        // Manager - Gestion quotidienne
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $managerPermissions = [
            'dashboard-view',
            'warehouse-view',
            'category-view',
            'product-view',
            'product-create',
            'product-edit',
            'supplier-view',
            'supplier-create',
            'supplier-edit',
            'customer-view',
            'customer-create',
            'customer-edit',
            'purchase-view',
            'purchase-create',
            'purchase-edit',
            'purchase-receive',
            'sale-view',
            'sale-create',
            'sale-edit',
            'sale-validate',
            'sale-payment',
            'transfer-view',
            'transfer-create',
            'transfer-send',
            'transfer-receive',
            'delivery-note-view',
            'delivery-note-create',
            'delivery-note-edit',
            'report-view',
        ];
        $manager->syncPermissions($managerPermissions);

        // Vendeur - Ventes et POS
        $vendeur = Role::firstOrCreate(['name' => 'Vendeur']);
        $vendeurPermissions = [
            'dashboard-view',
            'product-view',
            'customer-view',
            'customer-create',
            'sale-view',
            'sale-create',
            'sale-payment',
            'delivery-note-view',
            'pos-access',
        ];
        $vendeur->syncPermissions($vendeurPermissions);

        // Magasinier - Gestion du stock
        $magasinier = Role::firstOrCreate(['name' => 'Magasinier']);
        $magasinierPermissions = [
            'dashboard-view',
            'warehouse-view',
            'product-view',
            'purchase-view',
            'purchase-receive',
            'transfer-view',
            'transfer-create',
            'transfer-send',
            'transfer-receive',
            'delivery-note-view',
            'delivery-note-create',
            'delivery-note-edit',
            'report-view',
        ];
        $magasinier->syncPermissions($magasinierPermissions);

        $this->command->info('✅ Permissions et rôles créés/mis à jour avec succès!');
        $this->command->info('✅ Super Admin a maintenant TOUTES les permissions');
    }
}