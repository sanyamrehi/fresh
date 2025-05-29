<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\CategoryController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');// Create page
    Route::get('/', [ProductController::class, 'showadmin'])->name('admin.showadmin');//Index CRUD page
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');// Store data in table
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('products.update');// Update record in DB table
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');// Edit page
    Route::get('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');// Soft delete

    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');// Create page
    Route::get('/category/index', [CategoryController::class, 'index'])->name('category.index');// Index page
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');// Store data in table
    Route::post('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');// Update record in DB table
    Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');// Edit page
    Route::get('/category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');// Soft delete

    Route::get('/size/create', [SizeController::class, 'create'])->name('size.create');// Create page
    Route::get('/size/index', [SizeController::class, 'index'])->name('size.index');// Index page
    Route::post('/size/store', [SizeController::class, 'store'])->name('size.store');// Store data in table
    Route::post('/size/update/{id}', [SizeController::class, 'update'])->name('size.update');// Update record in DB table
    Route::get('/size/edit/{id}', [SizeController::class, 'edit'])->name('size.edit');// Edit page
    Route::get('/size/delete/{id}', [SizeController::class, 'delete'])->name('size.delete');// Soft delete

    Route::get('admin/products/export', [ProductController::class, 'export'])->name('admin.products.export');
Route::post('admin/products/import', [ProductController::class, 'import'])->name('admin.products.import');

});
    // Route::post('address/store', [AddressController::class, 'store'])->name('address.store');//store data in table
    // Route::get('/address', [AddressController::class, 'show'])->name('address.show');//display the pop up form of address



require __DIR__.'/auth.php';

