<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\SucursalService;
use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use App\Http\Resources\SucursalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Exceptions\SucursalCannotBeDeletedException;

class SucursalController extends Controller
{
    protected $sucursalService;

    public function __construct(SucursalService $sucursalService)
    {
        $this->sucursalService = $sucursalService;
        $this->middleware('auth:api');
        $this->middleware('permission:manage-branches');
    }

    /**
     * Muestra una lista de todas las sucursales activas.
     * Endpoint: GET /api/sucursales
     * Protegido por JWT y requiere permiso 'manage-branches' (o 'view-branches').
     *
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $onlyActive = $request->query('status', 'active') === 'active';
        $sucursales = $this->sucursalService->listSucursales($onlyActive);
        return SucursalResource::collection($sucursales);
    }

    /**
     * Muestra los detalles de una sucursal específica (solo si está activa).
     * Endpoint: GET /api/sucursales/{id}
     * Protegido por JWT y requiere permiso 'manage-branches' (o 'view-branches').
     *
     * @param int $id
     * @return SucursalResource|JsonResponse
     */
    public function show(int $id): SucursalResource|JsonResponse
    {
        $sucursal = $this->sucursalService->findSucursal($id);

        if (!$sucursal) {
            return response()->json(['message' => 'Sucursal no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return new SucursalResource($sucursal);
    }

    /**
     * Almacena una nueva sucursal.
     * Endpoint: POST /api/sucursales
     * Protegido por JWT y requiere permiso 'manage-branches'.
     *
     * @param StoreSucursalRequest $request <-- Usa el Form Request para validación y autorización
     * @return JsonResponse
     */
    public function store(StoreSucursalRequest $request): JsonResponse
    {
        $sucursal = $this->sucursalService->createSucursal($request->validated());
        return response()->json(new SucursalResource($sucursal), Response::HTTP_CREATED);
    }

    /**
     * Actualiza una sucursal existente.
     * Endpoint: PUT/PATCH /api/sucursales/{id}
     * Protegido por JWT y requiere permiso 'manage-branches'.
     *
     * @param UpdateSucursalRequest $request <-- Usa el Form Request para validación y autorización
     * @param int $id ID de la sucursal a actualizar
     * @return SucursalResource|JsonResponse
     */
    public function update(UpdateSucursalRequest $request, int $id): SucursalResource|JsonResponse
    {
        $sucursal = $this->sucursalService->updateSucursal($id, $request->validated());

        if (!$sucursal) {
            return response()->json(['message' => 'Sucursal no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return new SucursalResource($sucursal, Response::HTTP_OK);
    }

    /**
     * "Elimina" una sucursal cambiando su estado a false.
     * Endpoint: DELETE /api/sucursales/{id}
     * Protegido por JWT y requiere permiso 'manage-branches'.
     *
     * @param int $id ID de la sucursal a "eliminar"
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->sucursalService->deleteSucursal($id);

            if (!$success) {
                return response()->json(['message' => 'Sucursal no encontrada'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Sucursal desactivada con éxito'], Response::HTTP_OK);

        } catch (SucursalCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar la sucursal', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * Restaura una sucursal cambiando su estado a true.
     * Endpoint: POST /api/sucursales/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-branches'.
     * (Nota: Este endpoint no es parte del resource controller por defecto y debe definirse manualmente en rutas).
     *
     * @param int $id ID de la sucursal a restaurar
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        try {
        $success = $this->sucursalService->restoreSucursal($id);

        if (!$success) {
            return response()->json(['message' => 'Sucursal no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Sucursal restaurada con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
        return response()->json(['message' => 'Se produjo un error al restaurar la sucursal', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
