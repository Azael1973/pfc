<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Prepara y muestra la página principal con listados recientes vistos, categorías destacadas y últimas publicaciones
    public function index(Request $request)
    {
        $recentIds = array_filter($request->session()->get('recent_listings', []), 'is_numeric');

        $recentListings = [];
        if (!empty($recentIds)) {
            $recentListings = Listing::query()
                ->with(['images', 'category'])
                ->whereIn('id', $recentIds)
                ->orderByRaw('FIELD(id,'.implode(',', $recentIds).')')
                ->get();
        }

        $categories = Category::select('id', 'name', 'slug')
            ->orderBy('name')
            ->limit(4)
            ->get();

        $recentUploads = Listing::query()
            ->where('status', '!=', 'sold')
            ->with(['images', 'category'])
            ->latest()
            ->limit(20) // 5 filas x 4 columnas
            ->get();

        return view('home', [
            'recentListings' => $recentListings,
            'categories' => $categories,
            'recentUploads' => $recentUploads,
        ]);
    }
}
