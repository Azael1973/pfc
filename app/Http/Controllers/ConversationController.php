<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;

//Controlador para listar, ver, crear y eliminar conversaciones entre usuarios.
class ConversationController extends Controller
{
    // Lista las conversaciones del usuario autenticado con sus participantes y anuncio, ordenadas y paginadas.
    public function index(Request $request)
    {
        $conversations = $request->user()->conversations()
            ->with(['users:id,name', 'listing:id,title'])
            ->latest('conversations.updated_at')
            ->paginate(20);

        return $request->wantsJson()
            ? response()->json($conversations)
            : view('conversations.index', compact('conversations'));
    }

    // Muestra una conversación si el usuario participa, cargando usuarios, anuncio y ofertas relacionadas.
    public function show(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->users()->where('users.id', $request->user()->id)->exists(), 403);
        $conversation->load([
            'users:id,name',
            'listing' => function ($q) {
                $q->select('id', 'user_id', 'title')
                    ->with(['offers' => function ($qq) {
                        $qq->select('id', 'listing_id', 'user_id', 'amount', 'status', 'created_at')
                           ->with('user:id,name');
                    }]);
            },
        ]);
        return $request->wantsJson() ? response()->json($conversation) : view('conversations.show', compact('conversation'));
    }

    // Crea una conversación a partir de un anuncio y asocia a los participantes.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => ['nullable', 'exists:listings,id'],
            'user_id' => ['nullable', 'exists:users,id'], // con quién hablar (si no hay listing)
        ]);

        $conversation = new Conversation([
            'listing_id' => $validated['listing_id'] ?? null,
        ]);
        $conversation->save();

        $participants = [$request->user()->id];

        if (!empty($validated['listing_id'])) {
            $ownerId = Listing::query()->whereKey($validated['listing_id'])->value('user_id');
            if ($ownerId) { $participants[] = $ownerId; }
        } elseif (!empty($validated['user_id'])) {
            if ($validated['user_id'] !== $request->user()->id) {
                $participants[] = $validated['user_id'];
            }
        }

        $participants = array_values(array_unique($participants));
        if (count($participants) < 2) {
            return response()->json(['message' => 'No se puede crear una conversación sin otro participante'], 422);
        }

        $conversation->users()->sync($participants);

        return $request->wantsJson()
            ? response()->json($conversation->load('users:id,name'), 201)
            : redirect()->route('conversations.show', $conversation);
    }

    // Elimina la conversación si el usuario autenticado forma parte de ella.
    public function destroy(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->users()->where('users.id', $request->user()->id)->exists(), 403);

        $conversation->delete();

        return $request->wantsJson()
            ? response()->json(['deleted' => true])
            : redirect()->route('conversations.index')->with('status', 'Chat eliminado');
    }
}
