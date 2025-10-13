<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OmdbService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.omdb.api_key');
        $this->baseUrl = config('services.omdb.base_url');
    }

    /**
     * Buscar películas por título
     */
    public function searchByTitle(string $title, int $page = 1): ?array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'apikey' => $this->apiKey,
                's' => $title,
                'page' => $page,
                'type' => 'movie'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['Response'] === 'True') {
                    return [
                        'success' => true,
                        'movies' => $data['Search'] ?? [],
                        'total_results' => $data['totalResults'] ?? 0,
                        'current_page' => $page
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => $data['Error'] ?? 'No se encontraron resultados'
                ];
            }

            return [
                'success' => false,
                'error' => 'Error en la comunicación con OMDB API'
            ];

        } catch (\Exception $e) {
            Log::error('Error en OMDB searchByTitle: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor'
            ];
        }
    }

    /**
     * Obtener detalles completos de una película por IMDb ID
     */
    public function getMovieDetails(string $imdbId): ?array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'apikey' => $this->apiKey,
                'i' => $imdbId,
                'plot' => 'full'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['Response'] === 'True') {
                    return [
                        'success' => true,
                        'movie' => $this->formatMovieData($data)
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => $data['Error'] ?? 'Película no encontrada'
                ];
            }

            return [
                'success' => false,
                'error' => 'Error en la comunicación con OMDB API'
            ];

        } catch (\Exception $e) {
            Log::error('Error en OMDB getMovieDetails: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error interno del servidor'
            ];
        }
    }

    /**
     * Formatear datos de película para uso interno
     */
    private function formatMovieData(array $omdbData): array
    {
        return [
            'imdb_id' => $omdbData['imdbID'],
            'title' => $omdbData['Title'],
            'year' => $omdbData['Year'],
            'rated' => $omdbData['Rated'] ?? 'N/A',
            'released' => $omdbData['Released'] ?? null,
            'runtime' => $this->parseRuntime($omdbData['Runtime'] ?? '0 min'),
            'genres' => $this->parseGenres($omdbData['Genre'] ?? ''),
            'director' => $omdbData['Director'] ?? 'N/A',
            'writers' => $this->parseWriters($omdbData['Writer'] ?? ''),
            'actors' => $this->parseActors($omdbData['Actors'] ?? ''),
            'plot' => $omdbData['Plot'] ?? '',
            'languages' => $this->parseLanguages($omdbData['Language'] ?? ''),
            'countries' => $this->parseCountries($omdbData['Country'] ?? ''),
            'awards' => $omdbData['Awards'] ?? '',
            'poster' => $omdbData['Poster'] !== 'N/A' ? $omdbData['Poster'] : null,
            'metascore' => $omdbData['Metascore'] !== 'N/A' ? $omdbData['Metascore'] : null,
            'imdb_rating' => $omdbData['imdbRating'] !== 'N/A' ? $omdbData['imdbRating'] : null,
            'imdb_votes' => $omdbData['imdbVotes'] !== 'N/A' ? str_replace(',', '', $omdbData['imdbVotes']) : null,
            'box_office' => $omdbData['BoxOffice'] ?? null,
            'production' => $omdbData['Production'] ?? null,
            'website' => $omdbData['Website'] !== 'N/A' ? $omdbData['Website'] : null,
        ];
    }

    /**
     * Convertir runtime de texto a minutos
     */
    private function parseRuntime(string $runtime): int
    {
        preg_match('/(\d+)/', $runtime, $matches);
        return isset($matches[1]) ? (int) $matches[1] : 0;
    }

    /**
     * Parsear géneros separados por comas
     */
    private function parseGenres(string $genres): array
    {
        if (empty($genres) || $genres === 'N/A') {
            return [];
        }
        
        return array_map('trim', explode(',', $genres));
    }

    /**
     * Parsear escritores separados por comas
     */
    private function parseWriters(string $writers): array
    {
        if (empty($writers) || $writers === 'N/A') {
            return [];
        }
        
        return array_map('trim', explode(',', $writers));
    }

    /**
     * Parsear actores separados por comas
     */
    private function parseActors(string $actors): array
    {
        if (empty($actors) || $actors === 'N/A') {
            return [];
        }
        
        return array_map('trim', explode(',', $actors));
    }

    /**
     * Parsear idiomas separados por comas
     */
    private function parseLanguages(string $languages): array
    {
        if (empty($languages) || $languages === 'N/A') {
            return [];
        }
        
        return array_map('trim', explode(',', $languages));
    }

    /**
     * Parsear países separados por comas
     */
    private function parseCountries(string $countries): array
    {
        if (empty($countries) || $countries === 'N/A') {
            return [];
        }
        
        return array_map('trim', explode(',', $countries));
    }

    /**
     * Verificar si la API está configurada correctamente
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }
}