<?php

namespace App\Services;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductStockHistories;
use App\Models\Stock;
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
        // 1️⃣ Buat customer jika diinput nama
        $customer_id = null;

        if (!empty($validated['customer_name'])) {
            $customer = Customer::create([
                'customer_name' => $validated['customer_name'],
                'no_telepon'    => $validated['no_telepon'],
            ]);
            $customer_id = $customer->id;
        }

        // 2️⃣ Hitung total transaksi
        $total = collect($validated['items'])
            ->sum(fn ($item) => $item['quantity'] * $item['price']);

        // 3️⃣ Buat transaksi
        $transaction = Transaction::create([
            'date_order'  => $validated['date_order'],
            'customer_id' => $customer_id,
            'created_by'  => $validated['created_by'] ?? null,
            'total'       => $total,
            'paid_amount' => $validated['paid_amount'],
            'change'      => $validated['paid_amount'] - $total,
        ]);

        // 4️⃣ Loop item: Buat detail + potong stock + siapkan stock histories
        $stockHistories = [];

        foreach ($validated['items'] as $item) {

            // a) Buat detail transaksi
            $transaction->transactionDetails()->create([
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'sub_total'  => $item['quantity'] * $item['price'],
            ]);

            // b) Validasi & potong stock
            $stock = Stock::where('product_id', $item['product_id'])->firstOrFail();

            if ($stock->quantity < $item['quantity']) {
                throw new \Exception("Stock tidak cukup untuk produk ID {$item['product_id']}");
            }

            $stock->quantity -= $item['quantity'];
            $stock->save();

            // c) Siapkan history
            $stockHistories[] = [
                'product_id' => $item['product_id'],
                'date'       => $validated['date_order'],
                'quantity'   => $item['quantity'],
                'type'       => 'out',
                'reference'      => 'Transaction IDXX: ' . $transaction->id,
                'balance'      => $stock->quantity,
                'description'      => 'Transaction ID: ' . $transaction->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 5️⃣ Bulk insert stock histories
        ProductStockHistories::insert($stockHistories);

        // 6️⃣ Commit
        DB::commit();

        return $transaction;

    } catch (\Throwable $e) {
        DB::rollBack();

        // Opsional: log error

        // Rethrow supaya controller tangkap
        throw $e;
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