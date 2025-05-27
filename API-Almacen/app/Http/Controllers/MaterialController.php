<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\MaterialService;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Resources\MaterialResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\MaterialCannotBeDeletedException;


class MaterialController extends Controller
{
    protected $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;

        $this->middleware('auth:api');
        $this->middleware('permission:manage-materials');
    }

    /**
     * Muestra una lista paginada de materiales, opcionalmente filtrados por estado y otros criterios.
     * Endpoint: GET /api/materiales?status={active|all}&categoria_id={id}&proveedor_id={id}&color_id={id|null}&cod_articulo={string}&nombre={string}&per_page={cantidad}&page={numero}
     * Protegido por JWT y requiere permiso 'manage-materials'.
     */
    public function index(Request $request): ResourceCollection
    {

        $filters = $request->only([
            'status', 'categoria_id', 'proveedor_id',
            'cod_articulo', 'nombre'
        ]);

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $materiales = $this->materialService->listMaterials(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return MaterialResource::collection($materiales);
    }

    /**
     * Muestra los detalles de un material específico.
     * Endpoint: GET /api/materiales/{id}
     * Protegido por JWT y requiere permiso 'manage-materials'.
     */
     public function show(int $id): MaterialResource|JsonResponse
     {
        $material = $this->materialService->getMaterial($id, $onlyActive = false);

        if (!$material) {
            return response()->json(['message' => 'Material no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $material->load(['categoria', 'proveedor', 'color']);

        return new MaterialResource($material);
     }


    /**
     * Almacena un nuevo material.
     * Endpoint: POST /api/materiales
     * Protegido por JWT y requiere permiso 'manage-materials'.
     */
    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $material = $this->materialService->createMaterial($request->validated());
        return response()->json(new MaterialResource($material), Response::HTTP_CREATED);
    }

    /**
     * Actualiza un material existente.
     * Endpoint: PUT/PATCH /api/materiales/{id}
     * Protegido por JWT y requiere permiso 'manage-materials'.
     */
    public function update(UpdateMaterialRequest $request, int $id): MaterialResource|JsonResponse
    {
        $material = $this->materialService->updateMaterial($id, $request->validated());

        if (!$material) {
            return response()->json(['message' => 'Material no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new MaterialResource($material, Response::HTTP_OK);
    }

    /**
     * "Elimina" un material cambiando su estado a false.
     * Endpoint: DELETE /api/materiales/{id}
     * Protegido por JWT y requiere permiso 'manage-materials'.
     */
    public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->materialService->deleteMaterial($id);

            if (!$success) {
                return response()->json(['message' => 'Material no encontrado o ya inactivo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Material desactivado con éxito'], Response::HTTP_OK);

        } catch (MaterialCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar el material', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * Restaura un material cambiando su estado a true.
     * Endpoint: POST /api/materiales/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-materials'.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $success = $this->materialService->restoreMaterial($id);

            if (!$success) {
                return response()->json(['message' => 'Material no encontrado o ya activo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Material restaurado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al restaurar el material', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
