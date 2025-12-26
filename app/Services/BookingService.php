<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Space;
use Carbon\Carbon;

class BookingService
{
    public function calculatePrice(Space $space, string $startTime, string $endTime): float
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $hours = $start->diffInHours($end);
        
        return $hours * $space->price_per_hour;
    }

    public function validateBooking(
        string $startTime,
        string $endTime,
        int $spaceId,
        $user,
        ?int $attendeesCount = null,
        ?int $excludeBookingId = null
    ): ?string {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        if ($start->isPast()) {
            return 'No se pueden crear reservas en fechas pasadas';
        }

        $durationInMinutes = $start->diffInMinutes($end);
        if ($durationInMinutes < 30) {
            return 'La duración mínima de una reserva es de 30 minutos';
        }
        if ($durationInMinutes > 480) {
            return 'La duración máxima de una reserva es de 8 horas';
        }

        $space = Space::find($spaceId);
        if (!$space || !$space->is_available) {
            return 'El espacio no está disponible';
        }

        if ($attendeesCount && $attendeesCount > $space->capacity) {
            return 'El número de asistentes excede la capacidad del espacio';
        }

        if ($this->hasOverlappingBookings($spaceId, $start, $end, $excludeBookingId)) {
            return 'Ya existe una reserva para este espacio en el horario seleccionado';
        }

        if ($this->hasReachedBookingLimit($user->id, $excludeBookingId)) {
            return 'Has alcanzado el límite máximo de 5 reservas activas';
        }

        return null;
    }

    private function hasOverlappingBookings(
        int $spaceId,
        Carbon $startTime,
        Carbon $endTime,
        ?int $excludeBookingId = null
    ): bool {
        $query = Booking::where('space_id', $spaceId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    private function hasReachedBookingLimit(int $userId, ?int $excludeBookingId = null): bool
    {
        $activeBookingsCount = Booking::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>', now())
            ->count();

        if ($excludeBookingId) {
            $activeBookingsCount--;
        }

        return $activeBookingsCount >= 5;
    }

    public function createBooking(array $data, $user): Booking
    {
        $space = Space::findOrFail($data['space_id']);
        $totalPrice = $this->calculatePrice($space, $data['start_time'], $data['end_time']);

        $booking = Booking::create([
            'user_id' => $user->id,
            'space_id' => $data['space_id'],
            'event_title' => $data['event_title'],
            'event_description' => $data['event_description'] ?? null,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => 'confirmed',
            'attendees_count' => $data['attendees_count'] ?? null,
            'special_requirements' => $data['special_requirements'] ?? null,
            'total_price' => $totalPrice,
        ]);

        return $booking->load('space', 'user');
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        // Recalcular precio si se modificó el tiempo o el espacio
        if (isset($data['start_time']) || isset($data['end_time']) || isset($data['space_id'])) {
            $spaceId = $data['space_id'] ?? $booking->space_id;
            $space = Space::findOrFail($spaceId);
            $startTime = $data['start_time'] ?? $booking->start_time;
            $endTime = $data['end_time'] ?? $booking->end_time;
            $data['total_price'] = $this->calculatePrice($space, $startTime, $endTime);
        }

        $booking->update($data);
        return $booking->load('space', 'user');
    }

    public function cancelBooking(Booking $booking, string $reason): Booking
    {
        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        return $booking;
    }

    public function getUserBookings($user, array $filters = [], int $perPage = 15)
    {
        $query = Booking::where('user_id', $user->id);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['space_id'])) {
            $query->where('space_id', $filters['space_id']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('start_time', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('end_time', '<=', $filters['end_date']);
        }

        return $query->with(['space', 'user'])
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    public function canEditBooking(Booking $booking): bool
    {
        return !in_array($booking->status, ['cancelled', 'completed']);
    }

    public function canCancelBooking(Booking $booking): bool
    {
        return $booking->status !== 'cancelled';
    }
}
