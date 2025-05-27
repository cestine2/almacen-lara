<?php

namespace App\Services;

use App\Contracts\Repositories\SucursalRepositoryInterface; // Usa la interfaz del Repositorio
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\SucursalCannotBeDeletedException; // Ejemplo de excepción de negocio

class SucursalService
{
    protected $sucursalRepository;

    public function __construct(SucursalRepositoryInterface $sucursalRepository)
    {
        $this->sucursalRepository = $sucursalRepository;
    }

    /**
     * Obtiene una lista de todas las sucursales activas.
     *
     * @return Collection<Sucursal>
     */
    public function listSucursales(bool $onlyActive = false): Collection
    {
        if ($onlyActive) {
            return $this->sucursalRepository->getAllActive();
        }
        return $this->sucursalRepository->getAll();
    }

     /**
     * Obtiene una lista de todas las sucursales (activas e inactivos).
     *
     * @return Collection<Sucursal>
     */
    public function listAllSucursales(): Collection
    {
        return $this->sucursalRepository->getAll();
    }

    /**
     * Obtiene los detalles de una sucursal por su ID, solo si está activa.
     *
     * @param int $id
     * @return Sucursal|null
     */
    public function getSucursal(int $id): ?Sucursal
    {
        return $this->sucursalRepository->findActive($id);
    }

     /**
     * Obtiene los detalles de una sucursal por su ID (activa o inactiva).
     *
     * @param int $id
     * @return Sucursal|null
     */
    public function findSucursal(int $id): ?Sucursal
    {
        return $this->sucursalRepository->find($id);
    }

     /**
     * Crea una nueva sucursal.
     *
     * @param array $data Datos de la sucursal (nombre, direccion - opcional, estado - opcional)
     * @return Sucursal
     */
    public function createSucursal(array $data): Sucursal
    {
        $sucursal = $this->sucursalRepository->create($data);
        return $sucursal;
    }

    /**
     * Actualiza una sucursal existente.
     *
     * @param int $id ID de la sucursal a actualizar
     * @param array $data Nuevos datos
     * @return Sucursal|null La sucursal actualizada, o null si no se encontró.
     */
    public function updateSucursal(int $id, array $data): ?Sucursal
    {
        $sucursal = $this->sucursalRepository->update($id, $data);
        return $sucursal;
    }

    /**
     * "Elimina" una sucursal cambiando su estado a inactivo (false).
     *
     * @param int $id ID de la sucursal a "eliminar"
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba inactiva.
     * @throws SucursalCannotBeDeletedException Si la sucursal no puede ser eliminada por lógica de negocio.
     */
    public function deleteSucursal(int $id): bool
    {

        $sucursal = $this->sucursalRepository->find($id);

        if (!$sucursal) {
            return false;
        }

        $hasProductInventory = $sucursal->inventarios()
                                        ->whereNotNull('producto_id')
                                        ->exists(); // Usa exists() para mayor eficiencia

        $hasMaterialInventory = $sucursal->inventarios()
                                        ->whereNotNull('material_id')
                                        ->exists();

        if ($hasProductInventory || $hasMaterialInventory) {
            throw new SucursalCannotBeDeletedException("La sucursal '{$sucursal->nombre}' tiene inventario de productos o materiales y no puede ser eliminada.");
        }

        if ($sucursal->users()->where('estado', true)->exists()) {
            throw new SucursalCannotBeDeletedException("La sucursal '{$sucursal->nombre}' tiene usuarios activos asignados y no puede ser eliminada.");
        }

        return $this->sucursalRepository->delete($id);
    }

     /**
     * Restaura una sucursal cambiando su estado a activo (true).
     *
     * @param int $id ID de la sucursal a restaurar
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba activo.
     */
    public function restoreSucursal(int $id): bool
    {
        return $this->sucursalRepository->restore($id);
    }
}
