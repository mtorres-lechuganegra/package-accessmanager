<?php

namespace LechugaNegra\AccessManager\Services;

use LechugaNegra\AccessManager\Models\CapabilityRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CapabilityRoleService
{
    /**
     * Crear un nuevo CapabilityRole.
     */
    public function create(array $data)
    {
        return CapabilityRole::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'created_by' => $data['created_by'],
            'status' => $data['status'] ?? 'inactive',
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
    public function destroy($id)
    {
        $role = CapabilityRole::find($id);

        if (!$role) {
            throw new ModelNotFoundException('Role not found');
        }

        // Eliminar físicamente el rol
        $role->delete();

        return true;
    }

    /**
     * Listar todos los CapabilityRoles con paginación.
     */
    public function index($filters)
    {
        $page = $filters['page'] ?? config('admin.pagination.default_page');
        $size = $filters['size'] ?? config('admin.pagination.default_size');
        
        $query = CapabilityRole::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($size, ['*'], 'page', $page)->toArray();
    }
}
