<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionService
{
    public function getAll()
    {
        return Transaction::with('customer', 'transactionDetails.products' )->get();
    }

    public function getById($id)
        {
            return Transaction::with('author', 'updater')->findOrFail($id);
        }

    public function create(array $data)
    {
        return Transaction::create($data);
    }

    public function update($id, array $data)
    {
        // Cari produk, kalau tidak ada otomatis akan throw ModelNotFoundException
        $product = Transaction::findOrFail($id);

        // Cek SKU unik, jika ada SKU yang sama di produk lain
        if (isset($data['id'])) {
            $exists = Transaction::where('id', $data['id'])
                            ->where('id', '!=', $id)
                            ->exists();

            if ($exists) {
                abort(409, 'Transactin sudah ada.');
            }
        }

        // Update produk
        $product->update($data);

        return $product;
    }

    public function delete($id)
    {
        $product = Transaction::findOrFail($id);
        $product->delete();
        return true;
    }
}