<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    //
    protected $fillable = [
        'name',
        'address'
    ];

    public function user(){
        return $this->hasOne(User::class, 'branch_id');
    }
}