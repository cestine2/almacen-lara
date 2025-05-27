<?php

namespace App\Services;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;
use App\Models\Product;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\CategoriaCannotBeDeletedException;


class CategoriaService
{
    protected $categoriaRepository;

    public function __construct(CategoriaRepositoryInterface $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    /**
     * Obtiene lista de categorías, opcionalmente filtradas por estado.
     *
     * @param bool $onlyActive True para solo activas, False para todos.
     * @return Collection<Categoria>
     */
    public function listCategorias(array $filters = [], int $perPage = 20, ?int $page = null): LengthAwarePaginator
    {
        $query = $this->categoriaRepository->getQuery();

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

        if (isset($filters['type'])) {
            $query->where('tipo', $filters['type']);
        }

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene los detalles de una categoría por su ID.
     *
     * @param int $id
     * @param bool $onlyActive True para buscar solo activas, False para activos e inactivos.
     * @return Categoria|null
     */
    public function getCategoria(int $id, bool $onlyActive = true): ?Categoria
    {
        if ($onlyActive) {
            return $this->categoriaRepository->findActive($id);
        }

        return $this->categoriaRepository->find($id);
    }


     /**
     * Crea una nueva categoría.
     *
     * @param array $data Datos de la categoría
     * @return Categoria
     */
    public function createCategoria(array $data): Categoria
    {
        $categoria = $this->categoriaRepository->create($data);
        return $categoria;
    }

    /**
     * Actualiza una categoría existente.
     *
     * @param int $id ID de la categoría a actualizar
     * @param array $data Nuevos datos
     * @return Categoria|null La categoría actualizada, o null si no se encontró.
     */
    public function updateCategoria(int $id, array $data): ?Categoria
    {
        $categoria = $this->categoriaRepository->update($id, $data);
        return $categoria;
    }

    /**
     * "Elimina" una categoría cambiando su estado a inactivo (false).
     * Incluye lógica de negocio para prevenir la eliminación si no es posible.
     *
     * @param int $id ID de la categoría a "eliminar"
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba inactivo.
     * @throws CategoriaCannotBeDeletedException Si la categoría no puede ser eliminada.
     */
    public function deleteCategoria(int $id): bool
    {
        $categoria = $this->getCategoria($id, false);

        if (!$categoria) {
            return false;
        }


        $categoria->load(['productos', 'materiales']);

        if ($categoria->productos->count() > 0 || $categoria->materiales->count() > 0) {
            throw new CategoriaCannotBeDeletedException("La categoría '{$categoria->nombre}' está asignada a productos o materiales y no puede ser eliminada.");
        }

        return $this->categoriaRepository->delete($id);
    }

     /**
     * Restaura una categoría cambiando su estado a activo (true).
     *
     * @param int $id ID de la categoría a restaurar
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba activo.
     */
    public function restoreCategoria(int $id): bool
    {
        return $this->categoriaRepository->restore($id);
    }
}
