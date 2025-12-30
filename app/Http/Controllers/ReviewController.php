<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\RejectReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'space_id' => $request->input('space_id'),
            'is_approved' => $request->has('is_approved') ? $request->boolean('is_approved') : null,
            'is_flagged' => $request->has('is_flagged') ? $request->boolean('is_flagged') : null,
            'rating' => $request->input('rating'),
        ];

        $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $canModerate = $currentUser->hasPermission('reviews.moderate');
        
        $reviews = $this->reviewService->getReviews(
            $filters, 
            $request->get('per_page', 15),
            $canModerate
        );

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        try {
            $review = $this->reviewService->createReview(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Reseña creada exitosamente. Está pendiente de moderación.',
                'data' => new ReviewResource($review),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function approve(string $id): JsonResponse
    {
        try {
            $review = Review::findOrFail($id);
            $this->reviewService->approveReview($review);

            return response()->json([
                'success' => true,
                'message' => 'Reseña aprobada exitosamente',
                'data' => new ReviewResource($review->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function reject(RejectReviewRequest $request, string $id): JsonResponse
    {
        try {
            $review = Review::findOrFail($id);
            $this->reviewService->rejectReview($review, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Reseña rechazada exitosamente',
                'data' => new ReviewResource($review->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $review = Review::findOrFail($id);
            
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            $this->reviewService->deleteReview(
                $review, 
                Auth::id(), 
                $currentUser->hasPermission('reviews.moderate')
            );

            return response()->json([
                'success' => true,
                'message' => 'Reseña eliminada exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
