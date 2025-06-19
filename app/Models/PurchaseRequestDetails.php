<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetails extends Model
{
    //
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'price',
        'sub_total'
    ];

    public function purchaseRequest(){
        return $this->belongsTo(PurchaseRequest::class, 'purchase_id');
    }
}