<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\CancelBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    public function index(Request $request)
    {
        $user = auth('api')->user();
        
        $filters = $request->only(['status', 'space_id', 'start_date', 'end_date']);
        $perPage = $request->get('per_page', 15);
        
        $bookings = $this->bookingService->getUserBookings($user, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings)->response()->getData(true)
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $user = auth('api')->user();

        $validationError = $this->bookingService->validateBooking(
            $request->start_time,
            $request->end_time,
            $request->space_id,
            $user,
            $request->attendees_count
        );

        if ($validationError) {
            return response()->json([
                'success' => false,
                'message' => $validationError
            ], 422);
        }

        $booking = $this->bookingService->createBooking($request->validated(), $user);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => new BookingResource($booking)
        ], 201);
    }

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
            'data' => new BookingResource($booking)
        ]);
    }

    public function update(UpdateBookingRequest $request, $id)
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

        if (!$this->bookingService->canEditBooking($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede editar una reserva cancelada o completada'
            ], 422);
        }

        if ($request->has('start_time') || $request->has('end_time') || $request->has('space_id')) {
            $validationError = $this->bookingService->validateBooking(
                $request->start_time ?? $booking->start_time,
                $request->end_time ?? $booking->end_time,
                $request->space_id ?? $booking->space_id,
                $user,
                $request->attendees_count,
                $booking->id
            );

            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError
                ], 422);
            }
        }

        $booking = $this->bookingService->updateBooking($booking, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada exitosamente',
            'data' => new BookingResource($booking)
        ]);
    }

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

    public function cancel(CancelBookingRequest $request, $id)
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

        if (!$this->bookingService->canCancelBooking($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'La reserva ya está cancelada'
            ], 422);
        }

        $booking = $this->bookingService->cancelBooking($booking, $request->cancellation_reason);

        return response()->json([
            'success' => true,
            'message' => 'Reserva cancelada exitosamente',
            'data' => new BookingResource($booking)
        ]);
    }
}
