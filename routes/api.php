<?php

use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ColorsController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\CartItemsController;
use App\Http\Controllers\Api\ProductVariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index']);
    Route::post('/', [ProductsController::class, 'store']);
    Route::get('/{id}', [ProductsController::class, 'edit']);
    Route::post('/{id}', [ProductsController::class, 'update']);
    Route::delete('/', [ProductsController::class, 'delete']);
    Route::post('/view/{id}', [ProductsController::class, 'view']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::post('/', [CategoriesController::class, 'store']);
    Route::get('/{id}', [CategoriesController::class, 'edit']);
    Route::put('/{id}', [CategoriesController::class, 'update']);
    Route::delete('/', [CategoriesController::class, 'delete']);
});

Route::prefix('colors')->group(function () {
    Route::get('/', [ColorsController::class, 'index']); // Lấy danh sách màu
    Route::post('/', [ColorsController::class, 'store']); // Tạo mới một màu
    Route::get('/{id}', [ColorsController::class, 'edit']); // Lấy thông tin chi tiết của một màu
    Route::put('/{id}', [ColorsController::class, 'update']); // Cập nhật màu
    Route::delete('/{id}', [ColorsController::class, 'delete']); // Xóa màu
});

Route::prefix('sizes')->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::post('/', [SizeController::class, 'store']);
    Route::get('/{id}', [SizeController::class, 'edit']);
    Route::post('/{id}', [SizeController::class, 'update']);
    Route::delete('/{id}', [SizeController::class, 'delete']);

});

Route::prefix('product-variants')->group(function () {
    Route::post('/productID', [ProductVariantController::class, 'index']);
    Route::post('/', [ProductVariantController::class, 'store']);
    Route::post('/getVariantByID', [ProductVariantController::class, 'show']);
    Route::put('/', [ProductVariantController::class, 'update']);
    Route::delete('/{id}', [ProductVariantController::class, 'delete']);
});

Route::prefix('/cart-items')->group(function () {
    Route::get('/', [CartItemsController::class, 'index']);
    Route::post('/', [CartItemsController::class, 'store']);
    Route::patch('/{id}', [CartItemsController::class, 'update']);
    Route::delete('/{id}', [CartItemsController::class, 'destroy']);
});

Route::get('products/variants/{id}', [ProductsController::class, 'getProductVariants']);

