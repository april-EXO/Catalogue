<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
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



Route::post('/submit-order', [OrderController::class, 'submitOrder']);
Route::get('/get-order', [OrderController::class, 'getOrder']);
Route::put('/change-status', [OrderController::class, 'changeStatus']);
Route::get('/get-order-w-ID/{id}', [OrderController::class, 'getOrderWithID']);




