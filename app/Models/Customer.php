<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'customer_name',
        'no_telepon'
    ];
    public function transactions(){
        return $this->hasMany(Transaction::class, 'customer_id');
    }
}