<?php

namespace App\Http\Controllers;

use App\Models\Souvenir;
use App\Models\SouvenirInvite;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
        // Autoriser uniquement le créateur ou un membre à inviter
        // $this->authorize('invite', $souvenir);

        $token = Str::uuid();

        SouvenirInvite::create([
            'souvenir_id' => $souvenir->id,
            'token' => $token,
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'invite_link' => route('souvenirs.invite.show', ['token' => $token])
        ], 201);
    }

    public function joinFromToken(string $token)
    {
        $invite = SouvenirInvite::where('token', $token)->firstOrFail();

        if ($invite->expires_at && now()->gt($invite->expires_at)) {
            abort(410);
        }

        $souvenir = $invite->souvenir;

        $user = Auth::user();

        if (!$user) {
            // Stocker le token en session avant redirection login
            session(['pending_invite_token' => $token]);
            return redirect()->route('login');
        }

        // Ajouter le user comme membre s’il ne l’est pas déjà
        if (!$souvenir->users->contains($user->id)) {
            $souvenir->users()->attach($user->id);
        }

        return redirect()->route('souvenir.show', $souvenir->id);
    }
}
