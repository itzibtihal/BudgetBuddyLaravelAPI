<?php

use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Route::apiResource('expenses', ExpenseController::class);
Route::apiResource('expenses', ExpenseController::class)->middleware('auth:sanctum');
