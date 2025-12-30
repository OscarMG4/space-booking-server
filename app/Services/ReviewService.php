<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewService
{
    public function getReviews(array $filters, int $perPage = 15, bool $canModerate = false): LengthAwarePaginator
    {
        $query = Review::with(['user', 'space', 'booking']);

        if (!empty($filters['space_id'])) {
            $query->where('space_id', $filters['space_id']);
        }

        if (isset($filters['is_approved'])) {
            $query->where('is_approved', $filters['is_approved']);
        }

        if (isset($filters['is_flagged'])) {
            $query->where('is_flagged', $filters['is_flagged']);
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        if (!$canModerate) {
            $query->where('is_approved', true);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createReview(array $data, int $userId): Review
    {
        $booking = Booking::findOrFail($data['booking_id']);

        $this->validateReviewCreation($booking, $userId);

        $review = Review::create([
            'user_id' => $userId,
            'space_id' => $booking->space_id,
            'booking_id' => $booking->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'is_approved' => false,
            'is_flagged' => false,
        ]);

        $review->load(['user', 'space']);

        return $review;
    }

    public function approveReview(Review $review): Review
    {
        if ($review->is_approved) {
            throw new \Exception('Esta reseña ya está aprobada');
        }

        $review->update(['is_approved' => true]);

        return $review;
    }

    public function rejectReview(Review $review, string $reason): Review
    {
        $review->update([
            'is_approved' => false,
            'is_flagged' => true,
            'admin_notes' => $reason,
        ]);

        return $review;
    }

    public function deleteReview(Review $review, int $userId, bool $isModerator): void
    {
        if ($review->user_id !== $userId && !$isModerator) {
            throw new \Exception('No tienes permiso para eliminar esta reseña');
        }

        $review->delete();
    }

    private function validateReviewCreation(Booking $booking, int $userId): void
    {
        if ($booking->user_id !== $userId) {
            throw new \Exception('No puedes crear una reseña para una reserva que no te pertenece');
        }

        if (!in_array($booking->status, ['confirmed', 'completed'])) {
            throw new \Exception('Solo puedes reseñar reservas confirmadas o completadas');
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            throw new \Exception('Ya existe una reseña para esta reserva');
        }
    }
}
