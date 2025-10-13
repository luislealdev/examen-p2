<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Inventory;
use App\Services\OmdbService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    protected $omdbService;

    public function __construct(OmdbService $omdbService)
    {
        $this->omdbService = $omdbService;
    }

    public function index()
    {
        $stores = Store::with(['address.city.country', 'manager'])->get();
        return view('client.stores.index', compact('stores'));
    }

    public function showInventory($id)
    {
        $store = Store::findOrFail($id);
        $inventory = Inventory::where('store_id', $store->store_id)
            ->with('film')
            ->get();

        // Enriquecer datos con OMDB
        $inventoryWithOmdb = $inventory->map(function ($item) {
            $omdbData = $this->omdbService->searchByTitle($item->film->title);
            return [
                'inventory_id' => $item->id,
                'film' => $item->film,
                'omdb_data' => $omdbData
            ];
        });

        return view('client.stores.inventory', [
            'store' => $store,
            'inventory' => $inventoryWithOmdb
        ]);
    }

    public function rentMovie(Request $request, $inventoryId)
    {
        $inventory = Inventory::findOrFail($inventoryId);
        
        // Aquí irá la lógica para rentar la película
        // Por ahora solo validamos que exista el inventario
        
        return response()->json([
            'message' => 'Película rentada exitosamente'
        ]);
    }
}