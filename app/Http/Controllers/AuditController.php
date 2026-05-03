<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StockAudit;
use App\Models\StockAuditItem;
use App\Models\Inventory;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuditController extends Controller
{
    public function index()
    {
        $audits = StockAudit::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        return view('audit.index', compact('audits'));
    }

    public function create()
    {
        return view('audit.create');
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $audit = StockAudit::create([
                'audit_no' => 'AUD-' . strtoupper(uniqid()),
                'status' => 'draft',
                'created_by' => Auth::id(),
                'remarks' => $request->remarks
            ]);

            // Snapshot all inventory
            $items = Inventory::all();
            foreach ($items as $item) {
                StockAuditItem::create([
                    'audit_id' => $audit->id,
                    'inventory_id' => $item->id,
                    'unit_price' => $item->purchase_price ?? 0,
                    'system_qty' => $item->stock_quantity,
                ]);
            }

            return redirect()->route('inventory.audit.show', encrypt($audit->id))->with('success', 'Audit session started and stock snapshot captured.');
        });
    }

    public function show(string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $audit = StockAudit::with('items.inventory')->findOrFail($id);
        return view('audit.show', compact('audit'));
    }

    public function updateItems(Request $request, string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $audit = StockAudit::findOrFail($id);
        if ($audit->status !== 'draft') {
            return back()->with('error', 'Only draft audits can be modified.');
        }

        foreach ($request->items as $itemId => $data) {
            $item = StockAuditItem::findOrFail($itemId);
            $physicalQty = $data['physical_qty'];
            $item->update([
                'physical_qty' => $physicalQty,
                'mismatch_qty' => $physicalQty - $item->system_qty,
                'reason' => $data['reason'] ?? null
            ]);
        }

        if ($request->has('submit_for_approval')) {
            $audit->update(['status' => 'submitted']);
            return redirect()->route('inventory.audit.index')->with('success', 'Audit submitted for approval.');
        }

        return back()->with('success', 'Audit counts saved as draft.');
    }

    public function approve(string $encryptedId)
    {
        $id = decrypt($encryptedId);
        return DB::transaction(function () use ($id) {
            $audit = StockAudit::with('items')->findOrFail($id);
            
            if ($audit->status !== 'submitted') {
                return back()->with('error', 'Only submitted audits can be approved.');
            }

            foreach ($audit->items as $auditItem) {
                if ($auditItem->mismatch_qty != 0) {
                    $inventory = Inventory::find($auditItem->inventory_id);
                    
                    // Adjust inventory
                    $inventory->stock_quantity = $auditItem->physical_qty;
                    $inventory->save();

                    // Record transaction
                    \App\Models\StockTransaction::create([
                        'inventory_id' => $inventory->id,
                        'type' => $auditItem->mismatch_qty > 0 ? 'in' : 'out',
                        'quantity' => abs($auditItem->mismatch_qty),
                        'remarks' => "Audit Adjustment ($audit->audit_no): " . $auditItem->reason
                    ]);
                }
            }

            $audit->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'completed_at' => now()
            ]);

            return redirect()->route('inventory.audit.index')->with('success', 'Audit approved and stock adjusted.');
        });
    }
}
