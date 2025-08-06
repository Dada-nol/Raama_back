<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\SouvenirInvite;

class JoinSouvenirIfPending
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && session()->has('pending_invite_token')) {
            $token = session()->pull('pending_invite_token');
            $invite = SouvenirInvite::where('token', $token)->first();

            if ($invite) {
                $souvenir = $invite->souvenir;

                if (!$souvenir->users()->where('user_id', Auth::id())->exists()) {
                    $souvenir->users()->attach(Auth::id());
                }
            }
        }

        return $next($request);
    }
}
