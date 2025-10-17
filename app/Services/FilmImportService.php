<?php

namespace App\Services;

use App\Models\Film;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Language;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FilmImportService
{
    private OmdbService $omdbService;

    public function __construct(OmdbService $omdbService)
    {
        $this->omdbService = $omdbService;
    }

    /**
     * Importar película desde OMDB por IMDb ID
     */
    public function importFromOmdb(string $imdbId): array
    {
        try {
            // Verificar si la película ya existe
            $existingFilm = Film::where('imdb_id', $imdbId)->first();
            if ($existingFilm) {
                return [
                    'success' => false,
                    'error' => 'La película ya existe en la base de datos',
                    'film' => $existingFilm
                ];
            }

            // Obtener datos de OMDB
            $omdbResult = $this->omdbService->getMovieDetails($imdbId);
            
            if (!$omdbResult['success']) {
                return $omdbResult;
            }

            $movieData = $omdbResult['movie'];

            return DB::transaction(function () use ($movieData) {
                // 1. Procesar idiomas
                $language = $this->processLanguage($movieData['languages']);
                $originalLanguage = $language; // Por simplicidad, usar el mismo idioma

                // 2. Procesar categoría/género
                $category = $this->processCategory($movieData['genres']);

                // 3. Crear la película
                $film = $this->createFilm($movieData, $language, $originalLanguage, $category);

                // 4. Procesar actores
                $this->processActors($film, $movieData['actors']);

                // 5. Procesar directores
                $this->processDirectors($film, $movieData['director']);

                return [
                    'success' => true,
                    'film' => $film->load(['actors', 'directors', 'language', 'category']),
                    'message' => 'Película importada exitosamente'
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error importando película desde OMDB: ' . $e->getMessage(), [
                'imdb_id' => $imdbId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Error interno durante la importación: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesar idiomas de la película
     */
    private function processLanguage(array $languages): Language
    {
        if (empty($languages)) {
            // Idioma por defecto si no hay información
            return Language::firstOrCreate(
                ['code' => 'en'],
                ['name' => 'English', 'code' => 'en']
            );
        }

        $primaryLanguage = $languages[0];
        $languageCode = $this->getLanguageCode($primaryLanguage);
        
        return Language::firstOrCreate(
            ['code' => $languageCode],
            ['name' => $primaryLanguage, 'code' => $languageCode]
        );
    }

    /**
     * Obtener código ISO 639-1 para un idioma
     */
    private function getLanguageCode(string $languageName): string
    {
        $languageCodes = [
            'English' => 'en',
            'Spanish' => 'es',
            'French' => 'fr',
            'German' => 'de',
            'Italian' => 'it',
            'Portuguese' => 'pt',
            'Japanese' => 'ja',
            'Chinese' => 'zh',
            'Korean' => 'ko',
            'Russian' => 'ru',
            'Arabic' => 'ar',
            'Hindi' => 'hi',
            'Turkish' => 'tr',
            'Dutch' => 'nl',
            'Swedish' => 'sv',
            'Danish' => 'da',
            'Norwegian' => 'no',
            'Finnish' => 'fi',
            'Polish' => 'pl',
            'Czech' => 'cs',
            'Greek' => 'el',
            'Hebrew' => 'he',
            'Thai' => 'th',
            'Vietnamese' => 'vi',
            'Indonesian' => 'id',
            'Malay' => 'ms',
            'Romanian' => 'ro',
            'Hungarian' => 'hu',
            'Ukrainian' => 'uk',
            'Serbian' => 'sr',
            'Croatian' => 'hr',
            'Bulgarian' => 'bg',
            'Slovak' => 'sk',
            'Slovenian' => 'sl',
            'Lithuanian' => 'lt',
            'Latvian' => 'lv',
            'Estonian' => 'et',
            'Icelandic' => 'is',
            'Persian' => 'fa',
            'Urdu' => 'ur',
            'Bengali' => 'bn',
            'Tamil' => 'ta',
            'Telugu' => 'te',
            'Marathi' => 'mr',
            'Gujarati' => 'gu',
            'Kannada' => 'kn',
            'Malayalam' => 'ml',
            'Punjabi' => 'pa',
            'Swahili' => 'sw',
            'Afrikaans' => 'af',
            'Welsh' => 'cy',
            'Irish' => 'ga',
            'Basque' => 'eu',
            'Catalan' => 'ca',
            'Galician' => 'gl',
        ];

        // Si existe en el mapeo, retornar código
        if (isset($languageCodes[$languageName])) {
            return $languageCodes[$languageName];
        }

        // Fallback: usar primeras dos letras en minúsculas
        return strtolower(substr($languageName, 0, 2));
    }

    /**
     * Procesar categoría/género de la película
     */
    private function processCategory(array $genres): Category
    {
        if (empty($genres)) {
            // Categoría por defecto si no hay información
            return Category::firstOrCreate(
                ['name' => 'Other'],
                ['name' => 'Other']
            );
        }

        $primaryGenre = $genres[0];
        
        return Category::firstOrCreate(
            ['name' => $primaryGenre],
            ['name' => $primaryGenre]
        );
    }

    /**
     * Crear la película en la base de datos
     */
    private function createFilm(array $movieData, Language $language, Language $originalLanguage, Category $category): Film
    {
        $filmData = [
            'title' => $movieData['title'],
            'description' => $movieData['plot'] ?: 'Sin descripción disponible',
            'plot' => $movieData['plot'],
            'release_year' => $this->parseYear($movieData['year']),
            'language_id' => $language->language_id,
            'original_language_id' => $originalLanguage->language_id,
            'rental_duration' => 3, // Valor por defecto
            'rental_rate' => 4.99, // Valor por defecto
            'length' => $movieData['runtime'] ?: null,
            'replacement_cost' => 19.99, // Valor por defecto
            'rating' => $this->mapRating($movieData['rated']),
            'category_id' => $category->category_id,
            // Campos OMDB
            'imdb_id' => $movieData['imdb_id'],
            'poster_url' => $movieData['poster'],
            'imdb_rating' => $movieData['imdb_rating'],
            'imdb_votes' => $movieData['imdb_votes'],
            'metascore' => $movieData['metascore'],
            'awards' => $movieData['awards'],
            'box_office' => $movieData['box_office'],
            'production' => $movieData['production'],
            'website' => $movieData['website'],
            'writers' => $movieData['writers'],
            'countries' => $movieData['countries'],
            'imported_from_omdb' => true,
        ];

        return Film::create($filmData);
    }

    /**
     * Procesar actores de la película
     */
    private function processActors(Film $film, array $actorNames): void
    {
        foreach ($actorNames as $index => $actorName) {
            if (empty(trim($actorName))) {
                continue;
            }

            $actor = $this->findOrCreateActor($actorName);
            
            // Agregar a la relación muchos a muchos
            $film->actors()->attach($actor->actor_id, [
                'order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Procesar directores de la película
     */
    private function processDirectors(Film $film, string $directorNames): void
    {
        if (empty(trim($directorNames)) || $directorNames === 'N/A') {
            return;
        }

        // Los directores pueden venir separados por comas
        $directors = array_map('trim', explode(',', $directorNames));
        
        foreach ($directors as $directorName) {
            if (empty($directorName)) {
                continue;
            }

            $director = $this->findOrCreateDirector($directorName);
            
            // Agregar a la relación muchos a muchos
            $film->directors()->attach($director->director_id, [
                'role' => 'Director',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Buscar o crear actor
     */
    private function findOrCreateActor(string $fullName): Actor
    {
        // Dividir nombre completo en first_name y last_name
        $nameParts = explode(' ', trim($fullName), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        return Actor::firstOrCreate(
            [
                'first_name' => $firstName,
                'last_name' => $lastName
            ],
            [
                'first_name' => $firstName,
                'last_name' => $lastName
            ]
        );
    }

    /**
     * Buscar o crear director
     */
    private function findOrCreateDirector(string $fullName): Director
    {
        // Dividir nombre completo en first_name y last_name
        $nameParts = explode(' ', trim($fullName), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        return Director::firstOrCreate(
            [
                'first_name' => $firstName,
                'last_name' => $lastName
            ],
            [
                'first_name' => $firstName,
                'last_name' => $lastName
            ]
        );
    }

    /**
     * Parsear año de release
     */
    private function parseYear(string $year): ?int
    {
        // Extraer solo el año de strings como "2023", "2020-2021", etc.
        preg_match('/(\d{4})/', $year, $matches);
        return isset($matches[1]) ? (int) $matches[1] : null;
    }

    /**
     * Mapear rating de OMDB a formato local
     */
    private function mapRating(string $rated): string
    {
        $ratingMap = [
            'G' => 'G',
            'PG' => 'PG',
            'PG-13' => 'PG-13',
            'R' => 'R',
            'NC-17' => 'NC-17',
            'Not Rated' => 'G',
            'N/A' => 'G'
        ];

        return $ratingMap[$rated] ?? 'G';
    }

    /**
     * Previsualizar datos de importación sin guardar
     */
    public function previewImport(string $imdbId): array
    {
        try {
            $omdbResult = $this->omdbService->getMovieDetails($imdbId);
            
            if (!$omdbResult['success']) {
                return $omdbResult;
            }

            $movieData = $omdbResult['movie'];

            return [
                'success' => true,
                'preview' => [
                    'title' => $movieData['title'],
                    'year' => $movieData['year'],
                    'plot' => $movieData['plot'],
                    'genres' => $movieData['genres'],
                    'director' => $movieData['director'],
                    'actors' => array_slice($movieData['actors'], 0, 5), // Solo primeros 5
                    'poster' => $movieData['poster'],
                    'imdb_rating' => $movieData['imdb_rating'],
                    'runtime' => $movieData['runtime'],
                    'languages' => $movieData['languages']
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error previsualizando importación OMDB: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Error obteniendo previsualización'
            ];
        }
    }
}