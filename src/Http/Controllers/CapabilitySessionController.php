<?php

namespace LechugaNegra\AccessManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use LechugaNegra\AccessManager\Services\CapabilitySessionService;

class CapabilitySessionController extends Controller
{
    protected $capabilitySessionService;

    public function __construct(CapabilitySessionService $capabilitySessionService)
    {
        $this->capabilitySessionService = $capabilitySessionService;
    }

    /**
     * Obtiene los permisos de sesión.
     *
     * @param Request $request Parámetros opcionales.
     * @return \Illuminate\Http\JsonResponse Otorga el arreglo de permisos (200) o error (404).
     */
    public function permissions(Request $request)
    {
        try {
            $registers = $this->capabilitySessionService->permissions($request->all());
            return response()->json($registers, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
