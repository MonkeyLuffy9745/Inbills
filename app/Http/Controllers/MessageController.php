<?php

namespace App\Http\Controllers;

use App\Mail\MessageFromUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Client;


class MessageController extends Controller
{
    public function sendToClient(Request $request)
    {
        $request->validate([
            'client_email' => 'required|email|exists:clients,email',
            'message' => 'required|string',
        ]);

        $client = Client::where('email', $request->client_email)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

        $userEmail = auth()->user()->email;
        Mail::to($client->email)->send(new MessageFromUser($request->message, $userEmail));

        return response()->json(['message' => 'Email envoyé avec succès.'], 200);
    }
}
