<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'customer_id',
        'total',
        'paid_amount',
        'change',
        'date_order',
        'created_by',
        'updated_by'
    ];


    protected $casts = [
    'total' => 'integer',
    'paid_amount' => 'integer',
    'change' => 'integer',
];
    //
    public function author(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function transactionDetails(){
        return $this->hasMany(TransactionDetails::class, 'transaction_id');
    }
}