<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use App\Models\Van;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    
    public function index(Van $van): JsonResponse
    {
        $reviews = $van->reviews()
            ->with('customer:id,name,email')
            ->latest()
            ->paginate(15);

        return response()->json([
            'van_id' => $van->id,
            'average_rating' => $van->average_rating,
            'reviews' => $reviews,
        ], 200);
    }

    /**
     * Store a newly created review for the van (Customer only, cannot review own van).
     */
    public function store(StoreReviewRequest $request, Van $van): JsonResponse
    {
        Gate::authorize('create', [Review::class, $van]);

        $review = $van->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review->load('customer:id,name,email'),
        ], 201);
    }

    
    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        Gate::authorize('update', $review);

        $review->update($request->validated());

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review->load('customer:id,name,email'),
        ], 200);
    }

    
    public function destroy(Review $review): JsonResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully.',
        ], 200);
    }
}
