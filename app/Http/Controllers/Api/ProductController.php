<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
    {

        $product = $this->productService->getAll();

        return response()->json([
           'status' => 200,
           'message' => 'successful get data product',
           'data' => $product
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        return response()->json([
            'date' => $request

        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
        $product = $this->productService->getById($id);
        return response()->json([
            'status' => 200,
            'message' => 'Succesful get By Id',
            'data' => $product
        ]);
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