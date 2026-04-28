<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\Api\ParfumController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Api\AuthController;

// 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route yang membutuhkan autentikasi (harus bawa token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Masukkan route API lain di sini, misalnya untuk ambil data produk/keranjang
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
    'store'
]);
