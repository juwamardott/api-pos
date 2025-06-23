<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStockHistories extends Model
{
    //
    protected $fillable = [
        'date',
        'qty',
        'quantity',
        'type',
        'reference',
        'balance',
        'description'
    ];


    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}