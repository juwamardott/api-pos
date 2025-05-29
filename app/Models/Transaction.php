<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    public function author(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}