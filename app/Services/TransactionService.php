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


    public function generateDailySales()
    {
        $total_sales = Transaction::where('status', 1)->sum('total');
        $today_orders = Transaction::where('status', 1)
        ->whereDate('date_order', now())
        ->count();
        $total_product = Product::where('is_active', 1)->count();
        $low_stock = Product::where('stock', '<=' , 10)->count();
        return [
            'total_sales' => (float) $total_sales,
            'today_orders' => $today_orders,
            'total_product' => $total_product,
            'low_stock' => $low_stock 
        ];
    }


    public function generateSalesPerCategory()
    {
        $categories = CategoryProduct::with(['products.transactionDetails'])->get();
        $result = [];

        foreach ($categories as $category) {
            $totalSales = 0;

            foreach ($category->products as $product) {
                foreach ($product->transactionDetails as $detail) {
                    $totalSales += $detail->sub_total;
                }
            }

            $result[] = [
                'category_id' => $category->id,
                'category_name' => $category->category_name,
                'total_sales' => $totalSales,
            ];
        }

        return $result;
    }

}