<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'capacity',
        'price_per_hour',
        'location',
        'floor',
        'amenities',
        'image_url',
        'is_available',
        'rules',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amenities' => 'array', // JSON a array
        'is_available' => 'boolean',
        'capacity' => 'integer',
        'price_per_hour' => 'decimal:2',
    ];

    // Relaciones

    /**
     * Reservas de este espacio
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Categorías del espacio (muchos a muchos)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(SpaceCategory::class, 'space_category')
                    ->withTimestamps();
    }

    /**
     * Reseñas del espacio
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Configuraciones de disponibilidad
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(SpaceAvailability::class);
    }

    // Scopes

    /**
     * Scope para espacios disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para filtrar por capacidad mínima
     */
    public function scopeWithMinCapacity($query, int $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }
}
