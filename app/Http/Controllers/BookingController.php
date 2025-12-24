<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Listar reservas del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $query = Booking::where('user_id', $user->id);

        // Filtros opcionales
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('space_id')) {
            $query->where('space_id', $request->space_id);
        }

        // Filtrar por rango de fechas
        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        $bookings = $query->with(['space', 'user'])
            ->orderBy('start_time', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Crear una nueva reserva
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'space_id' => 'required|exists:spaces,id',
            'event_title' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'attendees_count' => 'nullable|integer|min:1',
            'special_requirements' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validaciones adicionales
        $validationError = $this->validateBooking($request, $user);
        if ($validationError) {
            return response()->json([
                'success' => false,
                'message' => $validationError
            ], 422);
        }

        // Calcular precio total
        $space = Space::findOrFail($request->space_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $hours = $startTime->diffInHours($endTime);
        $totalPrice = $hours * $space->price_per_hour;

        $booking = Booking::create([
            'user_id' => $user->id,
            'space_id' => $request->space_id,
            'event_title' => $request->event_title,
            'event_description' => $request->event_description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'confirmed',
            'attendees_count' => $request->attendees_count,
            'special_requirements' => $request->special_requirements,
            'total_price' => $totalPrice,
        ]);

        $booking->load('space', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => $booking
        ], 201);
    }

    /**
     * Ver detalle de una reserva
     */
    public function show($id)
    {
        $user = auth('api')->user();
        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['space', 'user'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada o no tienes permiso para verla'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    /**
     * Actualizar una reserva
     */
    public function update(Request $request, $id)
    {
        $user = auth('api')->user();
        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada o no tienes permiso para editarla'
            ], 404);
        }

        // No permitir editar reservas canceladas o completadas
        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede editar una reserva cancelada o completada'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'space_id' => 'sometimes|required|exists:spaces,id',
            'event_title' => 'sometimes|required|string|max:255',
            'event_description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'attendees_count' => 'nullable|integer|min:1',
            'special_requirements' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validaciones adicionales si cambia fecha o espacio
        if ($request->has('start_time') || $request->has('end_time') || $request->has('space_id')) {
            $validationError = $this->validateBooking($request, $user, $booking->id);
            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError
                ], 422);
            }
        }

        // Recalcular precio si cambiaron las fechas o el espacio
        if ($request->has('start_time') || $request->has('end_time') || $request->has('space_id')) {
            $spaceId = $request->space_id ?? $booking->space_id;
            $space = Space::findOrFail($spaceId);
            $startTime = Carbon::parse($request->start_time ?? $booking->start_time);
            $endTime = Carbon::parse($request->end_time ?? $booking->end_time);
            $hours = $startTime->diffInHours($endTime);
            $request->merge(['total_price' => $hours * $space->price_per_hour]);
        }

        $booking->update($request->all());
        $booking->load('space', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada exitosamente',
            'data' => $booking
        ]);
    }

    /**
     * Eliminar una reserva
     */
    public function destroy($id)
    {
        $user = auth('api')->user();
        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada o no tienes permiso para eliminarla'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reserva eliminada exitosamente'
        ]);
    }

    /**
     * Cancelar una reserva (cancelación lógica)
     */
    public function cancel(Request $request, $id)
    {
        $user = auth('api')->user();
        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada o no tienes permiso para cancelarla'
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'La reserva ya está cancelada'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva cancelada exitosamente',
            'data' => $booking
        ]);
    }

    /**
     * Validar una reserva
     */
    private function validateBooking(Request $request, $user, $excludeBookingId = null)
    {
        $startTime = Carbon::parse($request->start_time ?? $request->get('start_time'));
        $endTime = Carbon::parse($request->end_time ?? $request->get('end_time'));
        $spaceId = $request->space_id ?? $request->get('space_id');

        // 1. Validar que no sea una fecha pasada
        if ($startTime->isPast()) {
            return 'No se pueden crear reservas en fechas pasadas';
        }

        // 2. Validar duración mínima (30 minutos) y máxima (8 horas)
        $durationInMinutes = $startTime->diffInMinutes($endTime);
        if ($durationInMinutes < 30) {
            return 'La duración mínima de una reserva es de 30 minutos';
        }
        if ($durationInMinutes > 480) { // 8 horas
            return 'La duración máxima de una reserva es de 8 horas';
        }

        // 3. Verificar que el espacio esté disponible
        $space = Space::find($spaceId);
        if (!$space || !$space->is_available) {
            return 'El espacio no está disponible';
        }

        // 4. Validar capacidad si se especifica número de asistentes
        if ($request->has('attendees_count') && $request->attendees_count > $space->capacity) {
            return 'El número de asistentes excede la capacidad del espacio';
        }

        // 5. Validar superposición de reservas
        $overlappingBookings = Booking::where('space_id', $spaceId)
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
            $overlappingBookings->where('id', '!=', $excludeBookingId);
        }

        if ($overlappingBookings->exists()) {
            return 'Ya existe una reserva para este espacio en el horario seleccionado';
        }

        // 6. Límite de reservas activas por usuario (máximo 5 reservas futuras)
        $activeBookingsCount = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>', now())
            ->count();

        if ($excludeBookingId) {
            $activeBookingsCount--;
        }

        if ($activeBookingsCount >= 5) {
            return 'Has alcanzado el límite máximo de 5 reservas activas';
        }

        return null;
    }
}
