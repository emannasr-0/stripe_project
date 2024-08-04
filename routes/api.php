<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Route::middleware('auth:sanctum')->;
Route::group([
    'middleware' => 'api',
    
], function ($router) {
    
    // Route::get('/products', [ProductController::class, 'index']);
    Route::post('login', [AuthController::class, 'login']); 
});

Route::middleware('auth:sanctum')->get('products', [ProductController::class, 'index']);
Route::middleware('auth:sanctum')->post('products', [ProductController::class, 'store']);
