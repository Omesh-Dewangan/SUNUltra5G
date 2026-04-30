<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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
        Route::get('/users', [RBACController::class, 'userRolesIndex'])->name('users');
        Route::post('/users/assign', [RBACController::class, 'assignRole'])->name('users.assign');
        Route::get('/roles', [RBACController::class, 'rolePermissionsIndex'])->name('roles');
        Route::post('/roles/sync', [RBACController::class, 'syncPermissions'])->name('roles.sync');
    });
});

Route::get('/test-connection', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'AJAX connection to Laravel is working perfectly!'
    ]);
});
