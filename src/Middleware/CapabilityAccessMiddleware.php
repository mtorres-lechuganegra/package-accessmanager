<?php

namespace LechugaNegra\AccessManager\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Services\CapabilityPermissionService;

class CapabilityAccessMiddleware
{
    protected $permissionService;

    public function __construct(CapabilityPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Maneja la solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('api')->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = Auth::guard('api')->user();
    
        // Si el usuario es admin (admin == 1), permitimos la acción sin verificar los permisos
        if ($user->admin == 1) {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        // Verificar si el usuario tiene el permiso correspondiente para la ruta
        if (!$this->permissionService->hasPermissionForRoute($routeName)) {
            return response()->json(['error' => 'You do not have permission for this action'], 403);
        }

        return $next($request);
    }
}
