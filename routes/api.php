<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('x-api-key')->group(function () {
    Route::post('/users', [UserController::class, 'createOrLoginUser']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/users')->group(function () {
            Route::prefix('/{id}')->group(function () {
                Route::get('', [UserController::class, 'getUser']);
                Route::post('', [UserController::class, 'editUser']);
                Route::prefix('/transactions')->group(function () {
                    Route::prefix('/transfer')->group(function () {
                        Route::get('', [TransactionController::class, 'getUserTransferTransactions']);
                        Route::post('', [TransactionController::class, 'createUserTransferTransaction']);
                    });
                });
            });
        });
    });
});
