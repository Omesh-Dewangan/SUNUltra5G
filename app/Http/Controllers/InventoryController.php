<?php

namespace App\Http\Controllers;

use App\Repositories\InventoryRepository;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    protected $inventoryRepository;
    protected $inventoryService;

    public function __construct(InventoryRepository $inventoryRepository, InventoryService $inventoryService)
    {
        $this->inventoryRepository = $inventoryRepository;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        // For a simple view, we just fetch all with their categories
        $inventories = \App\Models\Inventory::with('category')->paginate(15);
        
        return view('inventory.index', compact('inventories'));
    }

    public function adjustStock(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out'
        ]);

        try {
            $this->inventoryService->adjustStock($request->inventory_id, $request->quantity, $request->type);
            
            return response()->json([
                'status' => true,
                'message' => 'Stock adjusted successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
