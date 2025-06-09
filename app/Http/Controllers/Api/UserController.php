<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected UserService $userService;

    public function __construct(UserService $userService ){
        $this->userService = $userService;
    }
    
    public function index()
    {
        //
        $user = $this->userService->getAll();
        // return $user;
        if($user->isEmpty()){
            return response()->json([
            'message' => 'Data users not found',
            ], 404);
        }

        return response()->json([
           'message' => 'Successful get data users',
           'data' => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        // Validasi manual supaya kita bisa custom responsenya
        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'role_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Proses jika validasi berhasil
        $user = $this->userService->createUser($validator->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $user = $this->userService->getById($id);

            return response()->json([
                'message' => 'Succesful get user by id ',
                'data' => $user
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Find Error',
                'error' => $e->getMessage()
            ], 500);
        }

        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name'     => 'sometimes|required|max:255',
                'email'    => 'sometimes|required|email',
                'password' => 'sometimes|nullable|min:6',
                'role_id'  => 'sometimes|required|',
            ]);

            // Panggil service untuk update user
            $result = $this->userService->updateUser($id, $validated);

            return response()->json([
                'message' => $result['message'],
                'data'    => $result['user'],
                'updated_fields' => $result['updated_fields'] ?? []
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);

        } catch (Exception $e) {
            // Cek jika error dari email yang sudah digunakan
            if ($e->getCode() == 409) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 409);
            }

            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy(string $id)
    {
        //
        $result = $this->userService->delete($id);
        if($result == 0){
            return response()->json([
            'message' => 'User not found'
        ]);
        }
        return response()->json([
            'message' => 'User delete succesful'
        ]);
    }


   
}