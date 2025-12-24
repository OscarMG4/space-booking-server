<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpaceAvailability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'space_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
        'effective_from',
        'effective_until',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    // Relaciones

    /**
     * Espacio al que pertenece esta configuración
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    // Scopes

    /**
     * Scope para configuraciones disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope para filtrar por día de la semana
     */
    public function scopeForDay($query, string $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Scope para configuraciones vigentes
     */
    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now();
        
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $date);
        })
        ->where(function ($q) use ($date) {
            $q->whereNull('effective_until')
              ->orWhere('effective_until', '>=', $date);
        });
    }
}
