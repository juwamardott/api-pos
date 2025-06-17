<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\CategoryProduct;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportService
{

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


//     public function generateSalesPerCategory()
//     {
//         $categories = CategoryProduct::with(['products.transactionDetails'])->get();
//         $result = [];

//         foreach ($categories as $category) {
//             $totalSales = 0;

//             foreach ($category->products as $product) {
//                 foreach ($product->transactionDetails as $detail) {
//                     $totalSales += $detail->sub_total;
//                 }
//             }

//             $result[] = [
//                 'category_id' => $category->id,
//                 'category_name' => $category->category_name,
//                 'total_sales' => $totalSales,
//             ];
//         }

//         return $result;
//     }

     public function generateSalesPerCategory()
     {
     $categories = CategoryProduct::with([
          'products.transactionDetails' => function ($query) {
               $query->whereHas('transactions', function ($q) {
                    $q->where('status', 1);
               });
          }
     ])->get();

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



      public function getTopProduct()
     {
     $topProducts = TransactionDetails::select(
               'product_id',
               DB::raw('SUM(sub_total) as total')
          )
          ->whereHas('transactions', function ($q) {
               $q->where('status', 1);
          })
          ->groupBy('product_id')
          ->orderByDesc('total')
          ->with('products')
          ->take(10)
          ->get()
          ->map(function ($item) {
               return [
                    'product_id' => $item->product_id,
                    'total' => (float) $item->total,
                    'product' => $item->products,
               ];
          });

     return $topProducts;
     }



}