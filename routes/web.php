<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Authentication
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
if (app()->environment('local')) {
    Route::get('login/quick/{role}', [AuthController::class, 'quickLogin'])->name('login.quick');
}
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Dashboard (All logged-in roles)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products (Catalog view open to all roles)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show')->whereNumber('product');

    // Products Management (Manager & Admin only)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    });

    // Delete Product (Admin only)
    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('products.destroy');

    // Stock Movement Ledger (View open to all)
    Route::get('stock', [StockMovementController::class, 'index'])->name('stock.index');

    // Record Stock Movement (Admin, Manager, Staff only - Auditor is read-only)
    Route::post('stock', [StockMovementController::class, 'store'])
        ->middleware('role:admin,manager,staff')
        ->name('stock.store');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('role:admin')->name('categories.destroy');
    });

    // Suppliers
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('role:admin')->name('suppliers.destroy');
    });

    // Exports (All authenticated users)
    Route::get('export/products', [ExportController::class, 'exportProducts'])->name('export.products');
    Route::get('export/movements', [ExportController::class, 'exportMovements'])->name('export.movements');

    // User Management (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });
});
