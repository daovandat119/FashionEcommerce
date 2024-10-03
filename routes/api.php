<?php

use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\CategoriesController;
use Illuminate\Support\Facades\Route;


Route::get('products', [ProductsController::class, 'index']);  
Route::post('products', [ProductsController::class, 'store']); 
Route::get('products/{id}', [ProductsController::class, 'edit']);  
Route::put('products/{id}', [ProductsController::class, 'update']);  
Route::delete('products/{id}', [ProductsController::class, 'delete'])->name('product.delete');
 


Route::prefix('categories')->group(function () {
    Route::get('/', [CategoriesController::class, 'index']);  
    Route::post('/', [CategoriesController::class, 'store']); 
    Route::get('/{id}', [CategoriesController::class, 'edit']); 
    Route::put('/{id}', [CategoriesController::class, 'update']); 
    Route::delete('/{id}', [CategoriesController::class, 'delete']); 
});

