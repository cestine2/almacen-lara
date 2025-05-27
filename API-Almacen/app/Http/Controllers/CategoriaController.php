<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\CategoriaService;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\CategoriaCannotBeDeletedException;


class CategoriaController extends Controller
{
    protected $categoriaService;

    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;

        $this->middleware('auth:api');
        $this->middleware('permission:manage-product-categories');
    }

    /**
     * Muestra una lista de categorías, opcionalmente filtrada por estado o tipo.
     * Endpoint: GET /api/categorias?status={active|all}&type={producto|material}}
     * Endpoint: GET /api/categorias?status=all&per_page=10&type=material
     * Protegido por JWT y requiere permiso 'manage-product-categories'.
     *
     * @param Request $request
     * @return ResourceCollection <-- Tipo de retorno para colecciones de Resources
     */
  public function index(Request $request): ResourceCollection
    {

        $filters = $request->only([
            'status', 'type', 'nombre'
        ]);

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $categorias = $this->categoriaService->listCategorias(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return CategoriaResource::collection($categorias);
    }

    /**
     * Muestra los detalles de una categoría específica.
     * Endpoint: GET /api/categorias/{id}
     * Protegido por JWT y requiere permiso 'manage-product-categories'.
     *
     * @param int $id
     * @return CategoriaResource|JsonResponse <-- Tipo de retorno para un solo Resource o error 404
     */
     public function show(int $id): CategoriaResource|JsonResponse
     {
        $categoria = $this->categoriaService->getCategoria($id, $onlyActive = false);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return new CategoriaResource($categoria);
     }


    /**
     * Almacena una nueva categoría.
     * Endpoint: POST /api/categorias
     * Protegido por JWT y requiere permiso 'manage-product-categories'.
     *
     * @param StoreCategoriaRequest $request
     * @return JsonResponse // O CategoriaResource si no usas response()->json()
     */
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $categoria = $this->categoriaService->createCategoria($request->validated());
        return response()->json(new CategoriaResource($categoria), Response::HTTP_CREATED);
    }

    /**
     * Actualiza una categoría existente.
     * Endpoint: PUT/PATCH /api/categorias/{id}
     * Protegido por JWT y requiere permiso 'manage-product-categories'.
     *
     * @param UpdateCategoriaRequest $request
     * @param int $id
     * @return CategoriaResource|JsonResponse // O CategoriaResource si no usas response()->json() en éxito
     */
    public function update(UpdateCategoriaRequest $request, int $id): CategoriaResource|JsonResponse
    {
        $categoria = $this->categoriaService->updateCategoria($id, $request->validated());

        if (!$categoria) {
             return response()->json(['message' => 'Categoría no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return new CategoriaResource($categoria, Response::HTTP_OK);
    }

    /**
     * "Elimina" una categoría cambiando su estado a false.
     * Endpoint: DELETE /api/categorias/{id}
     * Protegido por JWT y requiere permiso 'manage-product-categories'.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->categoriaService->deleteCategoria($id);

            if (!$success) {
                return response()->json(['message' => 'Categoría no encontrada o ya inactiva'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Categoría desactivada con éxito'], Response::HTTP_OK);

        } catch (CategoriaCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar la categoría', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * Restaura una categoría cambiando su estado a true.
     * Endpoint: POST /api/categorias/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-product-categories'.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $success = $this->categoriaService->restoreCategoria($id);

            if (!$success) {
                return response()->json(['message' => 'Categoría no encontrada o ya activa'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Categoría restaurada con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al restaurar la categoría', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
