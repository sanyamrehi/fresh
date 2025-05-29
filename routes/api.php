<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\addressController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ContactApiController;
use Illuminate\Support\Facades\Route;

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

// Product Routes
Route::match(['get', 'post'], '/products', [ProductController::class, 'index']);
Route::get('/products/index', [ProductController::class, 'index'])->name('product.index'); // List all products
Route::post('/products/store', [ProductController::class, 'store'])->name('api.products.store'); // Create a new product
Route::get('/products/{id}', [ProductController::class, 'show'])->name('api.products.show'); // Show a single product
Route::put('/products/{id}', [ProductController::class, 'update'])->name('api.products.update'); // Update product
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('api.products.destroy'); // Delete product

// Category Routes
Route::match(['get','post'],'/categories', [CategoryController::class, 'index']); // List all categories
Route::post('/categories/store', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

// Size Routes
Route::match(['get', 'post'], '/sizes', [SizeController::class, 'index']);  // List all sizes
Route::post('/sizes/store', [SizeController::class, 'store']);
Route::get('/sizes/{id}', [SizeController::class, 'show']);
Route::put('/sizes/{id}', [SizeController::class, 'update']);
Route::delete('/sizes/{id}', [SizeController::class, 'destroy']);

Route::post('/cart/add', [CartController::class, 'addToCart']); // Add product to cart
Route::get('/cart', [CartController::class, 'index']); // View cart items
 Route::post('/orders/store', [OrderController::class, 'store']); // Place order for a product
    Route::match(['get','post'],'/orders', [OrderController::class, 'index']); // Get order history
    Route::get('/orders/show/{id}', [OrderController::class, 'show']); // Show order history

    Route::post('/register', [CustomerController::class, 'register']);
Route::post('/login', [CustomerController::class, 'login']);

Route::post('/addresses/store', [AddressController::class, 'store']); // Add Address
    Route::match(['get','post'],'/addresses', [AddressController::class, 'index']); // Fetch All Addresses
    Route::get('/addresses/{id}', [AddressController::class, 'show']);

    Route::post('/send-form', [ContactApiController::class, 'send']);
