<?php

namespace LechugaNegra\AccessManager\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LechugaNegra\AccessManager\Http\Resources\v1\CapabilityPermissionAllResource;
use LechugaNegra\AccessManager\Http\Resources\v1\CapabilityPermissionIndexResource;
use LechugaNegra\AccessManager\Http\Resources\v1\CapabilityPermissionShowResource;
use LechugaNegra\AccessManager\Services\CapabilityPermissionService;

class CapabilityPermissionController extends Controller
{
    protected $capabilityPermissionService;

    public function __construct(CapabilityPermissionService $capabilityPermissionService)
    {
        $this->capabilityPermissionService = $capabilityPermissionService;
    }

    /**
     * Listar todos los registros con paginación.
     *
     * @param Request $request Parámetros opcionales como filtros, ordenamiento, etc.
     * @return \Illuminate\Http\JsonResponse Lista paginada de registros (200) o error (404).
     */
    public function index(Request $request)
    {
        try {
            $registers = $this->capabilityPermissionService->list($request->all());
            return CapabilityPermissionIndexResource::collection($registers)
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Obtener todos los registros sin paginación.
     *
     * @param Request $request Parámetros opcionales para filtrar o limitar resultados.
     * @return \Illuminate\Http\JsonResponse Colección completa de registros (200) o error (404).
     */
    public function all(Request $request)
    {
        try {
            $registers = $this->capabilityPermissionService->all($request->all());
            return CapabilityPermissionAllResource::collection($registers)
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Listado simple para selects o combos.
     *
     * @param Request $request Parámetros opcionales para filtrar resultados.
     * @return \Illuminate\Http\JsonResponse Lista simplificada de registros (200) o error (404).
     */
    public function options(Request $request)
    {
        try {
            $registers = $this->capabilityPermissionService->options($request->all());
            return response()->json([
                'data' => $registers
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Mostrar un permiso específico por su ID.
     *
     * @param int|string $id Identificador único del permiso.
     * @return \Illuminate\Http\JsonResponse Detalle del permiso (200) o error si no se encuentra (404).
     */
    public function show($id)
    {
        try {
            $permission = $this->capabilityPermissionService->show($id);

            return (new CapabilityPermissionShowResource($permission))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
