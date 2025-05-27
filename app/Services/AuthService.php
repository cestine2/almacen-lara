<?php

namespace App\Services;
use App\Models\User;

// use App\Contracts\Repositories\UserRepositoryInterface;
// use Illuminate\Support\Facades\Hash;
// use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Intenta autenticar a un usuario y generar un token JWT.
     *
     * @param array $credentials ['email' => '...', 'password' => '...']
     * @return string|null El token JWT si es exitoso, null si falla.
     */
    public function login(array $credentials): ?string
    {
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            return null;
        }

        return $token;
    }

    /**
     * Cierra la sesión del usuario (invalida el token actual).
     *
     * @return void
     */
    public function logout(): void
    {
        auth('api')->logout();
    }

    /**
     * Refresca un token JWT.
     *
     * @return string El nuevo token.
     * @throws \Tymon\JWTAuth\Exceptions\TokenInvalidException
     */
    public function refresh(): string
    {
        return auth('api')->refresh();
    }

    /**
     * Obtiene el usuario autenticado.
     *
     * @return \App\Models\User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        return auth('api')->user();
    }
}
