<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Lista mensajes de una conversación si el usuario participa.
    public function index(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->users()->where('users.id', $request->user()->id)->exists(), 403);
        $messages = $conversation->messages()->with('user:id,name')->latest()->paginate(30);
        return $request->wantsJson() ? response()->json($messages) : view('messages.index', compact('conversation', 'messages'));
    }

    // Guarda un nuevo mensaje en la conversación del usuario autenticado y actualiza la marca de tiempo.
    public function store(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->users()->where('users.id', $request->user()->id)->exists(), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = new Message([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'read' => false,
        ]);

        $conversation->messages()->save($message);
        $conversation->touch();

        return $request->wantsJson() ? response()->json($message->load('user:id,name'), 201) : back();
    }
}
