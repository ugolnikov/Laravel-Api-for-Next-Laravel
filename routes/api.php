<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\Auth\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::middleware('auth:web')->group(function () {
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::put('/cart/{id}', [CartController::class, 'updateCart']);
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('seller')->prefix('seller')->group(function () {
        Route::get('/products', [SellerProductController::class, 'index']);
        Route::post('/products', [SellerProductController::class, 'store']);
        Route::put('/products/{product}', [SellerProductController::class, 'update']);
        Route::delete('/products/{product}', [SellerProductController::class, 'destroy']);
    });
});


