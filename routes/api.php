<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthorizeMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/register', [App\Http\Controllers\Api\Auth\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\Auth\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); });
    // Buku
    Route::apiResource('/bukus', App\Http\Controllers\Api\BukuController::class);
    Route::get('/buku/search', [App\Http\Controllers\Api\BukuController::class, 'index']);

});

// // Kategori
Route::apiResource('/kategori', App\Http\Controllers\Api\KategoriController::class)->middleware(['auth:sanctum', AdminMiddleware::class])->except('index');
Route::get('/kategori', [KategoriController::class, 'index'])->middleware('auth:sanctum');
