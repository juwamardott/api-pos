<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

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
                'price' => 5000000,
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
                'price' => 50000,
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
                'price' => 50000,
                'stock' => 100,
                'category_id' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

         $users = User::take(10)->get();

        if ($users->count() < 10) {
            $this->command->warn('Minimal harus ada 10 user. Jalankan UserSeeder untuk menambahkan user.');
            return;
        }

        foreach ($users as $index => $user) {
            Transaction::create([
                'customer_name' => "Customer " . ($index + 1),
                'date_order' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }
    }
}