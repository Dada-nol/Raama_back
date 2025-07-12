<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
  public function user(Request $request): JsonResponse
  {
    return $request->user();

    if (! $user) {
      return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    return response()->json($user);
  }

  public function register(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required',
      'firstname' => 'required',
      'pseudo' => 'required|unique:users',
      'email' => 'required|email|unique:users',
      'password' => 'required|confirmed'
    ]);

    $user = User::create([
      'name' => $request->name,
      'firstname' => $request->firstname,
      'pseudo' => $request->pseudo,
      'email' => $request->email,
      'password' => bcrypt($request->password)
    ]);

    $token = $user->createToken('mobile')->plainTextToken;

    return response()->json([
      'user' => $user,
      'token' => $token,
    ]);
  }

  public function login(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
      return response()->json(['message' => 'Identifiants incorrects'], 401);
    }

    return response()->json([
      'token' => $user->createToken('mobile')->plainTextToken
    ]);
  }

  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Déconnexion réussie']);
  }

  public function delete(Request $request): JsonResponse
  {
    $user = $request->user();

    if (! $user) {
      throw ValidationException::withMessages([
        'email' => ['Les informations sont invalides.'],
      ]);
    }
    $request->user()->currentAccessToken()->delete();

    $user->delete();

    return response()->json(['message' => 'Suppression réussie']);
  }
}
