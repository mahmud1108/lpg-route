<?php

use App\Http\Controllers\User\UserController;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/registrasi', [UserController::class, 'register']);

Route::middleware(UserMiddleware::class)->prefix('user')->group(function () {
    Route::put('/update', [UserController::class, 'update']);
    Route::delete('/logout', [UserController::class, 'logout']);
});
