<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PrivilegeUserController;
use App\Http\Controllers\BusinessUnitController;
use App\Http\Controllers\TransaksiController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('/business-units/list', [BusinessUnitController::class, 'publicList']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/privileges', [AuthController::class, 'getUserPrivileges']);
    Route::post('/switch-business-unit', [AuthController::class, 'switchBusinessUnit']);
    
    // Users CRUD
    Route::apiResource('users', UserController::class);
    
    // Menus CRUD
    Route::get('/menus/tree', [MenuController::class, 'tree']);
    Route::apiResource('menus', MenuController::class);
    
    // Privilege Users CRUD
    Route::apiResource('privilege-users', PrivilegeUserController::class);
    
    // Business Units CRUD
    Route::apiResource('business-units', BusinessUnitController::class);
    
    // Transaksi CRUD
    Route::apiResource('transaksis', TransaksiController::class);
});
