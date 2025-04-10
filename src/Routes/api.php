<?php

use Illuminate\Support\Facades\Route;
use LechugaNegra\AccessManager\Http\Controllers\CapabilityRoleController;

// Route::middleware(['auth:api', 'capability.access'])->group(function () {
Route::middleware(['capability.access'])->group(function () {
    Route::prefix('api/access')->group(function () {
        Route::prefix('capability/roles')->group(function () {
            Route::get('/', [CapabilityRoleController::class, 'index']); // Listar roles
            Route::post('/', [CapabilityRoleController::class, 'store']); // Crear rol
            Route::get('/{id}', [CapabilityRoleController::class, 'show']); // Ver detalle
            Route::put('/{id}', [CapabilityRoleController::class, 'update']); // Actualizar rol
            Route::delete('/{id}', [CapabilityRoleController::class, 'destroy']); // Eliminar rol
        });
    });
});