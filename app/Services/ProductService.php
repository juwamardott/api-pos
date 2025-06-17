<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function getAll()
    {
        // 1️⃣ Ambil ID terbaru per nama produk
        $ids = Product::select(DB::raw('MAX(id) as id'))
            ->groupBy('name')
            ->pluck('id');

        // 2️⃣ Ambil detail produk + relasi category
        return Product::with('category')
            ->whereIn('id', $ids)
            ->orderByDesc('id')
            ->get();
    }
    
    public function getById($id)
        {
            return Product::with('category')->findOrFail($id);
        }
    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        // Cari produk, kalau tidak ada otomatis akan throw ModelNotFoundException
        $product = Product::findOrFail($id);

        // Cek SKU unik, jika ada SKU yang sama di produk lain
        if (isset($data['sku'])) {
            $exists = Product::where('sku', $data['sku'])
                            ->where('id', '!=', $id)
                            ->exists();

            if ($exists) {
                abort(409, 'SKU sudah digunakan oleh produk lain.');
            }
        }

        // Update produk
        $product->update($data);

        return $product;
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            // Lempar exception jika produk tidak ditemukan
            return 0;
        }else{
            
            $product->delete();
            return 1;
        }
    }


}