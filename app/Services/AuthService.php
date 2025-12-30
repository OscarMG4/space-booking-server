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

        $defaultRole = \App\Models\Role::firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'Usuario', 'description' => 'Usuario estándar del sistema']
        );
        $user->roles()->attach($defaultRole->id);

        $user->load('roles');

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

        if (!$user || !$user->is_active) {
            throw new \Exception('Usuario inactivo');
        }

        /** @var \App\Models\User|null $user */

        $user->load('roles');

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
        /** @var \App\Models\User|null $user */
        $user = auth('api')->user();
        
        if ($user) {
            $user->load('roles');
        }
        
        return $user;
    }

    public function isUserActive(User $user): bool
    {
        return $user->is_active;
    }
}
