<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BusinessUnitController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/business-units', [AuthController::class, 'getUserBusinessUnits']);
    Route::get('/user/menus', [AuthController::class, 'getUserMenus']);
    Route::post('/select-business-unit', [AuthController::class, 'selectBusinessUnit']);
    Route::post('/switch-business-unit', [AuthController::class, 'switchBusinessUnit']);
    
    // Users CRUD (Admin only)
    Route::apiResource('users', UserController::class);
    
    // User Access Management (Admin only)
    Route::get('/users/{id}/access', [UserController::class, 'getAccessData']);
    Route::post('/users/{id}/business-units', [UserController::class, 'assignBusinessUnits']);
    Route::post('/users/{id}/menus', [UserController::class, 'assignMenus']);
    
    // Business Units CRUD (Admin only)
    Route::apiResource('business-units', BusinessUnitController::class);
    
    // Menus CRUD (Admin only)
    Route::apiResource('menus', MenuController::class);
    Route::get('/menus-tree', [MenuController::class, 'tree']);
    
    // Customers CRUD (Filtered by selected business unit)
    Route::post('/customers/bulk-delete', [CustomerController::class, 'bulkDelete']);
    Route::apiResource('customers', CustomerController::class);
});
