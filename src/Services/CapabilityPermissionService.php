<?php

namespace LechugaNegra\AccessManager\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Models\CapabilityPermission;
use Lechuganegra\AccessManager\Models\CapabilityRole;

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
        $page = $filters['page'] ?? config('accessmanager.pagination.default_page');
        $size = $filters['size'] ?? config('accessmanager.pagination.default_size');
        
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
        $skip = $filters['skip'] ?? config('accessmanager.pagination.default_skip');
        $take = $filters['take'] ?? config('accessmanager.pagination.default_take');

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
        $take = $filters['take'] ?? config('accessmanager.pagination.default_take');

        $query = CapabilityPermission::query();

        $this->filters($query, $filters);

        return $query->select(['id', 'name'])->skip(0)->take($take)->get();
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
        $role = CapabilityPermission::find($id);

        if (!$role) {
            throw new ModelNotFoundException('Role not found');
        }

        return $role;
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
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }
        if (!empty($filters['code'])) {
            $query->where('code', '=', $filters['question']);
        }
        if (!empty($filters['name'])) {
            $query->where('keywords', 'like', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $orderField = $filters['order_field'] ?? 'updated_at';
        $orderSort = $filters['order_sort'] ?? 'desc';
        $query->orderBy($orderField, $orderSort);
    }

    /**
     * Asignar uno o varios roles a una entidad (usuario, grupo, etc.).
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
