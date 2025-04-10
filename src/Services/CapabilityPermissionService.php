<?php

namespace LechugaNegra\AccessManager\Services;

use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Models\CapabilityPermission;

class CapabilityPermissionService
{
    /**
     * Verifica si el usuario tiene permiso para una ruta determinada.
     *
     * @param string $routeName
     * @return bool
     */
    public function hasPermissionForRoute(string $routeName): bool
    {
        // Obtener el permiso relacionado con la ruta solicitada
        $permission = CapabilityPermission::whereHas('route', function($query) use ($routeName) {
            $query->where('path', $routeName);
        })->first();

        if (!$permission) {
            return false;
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario tiene el permiso correspondiente a través de los roles asignados
        return $user->roles->flatMap(function ($role) {
            return $role->permissions;  // Relación de muchos a muchos entre roles y permisos
        })->contains('id', $permission->id);
    }
}
