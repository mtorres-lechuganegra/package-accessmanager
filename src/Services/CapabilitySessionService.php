<?php

namespace LechugaNegra\AccessManager\Services;

use Illuminate\Support\Facades\Auth;

class CapabilitySessionService
{
    protected $capabilityPermissionService;

    public function __construct(CapabilityPermissionService $capabilityPermissionService)
    {
        $this->capabilityPermissionService = $capabilityPermissionService;
    }

    /**
     * Obtiene todos los permisos de la sesión.
     *
     * @param array $filters Filtros para personalizar la respuesta de permisos.
     * @return array Obtiene los permisos de sesión.
     */
    public function permissions($filters)
    {
        $user = Auth::user();

        // Obtener los permisos de sessión
        return $this->capabilityPermissionService->getPermissionsByEntity(
            $user,
            config('accessmanager.user_entity.table'),
            $user->id
        );
    }
}
