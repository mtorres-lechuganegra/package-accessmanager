<?php

namespace LechugaNegra\AccessManager\Http\Controllers;

use App\Http\Controllers\Controller;
use LechugaNegra\AccessManager\Http\Requests\StoreCapabilityRoleRequest;
use LechugaNegra\AccessManager\Http\Requests\UpdateCapabilityRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Http\Resources\CapabilityRoleResource;
use LechugaNegra\AccessManager\Services\CapabilityRoleService;

class CapabilityRoleController extends Controller
{
    protected $capabilityRoleService;

    public function __construct(CapabilityRoleService $capabilityRoleService)
    {
        $this->capabilityRoleService = $capabilityRoleService;
    }

    /**
     * Listar todos los registros con paginación.
     *
     * @param Request $request Parámetros de filtrado, ordenamiento y paginación.
     * @return \Illuminate\Http\JsonResponse Lista paginada de roles (200) o error (404).
     */
    public function index(Request $request)
    {
        try {
            $roles = $this->capabilityRoleService->list($request->all());
            return response()->json($roles, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Obtener todos los registros sin paginación.
     *
     * @param Request $request Parámetros opcionales para filtrar o limitar resultados.
     * @return \Illuminate\Http\JsonResponse Colección completa de roles (200) o error (404).
     */
    public function all(Request $request)
    {
        try {
            $registers = $this->capabilityRoleService->all($request->all());
            return response()->json([
                'data' => $registers
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Listado simple para selects o combos.
     *
     * @param Request $request Parámetros opcionales para filtrar resultados.
     * @return \Illuminate\Http\JsonResponse Lista simplificada de roles (200) o error (404).
     */
    public function options(Request $request)
    {
        try {
            $registers = $this->capabilityRoleService->options($request->all());
            return response()->json([
                'data' => $registers
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Crear un nuevo rol.
     *
     * @param StoreCapabilityRoleRequest $request Datos validados para crear el rol.
     * @return \Illuminate\Http\JsonResponse Detalle del rol creado (201) o error (404).
     */
    public function store(StoreCapabilityRoleRequest $request)
    {
        try {
            // Obtener al usuario logueado
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
        
            $data = $request->validated();
            $data['created_by'] = $user->id;
        
            $role = $this->capabilityRoleService->create($data);

            return (new CapabilityRoleResource($role))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Actualizar un rol existente.
     *
     * @param UpdateCapabilityRoleRequest $request Datos validados para la actualización.
     * @param int|string $id Identificador del rol a actualizar.
     * @return \Illuminate\Http\JsonResponse Rol actualizado (200) o error (404).
     */
    public function update(UpdateCapabilityRoleRequest $request, $id)
    {
        try {
            $role = $this->capabilityRoleService->update($id, $request->validated());

            return (new CapabilityRoleResource($role))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Mostrar un rol específico por ID.
     *
     * @param int|string $id Identificador único del rol.
     * @return \Illuminate\Http\JsonResponse Detalle del rol (200) o error si no se encuentra (404).
     */
    public function show($id)
    {
        try {
            $role = $this->capabilityRoleService->show($id);

            return (new CapabilityRoleResource($role))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Eliminar un rol existente.
     *
     * @param int|string $id Identificador del rol a eliminar.
     * @return \Illuminate\Http\JsonResponse Mensaje de éxito (200) o error (404).
     */
    public function destroy($id)
    {
        try {
            $this->capabilityRoleService->delete($id);
            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
