<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Relaciones

    /**
     * Usuarios que tienen este rol
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
                    ->withTimestamps();
    }

    /**
     * Permisos asociados a este rol
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
                    ->withTimestamps();
    }

    // Métodos auxiliares

    /**
     * Asignar un permiso al rol
     */
    public function givePermission(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }
        
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Remover un permiso del rol
     */
    public function removePermission(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }
        
        $this->permissions()->detach($permission->id);
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}
