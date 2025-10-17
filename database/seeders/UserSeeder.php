<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer ou mettre à jour un Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '0612345678',
                'is_active' => true,
            ]
        );

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
            $this->command->info('✓ Super Admin créé avec succès');
        } else {
            $this->command->info('✓ Super Admin existe déjà');
        }

        // Créer ou mettre à jour un Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'phone' => '0612345679',
                'is_active' => true,
            ]
        );

        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
            $this->command->info('✓ Admin créé avec succès');
        } else {
            $this->command->info('✓ Admin existe déjà');
        }

        // Créer ou mettre à jour un Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager Test',
                'password' => Hash::make('password'),
                'phone' => '0612345681',
                'is_active' => true,
            ]
        );

        if (!$manager->hasRole('Manager')) {
            $manager->assignRole('Manager');
            $this->command->info('✓ Manager créé avec succès');
        } else {
            $this->command->info('✓ Manager existe déjà');
        }

        // Créer ou mettre à jour un Vendeur
        $vendeur = User::firstOrCreate(
            ['email' => 'vendeur@example.com'],
            [
                'name' => 'Vendeur Test',
                'password' => Hash::make('password'),
                'phone' => '0612345680',
                'is_active' => true,
            ]
        );

        if (!$vendeur->hasRole('Vendeur')) {
            $vendeur->assignRole('Vendeur');
            $this->command->info('✓ Vendeur créé avec succès');
        } else {
            $this->command->info('✓ Vendeur existe déjà');
        }

        // Créer ou mettre à jour un Magasinier
        $magasinier = User::firstOrCreate(
            ['email' => 'magasinier@example.com'],
            [
                'name' => 'Magasinier Test',
                'password' => Hash::make('password'),
                'phone' => '0612345682',
                'is_active' => true,
            ]
        );

        if (!$magasinier->hasRole('Magasinier')) {
            $magasinier->assignRole('Magasinier');
            $this->command->info('✓ Magasinier créé avec succès');
        } else {
            $this->command->info('✓ Magasinier existe déjà');
        }

        $this->command->info('');
        $this->command->info('📧 Credentials de connexion:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Super Admin: admin@example.com / password');
        $this->command->info('Admin:       admin2@example.com / password');
        $this->command->info('Manager:     manager@example.com / password');
        $this->command->info('Vendeur:     vendeur@example.com / password');
        $this->command->info('Magasinier:  magasinier@example.com / password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
    }
}