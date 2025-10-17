<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->film_id,
            'title' => $this->title,
            'description' => $this->description,
            'release_year' => $this->release_year,
            'length' => $this->length,
            'rating' => $this->rating,
            'special_features' => $this->special_features,
            'replacement_cost' => $this->replacement_cost,
            'rental_rate' => $this->rental_rate,
            'rental_duration' => $this->rental_duration,
            
            // Relaciones
            'language' => [
                'id' => $this->whenLoaded('language', fn() => $this->language->language_id),
                'name' => $this->whenLoaded('language', fn() => $this->language->name),
            ],
            'original_language' => [
                'id' => $this->whenLoaded('originalLanguage', fn() => $this->originalLanguage?->language_id),
                'name' => $this->whenLoaded('originalLanguage', fn() => $this->originalLanguage?->name),
            ],
            'category' => [
                'id' => $this->whenLoaded('category', fn() => $this->category?->category_id),
                'name' => $this->whenLoaded('category', fn() => $this->category?->name),
            ],
            
            // OMDB data si está disponible
            'imdb_rating' => $this->imdb_rating,
            'imdb_votes' => $this->imdb_votes,
            'plot' => $this->plot,
            'poster_url' => $this->poster_url,
            'awards' => $this->awards,
            'country' => $this->country,
            'director' => $this->director,
            'writer' => $this->writer,
            'actors' => $this->actors,
            'genre' => $this->genre,
            'metascore' => $this->metascore,
            'imdb_id' => $this->imdb_id,
            'type' => $this->type,
            'box_office' => $this->box_office,
            'production' => $this->production,
            'website' => $this->website,
            
            // Metadatos
            'last_update' => $this->last_update?->toISOString(),
            
            // URLs
            'links' => [
                'self' => url("/api/v1/films/{$this->film_id}"),
                'inventory' => url("/api/v1/films/{$this->film_id}/inventory"),
            ],
        ];
    }
}
