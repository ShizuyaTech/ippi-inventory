<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialOutputController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\RoleController;

// Authentication Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes - Require Authentication
Route::middleware('auth')->group(function () {
    
    // Dashboard - All roles
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // All Data Stock - All roles
    Route::get('/all-stock', [DashboardController::class, 'allStock'])->name('all-stock');

    // Master Data - Permission based
    Route::middleware('permission:view-materials')->group(function () {
        Route::get('materials', [MaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/create', [MaterialController::class, 'create'])->name('materials.create')->middleware('permission:create-materials');
        Route::post('materials', [MaterialController::class, 'store'])->name('materials.store')->middleware('permission:create-materials');
        Route::get('materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
        Route::get('materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit')->middleware('permission:edit-materials');
        Route::put('materials/{material}', [MaterialController::class, 'update'])->name('materials.update')->middleware('permission:edit-materials');
        Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy')->middleware('permission:delete-materials');
        Route::get('materials-export', [MaterialController::class, 'export'])->name('materials.export')->middleware('permission:export-materials');
        Route::get('materials-template', [MaterialController::class, 'downloadTemplate'])->name('materials.template')->middleware('permission:import-materials');
        Route::post('materials-import', [MaterialController::class, 'import'])->name('materials.import')->middleware('permission:import-materials');
        Route::get('materials-pdf', [MaterialController::class, 'exportPdf'])->name('materials.pdf')->middleware('permission:export-materials');
    });
    
    Route::middleware('permission:view-suppliers')->group(function () {
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('permission:create-suppliers');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:create-suppliers');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:edit-suppliers');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:edit-suppliers');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:delete-suppliers');
        Route::get('suppliers-export', [SupplierController::class, 'export'])->name('suppliers.export')->middleware('permission:export-suppliers');
        Route::get('suppliers-template', [SupplierController::class, 'downloadTemplate'])->name('suppliers.template')->middleware('permission:import-suppliers');
        Route::post('suppliers-import', [SupplierController::class, 'import'])->name('suppliers.import')->middleware('permission:import-suppliers');
        Route::get('suppliers-pdf', [SupplierController::class, 'exportPdf'])->name('suppliers.pdf')->middleware('permission:export-suppliers');
    });
    
    Route::middleware('permission:view-customers')->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:create-customers');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:create-customers');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:edit-customers');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:edit-customers');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:delete-customers');
        Route::get('customers-export', [CustomerController::class, 'export'])->name('customers.export')->middleware('permission:export-customers');
        Route::get('customers-template', [CustomerController::class, 'downloadTemplate'])->name('customers.template')->middleware('permission:import-customers');
        Route::post('customers-import', [CustomerController::class, 'import'])->name('customers.import')->middleware('permission:import-customers');
        Route::get('customers-pdf', [CustomerController::class, 'exportPdf'])->name('customers.pdf')->middleware('permission:export-customers');
    });
    
    Route::middleware('permission:view-warehouses')->group(function () {
        Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create')->middleware('permission:create-warehouses');
        Route::post('warehouses', [WarehouseController::class, 'store'])->name('warehouses.store')->middleware('permission:create-warehouses');
        Route::get('warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit')->middleware('permission:edit-warehouses');
        Route::put('warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update')->middleware('permission:edit-warehouses');
        Route::delete('warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy')->middleware('permission:delete-warehouses');
        Route::get('warehouses-export', [WarehouseController::class, 'export'])->name('warehouses.export')->middleware('permission:export-warehouses');
        Route::get('warehouses-template', [WarehouseController::class, 'downloadTemplate'])->name('warehouses.template')->middleware('permission:import-warehouses');
        Route::post('warehouses-import', [WarehouseController::class, 'import'])->name('warehouses.import')->middleware('permission:import-warehouses');
        Route::get('warehouses-pdf', [WarehouseController::class, 'exportPdf'])->name('warehouses.pdf')->middleware('permission:export-warehouses');
    });
    
    // Material Outputs Management
    Route::middleware('permission:edit-materials')->group(function () {
        Route::post('materials/{material}/outputs', [MaterialOutputController::class, 'store'])->name('material-outputs.store');
        Route::put('material-outputs/{output}', [MaterialOutputController::class, 'update'])->name('material-outputs.update');
        Route::delete('material-outputs/{output}', [MaterialOutputController::class, 'destroy'])->name('material-outputs.destroy');
    });
    
    // User Management - Permission based
    Route::middleware('permission:view-users')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-users');
        Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-users');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-users');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-users');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-users');
        Route::get('users-export', [UserController::class, 'export'])->name('users.export')->middleware('permission:export-users');
        Route::get('users-pdf', [UserController::class, 'exportPdf'])->name('users.pdf')->middleware('permission:export-users');
    });
    
    // Roles & Permissions Management - Permission based
    Route::middleware('permission:view-roles')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create-roles');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create-roles');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:edit-roles');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:edit-roles');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete-roles');
        Route::get('roles-permissions', [RoleController::class, 'permissions'])->name('roles.permissions')->middleware('permission:manage-permissions');
        Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status')->middleware('permission:edit-roles');
    });

    // Stock Transactions - Permission based
    Route::middleware('permission:view-transactions')->group(function () {
        Route::get('transactions', [StockTransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create', [StockTransactionController::class, 'create'])->name('transactions.create')->middleware('permission:create-transactions');
        Route::post('transactions', [StockTransactionController::class, 'store'])->name('transactions.store')->middleware('permission:create-transactions');
        Route::get('transactions/{transaction}', [StockTransactionController::class, 'show'])->name('transactions.show');
    });

    // Stock Opname - Permission based
    Route::middleware('permission:view-opname')->group(function () {
        Route::get('opname', [StockOpnameController::class, 'index'])->name('opname.index');
        Route::get('opname/create', [StockOpnameController::class, 'create'])->name('opname.create')->middleware('permission:create-opname');
        Route::post('opname', [StockOpnameController::class, 'store'])->name('opname.store')->middleware('permission:create-opname');
        Route::get('opname/{opname}', [StockOpnameController::class, 'show'])->name('opname.show');
        Route::post('opname/{opname}/approve', [StockOpnameController::class, 'approve'])->name('opname.approve')->middleware('permission:approve-opname');
        Route::post('opname/{opname}/post', [StockOpnameController::class, 'post'])->name('opname.post')->middleware('permission:approve-opname');
    });

    // Production Orders - Permission based
    Route::middleware('permission:view-production-orders')->group(function () {
        Route::get('production-orders', [ProductionOrderController::class, 'index'])->name('production-orders.index');
        Route::get('production-orders/create', [ProductionOrderController::class, 'create'])->name('production-orders.create')->middleware('permission:create-production-orders');
        Route::post('production-orders', [ProductionOrderController::class, 'store'])->name('production-orders.store')->middleware('permission:create-production-orders');
        Route::get('production-orders/{productionOrder}', [ProductionOrderController::class, 'show'])->name('production-orders.show');
        Route::post('production-orders/{productionOrder}/start', [ProductionOrderController::class, 'start'])->name('production-orders.start')->middleware('permission:start-production-orders');
        Route::post('production-orders/{productionOrder}/complete', [ProductionOrderController::class, 'complete'])->name('production-orders.complete')->middleware('permission:complete-production-orders');
        Route::post('production-orders/{productionOrder}/cancel', [ProductionOrderController::class, 'cancel'])->name('production-orders.cancel')->middleware('permission:cancel-production-orders');
        Route::get('api/material-outputs', [ProductionOrderController::class, 'getOutputs'])->name('api.material-outputs');
    });
});
