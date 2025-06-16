<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        //
        $data = $this->transactionService->getAll();
        // return $data;
        if($data->isEmpty()){
            return response()->json([
                'message' => 'Data transaction not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Get data transaction Successfull',
            'data' => $data
        ], 200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'date_order' => 'required',
        'customer_id' => 'nullable',
        'customer_name' => 'nullable',
        'no_telepon' => 'nullable',
        'paid_amount' => 'required',
        'created_by' => 'nullable',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    $validated = $validator->validated();

    try {
        $transaction = $this->transactionService->createTransaction($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Transaction failed: ' . $e->getMessage(),
        ], 500);
    }
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = $this->transactionService->getById($id);
        // return $transaction;
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction retrieved successfully',
            'data' => $transaction,
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


     public function daily_sales(){
        $data = $this->transactionService->generateDailySales();
        return response()->json([
            'message' => 'get report',
            'data' => $data
        ]);
    }


    public function sales_per_category(){
        $data = $this->transactionService->generateSalesPerCategory();
        return response()->json([
            'message' => 'succes',
            'data' => $data
        ]);
    }
}