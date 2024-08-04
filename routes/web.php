<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});





Route::get('/checkout', [StripeController::class, 'createCheckoutSession']);
Route::get('/payment/success', [StripeController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [StripeController::class, 'cancel'])->name('payment.cancel');
Route::post('/webhook/stripe', [StripeController::class, 'handleStripe']);



Route::post('/add-product', [ProductController::class, 'addProduct']);
Route::get('/getproduct',[ProductController::class,'getProduct']);
Route::get('/user/{id}',[UserController::class,'show']);
Route::get('/Age/{age}',[UserController::class,'Age'])->middleware('checkage');
Route::get('/getproduct/price-greater-than/{price}',[ProductController::class,'getProductByPrice'])->middleware('authcustom');


// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
