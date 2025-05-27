<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\ColorService;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Exceptions\ColorCannotBeDeletedException;


class ColorController extends Controller
{
    protected $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;

        $this->middleware('auth:api');
        $this->middleware('permission:manage-colors');
    }

    /**
     * Muestra una lista de colores, opcionalmente filtrada por estado.
     * Endpoint: GET /api/colores?status={active|all}
     * Protegido por JWT y requiere permiso 'manage-colors'.
     *
     * @param Request $request
     * @return ResourceCollection <-- Tipo de retorno para colecciones de Resources
     */
    public function index(Request $request): ResourceCollection
    {
        $onlyActive = $request->query('status', 'active') === 'active';
        $colores = $this->colorService->listColores($onlyActive);

        return ColorResource::collection($colores);
    }

    /**
     * Muestra los detalles de un color específico.
     * Endpoint: GET /api/colores/{id}
     * Protegido por JWT y requiere permiso 'manage-colors'.
     *
     * @param int $id
     * @return ColorResource|JsonResponse <-- Tipo de retorno para un solo Resource o error 404
     */
     public function show(int $id): ColorResource|JsonResponse
     {
        $color = $this->colorService->getColor($id, $onlyActive = false);

        if (!$color) {
            return response()->json(['message' => 'Color no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new ColorResource($color);
     }


    /**
     * Almacena un nuevo color.
     * Endpoint: POST /api/colores
     * Protegido por JWT y requiere permiso 'manage-colors'.
     *
     * @param StoreColorRequest $request
     * @return JsonResponse // O ColorResource si no usas response()->json()
     */
    public function store(StoreColorRequest $request): JsonResponse
    {
        $color = $this->colorService->createColor($request->validated());
        return response()->json(new ColorResource($color), Response::HTTP_CREATED);
    }

    /**
     * Actualiza un color existente.
     * Endpoint: PUT/PATCH /api/colores/{id}
     * Protegido por JWT y requiere permiso 'manage-colors'.
     *
     * @param UpdateColorRequest $request
     * @param int $id
     * @return ColorResource|JsonResponse // O ColorResource si no usas response()->json() en éxito
     */
    public function update(UpdateColorRequest $request, int $id): ColorResource|JsonResponse
    {
        $color = $this->colorService->updateColor($id, $request->validated());

        if (!$color) {
            return response()->json(['message' => 'Color no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new ColorResource($color, Response::HTTP_OK);
    }

    /**
     * "Elimina" un color cambiando su estado a false.
     * Endpoint: DELETE /api/colores/{id}
     * Protegido por JWT y requiere permiso 'manage-colors'.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->colorService->deleteColor($id);

            if (!$success) {
                return response()->json(['message' => 'Color no encontrado o ya inactivo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Color desactivado con éxito'], Response::HTTP_OK);

        } catch (ColorCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar el color', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * Restaura un color cambiando su estado a true.
     * Endpoint: POST /api/colores/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-colors'.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        try {

            $success = $this->colorService->restoreColor($id);

            if (!$success) {
                return response()->json(['message' => 'Color no encontrado o ya activo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Color restaurado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al restaurar el color', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
