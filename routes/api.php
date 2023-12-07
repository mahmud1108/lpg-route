<?php

use App\Http\Controllers\User\UserController;
use App\Http\Middleware\UserMiddleware;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
Route::post('/user/reset-password', [UserController::class, 'reset_password']);
Route::post('/user/reset-password/{token}', [UserController::class, 'reset_action'])->name('reset-action');

Route::middleware(UserMiddleware::class)->prefix('user')->group(function () {
    Route::put('/update', [UserController::class, 'update']);
    Route::delete('/logout', [UserController::class, 'logout']);
});
