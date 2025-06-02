<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }
    
        // Générer un token de session
        $token = Str::uuid()->toString(); // ou Str::random(40);
    
        // Stocker la session dans ta table 'sessions'
        DB::table('sessions')->insert([
            'id' => $token,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => '', // vide ou des infos si tu veux
            'last_activity' => now()->timestamp,
        ]);
    
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }


    public function logout(Request $request)
    {
        $token = $request->bearerToken();
    
        if (! $token) {
            return response()->json(['message' => 'Aucun token fourni'], 400);
        }
    
        DB::table('sessions')->where('id', $token)->delete();
    
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
}
