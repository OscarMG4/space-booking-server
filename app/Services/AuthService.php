<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
    }

    public function login(array $credentials): ?array
    {
        $guard = Auth::guard('api');
        
        if (!$token = $guard->attempt($credentials)) {
            return null;
        }

        $user = $guard->user();

        if (!$user->is_active) {
            throw new \Exception('Usuario inactivo');
        }

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
    }

    public function logout(): void
    {
        $guard = Auth::guard('api');
        $guard->logout();
    }

    public function refreshToken(): array
    {
        $token = JWTAuth::refresh();

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
    }

    public function getAuthenticatedUser()
    {
        return auth('api')->user();
    }

    public function isUserActive(User $user): bool
    {
        return $user->is_active;
    }
}
