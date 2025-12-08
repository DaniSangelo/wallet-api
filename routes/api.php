<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'create']);
    Route::prefix('wallet')->middleware([AuthMiddleware::class])->group(function ()  {
        Route::post('/', [WalletController::class, 'create']);
        Route::get('/balance', [WalletController::class, 'balance']);
        Route::patch('/balance/add', [WalletController::class, 'addBalance']);
        Route::patch('/balance/withdraw', [WalletController::class, 'withdraw']);
        Route::post('/balance/transfer', [WalletController::class, 'transfer']);
    });
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware([AuthMiddleware::class]);
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware([AuthMiddleware::class]);
});

