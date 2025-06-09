<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
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
        $validated = $request->validate([
            'date_order' => 'required',
            'customer_id' => 'nullable|exists:customers,id',
            'paid_amount' => 'required|numeric|min:0',
            'created_by' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

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
        $data = $this->transactionService->report();
        return response()->json([
           'daily' => $data['daily'],
           'month' => $data['month'] 
        ]);
    }
}