<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\InventarioRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Ramsey\Uuid\Uuid;
use App\Exceptions\ProductCannotBeDeletedException;
use App\Models\Categoria;
use App\Models\Color;
use App\Models\Sucursal;


class ProductService
{
    protected $productRepository;
    protected $inventarioRepository;

    public function __construct(ProductRepositoryInterface $productRepository, InventarioRepositoryInterface $inventarioRepository)
    {
        $this->productRepository = $productRepository;
        $this->inventarioRepository = $inventarioRepository;
    }

    /**
     * Obtiene una lista paginada de productos, opcionalmente filtrados por estado y otros criterios.
     *
     * @param array $filters Array asociativo de filtros (ej: ['status' => true, 'categoria_id' => 1])
     * @param int $perPage Cantidad de elementos por página.
     * @param int|null $page Número de página (null para usar el de la request).
     */
    public function listProducts(array $filters = [], int $perPage = 20, ?int $page = null): LengthAwarePaginator
    {
        $query = $this->productRepository->getQuery();

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

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (isset($filters['talla'])) {
            $query->where('talla', $filters['talla']);
        }

        $query->with(['categoria', 'color']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene los detalles de un producto por su ID.
     *
     * @param int $id
     * @param bool $onlyActive True para buscar solo activos, False para activos e inactivos.
     */
    public function getProduct(int $id, bool $onlyActive = true): ?Product
    {
        if($onlyActive){
            return $this->productRepository->findActive($id);
        }

        return $this->productRepository->find($id);
    }

    /**
     * Crea un nuevo producto.
    */
    public function createProduct(array $data): Product
    {
        $data['codigo_barras'] = 'PROD-'.Uuid::uuid4()->toString();
        $product = $this->productRepository->create($data);

        return $product;
    }

    /**
     * Actualiza un producto existente.
     */
    public function updateProduct(int $id, array $data): ?Product
    {
        $product = $this->productRepository->update($id, $data);
        return $product;
    }

    /**
     * "Elimina" un producto cambiando su estado a inactivo (false).
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->getProduct($id, false);

        if (!$product) {
            return false;
        }

        $totalStock = $this->inventarioRepository->getQuery()
                                                 ->where('producto_id', $product->id)
                                                 ->where('tipo', 'Producto')
                                                 ->sum('stock_actual');

        if ($totalStock > 0) {
            throw new ProductCannotBeDeletedException("El producto '{$product->nombre}' tiene stock positivo ({$totalStock} unidades en total) y no puede ser desactivado.");
        }

        return $this->productRepository->delete($id);
    }

     /**
     * Restaura un producto cambiando su estado a activo (true).
     */
    public function restoreProduct(int $id): bool
    {
        return $this->productRepository->restore($id);
    }
}
