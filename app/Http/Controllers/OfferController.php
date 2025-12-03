<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Controlador de ofertas: crear, listar, actualizar estado y eliminar.
class OfferController extends Controller
{
    // Lista las ofertas recibidas de un anuncio del vendedor autenticado.
    public function index(Request $request, Listing $listing)
    {
        abort_unless($request->user()->id === $listing->user_id, 403);
        $offers = $listing->offers()->with('user:id,name')->latest()->paginate(20);
        return $request->wantsJson() ? response()->json($offers) : view('offers.index', compact('listing', 'offers'));
    }

    // Crea una oferta de un comprador sobre un anuncio y abre/usa la conversacion con el vendedor.
    public function store(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403, 'No puedes ofertar en tu propio anuncio');

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $offer = new Offer($validated);
        $offer->user_id = $request->user()->id;
        $offer->listing_id = $listing->id;
        $offer->status = 'pending';
        $offer->save();

        // Crear/encontrar conversación entre comprador y vendedor (sin enviar mensaje)
        $buyerId = $request->user()->id;
        $sellerId = $listing->user_id;
        $conversation = Conversation::query()
            ->where('listing_id', $listing->id)
            ->whereHas('users', fn ($q) => $q->where('users.id', $buyerId))
            ->whereHas('users', fn ($q) => $q->where('users.id', $sellerId))
            ->first();

        if (!$conversation) {
            $conversation = new Conversation(['listing_id' => $listing->id]);
            $conversation->save();
            $conversation->users()->sync([$buyerId, $sellerId]);
        }

        return $request->wantsJson()
            ? response()->json($offer->fresh('user'), 201)
            : redirect()->route('conversations.show', $conversation)->with('status', 'Oferta enviada');
    }

    // Actualiza el estado de una oferta (solo el vendedor del anuncio).
    public function updateStatus(Request $request, Offer $offer)
    {
        abort_unless($request->user()->id === $offer->listing->user_id, 403);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'accepted', 'rejected', 'withdrawn'])],
        ]);
        $offer->update(['status' => $validated['status']]);
        return $request->wantsJson() ? response()->json($offer) : back()->with('status', 'Estado de la oferta actualizado');
    }

    // Elimina una oferta hecha por el usuario autenticado.
    public function destroy(Request $request, Offer $offer)
    {
        abort_unless($request->user()->id === $offer->user_id, 403);
        $offer->delete();
        return $request->wantsJson() ? response()->json(['deleted' => true]) : back()->with('status', 'Oferta eliminada');
    }
}
