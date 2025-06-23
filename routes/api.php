<?php

/**
 * Auth Routes
 */

use App\Http\Controllers\UserController;
use App\Http\Controllers\SouvenirController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, "register"]);
Route::post('/login', [UserController::class, "login"]);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, "logout"]);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, "user"]);
Route::middleware('auth:sanctum')->delete('/user', [UserController::class, "delete"]);

Route::post('/souvenir-create', [SouvenirController::class, "store"]);
Route::get('/souvenir', [SouvenirController::class, "show"]);
Route::put('/souvenir-update', [SouvenirController::class, "update"]);
Route::delete('/souvenir-delete', [SouvenirController::class, "delete"]);
