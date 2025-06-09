<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\Role;
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
            ,
            [
                'name' => 'Baju Pollo',
                'sku' => 'SKU-004',
                'description' => 'Baju Pollo',
                'price' => 50000,
                'stock' => 100,
                'category_id' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);


        Customer::create([
            'customer_name' => 'Mardood',
            'address' => 'Banjar Wanasari' 
        ]);
        Customer::create([
            'customer_name' => 'Ari',
            'address' => 'Banjar Anyar' 
        ]);
        Customer::create([
            'customer_name' => 'Darmadi',
            'address' => 'Banjar Kelodan' 
        ]);


        Transaction::create([
            'date_order' => now(),
            'created_by' => 1,
            'updated_by' => 1,
            'customer_id' => 2,
            'total' => 200000,
            'paid_amount' => 200000,
            'change' => 0,
         ]);
        Transaction::create([
            'date_order' => now(),
            'created_by' => 1,
            'updated_by' => 1,
            'customer_id' => 1,
            'total' => 150000,
            'paid_amount' => 150000,
            'change' => 0
         ]);
        Transaction::create([
            'date_order' => now(),
            'created_by' => 1,
            'updated_by' => 1,
            'customer_id' => 3,
            'total' => 90000,
            'paid_amount' => 90000,
            'change' => 0
         ]);

        for ($i = 0; $i < 10; $i++) {
            $quantity = rand(1, 5);
            $productId = rand(1, 3); // anggap ID produk 1-3
            $price = rand(1000, 10000); // harga acak, jika tidak ambil dari tabel produk

            DB::table('transaction_details')->insert([
                'transaction_id'  => rand(1, 3),
                'product_id'      => $productId,
                'quantity'        => $quantity,
                'price' => $price,
                'sub_total'       => $price * $quantity,
            ]);
        }

        
    }
}