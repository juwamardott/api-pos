<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    //
    protected $fillable = [
       'product_id',
       'quantity',
       'buy_price',
       'branch_id'
    ];


    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}