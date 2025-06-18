<?php

namespace App\Services;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductStockHistories;
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
        // Buat customer jika ada nama
        $name = $validated['customer_name'];
        $no_telepon = $validated['no_telepon'];

        $customer_id = null;

        if (!empty($name)) {
            $customer = Customer::create([
                'customer_name' => $name,
                'no_telepon' => $no_telepon
            ]);
            $customer_id = $customer->id;
        }

        // Hitung total
        $total = collect($validated['items'])->sum(fn ($item) => $item['quantity'] * $item['price']);

        // Buat transaksi
        $transaction = Transaction::create([
            'date_order'   => $validated['date_order'],
            'customer_id'  => $customer_id,
            'created_by'   => $validated['created_by'] ?? null,
            'total'        => $total,
            'paid_amount'  => $validated['paid_amount'],
            'change'       => $validated['paid_amount'] - $total,
        ]);

        // Buat detail transaksi & siapkan stock histories
        $stockHistories = [];

        foreach ($validated['items'] as $item) {
            $transaction->transactionDetails()->create([
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'sub_total'  => $item['quantity'] * $item['price'],
            ]);

            $stockHistories[] = [
                'product_id' => $item['product_id'],
                'date'       => $validated['date_order'],
                'quantity'   => $item['quantity'],
                'type'       => 'out',
                'notes'      => 'Transaction ID: ' . $transaction->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert stock histories sekali bulk
        ProductStockHistories::insert($stockHistories);

        DB::commit();

        return $transaction;

    } catch (\Exception $e) {
        DB::rollBack();
        // Untuk debug bisa log error $e->getMessage()
        return false;
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