<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //

    public function users(){
        return $this->hasOne(User::class, 'role_id');
    }
}