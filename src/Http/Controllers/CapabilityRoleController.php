<?php

namespace LechugaNegra\AccessManager\Http\Controllers;

use App\Http\Controllers\Controller;
use LechugaNegra\AccessManager\Http\Requests\StoreCapabilityRoleRequest;
use LechugaNegra\AccessManager\Http\Requests\UpdateCapabilityRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Services\CapabilityRoleService;

class CapabilityRoleController extends Controller
{
    protected $capabilityRoleService;

    public function __construct(CapabilityRoleService $capabilityRoleService)
    {
        $this->capabilityRoleService = $capabilityRoleService;
    }

    /**
     * Almacenar un nuevo CapabilityRole.
     */
    public function store(StoreCapabilityRoleRequest $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
        
            $data = $request->validated();
            $data['created_by'] = $user->id;
        
            $role = $this->capabilityRoleService->create($data);
        
            return response()->json($role, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Actualizar un CapabilityRole.
     */
    public function update(UpdateCapabilityRoleRequest $request, $id)
    {
        try {
            $role = $this->capabilityRoleService->update($id, $request->validated());
            return response()->json($role, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Mostrar un CapabilityRole por ID.
     */
    public function show($id)
    {
        try {
            $role = $this->capabilityRoleService->show($id);
            return response()->json($role, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Eliminar un CapabilityRole.
     */
    public function destroy($id)
    {
        try {
            $this->capabilityRoleService->destroy($id);
            return response()->json(['message' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Listar todos los CapabilityRoles con paginación.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->get('page', config('accessmanager.default_page'));
            $size = $request->get('size', config('accessmanager.default_size'));
            
            $roles = $this->capabilityRoleService->index($page, $size);
            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
