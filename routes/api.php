<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'create']);
    Route::prefix('wallet')->group(function ()  {
        Route::post('/', [WalletController::class, 'create']);
        Route::get('/balance', [WalletController::class, 'balance']);
        Route::patch('/balance/add', [WalletController::class, 'addBalance']);
        Route::patch('/balance/withdraw', [WalletController::class, 'withdraw']);
    })->middleware([]); //todo: add auth middlware
});

