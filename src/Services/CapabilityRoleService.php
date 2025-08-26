<?php

namespace LechugaNegra\AccessManager\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use LechugaNegra\AccessManager\Models\CapabilityRole;
use LechugaNegra\AccessManager\Models\RelationEntityRole;

class CapabilityRoleService
{
    /**
     * Listar todos los roles con paginación.
     *
     * @param array $filters Filtros de búsqueda como 'page', 'size', 'search', etc.
     * @return array Lista paginada de roles en formato array.
     */
    public function list($filters)
    {
        $page = $filters['page'] ?? config('accessmanager.default_page');
        $size = $filters['size'] ?? config('accessmanager.default_size');

        $query = CapabilityRole::query();

        $this->filters($query, $filters);

        return $query->paginate($size, ['*'], 'page', $page);
    }

    /**
     * Obtener varios roles sin paginación, usando skip y take.
     *
     * @param array $filters Filtros incluyendo 'skip', 'take', 'search', etc.
     * @return object Colección de roles.
     */
    public function all(array $filters): object
    {
        $skip = $filters['skip'] ?? config('accessmanager.default_skip');
        $take = $filters['take'] ?? config('accessmanager.default_take');

        $query = CapabilityRole::query();

        $this->filters($query, $filters);

        return $query->skip($skip)->take($take)->get();
    }

    /**
     * Obtener roles con información básica (id y nombre).
     *
     * @param array $filters Filtros de búsqueda como 'take', 'search', etc.
     * @return object Colección simplificada de roles.
     */
    public function options(array $filters): object
    {
        $take = $filters['take'] ?? config('accessmanager.default_take');

        $query = CapabilityRole::query();

        $this->filters($query, $filters);

        return $query->select(['id', 'name'])->skip(0)->take($take)->get();
    }

    /**
     * Crear un nuevo rol.
     *
     * @param array $data Datos del rol: 'name', 'code', 'status', 'created_by'.
     * @return CapabilityRole El nuevo rol creado.
     */
    public function create(array $data)
    {
        DB::beginTransaction();
    
        try {
            // Crear rol
            $role = CapabilityRole::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'status' => $data['status'] ?? 'inactive',
                'created_by' => $data['created_by'],
            ]);

            // Procesar permissions
            if (!empty($data['permissions'])) {
                (new CapabilityPermissionService($this))->assigned($role, $data['permissions']);
            }

            // Si todo es exitoso, confirmar la transacción
            DB::commit();

            // Agregar roles al modelo
            $role->load('permissions');

            return $role;
        } catch (\Exception $e) {
            // Si ocurre un error, revertir la transacción
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar un rol existente (no permite modificar 'code' ni 'created_by').
     *
     * @param int $id ID del rol.
     * @param array $data Datos a actualizar: 'name', 'status'.
     * @return CapabilityRole Rol actualizado.
     * @throws ModelNotFoundException Si el rol no existe.
     */
    public function update($id, array $data)
    {
        DB::beginTransaction();

        try {
            $role = CapabilityRole::find($id);

            if (!$role) {
                throw new ModelNotFoundException('Role not found');
            }

            // No permitir la modificación de 'code' y 'created_by'
            $role->name = $data['name'];
            $role->status = $data['status'];
            $role->save();

            // Procesar permissions
            if (!empty($data['permissions'])) {
                (new CapabilityPermissionService($this))->assigned($role, $data['permissions'], true);
            }

            // Si todo es exitoso, confirmar la transacción
            DB::commit();

            // Agregar roles al modelo
            $role->load('permissions');

            return $role;
        } catch (\Exception $e) {
            // Si ocurre un error, revertir la transacción
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mostrar los detalles de un rol por su ID.
     *
     * @param int $id ID del rol.
     * @return CapabilityRole Instancia del rol encontrado.
     * @throws ModelNotFoundException Si no se encuentra el rol.
     */
    public function show($id)
    {
        $role = CapabilityRole::find($id);

        if (!$role) {
            throw new ModelNotFoundException('Role not found');
        }

        return $role;
    }

    /**
     * Eliminar un rol por su ID.
     *
     * @param int $id ID del rol.
     * @return bool True si se elimina correctamente.
     * @throws ModelNotFoundException Si no se encuentra el rol.
     */
    public function delete($id)
    {
        $role = CapabilityRole::find($id);

        if (!$role) {
            throw new ModelNotFoundException('Role not found');
        }

        $role->delete();

        return true;
    }

    /**
     * Aplicar filtros dinámicos a la consulta de roles.
     *
     * @param Builder $query Consulta base de Eloquent (por referencia).
     * @param array $filters Filtros como 'search', 'code', 'status', etc.
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
            $query->where('code', '=', $filters['code']);
        }
        if (!empty($filters['name'])) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($filters['name']) . '%']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $orderField = $filters['order_field'] ?? 'updated_at';
        $orderSort = $filters['order_sort'] ?? 'desc';
        $query->orderBy($orderField, $orderSort);
    }

    /**
     * Asignar uno o varios roles a una entidad (usuario, grupo, etc.).
     *
     * @param string $entityModule Módulo o tipo de entidad.
     * @param string $entityId ID de la entidad.
     * @param array|int $roleIds IDs de los roles a asignar.
     * @param bool $update Si es true, sincroniza eliminando los roles antiguos.
     * @return void
     */
    public function assigned(string $entityModule, string $entityId, $roleIds, bool $update = false): void
    {
        if (empty($roleIds)) {
            return;
        }

        $roleIds = array_map('intval', $roleIds); // asegurar formato entero

        if ($update) {
            // Obtener roles actuales en DB
            $existing = RelationEntityRole::where('entity_module', $entityModule)
                ->where('entity_id', $entityId)
                ->pluck('capability_role_id')
                ->toArray();
    
            // Determinar qué roles eliminar
            $toDelete = array_diff($existing, $roleIds);
            if (!empty($toDelete)) {
                RelationEntityRole::where('entity_module', $entityModule)
                    ->where('entity_id', $entityId)
                    ->whereIn('capability_role_id', $toDelete)
                    ->delete();
            }
    
            // Determinar qué roles agregar
            $toInsert = array_diff($roleIds, $existing);
            if (!empty($toInsert)) {
                $insert = [];
    
                foreach ($toInsert as $roleId) {
                    $insert[] = [
                        'entity_module' => $entityModule,
                        'entity_id' => $entityId,
                        'capability_role_id' => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
    
                RelationEntityRole::insert($insert);
            }
        } else {
            // Solo insertar si no es actualización
            $insert = [];

            foreach ($roleIds as $roleId) {
                $insert[] = [
                    'entity_module' => $entityModule,
                    'entity_id' => $entityId,
                    'capability_role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            RelationEntityRole::insert($insert);
        }
    }
}
