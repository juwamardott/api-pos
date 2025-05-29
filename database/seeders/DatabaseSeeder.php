<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed category_products terlebih dahulu
        DB::table('category_products')->insert([
            ['id' => 1, 'category_name' => 'Elektronik', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'category_name' => 'Pakaian', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'category_name' => 'Makanan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Lalu seed products
        DB::table('products')->insert([
            [
                'name' => 'Laptop ASUS',
                'sku' => 'SKU-001',
                'description' => 'Laptop untuk kerja dan belajar',
                'cost_price' => 5000000,
                'margin' => 1000000,
                'tax' => 50000,
                'discount' => 0,
                'stock' => 10,
                'category_id' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kaos Polos',
                'sku' => 'SKU-002',
                'description' => 'Kaos bahan cotton',
                'cost_price' => 50000,
                'margin' => 25000,
                'tax' => 5000,
                'discount' => 2000,
                'stock' => 100,
                'category_id' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
            ,
            [
                'name' => 'Celana Panjang',
                'sku' => 'SKU-003',
                'description' => 'Celana bahan cotton',
                'cost_price' => 50000,
                'margin' => 25000,
                'tax' => 5000,
                'discount' => 2000,
                'stock' => 100,
                'category_id' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}