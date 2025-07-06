<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@pizzeria.com',
            'password' => Hash::make('pizza123'),
            'is_super_admin' => true,
            'active' => true
        ]);
        
        Admin::create([
            'name' => 'Empleado',
            'email' => 'empleado@pizzeria.com',
            'password' => Hash::make('empleado123'),
            'is_super_admin' => false,
            'active' => true
        ]);
    }
}