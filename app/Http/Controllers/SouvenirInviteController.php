<?php

namespace App\Http\Controllers;

use App\Models\Souvenir;
use App\Models\SouvenirInvite;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class SouvenirInviteController extends Controller
{
    use AuthorizesRequests;

    public function generateInvite(Request $request, Souvenir $souvenir)
    {
        $user = $request->user();

        $souvenir->load('users');

        $role = $souvenir->users
            ->firstWhere('id', $user->id)?->pivot->role;

        if ($role !== "admin") {
            return response()->json(['message' => 'Vous n\'avez pas les permissions nécessaire pour faire cela'], 403);
        }

        $token = Str::uuid();

        SouvenirInvite::create([
            'souvenir_id' => $souvenir->id,
            'token' => $token,
            'expires_at' => now()->addDays(7),
        ]);

        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $inviteLink = $frontendUrl . '/invite/' . $token;

        return response()->json([
            'invite_link' => $inviteLink
        ], 201);
    }

    public function joinFromToken(string $token)
    {
        Log::info("joinFromToken called with token: $token");

        $invite = SouvenirInvite::where('token', $token)->first();

        if (!$invite) {
            Log::warning("Invite not found for token: $token");
            abort(404);
        }

        if ($invite->expires_at && now()->gt($invite->expires_at)) {
            Log::warning("Invite expired for token: $token");
            abort(410);
        }

        $souvenir = $invite->souvenir;
        Log::info("Found souvenir ID: {$souvenir->id}");

        $user = Auth::user();
        Log::info("User from Auth: " . ($user ? $user->id : 'null'));

        if (!$user) {
            Log::info("User not logged in, redirecting to login");
            return response()->json(401);
        }

        if (!$souvenir->users->contains($user->id)) {
            $souvenir->users()->attach($user->id);
            Log::info("User attached to souvenir");
        } else {
            Log::info("User already member of souvenir");
        }

        return response()->json([
            'souvenir_id' => $souvenir->id
        ]);
    }
}
