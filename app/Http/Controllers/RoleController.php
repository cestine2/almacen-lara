<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\RoleService;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\AssignPermissionsToRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        $this->middleware('auth:api');
        $this->middleware('permission:manage-roles');
    }

    /**
     * Muestra una lista de roles.
     * GET /api/roles
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        $roles = $this->roleService->listRoles();
        return RoleResource::collection($roles);
    }

    /**
     * Muestra un rol específico.
     * GET /api/roles/{id}
     *
     * @param int $id
     * @return RoleResource|JsonResponse
     */
    public function show(int $id): RoleResource|JsonResponse
    {
        $role = $this->roleService->getRole($id);

        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Almacena un nuevo rol.
     * POST /api/roles
     *
     * @param StoreRoleRequest $request
     * @return JsonResponse
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());
        return response()->json(new RoleResource($role), Response::HTTP_CREATED);
    }

    /**
     * Actualiza un rol existente.
     * PUT/PATCH /api/roles/{id}
     *
     * @param UpdateRoleRequest $request
     * @param int $id
     * @return RoleResource|JsonResponse
     */
    public function update(UpdateRoleRequest $request, int $id): RoleResource|JsonResponse
    {
        $role = $this->roleService->updateRole($id, $request->validated());

        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Elimina un rol.
     * DELETE /api/roles/{id}
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->roleService->deleteRole($id);

            if (!$success) {
                return response()->json(['message' => 'Rol no encontrado'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Rol eliminado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al eliminar el rol', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Asigna/Sincroniza permisos a un rol.
     * POST /api/roles/{role}/permissions
     *
     * @param AssignPermissionsToRoleRequest $request
     * @param Role $role Usamos Route Model Binding para obtener el rol directamente
     * @return RoleResource|JsonResponse
     */
    public function assignPermissions(AssignPermissionsToRoleRequest $request, Role $role): RoleResource|JsonResponse
    {
        $permissionNames = $request->input('permissions');

        $updatedRole = $this->roleService->assignPermissionsToRole($role->id, $permissionNames);

        if (!$updatedRole) {
            return response()->json(['message' => 'Rol no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $updatedRole->load('permissions');

        return new RoleResource($updatedRole);
    }
}
