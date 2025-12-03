<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Lista los anuncios marcados como favoritos por el usuario autenticado
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->with('user:id,name', 'category:id,name', 'images')->paginate(20);
        return $request->wantsJson() ? response()->json($favorites) : view('favorites.index', compact('favorites'));
    }

    // Anade un anuncio a favoritos del usuario sin eliminar otros existentes
    public function store(Request $request, Listing $listing)
    {
        $request->user()->favorites()->syncWithoutDetaching([$listing->id]);
        return $request->wantsJson() ? response()->json(['favorited' => true]) : back()->with('status', 'Añadido a favoritos');
    }

    // Quita un anuncio de los favoritos del usuario
    public function destroy(Request $request, Listing $listing)
    {
        $request->user()->favorites()->detach($listing->id);
        return $request->wantsJson() ? response()->json(['favorited' => false]) : back()->with('status', 'Eliminado de favoritos');
    }
}
