<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /**
     * Crea un nuevo rol.
     *
     * @param array $data Los datos para crear el rol (name, guard_name).
     * @return Role
     */
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Lista roles.
     *
     */
    public function listRoles(): Collection
    {
        return Role::get();
    }

    /**
     * Obtiene un rol por su ID.
     *
     * @param int $id ID del rol.
     * @return Role|null
     */
    public function getRole(int $id): ?Role
    {
        // Spatie tiene relaciones para permisos y usuarios, que se pueden cargar
        return Role::findById($id);
    }

    /**
     * Actualiza un rol existente.
     *
     * @param int $id ID del rol a actualizar.
     * @param array $data Los datos para actualizar el rol.
     * @return Role|null
     */
    public function updateRole(int $id, array $data): ?Role
    {
        $role = $this->getRole($id);

        if (!$role) {
            return null;
        }

        $role->update($data);
        return $role;
    }

    /**
     * Elimina un rol.
     *
     * @param int $id ID del rol a eliminar.
     * @return bool
     */
    public function deleteRole(int $id): bool
    {
        $role = $this->getRole($id);

        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    /**
     * Asigna (sincroniza) permisos a un rol.
     *
     * @param int $roleId ID del rol.
     * @param array $permissionNames Nombres de los permisos a asignar.
     * @return Role|null
     */
    public function assignPermissionsToRole(int $roleId, array $permissionNames): ?Role
    {
        $role = $this->getRole($roleId);

        if (!$role) {
            return null;
        }

        $role->syncPermissions($permissionNames);

        return $role;
    }

    /**
     * Revoca permisos específicos de un rol.
     * (Menos común que syncPermissions para un endpoint de asignación, pero útil)
     *
     * @param int $roleId ID del rol.
     * @param array $permissionNames Nombres de los permisos a revocar.
     * @return Role|null
     */
    public function revokePermissionsFromRole(int $roleId, array $permissionNames): ?Role
    {
        $role = $this->getRole($roleId);

        if (!$role) {
            return null;
        }

        $role->revokePermissionTo($permissionNames);

        return $role;
    }

    /**
     * Obtiene todos los permisos disponibles.
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }
}
