<?php

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


Route::apiResource('products', ProductController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('category-product', CategoryProductController::class);


Route::prefix('transaction')->group(function(){
    Route::get('products', [ProductController::class, 'allDataProduct']);
});



Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});


Route::prefix('reports')->group(function(){
    Route::get('top-product', [ReportController::class, 'get_top_product']);
    Route::get('daily-sales',[ReportController::class, 'daily_sales']);
    Route::get('sales_per_category', [ReportController::class, 'sales_per_category']);
});