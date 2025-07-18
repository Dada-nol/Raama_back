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
Route::middleware('auth:sanctum')->put('/user/{user}', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/user', [UserController::class, "delete"]);

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/souvenir', [SouvenirController::class, 'store']);
  Route::get('/souvenirs', [SouvenirController::class, 'index']);
  Route::get('/souvenir/{id}', [SouvenirController::class, 'show']);
  Route::put('/souvenir/{id}', [SouvenirController::class, 'update']);
  Route::delete('/souvenir/{souvenir}', [SouvenirController::class, 'delete']);
});

// Alternative
// Route::middleware('auth:sanctum')->apiResource('souvenir', SouvenirController::class);
