<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
     
     protected $table = 'products';

     protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'category_id',
        'is_active',
    ];

    

     public function category(){
          return $this->belongsTo(CategoryProduct::class, 'category_id');
     }

     public function transactionDetails(){
          return $this->hasMany(TransactionDetails::class, 'product_id');
     }


     public function productStockHistories(){
          return $this->hasMany(ProductStockHistories::class, 'product_id');
     }


     public function stock(){
          return $this->hasMany(Stock::class, 'product_id');
     }
}