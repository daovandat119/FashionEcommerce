<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Events\testingEvent;/*
=======
use App\Events\testingEvent;
use App\Http\Controllers\Api\GoogleController;
use App\Models\Reviews;
/*
>>>>>>> 6dc9afa2d7de3beee35dfb855abf6ac26ad5fa5e
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
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


