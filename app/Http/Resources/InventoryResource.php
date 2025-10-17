<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->inventory_id,
            'film_id' => $this->film_id,
            'store_id' => $this->store_id,
            'condition' => $this->condition,
            'condition_label' => $this->getConditionLabel(),
            'condition_updated_at' => $this->condition_updated_at?->toISOString(),
            'condition_notes' => $this->condition_notes,
            'is_available' => $this->isAvailable(),
            'is_rentable' => $this->canBeRented(),
            'last_update' => $this->last_update?->toISOString(),
            
            // Relaciones
            'film' => $this->whenLoaded('film', function() {
                return [
                    'id' => $this->film->film_id,
                    'title' => $this->film->title,
                    'rating' => $this->film->rating,
                    'rental_rate' => $this->film->rental_rate,
                ];
            }),
            'store' => [
                'id' => $this->whenLoaded('store', fn() => $this->store->store_id),
                'address' => $this->whenLoaded('store.address', fn() => [
                    'address' => $this->store->address->address,
                    'city' => $this->store->address->city?->city,
                    'country' => $this->store->address->city?->country?->country,
                ]),
            ],
            
            // Estado actual
            'current_rental' => $this->when($this->currentRental(), [
                'rental_id' => $this->currentRental()?->rental_id,
                'rental_date' => $this->currentRental()?->rental_date?->toISOString(),
                'customer_id' => $this->currentRental()?->customer_id,
            ]),
            
            // URLs
            'links' => [
                'self' => url("/api/v1/inventory/{$this->inventory_id}"),
                'film' => url("/api/v1/films/{$this->film_id}"),
                'movements' => url("/api/v1/inventory/movements?inventory_id={$this->inventory_id}"),
            ],
        ];
    }
}
