<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RBACController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\SaleOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // RBAC Management (Super Admin only)
    Route::middleware('role:super_admin')->prefix('rbac')->name('rbac.')->group(function() {
        Route::get('/superadmin-report', [DashboardController::class, 'superadminReport'])->name('superadmin_report');
        
        Route::get('/users', [RBACController::class, 'userRolesIndex'])->name('users');
        Route::post('/users', [RBACController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [RBACController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [RBACController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/assign', [RBACController::class, 'assignRole'])->name('users.assign');
        
        Route::get('/roles', [RBACController::class, 'rolePermissionsIndex'])->name('roles');
        Route::post('/roles', [RBACController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{id}', [RBACController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{id}', [RBACController::class, 'destroyRole'])->name('roles.destroy');
        Route::post('/roles/sync', [RBACController::class, 'syncPermissions'])->name('roles.sync');
        Route::get('/logs', [RBACController::class, 'systemLogs'])->name('logs');
        Route::post('/logs/{id}/restore', [RBACController::class, 'restoreResource'])->name('logs.restore');
    });

    // Sales Orders
    Route::middleware('permission:manage_orders')->prefix('sales')->name('sales.')->group(function() {
        Route::get('/', [SaleOrderController::class, 'index'])->name('index');
        Route::get('/export', [SaleOrderController::class, 'exportCSV'])->name('export');
        Route::get('/create', [SaleOrderController::class, 'create'])->name('create');
        Route::post('/store', [SaleOrderController::class, 'store'])->name('store');
        Route::get('/product/{id}/price', [SaleOrderController::class, 'getProductPrice'])->name('product.price');
        Route::get('/{id}', [SaleOrderController::class, 'show'])->name('show');
        Route::get('/{id}/print', [SaleOrderController::class, 'printInvoice'])->name('print');
        Route::post('/{id}/confirm', [SaleOrderController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/dispatch', [SaleOrderController::class, 'dispatch'])->name('dispatch');
        Route::post('/{id}/cancel', [SaleOrderController::class, 'cancel'])->name('cancel');
    });

    // Inventory Management
    Route::middleware('permission:manage_inventory')->prefix('inventory')->name('inventory.')->group(function() {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/adjust-stock', [InventoryController::class, 'adjustStock'])->name('adjust_stock');

        // Audit Sub-module
        Route::prefix('audit')->name('audit.')->group(function() {
            Route::get('/', [AuditController::class, 'index'])->name('index');
            Route::get('/create', [AuditController::class, 'create'])->name('create');
            Route::post('/store', [AuditController::class, 'store'])->name('store');
            Route::get('/{id}', [AuditController::class, 'show'])->name('show');
            Route::post('/{id}/items', [AuditController::class, 'updateItems'])->name('items.update');
            Route::post('/{id}/approve', [AuditController::class, 'approve'])->name('approve');
        });
    });

    // Master Management
    Route::middleware('permission:manage_masters')->prefix('master')->name('master.')->group(function() {
        Route::get('/categories', [MasterController::class, 'categoriesIndex'])->name('categories');
        Route::post('/categories', [MasterController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [MasterController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [MasterController::class, 'destroyCategory'])->name('categories.destroy');

        Route::get('/units', [MasterController::class, 'unitsIndex'])->name('units');
        Route::post('/units', [MasterController::class, 'storeUnit'])->name('units.store');
        Route::put('/units/{id}', [MasterController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{id}', [MasterController::class, 'destroyUnit'])->name('units.destroy');

        Route::get('/products', [MasterController::class, 'productsIndex'])->name('products');
        Route::post('/products', [MasterController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{id}', [MasterController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{id}', [MasterController::class, 'destroyProduct'])->name('products.destroy');
        Route::get('/products-export', [MasterController::class, 'exportProductsCSV'])->name('products.export');
        Route::get('/products/{id}/stock', [MasterController::class, 'productStockIndex'])->name('products.stock');
        Route::post('/products/{id}/stock', [MasterController::class, 'storeStockTransaction'])->name('products.stock.store');
        Route::get('/products/{id}/stock/export', [MasterController::class, 'exportStockTransactionsCSV'])->name('products.stock.export');

        // CSV Import Routes
        Route::post('/categories/import', [MasterController::class, 'importCategories'])->name('categories.import');
        Route::get('/categories/sample', [MasterController::class, 'downloadCategorySample'])->name('categories.sample');
        
        Route::post('/units/import', [MasterController::class, 'importUnits'])->name('units.import');
        Route::get('/units/sample', [MasterController::class, 'downloadUnitSample'])->name('units.sample');
        
        Route::post('/products/import', [MasterController::class, 'importProducts'])->name('products.import');
        Route::get('/products/sample', [MasterController::class, 'downloadProductSample'])->name('products.sample');
    });

    // Dealer Management
    Route::middleware('permission:manage_dealers')->group(function() {
        Route::resource('dealers', \App\Http\Controllers\DealerController::class);
        Route::post('dealers/{dealer}/toggle-status', [\App\Http\Controllers\DealerController::class, 'toggleStatus'])->name('dealers.toggle-status');
    });
});

Route::get('/test-connection', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'AJAX connection to Laravel is working perfectly!'
    ]);
});
