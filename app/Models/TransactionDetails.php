<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    //
    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function transactions(){
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}