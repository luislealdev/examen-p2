<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        // Obtener películas con información de OMDB
        $films = Film::with(['language', 'category'])
            ->whereNotNull('imdb_id') // Aseguramos que tenga info de OMDB
            ->orderBy('title')
            ->paginate(12); // 12 películas por página

        return view('catalog.index', compact('films'));
    }
}