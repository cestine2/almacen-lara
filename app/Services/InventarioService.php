<?php

namespace App\Services;

use App\Contracts\Repositories\InventarioRepositoryInterface;
use App\Models\Inventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\InventarioCannotBeDeletedException;
use App\Models\Material; // Importa Material para validación
use App\Models\Product; // Importa Product para validación
use App\Models\Sucursal; // Importa Sucursal para validación
use App\Models\User; // Importa User para validación (aunque el usuario logueado ya está validado por auth)
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado

class InventarioService
{
    protected $inventarioRepository;

    public function __construct(InventarioRepositoryInterface $inventarioRepository)
    {
        $this->inventarioRepository = $inventarioRepository;
    }

    /**
     * Obtiene una lista paginada de registros de inventario, aplicando filtros ya procesados.
     *
     * @param array $filters Array asociativo de filtros procesados (ej: ['estado' => true, 'sucursal_id' => 1])
     * @param int $perPage Cantidad de elementos por página.
     * @param int|null $page Número de página (null para usar el de la request).
     */
    public function listInventarios(array $filters = [], int $perPage = 20, ?int $page = null): LengthAwarePaginator
    {
        $query = $this->inventarioRepository->getQuery();

        if (isset($filters['sucursal_id'])) {
            $query->where('sucursal_id', $filters['sucursal_id']);
        }

        if (isset($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        $query->with(['sucursal', 'material', 'producto', 'usuario']);


        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene los detalles de un registro de inventario por su ID.
     *
     * @param int $id
     * @param bool $onlyActive True para buscar solo activos, False para activos e inactivos.
     */
    public function getInventario(int $id, bool $onlyActive = true): ?Inventario
    {
        if($onlyActive){
            return $this->inventarioRepository->findActive($id);
        }

        return $this->inventarioRepository->find($id);
    }


     /**
     * Crea un nuevo registro de inventario.
     *
     * @param array $data Datos del registro de inventario (validados por StoreInventarioRequest)
     */
    public function createInventario(array $data): Inventario
    {
        $data['usuario_id'] = Auth::id();

        if ($data['tipo'] === 'Material') {
            if (!Material::where('id', $data['material_id'])->exists()) {
                throw new \InvalidArgumentException("El Material seleccionado no existe.");
            }
            $data['producto_id'] = null;
        } elseif ($data['tipo'] === 'Producto') {
            if (!Product::where('id', $data['producto_id'])->exists()) {
                throw new \InvalidArgumentException("El Producto seleccionado no existe.");
            }
            $data['material_id'] = null;
        } else {
            throw new \InvalidArgumentException("Tipo de inventario inválido.");
        }

        if (!Sucursal::where('id', $data['sucursal_id'])->exists()) {
            throw new \InvalidArgumentException("La Sucursal seleccionada no existe.");
        }

        $existingInventario = $this->inventarioRepository->findByItemAndSucursal(
            $data['tipo'],
            $data['tipo'] === 'Material' ? $data['material_id'] : $data['producto_id'],
            $data['sucursal_id']
        );

        if ($existingInventario) {
            throw new \InvalidArgumentException("El producto ya fue registrado");
        }

        $inventario = $this->inventarioRepository->create($data);

        return $inventario;
    }

    /**
     * Actualiza un registro de inventario existente.
     * Nota: No se permite cambiar 'tipo', 'material_id', 'producto_id' después de la creación.
     *
     * @param int $id ID del registro de inventario a actualizar
     * @param array $data Nuevos datos (validados por UpdateInventarioRequest)
     */
    public function updateInventario(int $id, array $data): ?Inventario
    {
        $data['usuario_id'] = Auth::id();

        if (isset($data['sucursal_id']) && !Sucursal::where('id', $data['sucursal_id'])->exists()) {
            throw new \InvalidArgumentException("La Sucursal seleccionada no existe.");
        }

        $inventario = $this->inventarioRepository->update($id, $data);

        return $inventario;
    }

    /**
     * "Elimina" un registro de inventario cambiando su estado a inactivo (false).
     * Incluye lógica de negocio para prevenir la eliminación si no es posible.
     *
     * @param int $id ID del registro de inventario a "eliminar"
     */
    public function deleteInventario(int $id): bool
    {
        $inventario = $this->getInventario($id, false);

        if(!$inventario){
            return false;
        }

        return $this->inventarioRepository->delete($id);
    }

     /**
     * Restaura un registro de inventario cambiando su estado a activo (true).
     *
     * @param int $id ID del registro de inventario a restaurar
     */
    public function restoreInventario(int $id): bool
    {
        return $this->inventarioRepository->restore($id);
    }
}
