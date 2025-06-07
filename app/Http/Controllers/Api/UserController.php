<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}