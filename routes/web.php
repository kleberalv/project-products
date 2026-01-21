<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
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
