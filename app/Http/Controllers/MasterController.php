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
use App\Traits\LogsActivity;

class MasterController extends Controller
{
    use LogsActivity;

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

        $category = $this->categoryRepository->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        $this->logActivity('CREATE_CATEGORY', 'Category', $category->id, ['name' => $category->name]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully!'
        ]);
    }

    public function updateCategory(Request $request, string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $old = $this->categoryRepository->findById($id);
        $this->categoryRepository->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        $this->logActivity('UPDATE_CATEGORY', 'Category', $id, [
            'old' => ['name' => $old->name],
            'new' => ['name' => $request->name]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully!'
        ]);
    }

    public function destroyCategory(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $category = $this->categoryRepository->findById($id);
            if (!$category) return response()->json(['status' => false, 'message' => 'Category not found.'], 404);

            $this->logActivity('DELETE_CATEGORY', 'Category', $id, $category->toArray());
            
            $this->categoryRepository->delete($id);
            return response()->json(['status' => true, 'message' => 'Category deleted successfully! Backup saved in logs.']);
        } catch (QueryException $e) {
            return response()->json(['status' => false, 'message' => 'Cannot delete this category because it is being used by existing products.'], 400);
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

        $unit = $this->unitRepository->create([
            'name' => $request->name,
            'short_name' => strtoupper($request->short_name)
        ]);

        $this->logActivity('CREATE_UNIT', 'Unit', $unit->id, ['name' => $unit->name]);

        return response()->json([
            'status' => true,
            'message' => 'Unit created successfully!'
        ]);
    }

    public function updateUnit(Request $request, string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $id,
            'short_name' => 'required|string|max:10|unique:units,short_name,' . $id
        ]);

        $old = $this->unitRepository->findById($id);
        $this->unitRepository->update($id, [
            'name' => $request->name,
            'short_name' => strtoupper($request->short_name)
        ]);

        $this->logActivity('UPDATE_UNIT', 'Unit', $id, [
            'old' => ['name' => $old->name],
            'new' => ['name' => $request->name]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Unit updated successfully!'
        ]);
    }

    public function destroyUnit(string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $unit = $this->unitRepository->findById($id);
        if (!$unit) return response()->json(['status' => false, 'message' => 'Unit not found.'], 404);

        $this->logActivity('DELETE_UNIT', 'Unit', $id, $unit->toArray());
        
        $this->unitRepository->delete($id);
        return response()->json(['status' => true, 'message' => 'Unit deleted successfully! Backup saved in logs.']);
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
            'selling_price' => 'nullable|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
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

        $product = \App\Models\Inventory::create([
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'wattage' => $request->wattage,
            'selling_price' => $request->selling_price ?? 0,
            'specifications' => empty($specifications) ? null : $specifications,
            'stock_quantity' => 0,
            'low_stock_threshold' => $request->low_stock_threshold,
        ]);

        $this->logActivity('CREATE_PRODUCT', 'Inventory', $product->id, ['code' => $product->code, 'name' => $product->name]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to master successfully!'
        ]);
    }

    public function updateProduct(Request $request, string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $request->validate([
            'code' => 'required|string|max:255|unique:inventories,code,' . $id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'wattage' => 'nullable|string|max:50',
            'selling_price' => 'nullable|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
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

        $old = $this->inventoryRepository->findById($id);
        $this->inventoryRepository->update($id, [
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'wattage' => $request->wattage,
            'selling_price' => $request->selling_price ?? 0,
            'low_stock_threshold' => $request->low_stock_threshold,
            'specifications' => empty($specifications) ? null : $specifications,
        ]);

        $this->logActivity('UPDATE_PRODUCT', 'Inventory', $id, ['code' => $request->code]);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully!'
        ]);
    }

    public function destroyProduct(string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $product = $this->inventoryRepository->findById($id);
        if (!$product) return response()->json(['status' => false, 'message' => 'Product not found.'], 404);

        if ($product->stock_quantity > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete product with existing stock. Adjust stock to 0 first.'
            ], 400);
        }

        $this->logActivity('DELETE_PRODUCT', 'Inventory', $id, $product->toArray());

        $this->inventoryRepository->delete($id);
        return response()->json(['status' => true, 'message' => 'Product deleted successfully! Backup saved in logs.']);
    }

    public function productStockIndex(string $encryptedId)
    {
        $id = decrypt($encryptedId);
        $product = $this->inventoryRepository->findById($id);
        if (!$product) {
            return redirect()->route('master.products')->with('error', 'Product not found.');
        }

        $transactions = $this->stockTransactionRepository->getByInventoryId($id);
        return view('master.product_stock', compact('product', 'transactions'));
    }

    public function storeStockTransaction(Request $request, string $encryptedId)
    {
        $id = decrypt($encryptedId);
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

    public function exportStockTransactionsCSV(Request $request, string $encryptedId)
    {
        $id      = decrypt($encryptedId);
        $product = $this->inventoryRepository->findById($id);

        if (!$product) {
            abort(404, 'Product not found.');
        }

        // Get all transactions as Collection, then apply optional filters
        $transactions = $this->stockTransactionRepository->getByInventoryId($id);

        if ($request->filled('type') && in_array($request->type, ['in', 'out'])) {
            $transactions = $transactions->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $transactions = $transactions->filter(function ($t) use ($request) {
                return $t->created_at->format('Y-m-d') === $request->date;
            });
        }

        $fileName = 'Stock_' . preg_replace('/[^a-z0-9_]/i', '_', $product->name) . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($transactions, $product) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            fputcsv($file, ['Date', 'Time', 'Type', 'Quantity', 'Unit', 'Price Per Unit (INR)', 'Remarks']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->created_at->format('d M Y'),
                    $t->created_at->format('h:i A'),
                    $t->type === 'in' ? 'INBOUND' : 'OUTBOUND',
                    ($t->type === 'in' ? '+' : '-') . $t->quantity,
                    $product->unit,
                    $t->price_per_unit ? number_format($t->price_per_unit, 2, '.', '') : '-',
                    $t->remarks ?? 'No remarks provided',
                ]);
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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

    public function importCategories(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Skip header row

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;
            
            $name = trim($row[0]);
            $desc = isset($row[1]) ? trim($row[1]) : null;

            \App\Models\Category::updateOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'description' => $desc]
            );
            $count++;
        }
        fclose($handle);

        $this->logActivity('IMPORT_CATEGORIES', 'Category', null, ['count' => $count]);

        return response()->json(['status' => true, 'message' => "$count Categories processed successfully!"]);
    }

    public function importUnits(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) || empty($row[1])) continue;

            \App\Models\Unit::updateOrCreate(
                ['short_name' => strtoupper(trim($row[1]))],
                ['name' => trim($row[0])]
            );
            $count++;
        }
        fclose($handle);

        $this->logActivity('IMPORT_UNITS', 'Unit', null, ['count' => $count]);

        return response()->json(['status' => true, 'message' => "$count Units processed successfully!"]);
    }

    public function importProducts(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            // Expected format: Code, Name, CategoryName, UnitName, Wattage, Price, Threshold
            if (count($row) < 4 || empty($row[0])) continue;

            $category = \App\Models\Category::where('name', 'like', trim($row[2]))->first();
            if (!$category) continue; // Skip if category not found

            \App\Models\Inventory::updateOrCreate(
                ['code' => trim($row[0])],
                [
                    'name' => trim($row[1]),
                    'category_id' => $category->id,
                    'unit' => trim($row[3]),
                    'wattage' => isset($row[4]) ? trim($row[4]) : null,
                    'selling_price' => isset($row[5]) ? (float)$row[5] : 0,
                    'low_stock_threshold' => isset($row[6]) ? (int)$row[6] : 10,
                ]
            );
            $count++;
        }
        fclose($handle);

        $this->logActivity('IMPORT_PRODUCTS', 'Inventory', null, ['count' => $count]);

        return response()->json(['status' => true, 'message' => "$count Products processed successfully!"]);
    }

    public function downloadCategorySample()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=category_sample.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Name', 'Description'];
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['LED Lights', 'Premium lighting solutions']);
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function downloadUnitSample()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=unit_sample.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Name', 'Short Name'];
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Piece', 'PCS']);
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function downloadProductSample()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=product_sample.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['SKU Code', 'Name', 'Category Name', 'Unit', 'Wattage', 'Selling Price', 'Low Stock Threshold'];
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['SU-L001', 'LED Bulb 9W', 'LED Lights', 'Piece', '9W', '150', '20']);
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
