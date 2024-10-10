<?php

use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\AdminController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('user', [AuthController::class, 'getUserInfo']);

Route::middleware(['auth:sanctum', 'auth.admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']); // Lấy danh sách người dùng
    Route::post('/users', [UserController::class, 'store']); // Tạo người dùng
    Route::put('/users/{id}', [UserController::class, 'update']); // Cập nhật người dùng
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Xóa mềm người dùng
    Route::post('/users/{id}/restore', [UserController::class, 'restore']); // Khôi phục người dùng
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete']); // Xóa vĩnh viễn người dùng
});



