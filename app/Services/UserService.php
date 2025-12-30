<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function getUsers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('slug', $filters['role']);
            });
        }

        return $query->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active' => true,
        ]);

        if (isset($data['role_id'])) {
            $user->roles()->attach($data['role_id']);
        }

        $user->load('roles');

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active' => $data['is_active'] ?? null,
        ], fn($value) => $value !== null);

        $user->update($updateData);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (isset($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        }

        $user->load('roles');

        return $user;
    }

    public function deleteUser(User $user, int $currentUserId): void
    {
        if ($user->id === $currentUserId) {
            throw new \Exception('No puedes eliminar tu propia cuenta');
        }

        $user->delete();
    }

    public function getRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return Role::all(['id', 'name', 'slug', 'description']);
    }
}
