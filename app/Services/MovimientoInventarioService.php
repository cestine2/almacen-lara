<?php

namespace App\Services;

use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\InventarioRepositoryInterface; // <-- Necesitamos el Repositorio de Inventario
use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Material;
use App\Models\Product;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MovimientoInventarioService
{
    protected $movimientoInventarioRepository;
    protected $inventarioRepository;

    public function __construct(
        MovimientoInventarioRepositoryInterface $movimientoInventarioRepository,
        InventarioRepositoryInterface $inventarioRepository
    ) {
        $this->movimientoInventarioRepository = $movimientoInventarioRepository;
        $this->inventarioRepository = $inventarioRepository;
    }

    /**
     * Obtiene una lista paginada de movimientos de inventario, aplicando filtros ya procesados.
     * La ordenación por defecto (created_at DESC) está definida en el Repositorio.
     *
     * @param array $filters Array asociativo de filtros procesados (ej: ['sucursal_id' => 1, 'tipo' => 'entrada'])
     * @param int $perPage Cantidad de elementos por página.
     * @param int|null $page Número de página (null para usar el de la request).
     */
    public function listMovimientos(array $filters = [], int $perPage = 15, ?int $page = null): LengthAwarePaginator
    {
        $query = $this->movimientoInventarioRepository->getQuery();

        if (isset($filters['motivo'])) {
            $query->where('motivo', $filters['motivo']);
        }

        if (isset($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (isset($filters['material_id'])) {
            $query->where('material_id', $filters['material_id'])->where('tipo', 'Material');
        }

        if (isset($filters['producto_id'])) {
            $query->where('producto_id', $filters['producto_id'])->where('tipo', 'Producto');
        }

        if (isset($filters['sucursal_id'])) {
            $query->where('sucursal_id', $filters['sucursal_id']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $query->with(['sucursal', 'usuario', 'material', 'producto']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene los detalles de un registro de movimiento de inventario por su ID.
     *
     * @param int $id
     */
    public function getMovimiento(int $id): ?MovimientoInventario
    {
        $movimiento = $this->movimientoInventarioRepository->find($id);

        if ($movimiento) {
            $movimiento->load(['sucursal', 'usuario', 'material', 'producto']);
        }

        return $movimiento;
    }


     /**
     * Crea un nuevo registro de movimiento de inventario y actualiza el stock correspondiente.
     *
     * @param array $data Datos del movimiento (validados por StoreMovimientoInventarioRequest)
     * @throws \InvalidArgumentException Si el tipo/ID no coincide o la sucursal no existe.
     * @throws \RuntimeException Si no se encuentra el registro de Inventario para actualizar el stock.
     */
    public function createMovimiento(array $data): MovimientoInventario
    {
        $data['usuario_id'] = Auth::id();

        if (!$data['usuario_id']) {
            throw new \RuntimeException("No se pudo determinar el usuario autenticado.");
        }

        $itemId = null;
        if ($data['tipo'] === 'Material') {

            if (empty($data['material_id']) || !($item = Material::find($data['material_id']))) {
                throw new \InvalidArgumentException("El Material seleccionado no existe o es inválido.");
            }

            $data['producto_id'] = null;
            $itemId = $data['material_id'];

        } elseif ($data['tipo'] === 'Producto') {

             if (empty($data['producto_id']) || !($item = Product::find($data['producto_id']))) {
                throw new \InvalidArgumentException("El Producto seleccionado no existe o es inválido.");
            }

            $data['material_id'] = null;
            $itemId = $data['producto_id'];

        } else {
            throw new \InvalidArgumentException("Tipo de movimiento inválido.");
        }

        if (empty($data['sucursal_id']) || !Sucursal::find($data['sucursal_id'])) {
            throw new \InvalidArgumentException("La Sucursal seleccionada no existe o es inválida.");
        }

        $data['total'] = 0.00;


        if ($data['tipo'] === 'Producto') {
            $data['total'] = $data['cantidad'] * $data['precio_unitario'];
        }

        try {

            DB::beginTransaction();

            $movimiento = $this->movimientoInventarioRepository->create($data);
            $inventario = $this->inventarioRepository->getQuery()
                                                    ->where('tipo', $data['tipo'])
                                                    ->where('sucursal_id', $data['sucursal_id'])
                                                    ->where($data['tipo'] === 'Material' ? 'material_id' : 'producto_id', $itemId)
                                                    ->first();

            if (!$inventario) {
                if ($data['motivo'] === 'salida') {
                    DB::rollBack();
                    throw new \RuntimeException("No se encontró registro de inventario existente para actualizar stock de salida.");
                }

                $inventario = $this->inventarioRepository->create([
                    'tipo' => $data['tipo'],
                    'material_id' => $data['material_id'],
                    'producto_id' => $data['producto_id'],
                    'sucursal_id' => $data['sucursal_id'],
                    'usuario_id' => $data['usuario_id'],
                    'stock_actual' => 0,
                    'estado' => true,
                ]);

                if (!$inventario) {
                    DB::rollBack();
                    throw new \RuntimeException("Error al crear un nuevo registro de inventario para el movimiento.");
                }
            }

            if ($data['motivo'] === 'salida') {

                if ($inventario->stock_actual < $data['cantidad']) {
                    DB::rollBack();
                    throw new \InvalidArgumentException("Stock insuficiente para realizar la salida. Stock actual: {$inventario->stock_actual}, Cantidad solicitada: {$data['cantidad']}.");

                }
            }

            $cantidadAfectar = $data['cantidad'];

            if ($data['motivo'] === 'salida') {
                $cantidadAfectar = -$cantidadAfectar;
            }

            $inventario->stock_actual += $cantidadAfectar;
            $inventario->usuario_id = $data['usuario_id'];
            $inventario->save();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $movimiento;
    }

    // Puedes añadir métodos específicos si necesitas (ej: reporte de movimientos por fecha/item)
    // public function getReporteMovimientos(array $filters = []) { ... }
}
