<?php

use App\Enums\RolesEnum;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/me', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['middleware' => ['role:'.RolesEnum::ADMIN->value]], function () {
        Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    });
});
