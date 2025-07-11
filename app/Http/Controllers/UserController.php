<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
  public function user(Request $request)
  {
    return $request->user();
  }

  public function register(Request $request)
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

    return ['user' => $user, 'token' => $token];
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    /*  if (! $user || ! Hash::check($request->password, $user->password)) {
      return response()->json(['message' => 'Identifiants incorrects'], 401);
    } */

    return [
      'token' => $user->createToken('mobile')->plainTextToken
    ];
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Déconnexion réussie']);
  }

  public function delete(Request $request)
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
