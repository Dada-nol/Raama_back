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
    $user =  $request->user();

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
      'password' => 'required|min:8|confirmed'
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

  public function update(Request $request, User $user): JsonResponse
  {
    $request->validate([
      'name' => 'string|max:255',
      'firstname' => 'string|max:255',
      'pseudo' => 'string|max:255|unique:users,pseudo,' . $user->id,
      'email' => 'email|unique:users,email,' . $user->id,
      'password' => 'nullable|min:8|confirmed'
    ]);

    if ($request->filled('password')) {
      if (!$request->filled('old_password')) {
        return response()->json(['message' => 'Le mot de passe actuel est requis.'], 400);
      }

      if (!Hash::check($request->old_password, $user->password)) {
        return response()->json(['message' => 'Mot de passe actuel incorrect.'], 401);
      }

      // Si tout est bon, on change le mot de passe
      $user->password = bcrypt($request->password);
    }

    $user->update([
      'name' => $request->name,
      'firstname' => $request->firstname,
      'pseudo' => $request->pseudo,
      'email' => $request->email,
    ]);

    return response()->json(['message' => 'Utilisateur mis à jour', 'user' => $user]);
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
