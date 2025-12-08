<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'create']);
    Route::prefix('wallet')->group(function ()  {
        Route::post('/', [WalletController::class, 'create']);
    })->middleware([]); //todo: add auth middlware
});

