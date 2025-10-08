<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OmdbService;
use App\Services\FilmImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OmdbController extends Controller
{
    private OmdbService $omdbService;
    private FilmImportService $filmImportService;

    public function __construct(OmdbService $omdbService, FilmImportService $filmImportService)
    {
        $this->omdbService = $omdbService;
        $this->filmImportService = $filmImportService;
    }

    /**
     * Mostrar página de búsqueda OMDB
     */
    public function search(): View
    {
        return view('films.omdb-search');
    }

    /**
     * Buscar películas en OMDB API
     */
    public function searchMovies(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'page' => 'integer|min:1|max:100'
        ]);

        if (!$this->omdbService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'OMDB API no está configurada correctamente'
            ], 500);
        }

        $result = $this->omdbService->searchByTitle(
            $request->input('query'),
            $request->input('page', 1)
        );

        return response()->json($result);
    }

    /**
     * Obtener detalles de una película específica
     */
    public function getMovieDetails(Request $request): JsonResponse
    {
        $request->validate([
            'imdb_id' => 'required|string|regex:/^tt\d+$/'
        ]);

        if (!$this->omdbService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'OMDB API no está configurada correctamente'
            ], 500);
        }

        $result = $this->omdbService->getMovieDetails($request->input('imdb_id'));

        return response()->json($result);
    }

    /**
     * Previsualizar importación de película
     */
    public function previewImport(Request $request): JsonResponse
    {
        $request->validate([
            'imdb_id' => 'required|string|regex:/^tt\d+$/'
        ]);

        $result = $this->filmImportService->previewImport($request->input('imdb_id'));

        return response()->json($result);
    }

    /**
     * Importar película desde OMDB
     */
    public function importMovie(Request $request): JsonResponse
    {
        $request->validate([
            'imdb_id' => 'required|string|regex:/^tt\d+$/'
        ]);

        if (!$this->omdbService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'OMDB API no está configurada correctamente'
            ], 500);
        }

        $result = $this->filmImportService->importFromOmdb($request->input('imdb_id'));

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'film' => $result['film'],
                'redirect_url' => route('films.show', $result['film']->film_id)
            ]);
        }

        return response()->json($result, 400);
    }

    /**
     * Verificar estado de configuración OMDB
     */
    public function checkConfiguration(): JsonResponse
    {
        return response()->json([
            'configured' => $this->omdbService->isConfigured(),
            'message' => $this->omdbService->isConfigured() 
                ? 'OMDB API configurada correctamente' 
                : 'Necesita configurar OMDB_API_KEY en el archivo .env'
        ]);
    }
}
