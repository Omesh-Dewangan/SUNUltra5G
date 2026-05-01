<?php

namespace App\Repositories;

use App\Models\SaleOrder;

class SaleOrderRepository
{
    public function getAll()
    {
        return SaleOrder::with(['items.inventory', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getByStatus(string $status)
    {
        return SaleOrder::with(['items.inventory', 'creator'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function findById(int $id): ?SaleOrder
    {
        return SaleOrder::with(['items.inventory', 'creator'])->findOrFail($id);
    }

    public function create(array $data): SaleOrder
    {
        return SaleOrder::create($data);
    }

    public function updateStatus(int $id, string $status): SaleOrder
    {
        $order = SaleOrder::findOrFail($id);
        $order->update(['status' => $status]);
        return $order;
    }

    public function updateTotal(int $id, float $total): void
    {
        SaleOrder::where('id', $id)->update(['total_amount' => $total]);
    }
}
