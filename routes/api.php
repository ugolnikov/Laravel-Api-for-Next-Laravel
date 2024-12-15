<?php


use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VerifyController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::middleware('auth:web')->group(function () {
    // Корзина
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::put('/cart/{id}', [CartController::class, 'updateCart']);
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);

});

Route::middleware('auth:sell')->group(function () {
    // Валидация
    Route::post('/upload/logo', [SellerAuthController::class, 'uploadLogo']);
    Route::post('/validate/inn', [VerifyController::class, 'validateInn']);
    Route::post('/validate/seller', [VerifyController::class, 'validateSeller']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Смена роли
    Route::post('/change_role/seller', [RoleController::class, 'seller']);
    Route::post('/change_role/customer', [RoleController::class, 'customer']);
    Route::post('/update-phone', [RoleController::class, 'phone']);
    // Заказы
    Route::post('/orders', [OrderController::class, 'create']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});


//Route::middleware('auth:sanctum')->group(function () {
//    Route::middleware('seller')->prefix('seller')->group(function () {
//        Route::get('/products', [SellerProductController::class, 'index']);
//        Route::post('/products', [SellerProductController::class, 'store']);
//        Route::put('/products/{product}', [SellerProductController::class, 'update']);
//        Route::delete('/products/{product}', [SellerProductController::class, 'destroy']);
//    });
//});


