<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

/*
 |--------------------------------------------------------------------------
 | Rotas API
 |--------------------------------------------------------------------------
 | Endpoints REST/JSON protegidos por Sanctum. Inclui login (rate limit)
 | e rotas autenticadas para usuarios e produtos. O RouteServiceProvider
 | carrega este arquivo com prefixo /api.
 |--------------------------------------------------------------------------
 */

Route::get('/', function () {
    return redirect('/api/documentation');
});

Route::prefix('auth')->middleware('throttle:6,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::prefix('produtos')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/trashed', [ProductController::class, 'trashed']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::post('/{id}/restore', [ProductController::class, 'restore']);
    });
});

