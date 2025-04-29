<?php

namespace LechugaNegra\AccessManager\Services;

use LechugaNegra\AccessManager\Models\CapabilityRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder;

class CapabilityRoleService
{
    /**
     * Listar todos los CapabilityRoles con paginación.
     */
    public function list($filters)
    {
        $page = $filters['page'] ?? config('admin.pagination.default_page');
        $size = $filters['size'] ?? config('admin.pagination.default_size');
        
        $query = CapabilityRole::query();

        $this->filters($query, $filters);

        return $query->paginate($size, ['*'], 'page', $page)->toArray();
    }

    /**
     * Crear un nuevo CapabilityRole.
     */
    public function create(array $data)
    {
        return CapabilityRole::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'] ?? 'inactive',
            'created_by' => $data['created_by'],
        ]);
    }

    /**
     * Actualizar un CapabilityRole.
     */
    public function update($id, array $data)
    {
        $role = CapabilityRole::find($id);

        if (!$role) {
            throw new ModelNotFoundException('Role not found');
        }

        // No permitir la modificación de 'code' y 'created_by'
        $role->name = $data['name'];
        $role->status = $data['status'];
        $role->save();

        return $role;
    }

    /**
     * Mostrar un CapabilityRole por ID.
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
     * Eliminar un CapabilityRole.
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
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $orderField = $filters['order_field'] ?? 'updated_at';
        $orderSort = $filters['order_sort'] ?? 'desc';
        $query->orderBy($orderField, $orderSort);
    }
}
