<?php

namespace LechugaNegra\AccessManager\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Models\CapabilityPermission;
use LechugaNegra\AccessManager\Models\CapabilityRole;
use LechugaNegra\AccessManager\Models\CapabilityRoute;
use LechugaNegra\AccessManager\Models\RelationEntityRole;
use LechugaNegra\AccessManager\Scopes\VisiblePermissionScope;

class CapabilityPermissionService
{
    /**
     * Listar todos los CapabilityPermissions con paginación.
     *
     * @param array $filters Filtros opcionales como 'search', 'type', 'order_field', 'order_sort', etc.
     * @return array Resultado paginado con metadatos y registros.
     */
    public function list($filters)
    {
        $page = $filters['page'] ?? config('accessmanager.default_page');
        $size = $filters['size'] ?? config('accessmanager.default_size');
        
        $query = CapabilityPermission::query();

        $this->filters($query, $filters);

        return $query->paginate($size, ['*'], 'page', $page)->toArray();
    }

    /**
     * Obtener todos los CapabilityPermissions sin paginación.
     *
     * @param array $filters Filtros opcionales y parámetros de paginación manual como 'skip' y 'take'.
     * @return object Lista de registros obtenidos.
     */
    public function all(array $filters): object
    {
        $skip = $filters['skip'] ?? config('accessmanager.default_skip');
        $take = $filters['take'] ?? config('accessmanager.default_take');

        $query = CapabilityPermission::query();

        $this->filters($query, $filters);

        return $query->skip($skip)->take($take)->get();
    }

    /**
     * Listado simplificado de opciones de permisos.
     *
     * @param array $filters Filtros opcionales como 'search' o 'type'.
     * @return object Lista con solo campos 'id' y 'name' para usar en selects o combos.
     */
    public function options(array $filters): object
    {
        $take = $filters['take'] ?? config('accessmanager.default_take');

        $query = CapabilityPermission::with('module:id,code,name')
            ->select('id', 'name', 'capability_module_id');
    
        $this->filters($query, $filters);

        // Paginación aplicada ANTES de agrupar
        $output = $query->skip(0)->take($take)->get();
    
        // Agrupar por módulo solo los permisos obtenidos
        if (!empty($filters['group'])) {
            return $output->groupBy('capability_module_id')->map(function ($group) {
                $module = $group->first()->module;
    
                return [
                    'id' => $module->id,
                    'code' => $module->code,
                    'name' => $module->name,
                    'permissions' => $group->map(fn($permission) => [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ])->values()
                ];
            })->values();
        } else {
            // Retornar valores simples
            return $output->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name
                ];
            })->values();
        }
    }

    /**
     * Mostrar un CapabilityPermission por ID.
     *
     * @param int|string $id Identificador del permiso.
     * @return CapabilityPermission El permiso solicitado.
     *
     * @throws ModelNotFoundException Si no se encuentra el permiso.
     */
    public function show($id)
    {
        $permission = CapabilityPermission::find($id);

        if (!$permission) {
            throw new ModelNotFoundException('Permission not found');
        }

        return $permission;
    }

    /**
     * Aplicar filtros comunes a la consulta de permisos.
     *
     * @param Builder $query Instancia del query builder.
     * @param array $filters Filtros disponibles: 'search', 'code', 'name', 'type', 'order_field', 'order_sort'.
     * @return void
     */
    public function filters(Builder &$query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $search = strtolower($filters['search']);
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
            });
        }
        if (!empty($filters['code'])) {
            $query->where('code', '=', $filters['question']);
        }
        if (!empty($filters['name'])) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($filters['name']) . '%']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $orderField = $filters['order_field'] ?? 'updated_at';
        $orderSort = $filters['order_sort'] ?? 'desc';
        $query->orderBy($orderField, $orderSort);
    }

    /**
     * Asignar uno o varios permissions a un rol (capability_roles).
     *
     * @param CapabilityRole $role de entidad.
     * @param array|int $permissionIds IDs de los roles a asignar.
     * @param bool $update Si es true, sincroniza eliminando los roles antiguos.
     * @return void
     */
    public function assigned(CapabilityRole $role, $permissionIds, bool $update = false): void
    {
        if (empty($permissionIds)) {
            return;
        }
    
        $permissionIds = array_map('intval', (array) $permissionIds);
    
        if ($update) {
            // sincroniza (elimina los que no estén y agrega los nuevos)
            $role->permissions()->sync($permissionIds);
        } else {
            // simplemente agrega (sin eliminar previos)
            $role->permissions()->attach($permissionIds);
        }
    }

    /**
     * Verifica si el usuario autenticado tiene permiso para acceder a una ruta específica.
     *
     * @param string $routeName Nombre o path de la ruta.
     * @return bool True si el usuario tiene el permiso, false en caso contrario.
     */
    public function hasPermissionForRoute(string $routeName): bool
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener el permiso relacionado con la ruta solicitada
        $permissionCodes = CapabilityRoute::where('path', $routeName)
            ->first()
            ?->permissions()
            ->withoutGlobalScope(VisiblePermissionScope::class)
            ->pluck('capability_permissions.code')
            ->toArray();

        if (empty($permissionCodes)) {
            return true;
        }

        return RelationEntityRole::where('entity_module', config('accessmanager.user_entity.table'))
            ->where('entity_id', $user->id)
            ->whereHas('role.permissions', function ($query) use ($permissionCodes) {
                $query->whereIn('capability_permissions.code', $permissionCodes);
            })
            ->exists();
    }

    /**
     * Obtiene los códigos únicos de permisos asignados a una entidad según sus roles.
     *
     * @param object $user Objeto del usuario
     * @param string $entityModule Nombre del módulo (ej: 'users', 'admins', etc.)
     * @param int $entityId ID de la entidad (usuario u otra)
     * @return array Lista de códigos de permisos (sin repetidos)
     */
    public function getPermissionsByEntity(object $user, string $entityModule, int $entityId): array
    {
        if ($user->admin) {
            return CapabilityPermission::pluck('code')->toArray();
        } else {
            return RelationEntityRole::where('entity_module', $entityModule)
                ->where('entity_id', $entityId)
                ->get()
                ->pluck('role.permissions')
                ->flatten()
                ->pluck('code')
                ->unique()
                ->values()
                ->toArray();
        }
    }
}
