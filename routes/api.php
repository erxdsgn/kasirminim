<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;

// Public Route (Login)
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Harus Menggunakan Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Route CRUD Buku dari Acara 25
Route::apiResource('books', BookController::class);
