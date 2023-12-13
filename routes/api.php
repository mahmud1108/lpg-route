<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\AdminMiddleware;
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
Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/reset-password', [UserController::class, 'reset_password']);
Route::post('/user/reset-password/{token}', [UserController::class, 'reset_action'])->name('reset-action');

Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/register', [AdminController::class, 'register']);

Route::middleware(UserMiddleware::class)->prefix('user')->group(function () {
    Route::put('/update', [UserController::class, 'update']);
    Route::delete('/logout', [UserController::class, 'logout']);
});

Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
    Route::put('/update', [AdminController::class, 'update']);
    Route::delete('/logout', [AdminController::class, 'logout']);

    Route::post('/location', [LocationController::class, 'add_location']);
    Route::put('/location/{location_id}', [LocationController::class, 'update']);
    Route::get('/location/', [LocationController::class, 'get_all']);
    Route::get('/location/{location_id}', [LocationController::class, 'get_one']);
    Route::delete('/location/{lcoation_id}', [LocationController::class, 'delete']);
});
