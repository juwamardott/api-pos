<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    //
    protected $fillable = [
        'date_purchase',
        'supplier_name',
        'total_amount',
        'status',
        'invoice'
    ];

    public function purchaseRequestDetails(){
        return $this->hasMany(PurchaseRequestDetails::class, 'purchase_id');
    }
}