<?php

/**
 * Auth Routes
 */

use App\Http\Controllers\EntryController;
use App\Http\Controllers\MemoryTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\SouvenirInviteController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, "register"])->name('register');
Route::post('/login', [UserController::class, "login"])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, "logout"])->name('logout');
Route::middleware('auth:sanctum')->get('/user', [UserController::class, "user"]);
Route::middleware('auth:sanctum')->put('/user/{user}', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/user', [UserController::class, "delete"]);

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/souvenir', [SouvenirController::class, 'store']);
  Route::get('/souvenirs', [SouvenirController::class, 'index']);
  Route::get('/souvenir/{id}', [SouvenirController::class, 'show'])->name('souvenir.show');
  Route::put('/souvenir/{id}', [SouvenirController::class, 'update']);
  Route::delete('/souvenir/{souvenir}', [SouvenirController::class, 'delete']);
});

Route::get('/memory-type', [MemoryTypeController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/souvenir/{id}/entry', [EntryController::class, 'store']);
  Route::get('/souvenir/{id}/entry', [EntryController::class, 'index']);
});

Route::middleware(['auth:sanctum'])->get('/recent', [SouvenirController::class, 'recent']);

Route::middleware('auth:sanctum')->post('/souvenirs/{souvenir}/invite', [SouvenirInviteController::class, 'generateInvite']);
Route::middleware('auth:sanctum')->get('/invite/{token}', [SouvenirInviteController::class, 'joinFromToken'])->name('souvenirs.invite.show');

// Alternative
// Route::middleware('auth:sanctum')->apiResource('souvenir', SouvenirController::class);
