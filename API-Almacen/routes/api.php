<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\UserController;

// Rutas de Autenticación que NO requieren un token JWT
Route::post('/auth/login', [AuthController::class, 'login']);

// Grupo de Rutas PROTEGIDAS por el middleware de autenticación JWT ('auth:api')
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/permissions', [PermissionController::class, 'index']);

    // --- RUTAS DE NEGOCIO ---
    Route::prefix('sucursales')->middleware('permission:manage-branches')->group(function () {
        Route::get('/', [SucursalController::class, 'index']);
        Route::post('/', [SucursalController::class, 'store']);
        Route::get('/{id}', [SucursalController::class, 'show']);
        Route::put('/{id}', [SucursalController::class, 'update']);
        Route::patch('/{id}', [SucursalController::class, 'update']);
        Route::delete('/{id}', [SucursalController::class, 'destroy']);
        Route::post('/{id}/restore', [SucursalController::class, 'restore']);
    });

    Route::prefix('proveedores')->middleware('permission:manage-suppliers')->group(function () {
        Route::get('/', [ProveedorController::class, 'index']);
        Route::post('/', [ProveedorController::class, 'store']);
        Route::get('/{id}', [ProveedorController::class, 'show']);
        Route::put('/{id}', [ProveedorController::class, 'update']);
        Route::patch('/{id}', [ProveedorController::class, 'update']);
        Route::delete('/{id}', [ProveedorController::class, 'destroy']);
        Route::post('/{id}/restore', [ProveedorController::class, 'restore']);
    });

    Route::prefix('colores')->middleware('permission:manage-colors')->group(function () {
        Route::get('/', [ColorController::class, 'index']);
        Route::post('/', [ColorController::class, 'store']);
        Route::get('/{id}', [ColorController::class, 'show']);
        Route::put('/{id}', [ColorController::class, 'update']);
        Route::patch('/{id}', [ColorController::class, 'update']);
        Route::delete('/{id}', [ColorController::class, 'destroy']);
        Route::post('/{id}/restore', [ColorController::class, 'restore']);
    });

    Route::prefix('categorias')->middleware('permission:manage-product-categories')->group(function () {
        Route::get('/', [CategoriaController::class, 'index']);
        Route::post('/', [CategoriaController::class, 'store']);
        Route::get('/{id}', [CategoriaController::class, 'show']);
        Route::put('/{id}', [CategoriaController::class, 'update']);
        Route::patch('/{id}', [CategoriaController::class, 'update']);
        Route::delete('/{id}', [CategoriaController::class, 'destroy']);
        Route::post('/{id}/restore', [CategoriaController::class, 'restore']);
    });

    Route::prefix('productos')->middleware('permission:manage-products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::patch('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::post('/{id}/restore', [ProductController::class, 'restore']);
    });

    Route::prefix('materiales')->middleware('permission:manage-materials')->group(function () {
        Route::get('/', [MaterialController::class, 'index']);
        Route::post('/', [MaterialController::class, 'store']);
        Route::get('/{id}', [MaterialController::class, 'show']);
        Route::put('/{id}', [MaterialController::class, 'update']);
        Route::patch('/{id}', [MaterialController::class, 'update']);
        Route::delete('/{id}', [MaterialController::class, 'destroy']);
        Route::post('/{id}/restore', [MaterialController::class, 'restore']);
   });

    Route::prefix('inventarios')->middleware('permission:register-inventory')->group(function () {
        Route::get('/', [InventarioController::class, 'index']);
        Route::post('/', [InventarioController::class, 'store']);
        Route::get('/{id}', [InventarioController::class, 'show']);
        Route::put('/{id}', [InventarioController::class, 'update']);
        Route::patch('/{id}', [InventarioController::class, 'update']);
        Route::delete('/{id}', [InventarioController::class, 'destroy']);
        Route::post('/{id}/restore', [InventarioController::class, 'restore']);
    });

    Route::prefix('movimientos-inventario')->middleware('permission:register-inventory-movement')->group(function () {
        Route::get('/', [MovimientoInventarioController::class, 'index']);
        Route::post('/', [MovimientoInventarioController::class, 'store']);
        Route::get('/{id}', [MovimientoInventarioController::class, 'show']);
    });

    Route::prefix('usuarios')->middleware('permission:manage-users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::get('/barcode/{barcode}', [BarcodeController::class, 'generateImage'])->middleware('permission:manage-suppliers');
});

