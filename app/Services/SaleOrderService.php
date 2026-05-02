<?php

namespace App\Services;

use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Repositories\SaleOrderRepository;
use App\Repositories\InventoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SaleOrderService
{
    protected $saleOrderRepository;
    protected $inventoryRepository;

    public function __construct(
        SaleOrderRepository $saleOrderRepository,
        InventoryRepository $inventoryRepository
    ) {
        $this->saleOrderRepository = $saleOrderRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Create a new draft Sales Order with items.
     */
    public function createOrder(Request $request, int $userId): SaleOrder
    {
        return DB::transaction(function () use ($request, $userId) {
            // Create order header
            $order = $this->saleOrderRepository->create([
                'order_number'     => SaleOrder::generateOrderNumber(),
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'payment_mode'     => $request->payment_mode ?? 'Cash',
                'notes'            => $request->notes,
                'status'           => 'draft',
                'total_amount'     => 0,
                'created_by'       => $userId,
            ]);

            // Create line items
            $total = 0;
            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                SaleOrderItem::create([
                    'sale_order_id' => $order->id,
                    'inventory_id'  => $item['inventory_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'total_price'   => $lineTotal,
                ]);
                $total += $lineTotal;
            }

            // Update total
            $this->saleOrderRepository->updateTotal($order->id, $total);
            $order->total_amount = $total;

            Log::info('Sale Order Created', ['order_id' => $order->id, 'user_id' => $userId]);

            return $order;
        });
    }

    /**
     * Confirm an order: deduct stock for all items.
     */
    public function confirmOrder(int $orderId): SaleOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->saleOrderRepository->findById($orderId);

            if ($order->status !== 'draft') {
                throw new Exception('Only draft orders can be confirmed.');
            }

            // Stock availability check first
            foreach ($order->items as $item) {
                $inventory = $this->inventoryRepository->findForUpdate($item->inventory_id);
                if ($inventory->stock_quantity < $item->quantity) {
                    throw new Exception(
                        "Insufficient stock for product: {$inventory->name}. " .
                        "Available: {$inventory->stock_quantity}, Required: {$item->quantity}"
                    );
                }
            }

            // Deduct stock for each item
            foreach ($order->items as $item) {
                // Re-fetch with lock (though already locked above in the same transaction)
                $inventory = $this->inventoryRepository->findForUpdate($item->inventory_id);
                $inventory->stock_quantity -= $item->quantity;
                $inventory->save();

                // Record stock-out transaction
                \App\Models\StockTransaction::create([
                    'inventory_id' => $item->inventory_id,
                    'type'         => 'out',
                    'quantity'     => $item->quantity,
                    'remarks'      => "Sale Order #{$order->order_number}",
                ]);
            }

            $this->saleOrderRepository->updateStatus($orderId, 'confirmed');
            $order->status = 'confirmed';

            Log::info('Sale Order Confirmed', ['order_id' => $orderId]);

            return $order;
        });
    }

    /**
     * Cancel an order. If it was confirmed, restore stock.
     */
    public function cancelOrder(int $orderId): SaleOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->saleOrderRepository->findById($orderId);

            if (in_array($order->status, ['completed', 'cancelled'])) {
                throw new Exception('This order cannot be cancelled.');
            }

            // If confirmed, restore stock
            if ($order->status === 'confirmed') {
                foreach ($order->items as $item) {
                    $inventory = $this->inventoryRepository->findForUpdate($item->inventory_id);
                    $inventory->stock_quantity += $item->quantity;
                    $inventory->save();

                    // Record stock-in reversal transaction
                    \App\Models\StockTransaction::create([
                        'inventory_id' => $item->inventory_id,
                        'type'         => 'in',
                        'quantity'     => $item->quantity,
                        'remarks'      => "Reversal: Cancelled Order #{$order->order_number}",
                    ]);
                }
            }

            $this->saleOrderRepository->updateStatus($orderId, 'cancelled');
            $order->status = 'cancelled';

            Log::info('Sale Order Cancelled', ['order_id' => $orderId]);

            return $order;
        });
    }

    /**
     * Mark an order as dispatched.
     */
    public function dispatchOrder(int $orderId): SaleOrder
    {
        $order = $this->saleOrderRepository->findById($orderId);

        if ($order->status !== 'confirmed') {
            throw new Exception('Only confirmed orders can be dispatched.');
        }

        $this->saleOrderRepository->updateStatus($orderId, 'dispatched');
        $order->status = 'dispatched';

        return $order;
    }
}
