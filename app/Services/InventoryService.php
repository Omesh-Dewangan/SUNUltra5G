<?php

namespace App\Services;

use App\Repositories\InventoryRepository;
use App\Repositories\StockTransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryService
{
    protected $inventoryRepository;
    protected $stockTransactionRepository;

    public function __construct(InventoryRepository $inventoryRepository, StockTransactionRepository $stockTransactionRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
        $this->stockTransactionRepository = $stockTransactionRepository;
    }

    /**
     * Update stock for a specific inventory item.
     * $type can be 'in' or 'out'
     */
    public function adjustStock(int $id, int $quantity, string $type)
    {
        return DB::transaction(function () use ($id, $quantity, $type) {
            try {
                $inventory = $this->inventoryRepository->findForUpdate($id);
                if (!$inventory) throw new Exception("Inventory item not found.");

                if ($type === 'out' && $inventory->stock_quantity < $quantity) {
                    throw new Exception("Insufficient stock available.");
                }

                $oldStock = $inventory->stock_quantity;
                
                if ($type === 'in') {
                    $inventory->stock_quantity += $quantity;
                } else {
                    $inventory->stock_quantity -= $quantity;
                }

                $inventory->save();

                // Create Stock Transaction Record for History
                $this->stockTransactionRepository->create([
                    'inventory_id' => $id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'remarks' => 'Quick adjustment from Inventory Management'
                ]);

                Log::info('Stock Adjusted', [
                    'inventory_id' => $id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'old_stock' => $oldStock,
                    'new_stock' => $inventory->stock_quantity,
                    'user_id' => auth()->id()
                ]);

                return $inventory;
            } catch (Exception $e) {
                Log::error('Stock Adjustment Failed', ['exception' => $e]);
                throw $e;
            }
        });
    }
}
