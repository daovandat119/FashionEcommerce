<?php

use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ColorsController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\CartItemsController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductsController::class, 'index']);
Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::post('/', [ProductsController::class, 'store']);
    Route::get('/{id}', [ProductsController::class, 'edit']);
    Route::post('/{id}', [ProductsController::class, 'update']);
    Route::delete('/', [ProductsController::class, 'delete']);
    Route::post('/view/{id}', [ProductsController::class, 'view']);
});

Route::get('/categories', [CategoriesController::class, 'index']);
Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::post('/', [CategoriesController::class, 'store']);
    Route::get('/{id}', [CategoriesController::class, 'edit']);
    Route::put('/{id}', [CategoriesController::class, 'update']);
    Route::delete('/', [CategoriesController::class, 'delete']);
});

Route::middleware('auth:sanctum')->prefix('colors')->group(function () {
    Route::get('/', [ColorsController::class, 'index']);
    Route::post('/', [ColorsController::class, 'store']);
    Route::get('/{id}', [ColorsController::class, 'edit']);
    Route::put('/{id}', [ColorsController::class, 'update']);
    Route::delete('/{id}', [ColorsController::class, 'delete']);
});

Route::middleware('auth:sanctum')->prefix('sizes')->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::post('/', [SizeController::class, 'store']);
    Route::get('/{id}', [SizeController::class, 'edit']);
    Route::post('/{id}', [SizeController::class, 'update']);
    Route::delete('/{id}', [SizeController::class, 'delete']);
});


Route::middleware('auth:sanctum')->prefix('product-variants')->group(function () {
    Route::post('/productID', [ProductVariantController::class, 'index']);
    Route::post('/', [ProductVariantController::class, 'store']);
    Route::post('/getVariantByID', [ProductVariantController::class, 'show']);
    Route::put('/', [ProductVariantController::class, 'update']);
    Route::delete('/{id}', [ProductVariantController::class, 'delete']);
});

Route::middleware('auth:sanctum')->prefix('/cart-items')->group(function () {
    Route::get('/', [CartItemsController::class, 'index']);
    Route::post('/', [CartItemsController::class, 'store']);
    Route::patch('/{id}', [CartItemsController::class, 'update']);
    Route::delete('/{id}', [CartItemsController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('/wishlist')->group(function () {
    Route::post('/', [WishlistController::class, 'create']);
    Route::get('/', [WishlistController::class, 'index']);
    Route::delete('/{id}', [WishlistController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('/order')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'getOrderById']);
    Route::post('/status/{id}', [OrderController::class, 'updateOrderStatus']);
});

Route::middleware('auth:sanctum')->prefix('/address')->group(function () {
    Route::get('/', [AddressController::class, 'index']);
    Route::post('/', [AddressController::class, 'store']);
    Route::get('/{id}', [AddressController::class, 'edit']);
    Route::put('/{id}', [AddressController::class, 'update']);
    Route::delete('/{id}', [AddressController::class, 'delete']);
    Route::get('/setDefaultAddress/{id}', [AddressController::class, 'setDefaultAddress']);
});

