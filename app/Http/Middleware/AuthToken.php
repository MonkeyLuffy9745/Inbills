<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token manquant'], 401);
        }

        $session = DB::table('sessions')->where('id', $token)->first();

        if (! $session || ! $session->user_id) {
            return response()->json(['message' => 'Token invalide ou expiré'], 401);
        }

        $user = User::find($session->user_id);

        if (! $user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 401);
        }

        // Authentifie manuellement l'utilisateur
        auth()->setUser($user);

        return $next($request);

    }
}
