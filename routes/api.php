<?php

/**
 * Auth Routes
 */

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, "register"]);

Route::post('/login', [UserController::class, "login"]);

Route::middleware('auth:sanctum')->post('/logout', [UserController::class, "logout"]);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, "user"]);

Route::middleware('auth:sanctum')->delete('/delete', [UserController::class, "delete"]);
