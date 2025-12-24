<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
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
        'group',
    ];

    // Relaciones

    /**
     * Roles que tienen este permiso
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission')
                    ->withTimestamps();
    }

    // Scopes

    /**
     * Scope para filtrar permisos por grupo
     */
    public function scopeOfGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
