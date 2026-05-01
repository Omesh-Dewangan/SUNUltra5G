<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockTransaction;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Calculate Stats
        $totalLedSales = StockTransaction::where('type', 'out')
            ->whereHas('inventory', function($q) {
                $q->whereHas('category', function($cat) {
                    $cat->where('name', 'like', '%LED%');
                });
            })->sum('quantity');

        $wireStock = Inventory::whereHas('category', function($q) {
                $q->where('name', 'like', '%Cable%')->orWhere('name', 'like', '%Wire%');
            })->sum('stock_quantity');

        $totalOrdersCount = StockTransaction::where('type', 'out')->count();
        $activeDealersCount = 12; // Static for now

        // 3. Dynamic Sub-labels
        $ledTrend = "Updated today"; 
        
        $lowStockCount = Inventory::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $wireStatus = $lowStockCount > 0 ? "$lowStockCount items low in stock" : "Stock sufficient";
        
        $lowStockItems = Inventory::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->with('category')
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

        $dealerStatus = "Across Chhattisgarh";

        // 4. Fetch Recent Transactions
        $recentTransactions = StockTransaction::with(['inventory.category'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 5. Chart Data (Last 7 Days) - Movement Trend
        $chartLabels = [];
        $chartIn = [];
        $chartOut = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('D, d M');
            
            $chartIn[] = \App\Models\StockTransaction::where('type', 'in')
                ->whereDate('created_at', $date)
                ->sum('quantity');
                
            $chartOut[] = \App\Models\StockTransaction::where('type', 'out')
                ->whereDate('created_at', $date)
                ->sum('quantity');
        }

        // 6. Advanced Analytics
        // Top Selling Products (Last 30 Days)
        $topSellingProducts = \App\Models\SaleOrderItem::with('inventory')
            ->select('inventory_id', \DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('saleOrder', function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->where('created_at', '>=', now()->subDays(30));
            })
            ->groupBy('inventory_id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Category Revenue Breakdown
        $categoryRevenue = \App\Models\Category::with(['inventories.saleOrderItems' => function($q) {
                $q->whereHas('saleOrder', function($sq) {
                    $sq->where('status', '!=', 'cancelled');
                });
            }])
            ->get()
            ->map(function($category) {
                $revenue = $category->inventories->flatMap->saleOrderItems->sum('total_price');
                return [
                    'name' => $category->name,
                    'revenue' => $revenue
                ];
            })->filter(function($item) {
                return $item['revenue'] > 0;
            })->values();

        // 6-Month Monthly Sales Trend (Revenue)
        $monthlyRevenueLabels = [];
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyRevenueLabels[] = $date->format('M Y');
            $monthlyRevenueData[] = \App\Models\SaleOrder::where('status', '!=', 'cancelled')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');
        }

        return view('dashboard', compact(
            'totalLedSales', 
            'wireStock', 
            'totalOrdersCount', 
            'activeDealersCount',
            'recentTransactions',
            'lowStockItems',
            'lowStockCount',
            'ledTrend',
            'wireStatus',
            'dealerStatus',
            'chartLabels',
            'chartIn',
            'chartOut',
            'topSellingProducts',
            'categoryRevenue',
            'monthlyRevenueLabels',
            'monthlyRevenueData'
        ));
    }
}
