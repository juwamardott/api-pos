<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService{
     
     public function getAll(){
          return User::with('transactionsCreated', 'transactionsUpdated', 'role')->get();
     }
}