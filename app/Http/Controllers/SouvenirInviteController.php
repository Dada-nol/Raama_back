<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SouvenirInviteController extends Controller
{
    public function joinFromToken(string $token)
    {
        $invite = SouvenirInvite::where('token', $token)->firstOrFail();

        // Option : vérifier expiration
        // if ($invite->expires_at && now()->gt($invite->expires_at)) { abort(410); }

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

        return redirect()->route('souvenirs.show', $souvenir->id);
    }

    public function handle($request, Closure $next)
    {
        if (Auth::check() && session()->has('pending_invite_token')) {
            $token = session()->pull('pending_invite_token');
            $invite = SouvenirInvite::where('token', $token)->first();

            if ($invite) {
                $souvenir = $invite->souvenir;
                if (!$souvenir->users->contains(Auth::id())) {
                    $souvenir->users()->attach(Auth::id());
                }
            }
        }

        return $next($request);
    }
}
