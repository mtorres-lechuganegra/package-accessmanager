<?php

use Illuminate\Support\Facades\Route;
use LechugaNegra\AccessManager\Http\Controllers\V1\CapabilityPermissionController;
use LechugaNegra\AccessManager\Http\Controllers\V1\CapabilityRoleController;
use LechugaNegra\AccessManager\Http\Controllers\V1\CapabilitySessionController;

Route::middleware(['auth:api', 'capability.access'])->group(function () {
    Route::prefix('access')->name('api.access.')->group(function () {
        Route::prefix('capability')->name('capability.')->group(function () {
            Route::prefix('session')->name('session.')->group(function () {
                Route::get('/permissions', [CapabilitySessionController::class, 'permissions'])->name('permissions'); // Listar roles
            });
        });
        Route::prefix('capability')->name('capability.')->group(function () {
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', [CapabilityRoleController::class, 'index'])->name('index'); // Listar roles
                Route::get('/all', [CapabilityRoleController::class, 'all'])->name('all'); // Listar roles
                Route::get('/options', [CapabilityRoleController::class, 'options'])->name('options'); // Listar roles
                Route::post('/', [CapabilityRoleController::class, 'store'])->name('store'); // Crear rol
                Route::get('/{id}', [CapabilityRoleController::class, 'show'])->name('show'); // Ver detalle
                Route::put('/{id}', [CapabilityRoleController::class, 'update'])->name('update'); // Actualizar rol
                Route::delete('/{id}', [CapabilityRoleController::class, 'destroy'])->name('destroy'); // Eliminar rol
            });
            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/', [CapabilityPermissionController::class, 'index'])->name('index'); // Listar roles
                Route::get('/all', [CapabilityPermissionController::class, 'all'])->name('all'); // Listar roles
                Route::get('/options', [CapabilityPermissionController::class, 'options'])->name('options'); // Listar roles
                Route::get('/{id}', [CapabilityPermissionController::class, 'show'])->name('show'); // Ver detalle
            });
        });
    });
});