<?php

/**
 * Auth Routes
 */

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::post('/register', function (Request $request) {
  $request->validate([
    'name' => 'required',
    'email' => 'required|email|unique:users',
    'password' => 'required|confirmed'
  ]);

  $user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => bcrypt($request->password)
  ]);

  return response()->json($user, 201);
});

Route::post('/login', function (Request $request) {
  $request->validate([
    'email' => 'required|email',
    'password' => 'required'
  ]);

  $user = User::where('email', $request->email)->first();

  if (! $user || ! Hash::check($request->password, $user->password)) {
    throw ValidationException::withMessages([
      'email' => ['Les informations sont invalides.'],
    ]);
  }

  return [
    'token' => $user->createToken('mobile')->plainTextToken
  ];
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
  $request->user()->currentAccessToken()->delete();

  return response()->json(['message' => 'Déconnexion réussie']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:sanctum')->delete('/delete', function (Request $request) {
  $user = $request->user();

  if (! $user) {
    throw ValidationException::withMessages([
      'email' => ['Les informations sont invalides.'],
    ]);
  }
  $user->delete();

  return response()->json(['message' => 'Suppression réussie']);
});
