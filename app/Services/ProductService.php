<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function getAll()
    {
        return Product::with('category')->get();
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