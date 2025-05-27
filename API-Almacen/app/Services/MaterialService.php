<?php

namespace App\Services;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\InventarioRepositoryInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\MaterialCannotBeDeletedException;
use Ramsey\Uuid\Uuid;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\Color;


class MaterialService
{
    protected $materialRepository;
    protected $inventarioRepository;

    public function __construct(MaterialRepositoryInterface $materialRepository, InventarioRepositoryInterface $inventarioRepository)
    {
        $this->materialRepository = $materialRepository;
        $this->inventarioRepository = $inventarioRepository;
    }

    /**
     * Obtiene una lista paginada de materiales, opcionalmente filtrados por estado y otros criterios.
     *
     * @param array $filters Array asociativo de filtros procesados (ej: ['estado' => true, 'categoria_id' => 1])
     * @param int $perPage Cantidad de elementos por página.
     * @param int|null $page Número de página (null para usar el de la request).
     */
    public function listMaterials(array $filters = [], int $perPage = 20, ?int $page = null): LengthAwarePaginator
    {
        $query = $this->materialRepository->getQuery();

        $estadoFilter = true;

        if (isset($filters['status'])) {
            $estadoFilter = match ($filters['status']) {
                'active' => true,
                'inactive' => false,
                'all' => null,
                default => true,
            };
            unset($filters['status']);
        }

        if ($estadoFilter !== null) {
            $query->where('estado', $estadoFilter);
        }

        if (isset($filters['categoria_id'])) {
            $query->where('categoria_id', $filters['categoria_id']);
        }

        if (isset($filters['proveedor_id'])) {
            $query->where('proveedor_id', $filters['proveedor_id']);
        }

        if (isset($filters['cod_articulo'])) {
            $query->where('cod_articulo', $filters['cod_articulo']);
        }

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        $query->with(['categoria', 'proveedor', 'color']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene los detalles de un material por su ID.
     *
     * @param int $id
     * @param bool $onlyActive True para buscar solo activos, False para activos e inactivos.
     */
    public function getMaterial(int $id, bool $onlyActive = true): ?Material
    {
        if($onlyActive) {
            return $this->materialRepository->findActive($id);
        }

        return $this->materialRepository->find($id);
    }

     /**
     * Crea un nuevo material.
     * Genera automáticamente el código de barras con prefijo 'MAT-' si no se proporciona.
     *
     * @param array $data Datos del material (validados por StoreMaterialRequest)
     */
    public function createMaterial(array $data): Material
    {
        $data['codigo_barras'] = 'MAT-' . Uuid::uuid4()->toString();
        $material = $this->materialRepository->create($data);

        return $material;
    }

    /**
     * Actualiza un material existente.
     *
     * @param int $id ID del material a actualizar
     * @param array $data Nuevos datos
     */
    public function updateMaterial(int $id, array $data): ?Material
    {
        $material = $this->materialRepository->update($id, $data);
        return $material;
    }

    /**
     * "Elimina" un material cambiando su estado a inactivo (false).
     * Incluye lógica de negocio para prevenir la eliminación si no es posible.
     *
     * @param int $id ID del material a "eliminar"
     */
    public function deleteMaterial(int $id): bool
    {
        $material = $this->getMaterial($id, false);

        if (!$material) {
            return false;
        }

        $totalStock = $this->inventarioRepository->getQuery()
                                                 ->where('material_id', $material->id)
                                                 ->where('tipo', 'Material')
                                                 ->sum('stock_actual');

        if ($totalStock > 0) {
            throw new MaterialCannotBeDeletedException("El material '{$material->nombre}' tiene stock positivo ({$totalStock} unidades en total) y no puede ser desactivado.");
        }

        return $this->materialRepository->delete($id);
    }

     /**
     * Restaura un material cambiando su estado a activo (true).
     *
     * @param int $id ID del material a restaurar
     */
    public function restoreMaterial(int $id): bool
    {
        return $this->materialRepository->restore($id);
    }
}
