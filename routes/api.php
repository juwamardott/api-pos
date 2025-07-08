<?php

use App\Events\NewNotification;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryProductController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::apiResource('products', ProductController::class)->middleware('auth:sanctum');
Route::apiResource('transactions', TransactionController::class)->middleware('auth:sanctum');
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
Route::apiResource('category-product', CategoryProductController::class)->middleware('auth:sanctum');


Route::prefix('transaction')->group(function(){
    Route::get('products', [ProductController::class, 'allDataProduct'])->middleware('auth:sanctum');
});



Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});


Route::prefix('reports')->group(function(){
    Route::post('top-product', [ReportController::class, 'get_top_product'])->middleware('auth:sanctum');
    Route::post('daily-sales',[ReportController::class, 'daily_sales'])->middleware('auth:sanctum');
    Route::post('recent-orders',[ReportController::class, 'recent_orders'])->middleware('auth:sanctum');
    Route::get('sales_per_category', [ReportController::class, 'sales_per_category'])->middleware('auth:sanctum');
});


Route::get('/test-broadcast', function () {
    broadcast(new NewNotification('🚀 Notifikasi dari backend!'));
    return response()->json(['status' => 'sent']);
});