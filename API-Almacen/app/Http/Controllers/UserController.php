<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api');
        $this->middleware('permission:manage-users');
    }

    public function index(Request $request): ResourceCollection
    {

        $filters = $request->only([
            'status', 'nombre'
        ]);

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $user = $this->userService->listUsers(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return UserResource::collection($user);
    }

    public function show(int $id): UserResource|JsonResponse
    {
        $user = $this->userService->getUser($id, $onlyActive = false);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $user->load(['sucursal', 'role']);

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json(new UserResource($user), Response::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, int $id): UserResource|JsonResponse
    {

        $user = $this->userService->updateUser($id, $request->validated());

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user, Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->userService->deleteUser($id);

            if (!$success) {
                return response()->json(['message' => 'Usuario no encontrado o ya inactivo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Usuario desactivado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar al usuario', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restaura un usuario cambiando su estado a true.
     * Endpoint: POST /api/productos/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-products'.
     */
    public function restore(int $id): JsonResponse
    {
        try {

            $success = $this->userService->restoreUser($id);

            if (!$success) {
                return response()->json(['message' => 'Producto no encontrado o ya activo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Producto restaurado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al restaurar el producto', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
