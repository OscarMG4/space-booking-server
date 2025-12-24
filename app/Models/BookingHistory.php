<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'changes',
        'notes',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'changes' => 'array', // JSON a array
    ];

    // Relaciones

    /**
     * Reserva a la que pertenece este historial
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Usuario que realizó la acción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    /**
     * Scope para filtrar por tipo de acción
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
