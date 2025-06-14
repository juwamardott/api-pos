<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    //
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',
    ];

    protected $casts = [
    'price' => 'integer',
    'sub_total' => 'integer',
];
    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function transactions(){
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}