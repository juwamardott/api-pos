<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function getAllPagination($search = null)
    {
        // 1️⃣ Ambil ID terbaru per nama produk
        $idsQuery = Product::select(DB::raw('MAX(id) as id'))
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->groupBy('name');

        $ids = $idsQuery->pluck('id');

        // 2️⃣ Ambil detail produk + relasi category
        return Product::with('category', 'stock')
            ->whereIn('id', $ids)
            ->orderByDesc('id')
            ->paginate(8);
    }

    
    public function getById($id)
        {
            return Product::with('category', 'stock')->findOrFail($id);
        }
    public function create(array $data, $stock)
    {
        $product = Product::create($data);
        $id  = $product->id;
        Stock::create([
           'product_id' => $id,
           'quantity' => $stock,
           'buy_price' => $data['price']
        ]);
        return  $product;
    }

    public function update($id, array $data, $stock)
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


        $oldStock = Stock::where('product_id', $id)->first();
        
        $oldStock->quantity = $stock;
        $oldStock->save();

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