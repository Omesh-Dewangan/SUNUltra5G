<?php

namespace App\Http\Controllers;

use App\Repositories\SaleOrderRepository;
use App\Repositories\InventoryRepository;
use App\Services\SaleOrderService;
use Illuminate\Http\Request;
use Exception;
use App\Traits\LogsActivity;

class SaleOrderController extends Controller
{
    use LogsActivity;

    protected $saleOrderRepository;
    protected $inventoryRepository;
    protected $saleOrderService;

    public function __construct(
        SaleOrderRepository $saleOrderRepository,
        InventoryRepository $inventoryRepository,
        SaleOrderService $saleOrderService
    ) {
        $this->saleOrderRepository = $saleOrderRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->saleOrderService    = $saleOrderService;
    }

    /**
     * List all sales orders.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $orders = $status
            ? $this->saleOrderRepository->getByStatus($status)
            : $this->saleOrderRepository->getAll();

        return view('sales.index', compact('orders', 'status'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $products = $this->inventoryRepository->getAll();
        return view('sales.create', compact('products'));
    }

    /**
     * Export sales orders to CSV.
     */
    public function exportCSV(Request $request)
    {
        $status = $request->query('status');
        $orders = $status
            ? $this->saleOrderRepository->getByStatus($status)
            : $this->saleOrderRepository->getAll();

        $filename = "sales_report_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order Number', 'Date', 'Customer Name', 'Customer Phone', 'Status', 'Total Amount', 'Created By'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row = [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->customer_name,
                    $order->customer_phone ?? '-',
                    ucfirst($order->status),
                    $order->total_amount,
                    $order->creator->name ?? 'Unknown'
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store a new draft order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'nullable|string|max:20',
            'customer_address'    => 'nullable|string',
            'payment_mode'        => 'nullable|string|max:50',
            'notes'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.inventory_id'=> 'required|exists:inventories,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        try {
            $order = $this->saleOrderService->createOrder($request, auth()->id());
            $this->logActivity('CREATE_ORDER', 'SaleOrder', $order->id, ['order_number' => $order->order_number]);
            
            return response()->json([
                'success' => true,
                'message' => "Order {$order->order_number} created successfully!",
                'redirect'=> route('sales.show', encrypt($order->id)),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Show order details / invoice.
     */
    public function show(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $order = $this->saleOrderRepository->findById($id);
            return view('sales.show', compact('order'));
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * Print order invoice.
     */
    public function printInvoice(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $order = $this->saleOrderRepository->findById($id);
            return view('sales.print', compact('order'));
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * Confirm an order and deduct stock.
     */
    public function confirm(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $order = $this->saleOrderService->confirmOrder($id);
            $this->logActivity('CONFIRM_ORDER', 'SaleOrder', $order->id, ['order_number' => $order->order_number]);
            
            return response()->json([
                'success' => true,
                'message' => "Order {$order->order_number} confirmed! Stock has been deducted.",
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Mark order as dispatched.
     */
    public function dispatch(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $order = $this->saleOrderService->dispatchOrder($id);
            $this->logActivity('DISPATCH_ORDER', 'SaleOrder', $order->id, ['order_number' => $order->order_number]);
            
            return response()->json([
                'success' => true,
                'message' => "Order {$order->order_number} marked as dispatched.",
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel an order (with stock reversal if confirmed).
     */
    public function cancel(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $order = $this->saleOrderService->cancelOrder($id);
            $this->logActivity('CANCEL_ORDER', 'SaleOrder', $order->id, ['order_number' => $order->order_number]);
            
            return response()->json([
                'success' => true,
                'message' => "Order {$order->order_number} has been cancelled.",
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * AJAX: Get product price for a given inventory_id.
     */
    public function getProductPrice(int $id)
    {
        try {
            $product = $this->inventoryRepository->findById($id);
            return response()->json([
                'success'       => true,
                'selling_price' => $product->selling_price ?? 0,
                'stock'         => $product->stock_quantity,
                'unit'          => $product->unit,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
