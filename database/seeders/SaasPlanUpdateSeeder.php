<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaasPlanUpdateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plans')->where('name', 'Starter')->update([
            'features' => json_encode(['pos', 'sales', 'purchasing', 'inventory', 'api_access']),
        ]);

        DB::table('plans')->where('name', 'Professional')->update([
            'features' => json_encode(['pos', 'sales', 'purchasing', 'inventory', 'hrm', 'accounting', 'crm', 'api_access']),
        ]);

        DB::table('plans')->where('name', 'Enterprise')->update([
            'price_monthly' => 500000,
            'price_yearly' => 5000000,
        ]);

        DB::table('plans')->updateOrInsert(
            ['name' => 'Free'],
            [
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_users' => 1,
                'max_products' => 50,
                'max_warehouses' => 1,
                'max_stores' => 1,
                'features' => json_encode(['pos', 'sales', 'api_access']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('plans')->updateOrInsert(
            ['name' => 'Growth'],
            [
                'price_monthly' => 150000,
                'price_yearly' => 1500000,
                'max_users' => 5,
                'max_products' => 250,
                'max_warehouses' => 3,
                'max_stores' => 3,
                'features' => json_encode(['pos', 'sales', 'purchasing', 'inventory', 'hrm', 'api_access']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
