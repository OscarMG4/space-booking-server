<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => new UserResource($data['user']),
                'token' => $data['token'],
                'token_type' => $data['token_type'],
                'expires_in' => $data['expires_in']
            ]
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'user' => new UserResource($data['user']),
                    'token' => $data['token'],
                    'token_type' => $data['token_type'],
                    'expires_in' => $data['expires_in']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    public function refresh()
    {
        $data = $this->authService->refreshToken();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function me()
    {
        $user = $this->authService->getAuthenticatedUser();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }
}
