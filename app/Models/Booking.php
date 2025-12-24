<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'space_id',
        'event_title',
        'event_description',
        'start_time',
        'end_time',
        'status',
        'attendees_count',
        'special_requirements',
        'total_price',
        'cancellation_reason',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'attendees_count' => 'integer',
        'total_price' => 'decimal:2',
    ];

    // Relaciones

    /**
     * Usuario que realizó la reserva
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Espacio reservado
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * Historial de cambios de la reserva
     */
    public function histories(): HasMany
    {
        return $this->hasMany(BookingHistory::class);
    }

    /**
     * Reseña asociada a esta reserva
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Notificaciones asociadas
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes

    /**
     * Scope para reservas confirmadas
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope para reservas canceladas
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope para reservas completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para reservas futuras
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                     ->whereIn('status', ['confirmed', 'pending']);
    }

    /**
     * Scope para reservas de un usuario específico
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
