<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.dashboard', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('inventories')) {
                $lowStockItems = \App\Models\Inventory::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->get();
                $view->with([
                    'globalLowStockCount' => $lowStockItems->count(),
                    'globalLowStockItems' => $lowStockItems
                ]);
            }
        });
    }
}
