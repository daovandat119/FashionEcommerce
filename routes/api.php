<?php



use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdmin;

use App\Http\Controllers\Api\UserController;
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
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\ShippingController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


Route::post('/admin/login', [AuthController::class, 'loginAdmin']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::get('/email/verify/{id}', [AuthController::class, 'verify'])
->name('verification.verify');
Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);



Route::middleware(['auth:sanctum', 'auth.admin'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/restore/{id}', [UserController::class, 'restore']);
});

Route::get('/products', [ProductsController::class, 'index']);
Route::post('/products/view/{id}', [ProductsController::class, 'view']);
Route::get('/products/{id}', [ProductsController::class, 'edit']);
Route::middleware(['auth:sanctum', 'auth.admin'])->prefix('products')->group(function () {
    Route::post('/', [ProductsController::class, 'store']);
    Route::post('/{id}', [ProductsController::class, 'update']);
    Route::delete('/', [ProductsController::class, 'delete']);
    Route::post('/status/{id}', [ProductsController::class, 'updateStatus']);
});
//
Route::get('/categories', [CategoriesController::class, 'index']);

Route::get('categories', [CategoriesController::class, 'index']);

Route::middleware(['auth:sanctum', 'auth.admin'])->prefix('categories')->group(function () {
    Route::post('/', [CategoriesController::class, 'store']);
    Route::get('/{id}', [CategoriesController::class, 'edit']);
    Route::put('/{id}', [CategoriesController::class, 'update']);
    Route::delete('/', [CategoriesController::class, 'delete']);
    Route::post('/status/{id}', [CategoriesController::class, 'updateStatus']);
});

Route::middleware(['auth:sanctum', 'auth.admin'])->prefix('colors')->group(function () {
    Route::get('/', [ColorsController::class, 'index']);
    Route::post('/', [ColorsController::class, 'store']);
    Route::get('/{id}', [ColorsController::class, 'edit']);
    Route::put('/{id}', [ColorsController::class, 'update']);
    Route::delete('/{id}', [ColorsController::class, 'delete']);
});

Route::middleware(['auth:sanctum', 'auth.admin'])->prefix('sizes')->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::post('/', [SizeController::class, 'store']);
    Route::get('/{id}', [SizeController::class, 'edit']);
    Route::put('/{id}', [SizeController::class, 'update']);
    Route::delete('/{id}', [SizeController::class, 'delete']);
});

Route::post('/product-variants/getVariantByID', [ProductVariantController::class, 'show']);
Route::middleware(['auth:sanctum'])->prefix('product-variants')->group(function () {
    Route::post('/productID', [ProductVariantController::class, 'index']);
    Route::post('/', [ProductVariantController::class, 'store']);
    Route::get('/{VariantID}', [ProductVariantController::class, 'showAdmin']);
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

Route::get('/reviews/{id}', [ReviewsController::class, 'index']);
Route::middleware(['auth:sanctum'])->prefix('reviews')->group(function () {
    Route::post('/', [ReviewsController::class, 'store']);
});


Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping']);
