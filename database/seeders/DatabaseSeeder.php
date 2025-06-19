<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Stock;
use Faker\Factory as Faker;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['Cashier', 'Warehouse', 'Accounting', 'Superadmin'];

        foreach ($roles as $roleName) {
            Role::create(['role' => $roleName]);
        }

        
        User::factory(10)->create();

        User::create([
            'name' => 'Password',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 3
        ]);


         $categories = [
            'Alat Tulis',
            'Kertas & Buku',
            'Perlengkapan Kantor',
            'Perlengkapan Sekolah',
            'Aksesoris Meja',
        ];

        foreach ($categories as $name) {
            DB::table('category_products')->insert([
                'category_name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'name' => $faker->words(2, true), // contoh: "Pena Biru"
                'sku' => 'SKU' . $faker->unique()->numerify('####'),
                'description' => $faker->sentence(),
                'price' => $faker->numberBetween(1000, 100000),
                'category_id' => $faker->numberBetween(1, 5), // pastikan category_products ada id 1–5
                'is_active' => true,
            ]);
        }

        // Ambil semua produk
        $products = Product::all();

        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => $faker->numberBetween(10, 100), // jumlah stok per produk
                'buy_price' => $faker->numberBetween(500, $product->price), // harga beli lebih murah dari harga jual
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        
    }
}