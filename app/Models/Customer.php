<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    public function transactions(){
        return $this->hasMany(Transaction::class, 'customer_id');
    }
}