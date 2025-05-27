<?php

namespace App\Services;

use App\Contracts\Repositories\ProveedorRepositoryInterface;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\ProveedorCannotBeDeletedException;

class ProveedorService
{
    protected $proveedorRepository;

    public function __construct(ProveedorRepositoryInterface $proveedorRepository)
    {
        $this->proveedorRepository = $proveedorRepository;
    }

    /**
     * Obtiene lista de proveedores, opcionalmente filtrados por estado.
     *
     * @param bool $onlyActive True para solo activos, False para todos.
     * @return Collection<Proveedor>
     */
    public function listProveedores(array $filters = [], int $perPage = 20, ?int $page = null): LengthAwarePaginator
    {
        $query = $this->proveedorRepository->getQuery();

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

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene los detalles de un proveedor por su ID.
     *
     * @param int $id
     * @param bool $onlyActive True para buscar solo activos, False para activos e inactivos.
     * @return Proveedor|null
     */
    public function getProveedor(int $id, bool $onlyActive = true): ?Proveedor
    {
        if ($onlyActive) {
            return $this->proveedorRepository->findActive($id);
        }
        return $this->proveedorRepository->find($id);
    }


     /**
     * Crea un nuevo proveedor.
     *
     * @param array $data Datos del proveedor
     * @return Proveedor
     */
    public function createProveedor(array $data): Proveedor
    {
        $proveedor = $this->proveedorRepository->create($data);
        return $proveedor;
    }

    /**
     * Actualiza un proveedor existente.
     *
     * @param int $id ID del proveedor a actualizar
     * @param array $data Nuevos datos
     * @return Proveedor|null El proveedor actualizado, o null si no se encontró.
     */
    public function updateProveedor(int $id, array $data): ?Proveedor
    {
        $proveedor = $this->proveedorRepository->update($id, $data);
        return $proveedor;
    }

    /**
     * "Elimina" un proveedor cambiando su estado a inactivo (false).
     *
     * @param int $id ID del proveedor a "eliminar"
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba inactivo.
     * @throws ProveedorCannotBeDeletedException Si no puede ser eliminado por lógica de negocio.
     */
    public function deleteProveedor(int $id): bool
    {
        // --- Lógica de Negocio: Prevenir Eliminación ---
        // Implementa aquí las reglas de negocio (ej: no eliminar si tiene materiales asociados, si es un proveedor 'default', etc.)
        // $proveedor = $this->getProveedor($id, false); // Buscar incluso si está inactivo
        // if ($proveedor && $proveedor->materiales()->exists()) { // Ejemplo si tuvieras relación materiales
        //     throw new ProveedorCannotBeDeletedException("El proveedor tiene materiales asociados y no puede ser eliminado.");
        // }
        // --- Fin Lógica de Negocio ---


        return $this->proveedorRepository->delete($id);
    }

     /**
     * Restaura un proveedor cambiando su estado a activo (true).
     *
     * @param int $id ID del proveedor a restaurar
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba activo.
     */
    public function restoreProveedor(int $id): bool
    {
        return $this->proveedorRepository->restore($id);
    }
}
