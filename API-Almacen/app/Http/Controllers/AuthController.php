<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    protected $authService;

    // Inyecta el AuthService
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Maneja el intento de login y devuelve el token JWT.
     * Endpoint: POST /api/auth/login (El '/api' viene del prefijo en routes/web.php)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Llama al Servicio para el login
        $token = $this->authService->login($request->validated());

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Obtiene los datos del usuario autenticado.
     * Endpoint: POST /api/auth/me (Requiere token válido - protegido por middleware)
     *
     * @return UserResource
     */
    public function me(): UserResource|JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        $allPermissions = $user->getAllPermissions();
        $user->load('roles');

        return (UserResource::make($user))
           ->additional([
               'permissions' => $allPermissions->toArray(),
        ]);
    }

    /**
     * Cierra la sesión del usuario (invalida el token actual).
     * Endpoint: POST /api/auth/logout (Requiere token válido - protegido por middleware)
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json(['message' => 'Se ha cerrado sesión correctamente'], Response::HTTP_OK);
    }

    /**
     * Refresca un token expirado.
     * Endpoint: POST /api/auth/refresh (Requiere token válido - protegido por middleware)
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = $this->authService->refresh();
            return $this->respondWithToken($token);
        } catch (\Throwable $e) { // Captura excepciones de refresh, ej: TokenInvalidException
             return response()->json(['error' => 'No se pudo actualizar el token', 'details' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }


    /**
     * Helper para estructurar la respuesta del token.
     *
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
