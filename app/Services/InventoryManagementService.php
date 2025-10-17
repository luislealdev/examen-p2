<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryManagementService
{
    /**
     * Process a rental return
     */
    public function processReturn(int $rentalId, string $condition = 'available', string $notes = null): array
    {
        try {
            DB::beginTransaction();

            $rental = Rental::with('inventory')->findOrFail($rentalId);
            
            // Validate rental can be returned
            if ($rental->isReturned()) {
                throw new Exception('Esta renta ya ha sido devuelta.');
            }

            // Update rental return date
            $rental->update(['return_date' => now()]);

            // Update inventory condition if needed
            if ($rental->inventory->condition !== $condition) {
                $rental->inventory->updateCondition($condition, $notes, [
                    'returned_from_rental' => $rentalId,
                    'previous_condition' => $rental->inventory->condition,
                ]);
            }

            // Log the return movement
            InventoryMovement::create([
                'inventory_id' => $rental->inventory->inventory_id,
                'rental_id' => $rentalId,
                'movement_type' => 'return',
                'condition_from' => 'available', // Assumed rented in good condition
                'condition_to' => $condition,
                'user_id' => auth()->id(),
                'staff_id' => session('staff_id'),
                'customer_id' => $rental->customer_id,
                'notes' => $notes,
                'metadata' => [
                    'rental_date' => $rental->rental_date,
                    'days_rented' => now()->diffInDays($rental->rental_date),
                ],
            ]);

            // Log business activity
            BusinessActivityLogger::logInventory('return', $rental->inventory->inventory_id, 
                $rental->inventory->film_id, $rental->inventory->store_id, [
                'rental_id' => $rentalId,
                'customer_id' => $rental->customer_id,
                'film_title' => $rental->inventory->film->title ?? 'Unknown',
                'condition' => $condition,
                'notes' => $notes,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Devolución procesada exitosamente.',
                'rental' => $rental,
                'inventory' => $rental->inventory->fresh(),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing return: ' . $e->getMessage(), [
                'rental_id' => $rentalId,
                'condition' => $condition,
                'notes' => $notes,
            ]);

            return [
                'success' => false,
                'message' => 'Error al procesar la devolución: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mark inventory item as damaged
     */
    public function markAsDamaged(int $inventoryId, string $notes = null, array $metadata = []): array
    {
        try {
            DB::beginTransaction();

            $inventory = Inventory::findOrFail($inventoryId);
            
            // Check if item is currently rented
            if (!$inventory->isAvailable()) {
                throw new Exception('No se puede marcar como dañada una película que está rentada.');
            }

            $oldCondition = $inventory->condition;
            $inventory->updateCondition('damaged', $notes, array_merge($metadata, [
                'marked_by' => auth()->user()->name ?? 'System',
                'marked_at' => now()->toISOString(),
            ]));

            // Log business activity
            BusinessActivityLogger::logInventory('damage', $inventoryId, 
                $inventory->film_id, $inventory->store_id, [
                'film_title' => $inventory->film->title ?? 'Unknown',
                'previous_condition' => $oldCondition,
                'notes' => $notes,
                'metadata' => $metadata,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Película marcada como dañada exitosamente.',
                'inventory' => $inventory->fresh(),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error marking as damaged: ' . $e->getMessage(), [
                'inventory_id' => $inventoryId,
                'notes' => $notes,
            ]);

            return [
                'success' => false,
                'message' => 'Error al marcar como dañada: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mark inventory item as lost
     */
    public function markAsLost(int $inventoryId, string $notes = null, array $metadata = []): array
    {
        try {
            DB::beginTransaction();

            $inventory = Inventory::findOrFail($inventoryId);
            
            // Check if item is currently rented
            $currentRental = $inventory->currentRental();
            if ($currentRental) {
                // If it's currently rented, we may want to handle this differently
                $metadata['lost_while_rented'] = true;
                $metadata['rental_id'] = $currentRental->rental_id;
                $metadata['customer_id'] = $currentRental->customer_id;
            }

            $oldCondition = $inventory->condition;
            $inventory->updateCondition('lost', $notes, array_merge($metadata, [
                'marked_by' => auth()->user()->name ?? 'System',
                'marked_at' => now()->toISOString(),
            ]));

            // Log business activity
            BusinessActivityLogger::logInventory('loss', $inventoryId, 
                $inventory->film_id, $inventory->store_id, [
                'film_title' => $inventory->film->title ?? 'Unknown',
                'previous_condition' => $oldCondition,
                'was_rented' => !is_null($currentRental),
                'rental_info' => $currentRental ? [
                    'rental_id' => $currentRental->rental_id,
                    'customer_id' => $currentRental->customer_id,
                    'rental_date' => $currentRental->rental_date,
                ] : null,
                'notes' => $notes,
                'metadata' => $metadata,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Película marcada como perdida exitosamente.',
                'inventory' => $inventory->fresh(),
                'was_rented' => !is_null($currentRental),
                'rental_info' => $currentRental,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error marking as lost: ' . $e->getMessage(), [
                'inventory_id' => $inventoryId,
                'notes' => $notes,
            ]);

            return [
                'success' => false,
                'message' => 'Error al marcar como perdida: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Repair/restore inventory item to available condition
     */
    public function repairItem(int $inventoryId, string $notes = null): array
    {
        try {
            DB::beginTransaction();

            $inventory = Inventory::findOrFail($inventoryId);
            
            if ($inventory->isInGoodCondition()) {
                throw new Exception('La película ya está en buenas condiciones.');
            }

            if ($inventory->isLost()) {
                throw new Exception('No se puede reparar una película perdida. Considere reponerla.');
            }

            $oldCondition = $inventory->condition;
            $inventory->updateCondition('available', $notes, [
                'repaired_by' => auth()->user()->name ?? 'System',
                'repaired_at' => now()->toISOString(),
                'previous_condition' => $oldCondition,
            ]);

            // Log business activity
            BusinessActivityLogger::logInventory('repair', $inventoryId, 
                $inventory->film_id, $inventory->store_id, [
                'film_title' => $inventory->film->title ?? 'Unknown',
                'previous_condition' => $oldCondition,
                'notes' => $notes,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Película reparada y restaurada exitosamente.',
                'inventory' => $inventory->fresh(),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error repairing item: ' . $e->getMessage(), [
                'inventory_id' => $inventoryId,
                'notes' => $notes,
            ]);

            return [
                'success' => false,
                'message' => 'Error al reparar la película: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get inventory movements for a specific item
     */
    public function getInventoryMovements(int $inventoryId, int $limit = 50): array
    {
        $inventory = Inventory::with(['film', 'store'])->findOrFail($inventoryId);
        
        $movements = InventoryMovement::with(['rental.customer', 'user', 'staff', 'customer'])
            ->where('inventory_id', $inventoryId)
            ->orderBy('movement_date', 'desc')
            ->limit($limit)
            ->get();

        return [
            'inventory' => $inventory,
            'movements' => $movements,
        ];
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStats(): array
    {
        return [
            'total' => Inventory::count(),
            'available' => Inventory::inGoodCondition()->available()->count(),
            'rented' => Inventory::inGoodCondition()->whereHas('rentals', function($query) {
                $query->whereNull('return_date');
            })->count(),
            'damaged' => Inventory::damaged()->count(),
            'lost' => Inventory::lost()->count(),
            'by_condition' => Inventory::selectRaw('condition, COUNT(*) as count')
                ->groupBy('condition')
                ->pluck('count', 'condition')
                ->toArray(),
        ];
    }

    /**
     * Validate if inventory can be rented
     */
    public function canRent(int $inventoryId): array
    {
        $inventory = Inventory::findOrFail($inventoryId);
        
        if (!$inventory->canBeRented()) {
            return [
                'can_rent' => false,
                'reason' => $inventory->isDamaged() ? 'La película está dañada' : 
                           ($inventory->isLost() ? 'La película está perdida' : 'La película ya está rentada'),
                'condition' => $inventory->condition,
                'is_available' => $inventory->isAvailable(),
            ];
        }

        return [
            'can_rent' => true,
            'inventory' => $inventory,
        ];
    }
}