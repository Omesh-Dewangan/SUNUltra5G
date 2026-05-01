<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\UnitRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\StockTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    protected $categoryRepository;
    protected $unitRepository;
    protected $inventoryRepository;
    protected $stockTransactionRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        UnitRepository $unitRepository,
        InventoryRepository $inventoryRepository,
        StockTransactionRepository $stockTransactionRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->unitRepository = $unitRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->stockTransactionRepository = $stockTransactionRepository;
    }

    public function categoriesIndex()
    {
        $categories = $this->categoryRepository->getAll();
        return view('master.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name|max:255',
            'description' => 'nullable|string'
        ]);

        $this->categoryRepository->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully!'
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $this->categoryRepository->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully!'
        ]);
    }

    public function destroyCategory($id)
    {
        try {
            $this->categoryRepository->delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete this category because it is being used by existing products.'
            ], 400);
        }
    }

    public function unitsIndex()
    {
        $units = $this->unitRepository->getAll();
        return view('master.units', compact('units'));
    }

    public function storeUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:units,name|max:50',
            'short_name' => 'required|string|unique:units,short_name|max:10'
        ]);

        $this->unitRepository->create([
            'name' => $request->name,
            'short_name' => strtoupper($request->short_name)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Unit created successfully!'
        ]);
    }

    public function updateUnit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $id,
            'short_name' => 'required|string|max:10|unique:units,short_name,' . $id
        ]);

        $this->unitRepository->update($id, [
            'name' => $request->name,
            'short_name' => strtoupper($request->short_name)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Unit updated successfully!'
        ]);
    }

    public function destroyUnit($id)
    {
        $this->unitRepository->delete($id);
        return response()->json([
            'status' => true,
            'message' => 'Unit deleted successfully!'
        ]);
    }

    public function productsIndex()
    {
        $products = $this->inventoryRepository->getAllWithCategory();
        $categories = $this->categoryRepository->getAll();
        $units = $this->unitRepository->getAll();
        
        return view('master.products', compact('products', 'categories', 'units'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:inventories,code|max:255',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'wattage' => 'nullable|string|max:50',
            'spec_keys.*' => 'nullable|string',
            'spec_values.*' => 'nullable|string',
        ]);

        $specifications = [];
        if ($request->has('spec_keys') && $request->has('spec_values')) {
            $keys = $request->spec_keys;
            $values = $request->spec_values;
            for ($i = 0; $i < count($keys); $i++) {
                if (!empty($keys[$i]) && !empty($values[$i])) {
                    $specifications[$keys[$i]] = $values[$i];
                }
            }
        }

        \App\Models\Inventory::create([
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'wattage' => $request->wattage,
            'specifications' => empty($specifications) ? null : $specifications,
            'stock_quantity' => 0,
            'low_stock_threshold' => 10,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to master successfully!'
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:inventories,code,' . $id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'wattage' => 'nullable|string|max:50',
            'spec_keys.*' => 'nullable|string',
            'spec_values.*' => 'nullable|string',
        ]);

        $specifications = [];
        if ($request->has('spec_keys') && $request->has('spec_values')) {
            $keys = $request->spec_keys;
            $values = $request->spec_values;
            for ($i = 0; $i < count($keys); $i++) {
                if (!empty($keys[$i]) && !empty($values[$i])) {
                    $specifications[$keys[$i]] = $values[$i];
                }
            }
        }

        $this->inventoryRepository->update($id, [
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'wattage' => $request->wattage,
            'specifications' => empty($specifications) ? null : $specifications,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully!'
        ]);
    }

    public function destroyProduct($id)
    {
        // Safe check for inventory levels before deletion
        $product = $this->inventoryRepository->findById($id);
        if ($product && $product->stock_quantity > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete product with existing stock. Adjust stock to 0 first.'
            ], 400);
        }

        $this->inventoryRepository->delete($id);
        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully!'
        ]);
    }

    public function productStockIndex($id)
    {
        $product = $this->inventoryRepository->findById($id);
        if (!$product) {
            return redirect()->route('master.products')->with('error', 'Product not found.');
        }

        $transactions = $this->stockTransactionRepository->getByInventoryId($id);
        return view('master.product_stock', compact('product', 'transactions'));
    }

    public function storeStockTransaction(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'price_per_unit' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string'
        ]);

        $product = $this->inventoryRepository->findById($id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found.'], 404);
        }

        if ($request->type === 'out' && $product->stock_quantity < $request->quantity) {
            return response()->json(['status' => false, 'message' => 'Insufficient stock.'], 400);
        }

        DB::beginTransaction();
        try {
            // Create transaction record
            $this->stockTransactionRepository->create([
                'inventory_id' => $id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'price_per_unit' => $request->price_per_unit,
                'remarks' => $request->remarks
            ]);

            // Update inventory stock
            $newQuantity = ($request->type === 'in') 
                ? $product->stock_quantity + $request->quantity 
                : $product->stock_quantity - $request->quantity;

            $this->inventoryRepository->update($id, ['stock_quantity' => $newQuantity]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Stock updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error updating stock: ' . $e->getMessage()], 500);
        }
    }
    public function exportProductsCSV()
    {
        $products = $this->inventoryRepository->getAllWithCategory();
        $fileName = 'Products_Master_' . date('Y-m-d') . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Product Code', 'Name', 'Category', 'Unit', 'Wattage', 'Current Stock');

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $p) {
                fputcsv($file, array(
                    $p->code,
                    $p->name,
                    $p->category->name ?? '-',
                    $p->unit,
                    $p->wattage ?? '-',
                    $p->stock_quantity
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
