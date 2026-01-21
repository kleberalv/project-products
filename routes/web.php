<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\UserController;

/*
 |--------------------------------------------------------------------------
 | Rotas Web
 |--------------------------------------------------------------------------
 | Rotas servidas via Blade/sessao (middleware "web"). Defina aqui páginas
 | autenticadas e formulários. O RouteServiceProvider carrega este arquivo.
 |--------------------------------------------------------------------------
 */

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('produtos.index');
    });

    Route::resource('produtos', ProductWebController::class);

    Route::resource('usuarios', UserController::class);
});
