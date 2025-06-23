<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

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

        if($product->isEmpty()){
            return response()->json([
            'message' => 'Data products not found',
            ], 404);
        }

        return response()->json([
           'message' => 'Successful get data product',
           'data' => $product
        ], 200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'sku' => 'required|string',
                'description' => 'nullable',
                'price' => 'required|numeric',
                'category_id' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            
            if (Product::where('sku', $validated['sku'])->exists()) {
                return response()->json([
                    'message' => 'Produk dengan SKU tersebut sudah ada.'
                ], 409);
            }
            $product = $this->productService->create($validated, $request->stock);

            DB::commit();

            return response()->json([
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Kesalahan input data',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
   public function show(string $id)
    {
        try {
            $product = $this->productService->getById($id);
            

            return response()->json([
                'message' => 'Succesful get data by id ',
                'data' => $product
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produk not found'
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
    public function update(Request $request, string $id)
{
    // Gunakan Validator manual
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'sku' => 'required|max:100',
        'description' => 'nullable',
        'price' => 'required|numeric',
        'category_id' => 'required',
        'is_active' => 'nullable',
    ]);


    

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    DB::beginTransaction();

    try {
        // Pastikan produk ada
        $product = $this->productService->getById($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Jalankan update di service
        $updatedProduct = $this->productService->update($id, $validator->validated(), $request->stock);

        DB::commit();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $updatedProduct
        ], Response::HTTP_OK);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Internal server error',
            'error' => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $result = $this->productService->delete($id);
        if($result == 0){
            return response()->json([
            'message' => 'Product not found'
        ]);
        }
        return response()->json([
            'message' => 'Product delete succesful'
        ]);
    }


    
}