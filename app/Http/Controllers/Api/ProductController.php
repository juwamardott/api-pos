<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock' => 'required|numeric',
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
            $product = $this->productService->create($validated);

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
        //
         // Validasi input
    $validated = $request->validate([
        'name' => 'required|string',
        'sku' => 'required|string',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|numeric',
        'category_id' => 'nullable|integer',
        'is_active' => 'nullable|boolean',
    ]);

    try {
        DB::beginTransaction();

        // Update produk
        $product = $this->productService->update($id, $validated);

        DB::commit();

        return response()->json([
            'message' => 'Product success updated',
            'data' => $product
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation fail',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error',
            'error' => $e->getMessage()
        ], 500);
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


    public function get_top_product(){
        $top = $this->productService->getTopProduct();

        return response()->json([
           'message' => 'Succes get data top product',
           'data' => $top  
        ]);
    }
}