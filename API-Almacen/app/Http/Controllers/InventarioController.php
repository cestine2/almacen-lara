<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\InventarioService;
use App\Http\Requests\StoreInventarioRequest;
use App\Http\Requests\UpdateInventarioRequest;
use App\Http\Resources\InventarioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\InventarioCannotBeDeletedException;


class InventarioController extends Controller
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;

        $this->middleware('auth:api');
        $this->middleware('permission:register-inventory');
    }

    /**
     * Muestra una lista paginada de registros de inventario, aplicando filtros desde los parámetros de la request.
     * Endpoint: GET /api/inventarios?status={active|all}&tipo={Material|Producto}&material_id={id}&producto_id={id}&sucursal_id={id}&usuario_id={id}&per_page={cantidad}&page={numero}
     * Protegido por JWT y requiere permiso 'register-inventory'.
     */
    public function index(Request $request): ResourceCollection
    {
        $filters = $request->only([
           'tipo', 'material_id', 'producto_id', 'sucursal_id',
        ]);

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $inventarios = $this->inventarioService->listInventarios(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return InventarioResource::collection($inventarios);
    }

    /**
     * Muestra los detalles de un registro de inventario específico.
     * Endpoint: GET /api/inventarios/{id}
     * Protegido por JWT y requiere permiso 'register-inventory'.
     */
     public function show(int $id): InventarioResource|JsonResponse
     {
        $inventario = $this->inventarioService->getInventario($id, $onlyActive = false);

        if (!$inventario) {
            return response()->json(['message' => 'Registro de inventario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $inventario->load(['sucursal', 'usuario', 'material', 'producto']);

        return new InventarioResource($inventario);
     }


    /**
     * Almacena un nuevo registro de inventario.
     * Endpoint: POST /api/inventarios
     * Protegido por JWT y requiere permiso 'register-inventory'.
     */
    /* public function store(StoreInventarioRequest $request): JsonResponse
    {
        try {
            $inventario = $this->inventarioService->createInventario($request->validated());
            return response()->json(new InventarioResource($inventario), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al crear el registro de inventario', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } */

    /**
     * Actualiza un registro de inventario existente.
     * Endpoint: PUT/PATCH /api/inventarios/{id}
     * Protegido por JWT y requiere permiso 'register-inventory'.
     */
    /* public function update(UpdateInventarioRequest $request, int $id): InventarioResource|JsonResponse
    {
        $inventario = $this->inventarioService->updateInventario($id, $request->validated());

        if (!$inventario) {
            return response()->json(['message' => 'Registro de inventario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new InventarioResource($inventario, Response::HTTP_OK);
    } */

    /**
     * "Elimina" un registro de inventario cambiando su estado a false.
     * Endpoint: DELETE /api/inventarios/{id}
     * Protegido por JWT y requiere permiso 'register-inventory'.
     */
    /* public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->inventarioService->deleteInventario($id);

            if (!$success) {
                return response()->json(['message' => 'Registro de inventario no encontrado o ya inactivo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Registro de inventario desactivado con éxito'], Response::HTTP_OK);

        } catch (InventarioCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar el registro de inventario', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } */

     /**
     * Restaura un registro de inventario cambiando su estado a true.
     * Endpoint: POST /api/inventarios/{id}/restore
     * Protegido por JWT y requiere permiso 'register-inventory'.
     */
    /* public function restore(int $id): JsonResponse
    {
        try {

            $success = $this->inventarioService->restoreInventario($id);

            if (!$success) {
                    return response()->json(['message' => 'Registro de inventario no encontrado o ya activo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Registro de inventario restaurado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al restaurar el registro de inventario', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } */
}
