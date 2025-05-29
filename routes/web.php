<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\RazorpayController;

use App\Http\Controllers\SizeController;
use App\Http\Controllers\CategoryController;

use Illuminate\Support\Facades\Route;

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
});// Registration and login page

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');// Dashboard page after login

// Customer Authentication Routes
Route::get('customer/register', [CustomerController::class, 'showRegisterForm'])->name('customer.register');//display the register form of customer
Route::post('customer/registerform', [CustomerController::class, 'register'])->name('customer.register');//used to store customer data
Route::get('customer/login', [CustomerController::class, 'showLoginForm'])->name('customer.login');//display the login page
Route::post('customer/loginform', [CustomerController::class, 'login'])->name('customer.login');//used to store customer login
// Google
Route::get('auth/google', [CustomerController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [CustomerController::class, 'handleGoogleCallback']);


// Product and Purchase History Index (Accessible without Authentication)
Route::get('product/index', [ProductController::class, 'index'])->name('product.index');// Product listing page
Route::get('/purchase-history', [OrderController::class, 'index'])->name('order.index');// Purchase history page
Route::post('/checkout/store/{id}', [OrderController::class, 'store'])->name('checkout.store');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');//display the index CRUD page

    Route::post('address/store', [AddressController::class, 'store'])->name('address.store');//store data in table
    Route::get('/address', [AddressController::class, 'show'])->name('address.show');//display the pop up form of address


    Route::get('razorpay', [RazorpayController::class, 'index'])->name('razorpay.index');
    Route::post('razorpay/payment', [RazorpayController::class, 'payment'])->name('razorpay.payment');


// Route::middleware('auth')->group(function () {
//     Route::get('product/create', [ProductController::class, 'create'])->name('product.create');// Create page
//     Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
//     Route::post('product/store', [ProductController::class, 'store'])->name('product.store');// Store data in table
//     Route::post('product/update/{id}', [ProductController::class, 'update'])->name('product.update');// Update record in DB table
//     Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');// Edit page
//     Route::get('product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');// Soft delete

//     Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');// Create page
//     Route::get('category/index', [CategoryController::class, 'index'])->name('category.index');// Index page
//     Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');// Store data in table
//     Route::post('category/update/{id}', [CategoryController::class, 'update'])->name('category.update');// Update record in DB table
//     Route::get('category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');// Edit page
//     Route::get('category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');// Soft delete

//     Route::get('size/create', [SizeController::class, 'create'])->name('size.create');// Create page
//     Route::get('size/index', [SizeController::class, 'index'])->name('size.index');// Index page
//     Route::post('size/store', [SizeController::class, 'store'])->name('size.store');// Store data in table
//     Route::post('size/update/{id}', [SizeController::class, 'update'])->name('size.update');// Update record in DB table
//     Route::get('size/edit/{id}', [SizeController::class, 'edit'])->name('size.edit');// Edit page
//     Route::get('size/delete/{id}', [SizeController::class, 'delete'])->name('size.delete');// Soft delete

//     Route::post('address/store', [AddressController::class, 'store'])->name('address.store');
//     Route::get('/address', [AddressController::class, 'show'])->name('address.show');

//     Route::post('/checkout/{id}', [OrderController::class, 'store'])->name('checkout.store');
// });

require __DIR__.'/auth.php';
