<?php

namespace App\Repositories;

use App\Models\StockTransaction;

class StockTransactionRepository
{
    public function create(array $data)
    {
        return StockTransaction::create($data);
    }

    public function getByInventoryId($inventoryId)
    {
        return StockTransaction::where('inventory_id', $inventoryId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
