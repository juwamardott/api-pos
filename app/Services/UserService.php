<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserService{
     
     public function getAll(){
          return User::with('transactionsCreated', 'transactionsUpdated', 'role')->get();
     }


    public function createUser(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id']
        ]);
    }


    public function getById($id){
          return User::with('role')->findOrFail($id);
    }


     public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);
        
        // Ambil data yang akan diupdate (hanya field yang ada di $data)
        $updateData = [];
        
        // Cek perubahan untuk setiap field
        if (isset($data['name']) && $data['name'] !== $user->name) {
            $updateData['name'] = $data['name'];
        }
        
        if (isset($data['role_id']) && $data['role_id'] !== $user->role_id) {
            $updateData['role_id'] = $data['role_id'];
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }
        
        // Pengecekan khusus untuk email
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $this->checkEmailUniqueness($data['email'], $id);
            $updateData['email'] = $data['email'];
        }
        
        // Jika tidak ada data yang berubah
        if (empty($updateData)) {
            return [
                'success' => true,
                'message' => 'Tidak ada perubahan data',
                'user' => $user
            ];
        }
        
        // Update user dengan data yang berubah
        $user->update($updateData);
        
        return [
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'user' => $user->fresh(),
            'updated_fields' => array_keys($updateData)
        ];
    }
    
    /**
     * Cek keunikan email
     */
    private function checkEmailUniqueness($email, $userId)
    {
        $existingUser = User::where('email', $email)
                           ->where('id', '!=', $userId)
                           ->first();
        
        if ($existingUser) {
            throw new \Exception('Email sudah digunakan oleh user lain', 409);
        }
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            // Lempar exception jika produk tidak ditemukan
            return 0;
        }else{
            
            $user->delete();
            return 1;
        }

    }
}