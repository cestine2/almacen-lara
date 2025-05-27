<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\ProveedorService;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Http\Resources\ProveedorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Exceptions\ProveedorCannotBeDeletedException;


class ProveedorController extends Controller
{
    protected $proveedorService;

    public function __construct(ProveedorService $proveedorService)
    {
        $this->proveedorService = $proveedorService;

        $this->middleware('auth:api');
        $this->middleware('permission:manage-suppliers');
    }

    /**
     * Muestra una lista de proveedores, opcionalmente filtrada por estado.
     * Endpoint: GET /api/proveedores?status={active|all}&nombre={string}
     * Protegido por JWT y requiere permiso 'manage-suppliers'.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection <-- Tipo de retorno para colecciones de Resources
     */
    public function index(Request $request): ResourceCollection
    {

        $filters = $request->only([
            'status', 'nombre'
        ]);

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $proveedores = $this->proveedorService->listProveedores(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return ProveedorResource::collection($proveedores);
    }

    /**
     * Muestra los detalles de un proveedor específico.
     * Endpoint: GET /api/proveedores/{id}
     * Protegido por JWT y requiere permiso 'manage-suppliers'.
     *
     * @param int $id
     * @return ProveedorResource|JsonResponse <-- Tipo de retorno para un solo Resource o error 404
     */
     public function show(int $id): ProveedorResource|JsonResponse
     {
         $proveedor = $this->proveedorService->getProveedor($id, $onlyActive = false);

         if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], Response::HTTP_NOT_FOUND);
         }

         return new ProveedorResource($proveedor);
     }


    /**
     * Almacena un nuevo proveedor.
     * Endpoint: POST /api/proveedores
     * Protegido por JWT y requiere permiso 'manage-suppliers'.
     *
     * @param StoreProveedorRequest $request
     * @return JsonResponse // O ProveedorResource si no usas response()->json()
     */
    public function store(StoreProveedorRequest $request): JsonResponse
    {
        $proveedor = $this->proveedorService->createProveedor($request->validated());
        return response()->json(new ProveedorResource($proveedor), Response::HTTP_CREATED);
    }

    /**
     * Actualiza un proveedor existente.
     * Endpoint: PUT/PATCH /api/proveedores/{id}
     * Protegido por JWT y requiere permiso 'manage-suppliers'.
     *
     * @param UpdateProveedorRequest $request
     * @param int $id
     * @return ProveedorResource|JsonResponse // O ProveedorResource si no usas response()->json() en éxito
     */
    public function update(UpdateProveedorRequest $request, int $id): ProveedorResource|JsonResponse
    {
        $proveedor = $this->proveedorService->updateProveedor($id, $request->validated());

        if (!$proveedor) {
             return response()->json(['message' => 'Proveedor no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new ProveedorResource($proveedor, Response::HTTP_OK); // O response()->json(...)
    }

    /**
     * "Elimina" un proveedor cambiando su estado a false.
     * Endpoint: DELETE /api/proveedores/{id}
     * Protegido por JWT y requiere permiso 'manage-suppliers'.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->proveedorService->deleteProveedor($id);

            if (!$success) {
                return response()->json(['message' => 'Proveedor no encontrado o ya inactivo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Proveedor desactivado con éxito'], Response::HTTP_OK);

        // Si usas una excepción de negocio personalizada:
        } catch (ProveedorCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar el proveedor', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * Restaura un proveedor cambiando su estado a true.
     * Endpoint: POST /api/proveedores/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-suppliers'.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
         try {
            $success = $this->proveedorService->restoreProveedor($id);

            if (!$success) {
                return response()->json(['message' => 'Proveedor no encontrado o ya activo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Proveedor restaurado con éxito'], Response::HTTP_OK);

         } catch (\Throwable $e) {
             return response()->json(['message' => 'Se produjo un error al restaurar el proveedor', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
         }
    }
}
