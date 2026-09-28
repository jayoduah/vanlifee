<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use App\Models\Van;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService)
    {
    }
    
    public function index(Van $van): JsonResponse
    {
        $reviews = $this->reviewService->getForVan($van);

        return response()->json([
            'van_id' => $van->id,
            'average_rating' => $van->average_rating,
            'reviews' => $reviews,
        ], 200);
    }

    public function store(StoreReviewRequest $request, Van $van): JsonResponse
    {
        Gate::authorize('create', [Review::class, $van]);

        $review = $this->reviewService->create($van, $request->user()->id, $request->validated());

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review->load('customer:id,name,email'),
        ], 201);
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        Gate::authorize('update', $review);

        $review = $this->reviewService->update($review, $request->validated());

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review->load('customer:id,name,email'),
        ], 200);
    }
    
    public function destroy(Review $review): JsonResponse
    {
        Gate::authorize('delete', $review);

        $this->reviewService->delete($review);

        return response()->json([
            'message' => 'Review deleted successfully.',
        ], 200);
    }
}
