<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    protected $reportService;

    public function __construct(ReportService $reportService)
    
    {
        $this->reportService = $reportService;   
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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


    public function recent_orders(Request $request){
        $branch_id = $request->branch_id;
        $data = $this->reportService->getRecentOrders($branch_id);
        return response()->json([
            'message' => 'get recent order successful',
            'data' => $data
        ]);
    }


     public function daily_sales(Request $request){
        $branch_id = $request->branch_id;
        $data = $this->reportService->generateDailySales($branch_id);
        return response()->json([
            'message' => 'get report successful',
            'data' => $data
        ]);
    }


    public function sales_per_category(){
        $data = $this->reportService->generateSalesPerCategory();
        return response()->json([
            'message' => 'succes',
            'data' => $data
        ]);
    }

    public function get_top_product(Request $request){
        
        $branch_id = $request->branch_id;
        $top = $this->reportService->getTopProduct($branch_id);

        return response()->json([
           'message' => 'Succes get data top product',
           'data' => $top  
        ]);
    }

    
}