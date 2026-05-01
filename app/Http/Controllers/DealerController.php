<?php

namespace App\Http\Controllers;

use App\Services\DealerService;
use Illuminate\Http\Request;
use Exception;

class DealerController extends Controller
{
    protected $dealerService;

    public function __construct(DealerService $dealerService)
    {
        $this->dealerService = $dealerService;
    }

    /**
     * Display a listing of dealers.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'type'   => $request->get('type'),
        ];
        
        $dealers = $this->dealerService->getDealers($filters);
        
        if ($request->ajax()) {
            return view('dealers._list', compact('dealers'))->render();
        }
        
        return view('dealers.index', compact('dealers'));
    }

    /**
     * Store a newly created dealer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|unique:dealers,phone',
            'email'          => 'nullable|email|unique:dealers,email',
            'dealer_type'    => 'required|in:distributor,wholesaler,retailer',
            'credit_limit'   => 'nullable|numeric|min:0',
            'address'        => 'nullable|string',
            'gstin'          => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
        ]);

        try {
            $dealer = $this->dealerService->createDealer($request->all(), auth()->id());
            return response()->json([
                'success' => true,
                'message' => "Dealer {$dealer->name} created successfully!"
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified dealer in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|unique:dealers,phone,' . $id,
            'email'          => 'nullable|email|unique:dealers,email,' . $id,
            'dealer_type'    => 'required|in:distributor,wholesaler,retailer',
            'credit_limit'   => 'nullable|numeric|min:0',
            'address'        => 'nullable|string',
            'gstin'          => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
        ]);

        try {
            $this->dealerService->updateDealer($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => "Dealer updated successfully!"
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Toggle dealer active status.
     */
    public function toggleStatus($id)
    {
        try {
            $this->dealerService->toggleStatus($id);
            return response()->json(['success' => true, 'message' => 'Status updated!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified dealer from storage.
     */
    public function destroy($id)
    {
        try {
            $this->dealerService->deleteDealer($id);
            return response()->json(['success' => true, 'message' => 'Dealer deleted successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
