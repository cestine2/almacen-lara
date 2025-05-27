<?php

namespace App\Services;

use App\Contracts\Repositories\ColorRepositoryInterface;
use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ColorCannotBeDeletedException;

class ColorService
{
    protected $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    /**
     * Obtiene lista de colores, opcionalmente filtrados por estado.
     *
     * @param bool $onlyActive True para solo activos, False para todos.
     * @return Collection<Color>
     */
    public function listColores(bool $onlyActive = false): Collection
    {
        if ($onlyActive) {
            return $this->colorRepository->getAllActive();
        }
        return $this->colorRepository->getAll();
    }

    /**
     * Obtiene los detalles de un color por su ID.
     *
     * @param int $id
     * @param bool $onlyActive True para buscar solo activos, False para activos e inactivos.
     * @return Color|null
     */
    public function getColor(int $id, bool $onlyActive = true): ?Color
    {
        if ($onlyActive) {
             return $this->colorRepository->findActive($id);
        }
        return $this->colorRepository->find($id);
    }

    /**
     * Crea un nuevo color.
     *
     * @param array $data Datos del color
     * @return Color
    */
    public function createColor(array $data): Color
    {
        $color = $this->colorRepository->create($data);
        return $color;
    }

    /**
     * Actualiza un color existente.
     *
     * @param int $id ID del color a actualizar
     * @param array $data Nuevos datos
     * @return Color|null El color actualizado, o null si no se encontró.
    */
    public function updateColor(int $id, array $data): ?Color
    {
        $color = $this->colorRepository->update($id, $data);
        return $color;
    }

    /**
     * "Elimina" un color cambiando su estado a inactivo (false).
     * Incluye lógica de negocio para prevenir la eliminación si no es posible.
     *
     * @param int $id ID del color a "eliminar"
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba inactivo.
     * @throws ColorCannotBeDeletedException Si el color no puede ser eliminado.
    */
    public function deleteColor(int $id): bool
    {
        $color = $this->getColor($id, false);

        if (!$color) {
            return false;
        }

        $color->load(['productos', 'materiales']);

        if ($color->productos->count() > 0 || $color->materiales->count() > 0) {
            throw new ColorCannotBeDeletedException("El color '{$color->nombre}' está asignado a productos o materiales y no puede ser eliminado.");
        }

        return $this->colorRepository->delete($id);
    }

     /**
     * Restaura un color cambiando su estado a activo (true).
     *
     * @param int $id ID del color a restaurar
     * @return bool True si se cambió el estado, false si no se encontró o ya estaba activo.
     */
    public function restoreColor(int $id): bool
    {
        return $this->colorRepository->restore($id);
    }
}
