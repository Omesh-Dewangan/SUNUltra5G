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
        // Monthly LED Sales (Revenue)
        $ledSalesThisMonth = \App\Models\SaleOrderItem::whereHas('inventory.category', function($q) {
                $q->where('name', 'like', '%LED%');
            })
            ->whereHas('saleOrder', function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            })->sum('total_price');

        $ledSalesLastMonth = \App\Models\SaleOrderItem::whereHas('inventory.category', function($q) {
                $q->where('name', 'like', '%LED%');
            })
            ->whereHas('saleOrder', function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->whereMonth('created_at', now()->subMonth()->month)
                  ->whereYear('created_at', now()->subMonth()->year);
            })->sum('total_price');

        $totalLedSales = $ledSalesThisMonth;
        $ledTrend = $ledSalesLastMonth > 0 ? round((($ledSalesThisMonth - $ledSalesLastMonth) / $ledSalesLastMonth) * 100) : ($ledSalesThisMonth > 0 ? 100 : 0);

        // Wire Stock
        $wireStock = Inventory::whereHas('category', function($q) {
                $q->where('name', 'like', '%Cable%')->orWhere('name', 'like', '%Wire%');
            })->sum('stock_quantity');

        // Total Orders (Last 30 Days)
        $totalOrdersCount = \App\Models\SaleOrder::where('created_at', '>=', now()->subDays(30))->count();

        // Order Status Breakdown
        $orderStatusCounts = [
            'draft'      => \App\Models\SaleOrder::where('status', 'draft')->count(),
            'confirmed'  => \App\Models\SaleOrder::where('status', 'confirmed')->count(),
            'dispatched' => \App\Models\SaleOrder::where('status', 'dispatched')->count(),
            'completed'  => \App\Models\SaleOrder::where('status', 'completed')->count(),
            'cancelled'  => \App\Models\SaleOrder::where('status', 'cancelled')->count(),
        ];

        // Recent 6 Orders (with items count)
        $recentOrders = \App\Models\SaleOrder::withCount('items')
            ->orderBy('created_at', 'desc')->take(6)->get();
        
        // Dealers
        $activeDealersCount = \App\Models\Dealer::where('is_active', true)->count();
        $dealerStatus = \App\Models\Dealer::where('is_active', false)->count();

        // Low Stock Analysis
        $lowStockCount = Inventory::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $wireStatus = $lowStockCount > 0 ? "$lowStockCount items low in stock" : "Healthy";
        
        $lowStockItems = Inventory::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->with('category')
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

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
            'monthlyRevenueData',
            'orderStatusCounts',
            'recentOrders'
        ));
    }

    /**
     * Superadmin Comprehensive Report View
     */
    public function superadminReport()
    {
        // 1. Total System Valuation (Inventory Value)
        $totalValuation = \App\Models\Inventory::sum(\DB::raw('stock_quantity * selling_price'));

        // 2. All-Time Revenue (Completed/Dispatched Orders)
        $allTimeRevenue = \App\Models\SaleOrder::whereIn('status', ['dispatched', 'completed'])->sum('total_amount');

        // 3. Category Stock Distribution
        $categoryStock = \App\Models\Category::withSum('inventories', 'stock_quantity')
            ->get()
            ->filter(fn($c) => $c->inventories_sum_stock_quantity > 0)
            ->map(fn($c) => ['name' => $c->name, 'stock' => $c->inventories_sum_stock_quantity])
            ->values();

        // 4. User Performance (Orders Created by Users)
        $userPerformance = \App\Models\User::withCount(['saleOrders' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->withSum(['saleOrders' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->having('sale_orders_count', '>', 0)
            ->orderBy('sale_orders_sum_total_amount', 'desc')
            ->take(5)
            ->get();

        // 5. 12-Month Trend (Orders vs Revenue)
        $trendLabels = [];
        $trendOrders = [];
        $trendRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trendLabels[] = $date->format('M y');
            
            $monthData = \App\Models\SaleOrder::where('status', '!=', 'cancelled')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);
                
            $trendOrders[] = (clone $monthData)->count();
            $trendRevenue[] = (clone $monthData)->sum('total_amount');
        }

        return view('reports.superadmin', compact(
            'totalValuation',
            'allTimeRevenue',
            'categoryStock',
            'userPerformance',
            'trendLabels',
            'trendOrders',
            'trendRevenue'
        ));
    }
}
