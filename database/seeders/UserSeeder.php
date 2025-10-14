<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '0612345678',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        // Créer un Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'phone' => '0612345679',
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        // Créer un Vendeur
        $vendeur = User::create([
            'name' => 'Vendeur Test',
            'email' => 'vendeur@example.com',
            'password' => Hash::make('password'),
            'phone' => '0612345680',
            'is_active' => true,
        ]);
        $vendeur->assignRole('Vendeur');
    }
}