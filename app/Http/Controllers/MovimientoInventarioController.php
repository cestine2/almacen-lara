<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\MovimientoInventarioService;
use App\Http\Requests\StoreMovimientoInventarioRequest;
use App\Http\Resources\MovimientoInventarioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
// No necesitamos importar la excepción CannotBeDeleted aquí ya que no la lanzamos en el servicio store

class MovimientoInventarioController extends Controller
{
    protected $movimientoInventarioService;

    public function __construct(MovimientoInventarioService $movimientoInventarioService)
    {
        $this->movimientoInventarioService = $movimientoInventarioService;

        $this->middleware('auth:api');
        $this->middleware('permission:register-inventory-movement'); // Requiere permiso 'register-inventory-movement'
    }

    /**
     * Muestra una lista paginada de movimientos de inventario, aplicando filtros.
     * Endpoint: GET /api/movimientos-inventario?motivo={entrada|salida|ajuste}&tipo={Material|Producto}&material_id={id}&producto_id={id}&sucursal_id={id}&usuario_id={id}&per_page={cantidad}&page={numero}&start_date={fecha}&end_date={fecha}
     * Protegido por JWT y requiere permiso 'register-inventory-movement'.
     */
    public function index(Request $request): ResourceCollection
    {
        $filters = $request->only([
            'motivo', 'tipo', 'material_id', 'producto_id', 'sucursal_id',
            'start_date', 'end_date',
        ]);

        if (isset($filters['material_id'])) { $filters['material_id'] = (int) $filters['material_id']; }
        if (isset($filters['producto_id'])) { $filters['producto_id'] = (int) $filters['producto_id']; }
        if (isset($filters['sucursal_id'])) { $filters['sucursal_id'] = (int) $filters['sucursal_id']; }
        if (isset($filters['start_date']) && !strtotime($filters['start_date'])) { unset($filters['start_date']); }

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $movimientos = $this->movimientoInventarioService->listMovimientos(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return MovimientoInventarioResource::collection($movimientos);
    }

    /**
     * Muestra los detalles de un registro de movimiento de inventario específico.
     * Endpoint: GET /api/movimientos-inventario/{id}
     * Protegido por JWT y requiere permiso 'register-inventory-movement'.
     */
     public function show(int $id): MovimientoInventarioResource|JsonResponse
     {
        $movimiento = $this->movimientoInventarioService->getMovimiento($id);

        if (!$movimiento) {
        return response()->json(['message' => 'Movimiento de inventario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $movimiento->load(['sucursal', 'usuario', 'material', 'producto']);

        return new MovimientoInventarioResource($movimiento);
     }


    /**
     * Almacena un nuevo registro de movimiento de inventario y actualiza el stock.
     * Endpoint: POST /api/movimientos-inventario
     * Protegido por JWT y requiere permiso 'register-inventory-movement'.
     */
    public function store(StoreMovimientoInventarioRequest $request): JsonResponse
    {
        try {
            $movimiento = $this->movimientoInventarioService->createMovimiento($request->validated());
            return response()->json(new MovimientoInventarioResource($movimiento), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error inesperado al procesar el movimiento', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
