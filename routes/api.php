<?php

use App\Http\Controllers\Api\AddToCartController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Group Auth Anctum
Route::group(['middleware' => ['auth:sanctum']], function () { 
// Logout
Route::post('/logout', [AuthController::class, 'logout']);
});


// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//  Group Auth Sanctum Admin
Route::group(['middleware' => 'auth:sanctum', 'isAdmin', 'prefix' => 'admin'], function () {

    // Kategori Game
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Produk Game dengan kategori
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

// Group Auth Sanctum Customer
Route::group(['middleware' => ['auth:sanctum', 'isCustomer']], function () {
    Route::post('/add-to-cart', [AddToCartController::class, 'store']);
    Route::get('/add-to-cart', [AddToCartController::class, 'index']);
    Route::patch('/add-to-cart/{id}', [AddToCartController::class, 'update']);
    Route::delete('/add-to-cart/{id}', [AddToCartController::class, 'destroy']);

    // Checkout customer
    Route::post('/checkout', [CheckoutController::class, 'store']);
});
