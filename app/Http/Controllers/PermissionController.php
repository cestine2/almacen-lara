<?php

namespace App\Http\Controllers;

// use App\Services\PermissionService; // Si tienes un servicio de permisos
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controller;

class PermissionController extends Controller
{
    // protected $permissionService;

    public function __construct(/* PermissionService $permissionService */)
    {
        $this->middleware('auth:api');
        $this->middleware('permission:manage-roles');
    }

    /**
     * Muestra una lista de todos los permisos disponibles.
     * Endpoint: GET /api/permissions
     * Protegido por JWT. Opcionalmente requiere permiso.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all(['id', 'name', 'description']);
        return response()->json($permissions, Response::HTTP_OK);
    }
}
