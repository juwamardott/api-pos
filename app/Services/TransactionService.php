<?php

namespace App\Services;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function getAll()
    {
        return Transaction::with('customer', 'transactionDetails.products' )->get();
    }

    public function getById($id)
        {
            return Transaction::with('author', 'updater', 'transactionDetails.products')->find($id);
        }

    public function createTransaction(array $validated)
    {
        DB::beginTransaction();

        try {

            $name = $validated['customer_name'];
            $no_telepon = $validated['no_telepon'];

            if($name == null){
                $customer_id = null;
            }else{
                $customer = Customer::create([
                'customer_name' => $name,
                'no_telepon' => $no_telepon
            ]);

            $customer_id = $customer->id;
            }

            

            
            
            $total = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });
            
        
            $transaction = Transaction::create([
                'date_order' => $validated['date_order'],
                'customer_id' => $customer_id ?? null,
                'created_by' => $validated['created_by'] ?? null,
                'total' => $total,
                'paid_amount' => $validated['paid_amount'],
                'change' => $validated['paid_amount'] - $total,
            ]);

            foreach ($validated['items'] as $item) {
                $transaction->transactionDetails()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'sub_total' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // dilempar ke controller untuk ditangani
        }
    }

    public function update($id, array $data)
    {
        // Cari produk, kalau tidak ada otomatis akan throw ModelNotFoundException
        
    }

    public function delete($id)
    {
        $product = Transaction::findOrFail($id);
        $product->delete();
        return true;
    }


}