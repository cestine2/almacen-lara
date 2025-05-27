<?php

namespace App\Services;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function listUsers(array $filters = [], int $perPage = 20, ?int $page = null): LengthAwarePaginator
    {

        $query = $this->userRepository->getQuery();

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

        $query->with(['sucursal', 'role']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUser(int $id, bool $onlyActive = true): ?User
    {
        if($onlyActive){
            return $this->userRepository->findActive($id);
        }

        return $this->userRepository->find($id);
    }

     /**
     * Crea un nuevo usuario.
    */
    public function createUser(array $data): User
    {
        $user = $this->userRepository->create($data);
        return $user;
    }

     /**
     * Actualiza un usuario existente.
     */
    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->userRepository->update($id, $data);
        return $user;
    }

    /**
     * "Elimina" un usuario cambiando su estado a inactivo (false).
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->getUser($id, false);

        if (!$user) {
            return false;
        }

        return $this->userRepository->delete($id);
    }

     /**
     * Restaura un usuario cambiando su estado a activo (true).
     */
    public function restoreUser(int $id): bool
    {
        return $this->userRepository->restore($id);
    }
}
