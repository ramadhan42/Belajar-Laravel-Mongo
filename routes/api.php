<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParfumController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route yang membutuhkan autentikasi (harus bawa token)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    // Masukkan route API lain di sini, misalnya untuk ambil data produk/keranjang

    // ==========================================
    // CARTS ROUTES
    // ==========================================
    // Mendapatkan daftar keranjang user
    Route::get('/carts', [CartController::class, 'index']);

    // Menambahkan produk ke keranjang
    Route::post('/carts', [CartController::class, 'store']);

    // Menghapus produk dari keranjang berdasarkan ID keranjang
    Route::delete('/carts/{id}', [CartController::class, 'destroy']);

    // ==========================================
    // ORDERS & CHECKOUT ROUTES
    // ==========================================
    // Melakukan proses checkout dari keranjang menjadi order
    Route::post('/checkout', [OrderController::class, 'checkout']);

    // Melihat detail order yang spesifik
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);      // Get semua user
    Route::get('/{id}', [UserController::class, 'show']);   // Get user by ID
    Route::put('/{id}', [UserController::class, 'update']); // Update user

    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user

    // Route untuk Create User
    Route::post('/', [UserController::class, 'store']);
});

Route::prefix('parfum')->group(function () {
    Route::get('/', [ParfumController::class, 'index']);
    Route::post('/', [ParfumController::class, 'store']);

    Route::get('/{id}', [ParfumController::class, 'show']);
    Route::post('/{id}', [ParfumController::class, 'update']);

    Route::delete('/{id}', [ParfumController::class, 'destroy']);
});

Route::post('/parfum/{parfum_id}/upload/{produk_id}', [ParfumController::class, 'uploadPerProduk']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('movies', MovieController::class)->only([
    'store',
]);
