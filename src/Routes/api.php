<?php

use Illuminate\Support\Facades\Route;
use LechugaNegra\AccessManager\Http\Controllers\CapabilityRoleController;

Route::middleware(['auth:api', 'capability.access'])->group(function () {
    Route::prefix('api/access')->name('api.access.')->group(function () {
        Route::prefix('capability/roles')->name('capability.roles.')->group(function () {
            Route::get('/', [CapabilityRoleController::class, 'index'])->name('index'); // Listar roles
            Route::get('/all', [CapabilityRoleController::class, 'all'])->name('all'); // Listar roles
            Route::get('/options', [CapabilityRoleController::class, 'options'])->name('options'); // Listar roles
            Route::post('/', [CapabilityRoleController::class, 'store'])->name('store'); // Crear rol
            Route::get('/{id}', [CapabilityRoleController::class, 'show'])->name('show'); // Ver detalle
            Route::put('/{id}', [CapabilityRoleController::class, 'update'])->name('update'); // Actualizar rol
            Route::delete('/{id}', [CapabilityRoleController::class, 'destroy'])->name('destroy'); // Eliminar rol
        });
    });
});