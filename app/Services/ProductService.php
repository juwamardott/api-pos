<?php

namespace App\Services;

use App\Models\Product;

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
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }
}